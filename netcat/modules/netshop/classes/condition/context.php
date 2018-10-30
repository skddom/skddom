<?php

/**
 * Class nc_netshop_condition_context
 *
 * «Контекст» служит для передачи данных от различных частей модуля к условиям.
 * Наличие промежуточного класса призвано облегчить в будущем возможную интеграцию
 * подмодуля стимулирования продаж с модулем «Минимагазин», а также для вычисления
 * условий не для текущего пользователя или корзины (например, при тестировании).
 *
 */
class nc_netshop_condition_context {
    /** @var nc_netshop_cart $cart */
    protected $cart_contents;
    protected $user_id;
    protected $catalogue_id;

    protected $active_coupons;

    /** @var array ["class_id:message_id" => array("discount_id")] */
    protected $activated_item_discounts;

    /** @var  nc_netshop_order */
    protected $order;

    /**
     * @param nc_netshop_order $order
     * @return nc_netshop_condition_context
     */
    static public function for_order(nc_netshop_order $order) {
        $context = new self($order->get_catalogue_id());
        $context->set_order($order);
        $context->set_user_id($order->get('User_ID'));
        return $context;
    }

    /**
     * @param $catalogue_id
     */
    public function __construct($catalogue_id) {
        $this->catalogue_id = (int)$catalogue_id;
    }

    /**
     * @return int
     */
    public function get_catalogue_id() {
        return $this->catalogue_id;
    }

    /**
     * @param int $user_id
     */
    public function set_user_id($user_id) {
        $this->user_id = (int)$user_id;
    }

    /**
     * @return int|null
     */
    public function get_user_id() {
        if ($this->user_id) {
            return $this->user_id;
        }
        elseif ($this->order) {
            return $this->order->get('User_ID');
        }
        else {
            return null;
        }
    }

    /**
     * @param nc_netshop_item_collection $cart_contents
     */
    public function set_cart_contents(nc_netshop_item_collection $cart_contents) {
        $this->cart_contents = $cart_contents;
    }

    /**
     * @return nc_netshop_item_collection
     */
    public function get_cart_contents() {
        if ($this->cart_contents) {
            return $this->cart_contents;
        }
        elseif ($this->order) {
            return $this->order->get_items();
        }
        else {
            return new nc_netshop_item_collection;
        }

    }

    /**
     * @param $property_name
     * @return null|string
     */
    public function get_user_property($property_name) {
        if (!$this->user_id) { return null; }
        /** @var nc_user $user */
        $user = nc_core('user');
        return $user->get_by_id($this->user_id, $property_name);
    }

    /**
     * @param $group_id
     * @return bool
     */
    public function user_belongs_to_group($group_id) {
        static $user_groups = array();

        if (!$this->user_id) { return false; }
        if (!isset($user_groups[$this->user_id])) {
            $user_groups[$this->user_id] = array_flip(nc_usergroup_get_group_by_user($this->user_id));
        }
        return isset($user_groups[$this->user_id][$group_id]);
    }

    /**
     * @param null $from_date_timestamp
     * @param null $to_date_timestamp
     * @return int|float
     */
    public function get_user_previous_orders_sum($from_date_timestamp = null, $to_date_timestamp = null) {
        if (!$this->user_id) { return 0; }
        return nc_netshop::get_instance($this->catalogue_id)
                 ->get_previous_orders_sum($this->user_id, $from_date_timestamp, $to_date_timestamp);
    }

    /**
     * @param null $from_date_timestamp
     * @param null $to_date_timestamp
     * @return int
     */
    public function get_user_previous_orders_count($from_date_timestamp = null, $to_date_timestamp = null) {
        if (!$this->user_id) { return 0; }
        $netshop = nc_netshop::get_instance($this->catalogue_id);
        return $netshop->get_previous_orders_count($this->user_id, $from_date_timestamp, $to_date_timestamp);
    }

    /**
     * @param $component_id
     * @param null $item_id  if $item_id == null - any item of that component
     * @return bool
     */
    public function previous_orders_had_item($component_id, $item_id = null) {
        if (!$this->user_id) { return false; }
        return nc_netshop::get_instance($this->catalogue_id)
                 ->previous_orders_had_item($this->user_id, $component_id, $item_id);

    }

    /**
     * @return int
     */
    public function get_time() {
        return time();
    }


    /**
     * @param nc_netshop_promotion_coupon_collection $coupons
     */
    public function set_coupons(nc_netshop_promotion_coupon_collection $coupons = null) {
        $this->active_coupons = $coupons;
    }

    /**
     * @return nc_netshop_promotion_coupon_collection
     */
    public function get_coupons() {
        return $this->active_coupons;
    }

    /**
     * @param mixed $activated_item_discounts
     */
    public function set_activated_item_discounts(array $activated_item_discounts) {
        $this->activated_item_discounts = $activated_item_discounts;
    }

    /**
     * @param $discount_id
     * @param nc_netshop_item $item
     * @return bool
     */
    public function is_item_discount_activated($discount_id, nc_netshop_item $item) {
        $key = $item["_ItemKey"];
        return (isset($this->activated_item_discounts[$key]) &&
                in_array($discount_id, $this->activated_item_discounts[$key]));
    }

    /**
     * @param nc_netshop_order $order
     */
    public function set_order(nc_netshop_order $order) {
        $this->order = $order;
    }

    /**
     * @return nc_netshop_order
     */
    public function get_order() {
        return $this->order;
    }

    /**
     * @param $field
     * @return mixed
     */
    public function get_order_property($field) {
        if (isset($this->order)) {
            return $this->order->get($field);
        }
        return null;
    }

    /**
     * @param string $field_name
     * @return int
     */
    public function get_order_field_type($field_name) {
        static $cache = array();
        $catalogue_id = $this->get_catalogue_id();
        $cache_key = "$catalogue_id:$field_name";

        if (!isset($cache[$cache_key])) {
            $order_component_id = nc_netshop::get_instance($catalogue_id)->get_setting('OrderComponentID');
            $order_component = new nc_component($order_component_id);
            
            $type = $order_component->get_field($field_name, 'type');
            $cache[$cache_key] = $type ? $type : NC_FIELDTYPE_STRING;
        }

        return $cache[$cache_key];
    }
}