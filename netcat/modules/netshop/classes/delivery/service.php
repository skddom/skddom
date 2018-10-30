<?php

abstract class nc_netshop_delivery_service {

    /**
     * Коды ошибок
     */
    const ERROR_OK = nc_netshop_delivery_estimate::ERROR_OK;
    const ERROR_CANNOT_CONNECT_TO_GATE = nc_netshop_delivery_estimate::ERROR_SERVICE_CANNOT_CONNECT_TO_GATE;
    const ERROR_GATE_ERROR = nc_netshop_delivery_estimate::ERROR_SERVICE_GATE_ERROR;
    const ERROR_WRONG_WEIGHT = nc_netshop_delivery_estimate::ERROR_SERVICE_WRONG_WEIGHT;
    const ERROR_WRONG_RECIPIENT = nc_netshop_delivery_estimate::ERROR_SERVICE_WRONG_RECIPIENT;
    const ERROR_WRONG_SENDER = nc_netshop_delivery_estimate::ERROR_SERVICE_WRONG_SENDER;

    /**
     * Максимальное количество секунд для ожидания ответа от удалённого сервера
     *
     * @var int
     */
    protected $http_request_timeout = 5;

    /**
     * Название службы
     *
     * @var string
     */
    protected $name = '';
    /** @var string устаревшее свойство вместо $name, оставлено для обратной совместимости. Не используйте! */
    protected $delivery_type_name;

    /**
     * Тип доставки (переопределите в конкретном классе при необходимости)
     *
     * @var string одна из констант nc_netshop_delivery::DELIVERY_TYPE_*
     */
    protected $delivery_type = nc_netshop_delivery::DELIVERY_TYPE_COURIER;

    /**
     * Поля, которым нужны соответствия
     *
     * @var array
     */
    protected $mapped_fields = array();

    /**
     * Дополнительные настройки, специфичные для службы расчёта доставки
     */
    protected $settings = array();

    /**
     * @var bool служба может предложить более одного варианта доставки
     */
    protected $can_provide_multiple_variants = false;

    /**
     * Атрибуты посылки
     *
     * @var array
     */
    protected $fields = array(
        'from_legal_entity', //юр. наименование отправителя
        'from_fullname', //полное имя отправителя
        'from_country', //страна отправления
        'from_city', //город отправления
        'from_region', //регион отправления
        'from_district', //район отправления
        'from_street', //улица отправления
        'from_house', //дом отправления
        'from_block', //строение отправления
        'from_floor', //этаж отправления
        'from_apartment', //квартира/офис отправления
        'from_zipcode', //индекс пункта отправления
        'from_phone', //телефон отправителя
        'to_legal_entity', //юр. наименование получателя
        'to_fullname', //полное имя получателя
        'to_country', //страна получения
        'to_city', //город получения
        'to_region', //регион получения
        'to_district', //район получения
        'to_street', //улица получения
        'to_house', //дом получения
        'to_block', //строение получения
        'to_floor', //этаж получения
        'to_apartment', //квартира/офис получения
        'to_zipcode', //индекс пункта получения
        'to_phone', //телефон получателя
        'description', //описание посылки
        'weight', //вес посылки
        'valuation', //ценость посылки
        'cash_on_delivery', //сумма наложенного платежа
        'receiver_inn', //инн получателя платежа
        'receiver_corr', //корр. счет получателя платежа
        'receiver_account', //номер расчетного счета получателя платежа
        'receiver_bank', //банк получателя платежа
        'receiver_bik', //БИК банка получателя платежа
        'tracking_number', //номер отслеживания посылки
        'items', // состав заказа, объект nc_netshop_item_collection
    );

    /**
     * Последняя ошибка
     *
     * @var string
     */
    protected $last_error = '';

    /**
     * Код последней ошибки,
     * 0 - выполнение успешно
     *
     * @var int
     */
    protected $last_error_code = self::ERROR_OK;

    /**
     * Данные об отправлении
     *
     * @var array
     */
    protected $data = array();

    /**
     * Данные посылки
     *
     * @param $data
     */
    public function __construct(array $data = array()) {
        $this->data = array();

        $this->set_data($data);

        foreach ($this->fields as $field) {
            if (!isset($this->data[$field])) {
                $this->data[$field] = null;
            }
        }
    }

    /**
     * @param array $data
     */
    public function set_data(array $data) {
        foreach ($data as $index => $value) {
            if (in_array($index, $this->fields)) {
                $this->data[$index] = $value;
            }
        }
    }

    /**
     * Возвращает имя службы доставки
     *
     * @return string
     */
    public function get_name() {
        return !empty($this->delivery_type_name) ? $this->delivery_type_name : $this->name;
    }

    /**
     * Устаревший метод вместо get_name(), [пока] оставлен для обратной
     * совместимости
     *
     * @deprecated
     * @return string
     */
    public function get_delivery_type_name() {
        return $this->get_name();
    }

