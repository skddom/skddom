<?php

class nc_netshop_delivery_method_table extends nc_netshop_table {

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
        'ShopDeliveryService_ID' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE,
            'field' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'ShopDeliveryService',
            'empty_option_text' => NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_DONT_USE,
        ),
        'ServiceOptions' => array('field' => 'custom'),
        'ShopDeliveryService_Mapping' => array(),
        'ShopDeliveryService_Settings' => array(),
        'DeliveryType' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_TYPE,
            'field' => 'select',
            'subtype' => 'static',
            'values'=> array(
                nc_netshop_delivery::DELIVERY_TYPE_COURIER => NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_COURIER,
                nc_netshop_delivery::DELIVERY_TYPE_PICKUP => NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_PICKUP,
                nc_netshop_delivery::DELIVERY_TYPE_POST => NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_POST,
            ),
            'default_value' => nc_netshop_delivery::DELIVERY_TYPE_COURIER,
        ),
        'DeliveryPointGroup' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP,
            'field' => 'select',
            'subtype' => 'static',
            'values'=> array(), // см. метод make_form()
            'empty_option_text' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP_ANY,
        ),
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
            'default_value' => '00:00',
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
        $site_id = nc_array_value($data, 'Catalogue_ID');

        $this->fields['ServiceOptions']['html'] =
            nc_netshop_delivery_admin_helpers::get_delivery_service_options($site_id, $data);

        $delivery_point_groups = nc_db()->get_col(
            "SELECT DISTINCT(`Group`) 
               FROM `Netshop_DeliveryPoint`
              WHERE `Group` != '' 
              ORDER BY `Group`",
            0,
            0
        ) ?: array();

        $selected_delivery_point_group = nc_array_value($data, 'DeliveryPointGroup');
        if ($delivery_point_groups || strlen($selected_delivery_point_group)) {
            if (strlen($selected_delivery_point_group) && !isset($delivery_point_groups[$selected_delivery_point_group])) {
                $delivery_point_groups[$selected_delivery_point_group] = $selected_delivery_point_group;
            }
            $this->fields['DeliveryPointGroup']['values'] = $delivery_point_groups;
        } else {
            // прячем поле
            unset($this->fields['DeliveryPointGroup']);
        }

        return parent::make_form($data);
    }

}