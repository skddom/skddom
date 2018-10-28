<?php

class nc_netshop_payment {

    /** @var nc_netshop  */
    protected $netshop;

    /**
     *
     */
    public function __construct(nc_netshop $netshop) {
        $this->netshop = $netshop;
    }

    /**
     * @return nc_netshop_record_conditional_collection nc_netshop_payment_method[]
     */
    public function get_enabled_methods() {
        $query = "SELECT *
                   FROM `%t%`
                  WHERE `Catalogue_ID` = " . (int)$this->netshop->get_catalogue_id() . "
                    AND `Checked` = 1
                  ORDER BY `Priority`";

        return nc_record_collection::load('nc_netshop_payment_method', $query);
    }

    /**
     *
     */
    public function get_all_methods() {
        $query = "SELECT *
                   FROM `%t%`
                  WHERE `Catalogue_ID` = " . (int)$this->netshop->get_catalogue_id() . "
                  ORDER BY `Priority`";

        return nc_record_collection::load('nc_netshop_payment_method', $query);
    }

    /**
     * Возвращает объект nc_netshop_payment_method с указанным ID, при условии
     * что он привязан к текущему сайту, включён и удовлетворяет условиям ($context);
     * иначе возвращает NULL
     *
     * @param $method_id
     * @param nc_netshop_condition_context $context
     * @return nc_netshop_payment_method|null
     */
    public function get_method_if_enabled($method_id, nc_netshop_condition_context $context) {
        try {
            $method = new nc_netshop_payment_method($method_id);
            if ($method->get_id() &&
                $method->get('enabled') &&
                $method->get('catalogue_id') == $this->netshop->get_catalogue_id() &&
                $method->evaluate_conditions($context)
            ) { return $method; }
        }
        catch (Exception $e) {}
        return null;
    }

    /**
     * Проверка заказа перед оформлением
     * @param nc_netshop_order $order
     * @param nc_netshop_condition_context $context
     * @return array
     */
    public function check_new_order(nc_netshop_order $order, nc_netshop_condition_context $context) {
        $errors = array();
        // Проверка на существование и применимость метода оплаты
        $method_id = $order->get('PaymentMethod');
        if ($method_id && !$this->get_method_if_enabled($method_id, $context)) {
            $errors[] = NETCAT_MODULE_NETSHOP_CHECKOUT_INCORRECT_PAYMENT_METHOD;
        }
        return $errors;
    }

    /**
     * @param nc_netshop_order $order
     */
    public function checkout(nc_netshop_order $order) {
        $payment_method_id = $order->get('PaymentMethod');
        if (!$payment_method_id) { return; }

        // Ранее должна была проведена проверка на то, существует ли метод оплаты
        // и возможен ли такой способ оплаты для оформляемого заказа
        $payment_method = new nc_netshop_payment_method($payment_method_id);
        $payment_price = $payment_method->get_extra_cost($order);
        $order->set('PaymentCost', $payment_price);
    }



}