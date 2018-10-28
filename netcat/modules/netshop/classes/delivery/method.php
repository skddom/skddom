<?php

class nc_netshop_delivery_method extends nc_netshop_record_conditional {

    protected $primary_key = 'id';
    protected $properties = array(
        'id' => null,
        'catalogue_id' => null,
        'name' => '',
        'description' => '',
        'condition' => '',
        'handler_id' => null,
        'handler_settings' => null,
        'delivery_type' => nc_netshop_delivery::DELIVERY_TYPE_COURIER,
        'delivery_point_group' => '',
        'extra_charge_absolute' => null,
        'extra_charge_relative' => null,
        'minimum_delivery_days' => null,
        'maximum_delivery_days' => null,
        'shipment_days_of_week' => '', // '1,2,3,4,5,6,7'
        'shipment_time' => '', // '00:00'
        'priority' => 0,
        'enabled' => null,
    );

    protected $table_name = 'Netshop_DeliveryMethod';
    protected $mapping = array(
        'id' => 'DeliveryMethod_ID',
        'catalogue_id' => 'Catalogue_ID',
        'name' => 'Name',
        'description' => 'Description',
        'condition' => 'Condition',
        'handler_id' => 'ShopDeliveryService_ID',
        'handler_mapping' => 'ShopDeliveryService_Mapping',
        'handler_settings' => 'ShopDeliveryService_Settings',
        'delivery_type' => 'DeliveryType',
        'delivery_point_group' => 'DeliveryPointGroup',
        'extra_charge_absolute' => 'ExtraChargeAbsolute',
        'extra_charge_relative' => 'ExtraChargeRelative',
        'minimum_delivery_days' => 'MinimumDeliveryDays',
        'maximum_delivery_days' => 'MaximumDeliveryDays',
        'shipment_days_of_week' => 'ShipmentDaysOfWeek',
        'shipment_time' => 'ShipmentTime',
        'priority' => 'Priority',
        'enabled' => 'Checked',
    );

    protected $serialized_properties = array('handler_settings');

    /**
     * @var  bool|null|nc_netshop_delivery_service  экземпляр класса расчёта доставки
     * (false — не инициализирован, null — отсутствует)
     */
    protected $handler = false;

    /** @var  array   массив для хранения оценок стоимости и времени доставки */
    protected $estimations_cache = array();

    /** @var  nc_netshop_delivery_point_collection */
    protected $delivery_points;

    /** @var  string|null  город, для которого загружены $delivery_points */
    protected $loaded_delivery_points_location_name = null;

    /**
     * @var bool|int|float стоимость оплаты при получении [одинаковая для всех способов]
     *   (false — способ доставки не предполагает возможности оплаты при получении)
     */
    protected $payment_on_delivery_cost = false;

    /**
     * Возвращает название варианта и способа доставки (для шаблонов для
     * панели управления)
     *
     * @return string
     */
    public function get_variant_and_method_name() {
        return $this->get('name');
    }

    /**
     * Возвращает стоимость доставки указанного заказа. В возникновения ошибки
     * при расчёте стоимости возвращает NULL (следует отличать от 0, то есть
     * бесплатной доставки).
     *
     * @param nc_netshop_order $order
     * @return int|float|null
     */
    public function get_delivery_price(nc_netshop_order $order) {
        $estimate = $this->get_estimate($order);

        // error occurred:
        if (isset($estimate['error_code']) || !isset($estimate['price'])) {
            return null;
        }

        return $estimate['price'];
    }

    /**
     * Проверяет, зависит ли способ доставки от каких-либо данных, указываемых
     * при оформлении заказа.
     *
     * @return bool
     */
    public function depends_on_order_data() {
        return ($this->get('handler_id') || $this->has_condition_of_type('order'));
    }

