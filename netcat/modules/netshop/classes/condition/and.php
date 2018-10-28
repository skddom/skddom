<?php

class nc_netshop_condition_and extends nc_netshop_condition_composite {

    public function evaluate(nc_netshop_condition_context $context, $current_item = null) {
        foreach ($this->children as $sub_condition) {
            if ($sub_condition->evaluate($context, $current_item) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Полное описание
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_full_description(nc_netshop $netshop) {
        return $this->get_children_descriptions($netshop, NETCAT_MODULE_NETSHOP_COND_AND, NETCAT_MODULE_NETSHOP_COND_AND_SAME);
    }

}