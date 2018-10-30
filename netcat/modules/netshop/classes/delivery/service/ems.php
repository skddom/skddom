<?php

class nc_netshop_delivery_service_ems extends nc_netshop_delivery_service_russianpost {

    /** @var string название службы */
    protected $name = NETCAT_MODULE_NETSHOP_DELIVERY_EMS;

    /** @var string тип доставки */
    protected $delivery_type = nc_netshop_delivery::DELIVERY_TYPE_COURIER;

    /** @var int максимальный вес посылки (в граммах) */
    protected $max_weight = 31500;

    // название свойств в ответе для получения данных о стоимости и сроках
    protected $request_posting_kind = 'EMS';
    protected $request_way_forward = 'AVIA';
    protected $response_time_range_property = 'emsDeliveryTimeRange';
    protected $response_time_property = 'emsTime';
    protected $response_min_time_property = 'emsMinTime';
    protected $response_max_time_property = 'emsMaxTime';

    /**
     * Возврат HTML кода сформированного
     * бланка посылки
     *
     * @return string
     */
    public function print_package_form() {
        $forms = nc_netshop::get_instance()->forms->get_objects();

        $delivery_data = $this->data;

        $international = false;

        if ($delivery_data['to_country']) {
            $to_country = mb_strtolower($delivery_data['to_country'], 'utf-8');
            if ($to_country != 'россия' && $to_country != 'российская федерация') {
                $international = true;
            }
        }

        $form = $international ? $forms->ems_package_international : $forms->ems_package_russia;

        ob_start();
        $form->template($delivery_data);
        return ob_get_clean();
    }

    /**
     * Возврат HTML кода сформированного
     * бланка наложенного платежа
     *
     * @return string
     */
    public function print_cash_on_delivery_form() {
        return $this->print_package_form();
    }

    /**
     * Возврат информации по точкам
     * следования посылки
     *
     * @return array|null
     */
    public function get_tracking_information() {
        $nc_core = nc_Core::get_object();

        $tracking_number = urlencode($this->data['tracking_number']);

        $result = $this->make_http_request('http://www.emspost.ru/ru/tracking/?id=' . $tracking_number, null, array('Referer' => 'http://www.emspost.ru/ru/'));

        $tracking = null;

        if ($result) {
            $result = $nc_core->utf8->utf2win($result);

            $tracking_results_start = strpos($result, '<div id="trackingResult"');
            $tracking_results = substr($result, $tracking_results_start);

            $table_start = strpos($tracking_results, '<table');
            $table_start = strpos($tracking_results, '<table', $table_start + 6);
            $table_start = strpos($tracking_results, '<table', $table_start + 6);

            $tracking_results = substr($tracking_results, $table_start);
            $tracking_results = substr($tracking_results,0, strpos($tracking_results, '/table>') + 7);

            $rows = explode('</tr>', $tracking_results);
            foreach($rows as $row) {
                $cols = explode('</td>', $row);
                foreach($cols as $index => $col) {
                    $cols[$index] = trim(strip_tags($col));
                }

                if (count($cols) == 5) {
                    if ($tracking === null) {
                        $tracking = array();
                    }

                    $tracking[] = array(
                        'time' => $cols[0],
                        'index' => $cols[1],
                        'index_description' => $cols[2],
                        'status' => $cols[3],
                    );
                }
            }
        }

        if ($tracking && $nc_core->NC_UNICODE) {
            $tracking = $nc_core->utf8->array_win2utf($tracking);
        }

        return $tracking;
    }

}