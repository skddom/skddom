<?php

/**
 * Интеграция с Яндекс.Доставкой
 */
class nc_netshop_delivery_service_yandex extends nc_netshop_delivery_service {

    /** @var string название службы */
    protected $name = NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX;

    /** @var string тип доставки */
    protected $delivery_type = nc_netshop_delivery::DELIVERY_TYPE_MULTIPLE;

    /** @var bool служба может предложить более одного варианта доставки */
    protected $can_provide_multiple_variants = true;

    /**
     * Поля, которым нужны соответствия
     * @var array
     */
    protected $mapped_fields = array(
        'from_city' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_FROM_CITY,
        'to_zipcode' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_ZIP_CODE,
        'to_city' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_CITY,
        'to_address' =>  NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_ADDRESS,
    );

    protected $yandex_keys = array();
    protected $yandex_ids = array();

    /**
     * Рассчитать стоимость посылки.
     * При успешном выполнении возвращается массив:
     * array(
     *     'price' => стоимость доставки,
     *     'currency' => валюта стоимости доставки
     *     'min_days' => минимальное количество дней на доставку
     *     'max_days' => максимальное количество дней на доставку
     * )
     *
     * При ошибке возвращается null
     *
     * @return array|null
     */
    public function calculate_delivery() {
        return null;
    }


    /**
     * Запрос к API Яндекс.Доставки
     *
     * @return array|false
     */
    protected function get_yandex_delivery_data() {
        $package_size = $this->data['items']->get_package_size();

        $data = array(
            'city_from' => $this->data['from_city'],
            'city_to' => $this->data['to_city'],
            'index_city' => $this->data['to_zipcode'],
            'weight' => sprintf('%.3F', $this->data['weight'] / 1000),
            'length' => (int)$package_size[0],
            'width' => (int)$package_size[1],
            'height' => (int)$package_size[2],
            'assessed_value' => $this->data['valuation'],
        );

        return $this->make_yandex_api_request('searchDeliveryList', $data);
    }


    /**
     * @param string $method
     * @param array $data
     * @return array|false
     */
    protected function make_yandex_api_request($method, array $data) {
        if (!isset($this->yandex_keys[$method])) {
            $this->show_message_for_supervisor("Yandex.Delivery keys are not set for method $method");
            return false;
        }

        $nc_core = nc_core::get_object();

        if (!$nc_core->NC_UNICODE) {
            $data = $nc_core->utf8->array_win2utf($data);
        }

        $data['client_id'] = $this->yandex_ids['client']['id'];
        $data['sender_id'] = $this->yandex_ids['senders'][0]['id'];

        $values_string = self::get_array_values_for_yandex_signature($data);
        $data['secret_key'] = md5($values_string . $this->yandex_keys[$method]);
        
        $url = 'https://delivery.yandex.ru/api/last/' . $method;

        $result = $this->make_http_request($url, $data);
        if (!$result) {
            $this->show_message_for_supervisor("No response from $url");
            return false;
        }

        $result = json_decode($result, true);
        if (!$result) {
            $this->show_message_for_supervisor('Cannot decode response');
            return false;
        }

        if (nc_array_value($result, 'status') === 'error') {
            $errors = '';
            foreach ($result['data']['errors'] as $key => $value) {
                $errors[] = "<em>$key:</em> $value";
            }
            $this->show_message_for_supervisor(implode('<br>', $errors));
        }

        return $result;
    }

    /**
     * @param $message
     * @param string $status
     */
    protected function show_message_for_supervisor($message, $status = 'error') {
        global $perm;
        if ($perm instanceof Permission && $perm->isSupervisor()) {
            nc_print_status("<strong>{$this->name}</strong><br>$message", $status);
        }
    }

    /**
     * @param $data
     * @return array|string
     */
    static protected function get_array_values_for_yandex_signature($data) {
        if (!is_array($data)) {
            return $data;
        }
        ksort($data);
        return implode('', array_map(function($k) {
            return nc_netshop_delivery_service_yandex::get_array_values_for_yandex_signature($k);
        }, $data));
    }

    /**
     * Возврат HTML кода сформированного
     * бланка посылки
     *
     * @return string
     */
    public function print_package_form() {
        return '';
    }

    /**
     * Возврат HTML кода сформированного
     * бланка наложенного платежа
     *
     * @return string
     */
    public function print_cash_on_delivery_form() {
        return '';
    }

    /**
     * Возврат информации по точкам
     * следования посылки
     *
     * @return array|null
     */
    public function get_tracking_information() {
        return null;
    }

    /**
     * Устанавливает дополнительные настройки (из настроек способа доставки)
     *
     * @param array $settings
     */
    public function set_settings(array $settings) {
        parent::set_settings($settings);
        $this->yandex_keys = json_decode($this->get_setting('keys') ?: '{}', true);
        $this->yandex_ids = json_decode($this->get_setting('ids') ?: '{}', true);
    }

    /**
     * Возвращает массив с описанием дополнительных настроек способа доставки
     * (в формате, подходящем для nc_a2f).
     *
     * @return array
     */
    public function get_settings_fields() {
        return array(
            'keys' =>
                array(
                    'type' => 'textarea',
                    'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_KEYS,
                    'size' => '6',
                    'codemirror' => false,
                ),
            'ids' =>
                array(
                    'type' => 'textarea',
                    'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_IDS,
                    'size' => '6',
                    'codemirror' => false,
                ),
            'payment_charge' =>
                array (
                    'type' => 'select',
                    'subtype' => 'static',
                    'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE,
                    'values' => array(
                        0 => NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE_INCLUDED,
                        1 => NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE_EXTRA,
                    ),
                    'default_value' => 0,
                )
        );
    }

    /**
     * @param nc_netshop_delivery_method $method
     * @return nc_netshop_delivery_method_collection
     */
    public function get_variants(nc_netshop_delivery_method $method) {
        $response = $this->get_yandex_delivery_data();
        if (!$response || nc_array_value($response, 'status') !== 'ok') {
            return new nc_netshop_delivery_method_collection();
        }

        $converter = new nc_netshop_delivery_service_yandex_converter($this, $method, $this->data);
        return $converter->get_delivery_variants($response);
    }

}
