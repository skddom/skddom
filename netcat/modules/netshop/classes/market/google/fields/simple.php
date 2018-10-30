<?php

class nc_netshop_market_google_fields_simple extends nc_netshop_market_google_fields_type
{

  public function get_fields()
  {
    return array(
        'id' => array('required' => true, 'editable' => false),
        'title' => array('required' => true, 'editable' => true, 'not_g' => true),
        'description' => array('required' => true, 'editable' => true, 'not_g' => true),
        'google_product_category' => array('required' => false, 'editable' => true),
        'product_type' => array('required' => false, 'editable' => true),
        'link' => array('required' => true, 'editable' => false, 'not_g' => true),
        'image_link' => array('required' => true, 'editable' => true),
        'additional_image_link' => array('required' => false, 'editable' => true),
        'condition' => array('required' => true, 'editable' => false),
        'availability' => array('required' => true, 'editable' => false),
        'price' => array('required' => true, 'editable' => false),
        'sale_price' => array('required' => false, 'editable' => true),
        'brand' => array('required' => false, 'editable' => true),
        'gender' => array('required' => false, 'editable' => true),
        'age_group' => array('required' => false, 'editable' => true),
        'color' => array('required' => false, 'editable' => true),
        'size' => array('required' => false, 'editable' => true),
        'size_type' => array('required' => false, 'editable' => true),
        'size_system' => array('required' => false, 'editable' => true),
    );
  }

}
