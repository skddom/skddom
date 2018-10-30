<?php

class nc_netshop_market
{

  public $bundles_table;
  public $bundle_class;
  public $catalogue_id;

  /**
   * Constructor
   * @param nc_netshop $netshop
   */
  public function __construct(nc_netshop $netshop)
  {
    $this->netshop = $netshop;
    $this->catalogue_id = $netshop->get_catalogue_id();
  }

  public function init_place($place)
  {
    if (in_array($place, $this->allowed_markets)) {
      $class = "nc_netshop_market_".$place;
      return new $class();
    }
  }
  

  /**
   *
   * @return type
   */
  public function get_bundles_list()
  {

    return $bundles = nc_db()->get_results("
            SELECT b.*
              FROM `$this->bundles_table` AS b
             WHERE b.`Catalogue_ID` = $this->catalogue_id
             ORDER BY b.`Name`", ARRAY_A);
  }

  /**
   * 
   * @param type $goods_table
   * @return string
   */
  public function get_netcat_fields($goods_table, $reverced = false)
  {
    $sql = "SELECT `Field_ID`, `Field_Name`, `Description` FROM `Field` WHERE `Class_ID` = '".intval($goods_table)."'";
    $netcat_fields = array();

    foreach ((array) nc_db()->get_results($sql, ARRAY_A) as $netcat_field) {
      if ($reverced == true) {
        $netcat_fields[$netcat_field['Field_Name']] = $netcat_field['Field_ID'];
      } else {
        $netcat_fields[$netcat_field['Field_ID']] = '[' . $netcat_field['Field_Name'] . '] - ' . ($netcat_field['Description'] ? $netcat_field['Description'] : $netcat_field['Field_Name']);
      }
    }
    return $netcat_fields;
  }
  
  public function get_export_names($_MaxNameLen)
  {
    $name = $this->netshop->get_setting('ShopName');
    $this->export_shopname = (nc_strlen($name) > $_MaxNameLen) ? nc_substr($name, 0, $_MaxNameLen) : $name;
    $this->export_companyname = $this->netshop->get_setting('CompanyName');
  }
  
  public function get_export_currencies()
  {
    $export_currencies = array();
    foreach($this->netshop->get_setting('CurrencyDetails') as $id => $currency) {
      $export_currencies[$id] = $this->netshop->get_currency_code($id);
    }

    $this->export_currencies = $export_currencies;
    if ($this->netshop->get_setting('DefaultCurrencyID') > 0) {
      $this->export_default_currency = $this->export_currencies[$this->netshop->get_setting('DefaultCurrencyID')];
    } else {
      $this->export_default_currency = reset($this->export_currencies);
    }
    $this->export_rates = $this->netshop->get_setting('Rates');
  }
  
  public function get_export_offers_data($bundle, $bundle_id, $goods_table, $all_sections_id)
  {
    $fields_obj = $bundle->get_fields_object($bundle->get('type'));
    $this->export_xml_fields = $fields_obj->get_fields();

    // берем map_values
    $this->map_values = $bundle->get_map_values($bundle_id, $goods_table);

    if (count($this->map_values) == 0 || empty($all_sections_id[$goods_table])) return array();

    $component = new nc_component($goods_table);

    // берем поля товара
    $this->netcat_fields = $this->netcat_file_fields = $this->netcat_multifile_fields = array();

    foreach ($component->get_fields() as $field) {
      $this->netcat_fields[$field['id']] = $field['name'];
      if (in_array($field['id'], $this->map_values) || in_array($field['id'], (array)$this->map_values['param']['field'])) {
          if ($field['type'] == NC_FIELDTYPE_FILE){
              $this->netcat_file_fields[$field['id']] = $field['name'];
          }
          if ($field['type'] == NC_FIELDTYPE_MULTIFILE){
              $this->netcat_multifile_fields[$field['id']] = $field['name'];
          }
      }
    }
    // берем товары
    $goods_data = nc_db()->get_results("SELECT " . $component->get_fields_query() . "\n" .
            "FROM `Message" . $goods_table . "` AS a\n" . $component->get_joins() . "\n"
        . "WHERE a.`Checked` = 1
                  AND a.`Subdivision_ID` IN (" . implode(',' , (array)$all_sections_id[$goods_table]) . ")"
        . "", ARRAY_A);

    return $goods_data != null ? $goods_data : array();
  }

    public function prepare_special_fields($item, $domain) {

        $catalogue_url = nc_Core::get_object()->catalogue->get_url_by_host_name($domain);
  
        foreach ((array)$this->netcat_file_fields as $name) {
            $item[$name] = $catalogue_url . $item[$name];
        }
        foreach ((array)$this->netcat_multifile_fields as $name) {
            $files = $item->get($name)->to_array();
            if($files[0]['Path']) {
               $item[$name] = $catalogue_url . $files[0]['Path'];
            }
        }
        return $item;
    }

    public function prepare_fieldvalue($field_value) {
        if (is_array($field_value)) {
            $field_value = implode(', ', $field_value);
        }
        if (preg_match('/<\w.*?>/s', $field_value)) {
            return '<![CDATA[' . $field_value . ']]>';
        }
        return xmlspecialchars($field_value);
    }

    public function add_utm($url, $utm) {
        if ($utm) {
            $url .=  '?' . (!strpos($utm, '=') ? 'utm_source=' : '') . str_replace('?', '', $utm);
        }
        return $url;
    }
}
