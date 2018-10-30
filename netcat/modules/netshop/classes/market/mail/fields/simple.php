<?php

class nc_netshop_market_mail_fields_simple extends nc_netshop_market_mail_fields_type
{

  public function get_fields()
  {
    return array(
        'url' => array('required' => true, 'editable' => false),
        'price' => array('required' => true, 'editable' => false),
        'name' => array('required' => true, 'editable' => true),
        'currencyId' => array('required' => true, 'editable' => true),
        'categoryId' => array('required' => true, 'editable' => false),
        'picture' => array('required' => false, 'editable' => true),
        'pickup' => array('required' => false, 'editable' => true),
        'delivery' => array('required' => false, 'editable' => true),
        'local_delivery_cost' => array('required' => false, 'editable' => true),
        'typePrefix' => array('required' => true, 'editable' => true),
        'vendor' => array('required' => false, 'editable' => true),
        'description' => array('required' => false, 'editable' => true),
        'model' => array('required' => false, 'editable' => true),
        'param' => array('required' => false, 'editable' => true, 'multi' => true),
    );
  }
}
