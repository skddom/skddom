<?php

class nc_netshop_delivery_point_interval extends nc_netshop_delivery_interval {

    protected $table_name = 'Netshop_DeliveryPointInterval';

    protected $mapping = array(
        'id' => 'DeliveryPointInterval_ID',
        'parent_id' => 'DeliveryPoint_ID',
        '_generate' => true,
    );

}