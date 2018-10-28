<?php

class nc_netshop_condition_order_deliverymethod extends nc_netshop_condition {

    /**
     * Parameters:
     *    'value'  − ID of the delivery method
     */

    protected $op;
    protected $value;


    public function evaluate(nc_netshop_condition_context $context, $current_item = null) {
	    
        // ID способа доставки может быть указан в свойствах заказа
        if ($this->compare($context->get_order_property('DeliveryMethod'), $this->op, $this->value)) {
            return true;
        }

        // или может быть передан в качестве параметра
        if (is_numeric($current_item) && $this->compare($current_item, $this->op, $this->value)) {
            return true;
        }

        return false;
    }

    /**
     * Короткое описание (только значение, для повторяющихся условий)
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_short_description(nc_netshop $netshop) {
        static $cache = array();
        if (!isset($cache[$this->value])) {
            try {
                $method = new nc_netshop_delivery_method($this->value);
                $cache[$this->value] = nc_ui_helper::get()->hash_link(
                    "module.netshop.delivery.edit($this->value)",
                    $method->get('name')
                );
            }
            catch (nc_record_exception $e) {
                $cache[$this->value] = "<em class='nc--status-error'>" . NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_DELIVERY_METHOD . "</em>";
            }
        }
        return $this->add_operator_description($cache[$this->value]);
    }

    /**
     * @param nc_backup_dumper $dumper
     * @return array
     */
    public function get_updated_parameters_on_import(nc_backup_dumper $dumper) {
        return array('op' => $this->op,
                     'value' => $dumper->get_dict('Netshop_DeliveryMethod.DeliveryMethod_ID', $this->value));
    }

}