    /**
     * Возвращает массив с оценкой стоимости и времени доставки заказа с указанными
     * параметрами.
     *
     * @param nc_netshop_order $order
     * @return nc_netshop_delivery_estimate
     */
    public function get_estimate(nc_netshop_order $order) {
        $order_data = $order->to_array();
        $cart_contents = $order->get_items();
        // cache responses for re-use
        $cache_key = sha1(serialize($order_data) . "\n/" . $cart_contents->get_hash());
        if ($this->estimations_cache[$cache_key]) {
            return $this->estimations_cache[$cache_key];
        }

        $result = array(
            'catalogue_id' => $this->get('catalogue_id'),
            'delivery_method_id' => $this->get_id(),
            'delivery_method_name' => $this->get('name'),
            'order_id' => $order->get_id(),
            'calculation_timestamp' => time(),
            'full_price' => null,
            'price' => null,
            'discount' => null,
            'min_days' => null,
            'max_days' => null,
            'error_code' => nc_netshop_delivery_estimate::ERROR_OK,
            'error' => '',
        );

        $netshop = $this->get_netshop();

        // Постоянная (независящая от службы доставки) часть стоимости
        $order_totals = $cart_contents->sum('TotalPrice');
        $delivery_cost = $this->get('extra_charge_absolute') +
                         $this->get('extra_charge_relative') * $order_totals / 100;

        $service_min_days = null;
        $service_max_days = null;

        $handler = $this->get_handler();
        if ($handler) {
            $handler->set_data($this->get_data_for_handler($order));
            $estimate = $handler->calculate_delivery();

            if ($handler->get_last_error_code() != nc_netshop_delivery_service::ERROR_OK) {
                // коды ошибок — одинаковые в estimate и service
                $result['error_code'] = $handler->get_last_error_code();
                $result['error'] = $handler->get_last_error();
            }
            else {
                $service_price = $netshop->convert_currency($estimate['price'], $estimate['currency']);
                $delivery_cost += $service_price;
                $service_min_days = isset($estimate['min_days']) ? $estimate['min_days'] : null;
                $service_max_days = isset($estimate['max_days']) ? $estimate['max_days'] : null;
            }
        }

        if (!$result['error_code']) {
            $delivery_cost = $netshop->round_price($delivery_cost);
            $result['full_price'] = $delivery_cost;

            // Учёт скидок на доставку
            if ($delivery_cost) {
                $discount = $netshop->promotion->get_delivery_discount_sum($delivery_cost, $this->get_id());
                $delivery_cost = $delivery_cost - $discount;
                $result['discount'] = $discount;
            }

            // Добавить в результат цену и отформатированную цену (с учётом скидок)
            $result['price'] = $delivery_cost;

            // Сроки доставки
            if ($service_min_days != null || is_numeric($this->get('minimum_delivery_days'))) {
                // День начала доставки
                $shipment_time = $now = time();

                // Поиск ближайшего дня недели, когда возможна отправка
                $shipment_days = $this->get('shipment_days_of_week');
                if (!preg_match('/^[\d,]+$/', $shipment_days)) { $shipment_days = '1,2,3,4,5,6,7'; }
                $shipment_days = explode(",", $shipment_days);

                // Если отправка возможна в текущий день недели, но время, до которого
                // возможна отправка, прошло, прибавить день
                $the_train_is_off = in_array(date('N', $now), $shipment_days) &&
                                    strtotime($this->get('shipment_time')) <= $now;
                // (для простоты здесь и далее возможные проблемы с переводом часов игнорируются)
                if ($the_train_is_off) { $shipment_time += 86400; }

                $security_counter = 30;
                while ($security_counter && !in_array(date('N', $shipment_time), $shipment_days)) {
                    $shipment_time += 86400;
                    $security_counter--;
                }

                // Через сколько дней возможно начало доставки?
                $days_until_shipment = round(($shipment_time - $now) / 86400);

                // Теперь можно определиться с тем, когда может быть осуществлена доставка
                $min_days = intval($days_until_shipment +
                                   $this->get('minimum_delivery_days') +
                                   $service_min_days);

                $max_days = intval($days_until_shipment +
                                   $this->get('maximum_delivery_days') +
                                   ($service_max_days ? $service_max_days : $service_min_days));

                $result['min_days'] = $min_days;
                $result['max_days'] = max($min_days, $max_days);
            }

        }

        $this->estimations_cache[$cache_key] = new nc_netshop_delivery_estimate($result);

        return $this->estimations_cache[$cache_key];
    }

    /**
     * @return nc_netshop
     */
    protected function get_netshop() {
        return nc_netshop::get_instance($this->get('catalogue_id'));
    }

