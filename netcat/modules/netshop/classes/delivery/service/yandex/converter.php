<?php

/**
 * Класс для конвертации данных от Яндекс.Доставки в объекты netshop
 */
class nc_netshop_delivery_service_yandex_converter {

    /** @var nc_netshop_delivery_service_yandex  */
    protected $service;

    /** @var  nc_netshop_delivery_method */
    protected $method;

    /** @var  array */
    protected $data;

    /**
     *
     * @param nc_netshop_delivery_service_yandex $service
     * @param nc_netshop_delivery_method $method
     * @param array $delivery_data
     */
    public function __construct(nc_netshop_delivery_service_yandex $service, nc_netshop_delivery_method $method, array $delivery_data) {
        $this->service = $service;
        $this->method = $method;
        $this->data = $delivery_data;
    }

    /**
     * @param array $response
     * @return nc_netshop_delivery_method_collection
     */
    public function get_delivery_variants(array $response) {
        if (!nc_core::get_object()->NC_UNICODE) {
            $response = nc_core::get_object()->utf8->array_utf2win($response);
        }

        $variants = new nc_netshop_delivery_method_collection();

        $method = $this->method;
        $method_id = $method->get_id();
        $site_id = $method->get('catalogue_id');
        $shipment_days_of_week = $method->get('shipment_days_of_week');
        $shipment_time = $method->get('shipment_time');
        $priority = $method->get('priority');
        $extra_charge_absolute = $method->get('extra_charge_absolute');
        $extra_charge_relative = $method->get('extra_charge_relative');
        $minimum_delivery_days = $method->get('minimum_delivery_days');
        $maximum_delivery_days = $method->get('maximum_delivery_days');

        foreach (nc_array_value($response, 'data', array()) as $data) {
            $delivery_type = $this->get_variant_delivery_type($data);
            $variant = new nc_netshop_delivery_method_variant(array(
                'id' => "$method_id:$data[tariffId]",
                'method_id' => $method_id,
                'external_id' => $data['tariffId'],
                'catalogue_id' => $site_id,
                'name' => $data['delivery']['name'],
                'delivery_type' => $delivery_type,
                'description' => $this->get_variant_description($data),
                'condition' => '',
                'extra_charge_absolute' => $data['costWithRules'] + $extra_charge_absolute,
                'extra_charge_relative' => $extra_charge_relative,
                'minimum_delivery_days' => $data['minDays'] + $minimum_delivery_days,
                'maximum_delivery_days' => $data['maxDays'] + $maximum_delivery_days,
                'shipment_days_of_week' => $shipment_days_of_week,
                'shipment_time' => $shipment_time,
                'priority' => $priority,
                'enabled' => true,
            ));

            if ($delivery_type === nc_netshop_delivery::DELIVERY_TYPE_PICKUP) {
                $variant->set_delivery_points($this->get_variant_delivery_points($data, $data['delivery']['name']));
            }

            $variant->set_payment_on_delivery_cost($this->get_variant_payment_on_delivery_cost($data));

            $variants->add($variant);
        }
        return $variants;
    }

    /**
     * @param $data
     * @return string
     */
    protected function get_variant_delivery_type(array $data) {
        switch (strtoupper($data['type'])) {
            case 'PICKUP': return nc_netshop_delivery::DELIVERY_TYPE_PICKUP;
            case 'TODOOR': return nc_netshop_delivery::DELIVERY_TYPE_COURIER;
            case 'POST':   return nc_netshop_delivery::DELIVERY_TYPE_POST;
            default:       return nc_netshop_delivery::DELIVERY_TYPE_COURIER;
        }
    }

    /**
     * @param $data
     * @return string
     */
    protected function get_variant_description(array $data) {
        $description = '';

        if ($data['type'] === 'TODOOR') {
            if (!empty($data['settings']['wait_in_cost']) && !empty($data['settings']['wait_enabled'])) {
                $description .= NETCAT_MODULE_NETSHOP_DELIVERY_WITH_CHECK;
            }
            if (isset($data['delivery']['courier']['schedules'])) {
                $schedule = $this->get_delivery_schedule($data['delivery']['courier']['schedules']);
                if (count($schedule)) {
                    $description .= NETCAT_MODULE_NETSHOP_DELIVERY_COURIER_TIME . $schedule->get_compact_schedule_summary_string() . '.';
                }
            }
        }

        return $description;
    }

