<?php

class nc_netshop_goodslist_recent extends nc_netshop_goodslist_base {

    protected $tablename = 'Netshop_RecentGoods';

    //--------------------------------------------------------------------------

    public function __construct(nc_netshop $netshop) {
        parent::__construct($netshop);
    }
}