    /**
     * @return nc_netshop_delivery_service|null
     */
    protected function get_handler() {
        if ($this->handler === false) {
            $this->handler = $this->get_netshop()->delivery->get_delivery_service_by_id($this->get('handler_id'));
            if ($this->handler) {
                $handler_settings = $this->get('handler_settings');
                if ($handler_settings) {
                    $this->handler->set_settings($handler_settings);
                }

                // для способов доставки с автоматическим расчётом тип доставки
                // определяет эта служба
                $this->set('delivery_type', $this->handler->get_delivery_type());
            }
        }
        return $this->handler;
    }

    /**
     * Устанавливает данные заказа
     * (meh)
     *
     * @param nc_netshop_order $order
     * @return array
     */
    protected function get_data_for_handler(nc_netshop_order $order) {
        $cart_contents = $order->get_items();

        $data_for_handler = $this->map_handler_params($order->to_array());
        $data_for_handler['items'] = $cart_contents;
        $data_for_handler['weight'] = $cart_contents->get_field_sum('Weight') ?: 100;
        $data_for_handler['valuation'] = $cart_contents->sum('TotalPrice');

        return $data_for_handler;
    }

    /**
     * @param array $order_data  Значения полей заказа (без префикса f_)
     * @return array
     */
    protected function map_handler_params(array $order_data) {
        $result = array();

        $mapping = $this->get('handler_mapping');
        if (!$mapping) { return $result; }

        /** @var nc_netshop $netshop */
        $netshop = nc_modules('netshop');  // экземпляр nc_netshop для текущего сайта

        $mapping = @json_decode($mapping, true);
        if (!is_array($mapping)) { return $result; }

        $order_component = new nc_component($netshop->get_setting('OrderComponentID'));
        $order_fields = $order_component->get_fields(0, 1);
        $shop_fields = nc_netshop_admin_helpers::get_shop_fields();

        foreach ($mapping as $to => $from) {
            $value = null;
            list ($from_source, $from_field) = explode("_", $from, 2);

            if ($from_source == 'shop') {
                $value = $netshop->get_setting($from_field);
                if (isset($shop_fields[$from_field]['classificator'])) {
                    $value = nc_get_list_item_name($shop_fields[$from_field]['classificator'], $value);
                }
            }
            elseif ($from_source == 'order') {
                foreach ($order_fields as $field) {
                    if ($from_field == $field['id'] || $from_field == $field['name']) {
                        $value = $order_data[$field['name']];
                        if ($field['type'] == NC_FIELDTYPE_SELECT) {
                            $value = nc_get_list_item_name($field['table'], $value);
                        }
                        break; // exit inner foreach
                    }
                }
            }
            $result[$to] = $value;
        }

        return $result;
    }

    /**
     * Возвращает все возможные варианты доставки
     *
     * @param nc_netshop_order $order
     * @return nc_netshop_delivery_method_collection
     */
    public function get_variants(nc_netshop_order $order) {
        $handler = $this->get_handler();
        if ($handler) {
            $handler->set_data($this->get_data_for_handler($order));
            return $handler->get_variants($this);
        }
        else {
            return new nc_netshop_delivery_method_collection(array($this));
        }
    }

    /**
     * @param $variant_id
     * @param nc_netshop_order $order
     * @return nc_netshop_delivery_method_variant|null
     */
    public function get_variant($variant_id, nc_netshop_order $order) {
        /** @var nc_netshop_delivery_method_variant $result */
        $result = $this->get_variants($order)->first('external_id', $variant_id);
        return $result;
    }

    /**
     * Возвращает тип доставки способом (почтовая/курьерская/до пункта выдачи)
     * @return string константа nc_netshop_delivery::DELIVERY_TYPE_*
     */
    public function get_delivery_type() {
        $this->get_handler(); // установит 'delivery_type' из класса расчёта доставки, если он есть
        return $this->get('delivery_type');
    }

