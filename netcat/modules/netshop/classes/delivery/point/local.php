<?php

/**
 * Пункт выдачи, информация о котором задана в Netcat
 */
class nc_netshop_delivery_point_local extends nc_netshop_delivery_point {

    protected $table_name = 'Netshop_DeliveryPoint';

    protected $mapping = array(
        'id' => 'DeliveryPoint_ID',
        'catalogue_id' => 'Catalogue_ID',
        'name' => 'Name',
        'description' => 'Description',
        'phones' => 'Phones',
        'location_name' => 'LocationName',
        'address' => 'Address',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'group' => 'Group',
        'payment_on_delivery_cash' => 'PaymentOnDeliveryCash',
        'payment_on_delivery_card' => 'PaymentOnDeliveryCard',
        'enabled' => 'Checked',
    );

}