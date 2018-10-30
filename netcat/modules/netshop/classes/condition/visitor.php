<?php
/**
 *
 */
interface nc_netshop_condition_visitor {
    public function accept_condition(nc_netshop_condition $condition);
}