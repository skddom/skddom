<?php

class nc_netshop_market_google_bundle extends nc_netshop_record_conditional
{

  protected $primary_key = 'bundle_id';
  protected $properties = array(
      'bundle_id' => null,
      'catalogue_id' => null,
      'name' => null,
      'last_updated' => null,
      'type' => 'simple',
      'utm' => null,
  );
  protected $table_name = 'Netshop_GoogleBundles';
  protected $map_table_name = 'Netshop_GoogleBundlesMap';
  protected $mapping = array(
      "_generate" => true
  );
  public $defaults_to_try = array(
      'Name' => 'title', 'Price' => 'price', 'Image' => 'image_link', 'Description' => 'description'
  );

  public $default_types = array(
      'simple' => NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_TYPE_SIMPLE,
  );
  
  public function get_default_types()
  {
    return $this->default_types;
  }

  public function get_fields_object($type)
  {
    if (in_array($type, array_keys($this->default_types))) {
      $className = "nc_netshop_market_google_fields_" . $type;
      return new $className();
    } else {
      return false;
    }
  }

  public function save_map($bundle_id, $map_fields)
  {
    foreach ($map_fields as $class_id => $fields) {
      foreach ($fields as $string => $field_id) {
        if ($field_id != '-1') {
          $multi = "";
          if (is_array($field_id)) {
            $multi = serialize($field_id);
            $field_id = 0;
          }
          $str = "INSERT INTO `".$this->map_table_name."` SET "
                  . " `Bundle_ID` = '" . intval($bundle_id) . "',"
                  . " `Class_ID` = '" . intval($class_id) . "',"
                  . " `String` = '" . nc_db()->escape($string) . "',"
                  . " `Field_ID` = '" . intval($field_id) . "', "
                  . " `Multi` = '" . nc_db()->escape($multi) . "'";
          nc_db()->query($str);
        }
      }
    }
  }

  public function delete_map($bundle_id)
  {
    nc_db()->query("DELETE FROM `".$this->map_table_name."` WHERE Bundle_ID='" . intval($bundle_id) . "' ");
  }

  /**
   *
   * @param type $bundle_id
   * @param type $type
   * @param type $param
   * @return array
   */
  public function get_map_values($bundle_id, $class_id)
  {
    $sql = "SELECT `String`, `Field_ID`, `Multi` FROM `".$this->map_table_name."` "
            . "WHERE `Bundle_ID` = '" . intval($bundle_id) . "' ";

    $sql .= " AND `Class_ID` = '" . intval($class_id) . "' ";

    $map_values = array();
    $results = nc_db()->get_results($sql, ARRAY_A);
    if (count($results) > 0) {
      foreach ($results as $res) {
        if ($res['Multi'] != '' && $res['Field_ID'] == 0) {
          $map_values[$res['String']] = unserialize($res['Multi']);
        } else {
          $map_values[$res['String']] = $res['Field_ID'];
        }
      }
    }
    return $map_values;
  }

  public function get_xml_fields()
  {
    $fields_obj = $this->get_fields_object($this->get('type'));

    $xml_fields = $fields_obj->get_fields();
    foreach ($xml_fields as $key => $attrs) {
      if (isset($attrs['editable']) && $attrs['editable'] === false) {
        unset($xml_fields[$key]);
      }
    }
    return $xml_fields;
  }

  public function get_goods_tables()
  {
    $netshop = nc_netshop::get_instance($this->get('catalogue_id'));
    $goods_tables = array();
    if ($netshop->is_netshop_v1_in_use()) {
      $nc_core = nc_core();
      foreach (explode(',', $nc_core->modules->get_vars('netshop', 'GOODS_TABLE')) as $table) {
        $goods_tables[] = (int) trim($table);
      }
    } else {
      $goods_tables = $netshop->get_goods_components_ids();
    }
    return $goods_tables;
  }

  public function try_defaults(nc_netshop_market_google $google)
  {
    $xml_fields = $this->get_xml_fields();
    $goods_tables = $this->get_goods_tables();

    $map = array();
    foreach ($goods_tables as $goods_table) {
      $netcat_fields = $google->get_netcat_fields($goods_table, true);
      foreach ($netcat_fields as $field_name => $field_id) {
        if (isset($this->defaults_to_try[$field_name]) && isset($xml_fields[$this->defaults_to_try[$field_name]])) {
          $map[$goods_table][$this->defaults_to_try[$field_name]] = $field_id;
        }
      }
    }
    if (count($map) > 0) {
      $this->save_map($this->get('bundle_id'), $map);
    }
  }

}
