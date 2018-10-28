<?php

class nc_netshop_market_mail extends nc_netshop_market
{

  public function __construct(nc_netshop $nc_netshop)
  {
    parent::__construct($nc_netshop);
    $this->bundles_table = 'Netshop_MailBundles';
    $this->bundle_class = 'nc_netshop_market_mail_bundle';

  }
  
    
  public function get_export_head($_MaxNameLen, $charset, $domain)
  {
    parent::get_export_names($_MaxNameLen);
    parent::get_export_currencies();

    $catalogue_url = nc_Core::get_object()->catalogue->get_url_by_host_name($domain);

    $ret_head = array();
    $ret_head[] = "<?xml version=\"1.0\" encoding=\"" . $charset . "\"?>\n";
    $ret_head[] = "<torg_price date=\"" . (strftime("%Y-%m-%d %H:%M")) . "\">\n";
    $ret_head[] = "\t<shop>\n";
    $ret_head[] = "\t\t<shopname>" . xmlspecialchars($this->export_shopname) . "</shopname>\n";
    $ret_head[] = "\t\t<company>" . xmlspecialchars($this->export_companyname) . "</company>\n";
    $ret_head[] = "\t\t<url>" . $catalogue_url . "/</url>\n";
    $ret_head[] = "\t\t<currencies>\n";
    $ret_head[] = "\t\t\t<currency id=\"" . $this->export_default_currency . "\" rate=\"1\"/>\n";    
    $ret_head[] = "\t\t</currencies>\n";
    $ret_head[] = "\t\t<categories>\n";

    $structure = GetStructureYandexml(implode(",", $this->netshop->get_goods_components_ids()), $this->netshop->get_catalogue_id());

    $this->all_sections_id = array();
    if (is_array($structure) && count($structure) > 0) {
      foreach ($structure as $category) {
        $ret_tmp = "\t\t<category id=\"{$category['Subdivision_ID']}\"";

        if (array_key_exists($category['Parent_Sub_ID'], $structure)) {
          $ret_tmp .= " parentId=\"{$category['Parent_Sub_ID']}\"";
        }
        $ret_tmp .= ">" . xmlspecialchars($category["Subdivision_Name"]) . "</category>\n";
        $this->all_sections_id[$category["Class_ID"]][] = $category["Subdivision_ID"];
        $ret_head[] = $ret_tmp;
      }
      $ret_head[] = "\t\t</categories>\n";
    }
    return $ret_head;
  }

  public function get_export_bundle_offers($bundle_id, $domain)
  {
    $scheme = nc_Core::get_object()->catalogue->get_scheme_by_host_name($domain);
    $ret_offer = array();
    $ret_offer[] = "\t\t<offers>\n";
    foreach ($this->netshop->get_goods_components_ids() as $goods_table) {

      $bundle = new $this->bundle_class($bundle_id);
      $goods_data = parent::get_export_offers_data($bundle, $bundle_id, $goods_table, $this->all_sections_id);
      
      foreach ($goods_data as $row) {
        $row = new nc_netshop_item($row);
        $row->mark_as_loaded();
        $this->prepare_special_fields($row, $domain);
        
        // stock hook
        if (isset($row["StockUnits"]) && strlen($row["StockUnits"])) {
          $row["Available"] = ($row["StockUnits"] ? "true" : "false");
        } else {
          $row["Available"] = "true";
        }
        $row["URL"] = $this->add_utm($row["URL"], $bundle['utm']);
        
        $ret_offer[] = "\t\t\t<offer id=\"" . sprintf("%d%05d", $goods_table, $row["Message_ID"]) . "\" available=\"" . $row["Available"] . "\" >\n";
        foreach ($this->export_xml_fields as $field => $attrs) {
          $field_value = "";
          if (empty($attrs['multi'])) {
            if ($attrs['editable'] == true && !empty($this->map_values[$field])) {
              $field_value = $row[$this->netcat_fields[$this->map_values[$field]]];
              if ($field == 'description') {
                $field_value = "<![CDATA[".$field_value."]]>";
              } elseif ($field === 'currencyId') {
                $field_value = $this->export_default_currency;
              }

            } else {
              //todo improve
              switch ($field) {
                case 'categoryId':
                  $field_value = $this->all_sections_id[$goods_table];
                  break;
                case 'url':
                    $field_value = $row["URL"];
                    if ($scheme === 'https') {
                        $field_value = str_replace("http://", "https://", $field_value);
                    }         
                  break;
                case 'price':
                    $field_value = $row["ItemPrice"];
                    break;                    
              }
            }
            $field_value = ($field == 'description' ? $field_value : $this->prepare_fieldvalue($field_value));
            if ($field_value || $attrs['required'] == true) {
                $ret_offer[] = "\t\t\t\t<" . $field . ">" . $field_value . "</" . $field . ">\n";
            }
          } else if (!empty($this->map_values[$field])) {
            //multi
            foreach ($this->map_values[$field]['field'] as $key => $field_id) {
                $field_value = $this->prepare_fieldvalue($row[$this->netcat_fields[$field_id]]);
                $ret_offer[] = "\t\t\t\t<" . $field . " name=\"{$this->map_values[$field]['name'][$key]}\"" . (!empty($this->map_values[$field]['units'][$key]) ? " unit=\"{$this->map_values[$field]['units'][$key]}\"" : "") . ">" . $field_value . "</" . $field . ">\n";
            }
          }
        }
        $ret_offer[] = "\t\t\t</offer>\n";
      }
    }
    return $ret_offer;
  }
    
  public function get_export_footer() 
  {
    $ret_offer[] = "\t\t</offers>\n";
    $ret_offer[] = "\t</shop>\n";
    $ret_offer[] = "</torg_price>";
    return $ret_offer;
  }
  
}