    /**
     * @param $data
     * @param $service_name
     * @return nc_netshop_delivery_point_collection
     */
    protected function get_variant_delivery_points(array $data, $service_name) {
        $delivery_points = new nc_netshop_delivery_point_external_collection();
        foreach (nc_array_value($data, 'pickupPoints', array()) as $point_data) {
            $delivery_points->add($this->get_delivery_point($point_data, $service_name));
        }
        return $delivery_points;
    }


    /**
     * @param array $point_data
     * @param $service_name
     * @return nc_netshop_delivery_point_external
     */
    protected function get_delivery_point(array $point_data, $service_name) {
        // убираем почтовый индекс из адреса, в нём нет смысла для доставки до пункта выдачи
        $local_address = preg_replace('/^\d+,\s*/', '', $point_data['full_address']);
        // город тоже убираем: он у всех пунктов одинаковый
        $local_address = preg_replace('/^[^,]+,\s*/', '', $local_address);

        // часто у пунктов выдачи в качестве названия указано некрасивое «техническое» имя,
        // оно обычно содержит знак '_'; в этом случае подменяем на название службы доставки
        $name = strpos($point_data['name'], '_') !== false ? $service_name : $point_data['name'];

        $point = new nc_netshop_delivery_point_external(array(
            'id' => 'YD_' . $point_data['id'], // с префиксом, чтобы не путать с пунктами выдачи других служб или локальными
            'name' => $name,
            'description' => $point_data['address']['comment'],
            'phones' => $this->get_delivery_point_phones_string($point_data['phones']),
            'location_name' => $point_data['location_name'],
            'address' => $local_address,
            'latitude' => $point_data['lat'],
            'longitude' => $point_data['lng'],
            'payment_on_delivery_cash' => (bool)$point_data['has_payment_cash'],
            'payment_on_delivery_card' => (bool)$point_data['has_payment_card'],
            'enabled' => true,
        ));
        $point->set_schedule($this->get_delivery_schedule($point_data['schedules']));

        return $point;
    }

    /**
     * @param $phones
     * @return string
     */
    protected function get_delivery_point_phones_string($phones) {
        $result = array();
        foreach ((array)$phones as $phone) {
            $number = $phone['number'];
            $number = preg_replace('/\b(\d)(\d{3})(\d{3})(\d{2})(\d{2})\b/', "+$1 $2 $3-$4-$5", $number);

            if (strlen(trim($phone['internal_number']))) {
                 $number .= ' ' . NETCAT_MODULE_NETSHOP_PHONE_EXTENSION . ' ' . $phone['internal_number'];
            }
            if (strlen(trim($phone['comment']))) {
                $number .= ' (' . trim($phone['comment']) . ')';
            }

            $result[] = $number;
        }
        return implode(', ', array_unique($result));
    }

    /**
     * @param $interval_data
     * @return nc_netshop_delivery_schedule
     */
    protected function get_delivery_schedule($interval_data) {
        $schedule = new nc_netshop_delivery_schedule();
        foreach ((array)$interval_data as $data) {
            if ($data['from'] != $data['to']) {
                $interval = new nc_netshop_delivery_interval(array(
                    'day' . $data['day'] => true,
                    'time_from' => $data['from'],
                    'time_to' => $data['to'],
                ));
                $schedule->add($interval);
            }
        }

        return $schedule;
    }

    /**
     * @param array $data
     * @return bool|int|float
     */
    protected function get_variant_payment_on_delivery_cost(array $data) {
        foreach ($data['services'] as $service) {
            if ($service['code'] === 'CASH_SERVICE') {
                if (!$service['possibility']) {
                    return false;
                }

                if (!$this->service->get_setting('payment_charge') || $data['settings']['cash_service_in_cost']) {
                    // Вознаграждение за перечисление денежных средств включено в общую стоимость
                    return 0;
                }

                // есть свойство $service['cost'], но оно почему-то всегда равно 0
                $cost = 0;
                $rule = $service['calculateRules'];
                if ($rule['calculate_type'] === 'PERCENT_CASH') {
                    $cost = $this->data['valuation'] * $rule['service_value'];
                }
                $cost = max($rule['min_cost'], $cost);
                $cost = min($rule['max_cost'], $cost);

                return $cost;
            }
        }
        return false;
    }

}