<?php

class nc_netshop_delivery_table extends nc_netshop_table {

    protected $table = 'Netshop_DeliveryMethod';
    protected $primary_key = 'DeliveryMethod_ID';

    protected $fields = array(
        'DeliveryMethod_ID' => array(
            'field' => 'hidden',
        ),
        'Catalogue_ID' => array(
            'field' => 'hidden',
        ),
        'Priority' => array(),
        'Checked' => array(
            'default' => 1,
            'field' => 'hidden',
        ),
        'Name' => array(
            'title' => NETCAT_MODULE_NETSHOP_NAME_FIELD,
            'field' => 'string',
            'required' => 1,
        ),
        'Description' => array(
            'title' => NETCAT_MODULE_NETSHOP_DESCRIPTION_FIELD,
            'field' => 'textarea',
            'size' => 3,
            'codemirror' => false,
        ),
        'Condition' => array(
            'title' => NETCAT_MODULE_NETSHOP_CONDITION_FIELD,
            'field' => 'custom',
            'html' => "<div id='nc_netshop_condition_editor'></div>",
            'wrap' => true,
        ),
        'DeliveryServiceSelect' => array(
            'field' => 'custom',
            'html' => '',
        ),
        'ShopDeliveryService_ID' => array(),
        'ShopDeliveryService_Mapping' => array(),

        'MinimumDeliveryDays' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_MIN_DAYS,
            'field' => 'string',
            'size' => 10,
            'save_empty_value_as_null' => true,
        ),

        'MaximumDeliveryDays' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_MAX_DAYS,
            'field' => 'string',
            'size' => 10,
            'save_empty_value_as_null' => true,
        ),

        'ShipmentDaysOfWeek' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SHIPMENT_DAYS,
            'field' => 'select',
            'multiple' => true,
            'size' => 7,
            'default_value' => '1,2,3,4,5,6,7',
            'values' => array(
                1 => NETCAT_MODULE_NETSHOP_CONDITION_MONDAY,
                2 => NETCAT_MODULE_NETSHOP_CONDITION_TUESDAY,
                3 => NETCAT_MODULE_NETSHOP_CONDITION_WEDNESDAY,
                4 => NETCAT_MODULE_NETSHOP_CONDITION_THURSDAY,
                5 => NETCAT_MODULE_NETSHOP_CONDITION_FRIDAY,
                6 => NETCAT_MODULE_NETSHOP_CONDITION_SATURDAY,
                7 => NETCAT_MODULE_NETSHOP_CONDITION_SUNDAY,
            )
        ),

        'ShipmentTime' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SAME_DAY_SHIPMENT_TIME,
            'field' => 'string',
            'default_value' => '00:01',
            'size' => 10,
        ),

        'ExtraChargeAbsolute' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_EXTRA_CHARGE_ABSOLUTE,
            'field' => 'float',
            'size' => 10,
        ),
        'ExtraChargeRelative' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_EXTRA_CHARGE_RELATIVE,
            'field' => 'float',
            'size' => 10,
        ),
    );


    public function make_form($data) {
        $catalogue_id = @$data['Catalogue_ID'];

        $this->fields['DeliveryServiceSelect']['html'] =
            "<div class='ncf_row'><div class='ncf_value'>" .
            nc_netshop_delivery_admin_helpers::get_delivery_type_select(
                nc_netshop::get_instance($catalogue_id),
                @$data['ShopDeliveryService_ID'],
                @$data['ShopDeliveryService_Mapping']
            ) .
            "</div></div>";

        return parent::make_form($data);
    }

}