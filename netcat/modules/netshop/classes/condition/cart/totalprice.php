<?php

/**
 * Сумма стоимости товаров в корзине с учётом скидок на товары
 */
class nc_netshop_condition_cart_totalprice extends nc_netshop_condition {

    /**
     * Parameters:
     *   op
     *   value
     */
    protected $op;
    protected $value;

    public function __construct($parameters = array()) {
        $this->op = $parameters['op'];
        $this->value = $this->convert_decimal_point($parameters['value']);
    }


    public function evaluate(nc_netshop_condition_context $context, $current_item = null) {
        $sum = $context->get_cart_contents()->sum('TotalPrice');
        return $this->compare($sum, $this->op, $this->value);
    }


    public function get_short_description(nc_netshop $netshop) {
        return $this->add_operator_description($netshop->format_price($this->value));
    }

}