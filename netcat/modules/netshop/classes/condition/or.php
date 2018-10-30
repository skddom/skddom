<?php

class nc_netshop_condition_or extends nc_netshop_condition_composite {
    
    public function evaluate(nc_netshop_condition_context $context, $current_item = null) {
        foreach ($this->children as $sub_condition) {
            if ($sub_condition->evaluate($context, $current_item) == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Полное описание
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_full_description(nc_netshop $netshop) {
        return $this->get_children_descriptions($netshop, NETCAT_MODULE_NETSHOP_COND_OR, NETCAT_MODULE_NETSHOP_COND_OR_SAME);
    }

}