    /**
     * Возвращает пункт выдачи с указанным идентификатором
     *
     * @param $point_id
     * @return nc_netshop_delivery_point|null
     */
    public function get_delivery_point($point_id) {
        if ($this->delivery_points && $this->get_delivery_points()->count()) {
            // Все пункты выдачи загружены в $this->delivery_points
            return $this->get_delivery_points()->first('id', $point_id);
        }

        $handler = $this->get_handler();
        if ($handler) {
            // возможно, служба расчёта доставки может вернуть пункт по ID
            return $handler->get_delivery_point($point_id);
        }
        else {
            // пробуем загрузить информацию о собственном пункте выдачи
            try {
                return new nc_netshop_delivery_point_local($point_id);
            }
            catch (Exception $e) {
                return null; // точка доставки не существует
            }
        }
    }


    /**
     * Возвращает коллекцию с пунктами выдачи заказа для указанного города
     * с учётом группы пунктов выдачи, указанной в настройках способа доставки.
     *
     * Фильтрация по городу производится только для пунктов выдачи, которые заданы
     * в модуле (не производится для пунктов выдачи, которые были установлены
     * классом автоматического расчёта доставки).
     *
     * @param string|null $location_name название населённого пункта.
     *   Если нестрого равно false (например, null), возвращает пункты выдачи для всех населённых пунктов.
     * @return nc_netshop_delivery_point_collection
     */
    public function get_delivery_points($location_name = null) {
        $are_all_delivery_points_loaded =
            $this->delivery_points && !$this->loaded_delivery_points_location_name;

        $are_same_delivery_points_loaded =
            $are_all_delivery_points_loaded ||
            ($this->delivery_points && $this->loaded_delivery_points_location_name === $location_name);

        if (!$this->delivery_points || !$are_same_delivery_points_loaded) {
            if ($this->get('delivery_type') == nc_netshop_delivery::DELIVERY_TYPE_PICKUP) {
                $this->delivery_points = $this->load_delivery_points($location_name);
            }
            else {
                $this->delivery_points = new nc_netshop_delivery_point_local_collection();
            }
        }

        $is_local = $this->delivery_points instanceof nc_netshop_delivery_point_local_collection;
        if ($is_local && $location_name && $are_all_delivery_points_loaded) {
            return $this->delivery_points->where('location_name', $location_name);
        }

        return $this->delivery_points;
    }

    /**
     * Проверяет, есть ли пункты выдачи заказов у способа доставки для указанного
     * населённого пункта.
     *
     * @param string|null $location_name название населённого пункта
     *   Если нестрого равно false (например, null), возвращает пункты выдачи для всех населённых пунктов.
     * @return bool
     */
    public function has_delivery_points($location_name = null) {
        return count($this->get_delivery_points($location_name)) > 0;
    }

    /**
     * @param string|null $location_name
     * @return nc_netshop_delivery_point_collection
     */
    protected function load_delivery_points($location_name) {
        $site_id = (int)$this->get('catalogue_id');
        $query = "SELECT * FROM `%t%` WHERE `Catalogue_ID` = $site_id AND `Checked` = 1";
        if ($location_name) {
            $query .= " AND `LocationName` = '" . nc_db()->escape($location_name) . "'";
        }
        $group = $this->get('delivery_point_group');
        if (strlen($group)) {
            $query .= " AND `Group` = '" . nc_db()->escape($group) . "'";
        }

        $this->loaded_delivery_points_location_name = $location_name;

        return nc_record_collection::load('nc_netshop_delivery_point_local', $query);
    }

    /**
     * Устанавливает пункты выдачи заказа
     * @param nc_netshop_delivery_point_collection $delivery_points
     */
    public function set_delivery_points(nc_netshop_delivery_point_collection $delivery_points) {
        $this->delivery_points = $delivery_points;
    }

    /**
     * Проверяет, есть ли координаты хотя бы у одного пункта выдачи заказа
     * @return bool
     */
    public function has_delivery_points_with_coordinates() {
        return $this->get_delivery_points()->any('latitude', '', '!=');
    }

    /**
     * @return bool|float|int
     */
    public function get_payment_on_delivery_cost() {
        return $this->payment_on_delivery_cost;
    }

    /**
     * @param bool|float|int $payment_on_delivery_cost
     */
    public function set_payment_on_delivery_cost($payment_on_delivery_cost) {
        $this->payment_on_delivery_cost = $payment_on_delivery_cost;
    }

}