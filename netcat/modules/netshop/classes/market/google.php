<?php

class nc_netshop_market_google extends nc_netshop_market
{

  public function __construct(nc_netshop $nc_netshop)
  {
    parent::__construct($nc_netshop);
    $this->bundles_table = 'Netshop_GoogleBundles';
    $this->bundle_class = 'nc_netshop_market_google_bundle';

  }
  
  public function get_export_head($_MaxNameLen, $charset, $domain)
  {
    parent::get_export_names($_MaxNameLen);
    parent::get_export_currencies();

    $catalogue_url = nc_Core::get_object()->catalogue->get_url_by_host_name($domain);
    
    $ret_head = array();
    $ret_head[] = "<?xml version=\"1.0\" encoding=\"" . $charset . "\"?>\n";
    $ret_head[] = "<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">\n";
    $ret_head[] = "<channel>\n";
    $ret_head[] = "\t\t<title>" . xmlspecialchars($this->export_shopname) . "</title>\n";
    $ret_head[] = "\t\t<description>Products Feed of " . xmlspecialchars($this->export_companyname) . "</description>\n";
    $ret_head[] = "\t\t<link>" . $catalogue_url . "/</link>\n";

    $structure = GetStructureYandexml(implode(",", $this->netshop->get_goods_components_ids()), $this->netshop->get_catalogue_id());

    $this->all_sections_id = array();
    if (is_array($structure) && count($structure) > 0) {
      foreach ($structure as $category) {
        $this->all_sections_id[$category["Class_ID"]][] = $category["Subdivision_ID"];
      }
    }
    return $ret_head;
  }  

  public function get_export_bundle_offers($bundle_id, $domain)
  {
    $scheme = nc_Core::get_object()->catalogue->get_scheme_by_host_name($domain);
    $ret_offer = array();
    foreach ($this->netshop->get_goods_components_ids() as $goods_table) {

      $bundle = new $this->bundle_class($bundle_id);
      $goods_data = parent::get_export_offers_data($bundle, $bundle_id, $goods_table, $this->all_sections_id);
      
      foreach ($goods_data as $row) {
        $row = new nc_netshop_item($row);
        $row->mark_as_loaded();
        $this->prepare_special_fields($row, $domain);

        // stock hook
        if (isset($row["StockUnits"]) && strlen($row["StockUnits"])) {
          $row["Available"] = ($row["StockUnits"] ? "in stock" : "out of stock");
        } else {
          $row["Available"] = "in stock";
        }
        $row["URL"] = $this->add_utm($row["URL"], $bundle['utm']);

        $ret_offer[] = "\t\t<item>\n";
        foreach ($this->export_xml_fields as $field => $attrs) {
          $field_value = "";
          if ($attrs['editable'] == true && !empty($this->map_values[$field])) {
            $field_value = $row[$this->netcat_fields[$this->map_values[$field]]];
          } else {
            //todo improve
            switch ($field) {
              case 'id':
                $field_value = sprintf("%d%05d", $goods_table, $row["Message_ID"]);
                break;
              case 'availability':
                $field_value = $row["Available"];
                break;
              case 'link':
                $field_value = $row["URL"];
                if ($scheme === 'https') {
                    $field_value = str_replace("http://", "https://", $field_value);
                }  
                break;
              //todo
              case 'condition':
                $field_value = "new";
                break;
                case 'price':
                    $field_value = $row["ItemPrice"];
                    break;            
            }
          }
          $field_value = $this->prepare_fieldvalue($field_value);
          if ($field_value || $attrs['required'] == true) {
            $ret_offer[] = "\t\t\t<".(empty($attrs['not_g']) ? "g:": "").$field . ">" . $field_value . "</".(empty($attrs['not_g']) ? "g:": ""). $field . ">\n";
          }
        }
        $ret_offer[] = "\t\t</item>\n";
      }
    }
    return $ret_offer;
  }
   
  public function get_export_footer() 
  {
    $ret_offer[] = "</channel>\n";
    $ret_offer[] = "</rss>";
    return $ret_offer;
  }
}