    /**
     * Возвращает поля
     *
     * @return array
     */
    public function get_mapped_fields() {
        return $this->mapped_fields;
    }

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
    abstract public function calculate_delivery();

    /**
     * Возврат HTML кода сформированного
     * бланка посылки
     *
     * @return string
     */
    abstract public function print_package_form();

    /**
     * Возврат HTML кода сформированного
     * бланка наложенного платежа
     *
     * @return string
     */
    abstract public function print_cash_on_delivery_form();

    /**
     * Возврат информации по точкам
     * следования посылки
     *
     * @return array|null
     */
    abstract public function get_tracking_information();

    /**
     * @return string
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * @return int
     */
    public function get_last_error_code() {
        return $this->last_error_code;
    }

    /**
     * HTTP(s)-запрос
     *
     * @param string $url
     * @param string|array|null $post  тело POST (строка) или параметры для POST (массив).
     *   Если не указано, будет выполнен GET-запрос.
     * @param array $headers   массив с заголовками (заголовок => значение)
     * @return mixed|string
     */
    protected function make_http_request($url, $post = null, array $headers = array()) {
        if (function_exists('curl_init')) {
            return $this->fetch_with_curl($url, $post, $headers);
        }
        else {
            return $this->fetch_with_stream($url, $post, $headers);
        }
    }

    /**
     * HTTP(s)-запрос с использованием cURL
     *
     * @param string $url
     * @param string|array|null $post  тело POST (строка) или параметры для POST (массив).
     *   Если не указано, будет выполнен GET-запрос.
     * @param array $headers   массив с заголовками (заголовок => значение)
     * @return mixed|string
     */
    protected function fetch_with_curl($url, $post, array $headers) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->http_request_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->http_request_timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        if ($post !== null) {
            curl_setopt($curl, CURLOPT_POST, true);
            if (is_array($post)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post, null, '&'));
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
            else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                $headers['Content-Length'] = strlen($post);
            }
        }

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->get_string_headers($headers));
        }

        $result = curl_exec($curl);
        return $result;
    }

    /**
     * HTTP(s)-запрос с использованием потоков
     *
     * @param string $url
     * @param string|array|null $post  тело POST (строка) или параметры для POST (массив).
     *   Если не указано, будет выполнен GET-запрос.
     * @param array $headers   массив с заголовками (заголовок => значение)
     * @return mixed|string
     */
    protected function fetch_with_stream($url, $post, array $headers) {
        $protocol_options = array(
            'timeout' => $this->http_request_timeout,
        );

        if ($post) {
            $protocol_options['method'] = 'POST';

            if (is_array($post)) {
                $post = http_build_query($post, null, '&');
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }

            $protocol_options['content'] = $post;
        }

        if ($headers) {
            $protocol_options['header'] = join("\r\n", $this->get_string_headers($headers));
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $stream_options[$scheme] = $protocol_options;
        $stream_context = stream_context_create($stream_options);

        // ошибки не выводятся, чтобы не порушить вывод ответа (JSON)
        $result = @file_get_contents($url, false, $stream_context);

        return $result;
    }

    /**
     * Преобразует ассоциативный массив заголовков [заголовок => значение]
     * в массив строк "заголовок: значение"
     * @param array $headers
     * @return array
     */
    protected function get_string_headers(array $headers) {
        $string_headers = array();
        foreach ($headers as $k => $v) {
            $string_headers[] = "$k: $v";
        }
        return $string_headers;
    }

    /**
     * Устанавливает дополнительные настройки (из настроек способа доставки)
     * @param array $settings
     */
    public function set_settings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * Возвращает значение дополнительной настройки
     * @param string $setting_name
     * @return string|null
     */
    public function get_setting($setting_name) {
        return nc_array_value($this->settings, $setting_name);
    }

    /**
     * Возвращает массив с описанием дополнительных настроек способа доставки
     * (в формате, подходящем для nc_a2f).
     *
     * @return array
     */
    public function get_settings_fields() {
        return array();
    }

    /**
     * Возвращает истину, если служба может вернуть более одного варианта доставки
     *
     * @return bool
     */
    public function can_provide_multiple_variants() {
        return $this->can_provide_multiple_variants;
    }

    /**
     * Возвращает варианты доставки
     *
     * @param nc_netshop_delivery_method $method
     * @return nc_netshop_delivery_method_collection
     */
    public function get_variants(nc_netshop_delivery_method $method) {
        return new nc_netshop_delivery_method_collection(array($method));
    }

    /**
     * Возвращает тип доставки для данной службы
     *
     * @return string одна из констант nc_netshop_delivery::DELIVERY_TYPE_*
     */
    public function get_delivery_type() {
        return $this->delivery_type;
    }

    /**
     * Возвращает пункт выдачи по его идентификатору
     * @param $point_id
     * @return nc_netshop_delivery_point|null
     * @throws Exception
     */
    public function get_delivery_point($point_id) {
        return null;
    }

}
