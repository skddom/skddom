<?php

class nc_netshop_condition_dummy extends nc_netshop_condition {

    public function __construct($parameters = array()) {
    }


    public function evaluate(nc_netshop_condition_context $context, $current_item = null) {
        return false;
    }

    /**
     * Короткое описание (только значение, для повторяющихся условий)
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_short_description(nc_netshop $netshop) {
        return '';
    }


}