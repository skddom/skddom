<?php

class nc_netshop_market_yandex_fields_simple extends nc_netshop_market_yandex_fields_type
{

  public function get_fields()
  {
    return array(
        'url' => array('required' => false, 'editable' => false),
        'price' => array('required' => true, 'editable' => false),
        'currencyId' => array('required' => true, 'editable' => true),
        'categoryId' => array('required' => true, 'editable' => false),
        'market_category' => array('required' => false, 'editable' => true),
        'picture' => array('required' => false, 'editable' => true),
        'store' => array('required' => false, 'editable' => true),
        'pickup' => array('required' => false, 'editable' => true),
        'delivery' => array('required' => false, 'editable' => true),
        'local_delivery_cost' => array('required' => false, 'editable' => true),
        'name' => array('required' => true, 'editable' => true),
        'vendor' => array('required' => false, 'editable' => true),
        'vendorCode' => array('required' => false, 'editable' => true),
        'description' => array('required' => false, 'editable' => true),
        'sales_notes' => array('required' => false, 'editable' => true),
        'manufacturer_warranty' => array('required' => false, 'editable' => true),
        'country_of_origin' => array('required' => false, 'editable' => true),
        'adult' => array('required' => false, 'editable' => true),
        'age' => array('required' => false, 'editable' => true),
        'barcode' => array('required' => false, 'editable' => true),
        'cpa' => array('required' => false, 'editable' => true),
        'param' => array('required' => false, 'editable' => true, 'multi' => true),
    );
  }
  public function get_vendor_type() {
    return false;
  }

}
