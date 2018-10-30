<?php

class nc_netshop_payment_method extends nc_netshop_record_conditional {

    protected $primary_key = 'id';
    protected $properties = array(
        'id' => null,
        'catalogue_id' => null,
        'name' => '',
        'description' => '',
        'condition' => '',
        'payment_from_delivery' => false,
        'payment_on_delivery_cash' => false,
        'payment_on_delivery_card' => false,
        'handler_id' => null,
        'extra_charge_absolute' => null,
        'extra_charge_relative' => null,
        'priority' => 0,
        'enabled' => null,
    );

    protected $table_name = 'Netshop_PaymentMethod';
    protected $mapping = array(
        'id' => 'PaymentMethod_ID',
        'catalogue_id' => 'Catalogue_ID',
        'name' => 'Name',
        'description' => 'Description',
        'condition' => 'Condition',
        'payment_from_delivery' => 'PaymentFromDelivery',
        'payment_on_delivery_cash' => 'PaymentOnDeliveryCash',
        'payment_on_delivery_card' => 'PaymentOnDeliveryCard',
        'handler_id' => 'PaymentSystem_ID',
        'extra_charge_absolute' => 'ExtraChargeAbsolute',
        'extra_charge_relative' => 'ExtraChargeRelative',
        'priority' => 'Priority',
        'enabled' => 'Checked',
    );


    /**
     * Возвращает наценку при использовании данного способа оплаты.
     *
     * @param nc_netshop_order $order
     * @return int|float
     */
    public function get_extra_cost(nc_netshop_order $order) {
        $cart_contents = $order->get_items();
        $extra = $this->get('extra_charge_absolute') +
                 $this->get('extra_charge_relative') * $cart_contents->sum('TotalPrice') / 100 +
                 $this->get_payment_from_delivery_cost($order);
        return nc_netshop::get_instance($this->get('catalogue_id'))->round_price($extra);
    }

    /**
     * Проверяет, зависит ли способ оплаты от каких-либо данных, указываемых
     * при оформлении заказа.
     *
     * @return bool
     */
    public function depends_on_order_data() {
        return $this->has_condition_of_type('order');
    }

    /**
     * Проверяет, зависит ли способ оплаты от способа доставки.
     *
     * @return bool
     */
    public function depends_on_delivery_method() {
        return $this->get('payment_from_delivery') || $this->has_condition_of_type('order_deliverymethod');
    }

    /**
     * @param nc_netshop_condition_context $context
     * @param nc_netshop_item|mixed $current_item
     * @return bool
     */
    public function evaluate_conditions(nc_netshop_condition_context $context, $current_item = null) {
        // (1) проверка свойства payment_from_delivery — выбранный способ доставки
        // [c автоматическим расчётом] допускает приём оплаты при получении
        if ($this->get('payment_from_delivery')) {
            if ($this->get_payment_from_delivery_cost($context->get_order()) === false) {
                return false;
            }
        }

        // (2) проверка условий
        return parent::evaluate_conditions($context, $current_item);
    }

    /**
     * @param nc_netshop_order|null $order
     * @return bool|float|int
     */
    protected function get_payment_from_delivery_cost(nc_netshop_order $order = null) {
        if (!$order) {
            return false;
        }

        $delivery_method = $order->get_delivery_method();
        if (!$delivery_method) {
            return false;
        }

        return $delivery_method->get_payment_on_delivery_cost();
    }

}