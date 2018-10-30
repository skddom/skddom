<?php

/**
 *
 */
class nc_netshop_market_admin_controller extends nc_netshop_admin_controller
{

  /** @var  nc_netshop_market_admin_ui */
  protected $ui_config;
  protected $ui_config_class = 'nc_netshop_market_admin_ui';

  protected $allowed_markets = array('yandex', 'google', 'mail', 'yandex_order');

  /**
   *
   */
  protected function init()
  {
    parent::init();
    $this->bundle_id = $id = (int) $this->input->fetch_post_get('bundle_id');
    $this->place = $this->input->fetch_post_get('place');
    $placeClass = "nc_netshop_market_".$this->place;
    if (in_array($this->place, $this->allowed_markets) && class_exists($placeClass)) {
      $this->market = new $placeClass($this->netshop);
    }
  }

  protected function view($view, $data = array())
  {
    $view = "market/".$this->place."/".$view;
    $view = parent::view($view, $data)
            ->with('bundle_class', $this->bundle_class)
            ->with('place', $this->place)
            ->with('link_prefix', nc_core('ADMIN_PATH') . '#module.netshop.market.'.$this->place);
    return $view;
  }

  /**
   *
   */
  protected function before_action()
  {
    $this->bundle_id = $id = (int) $this->input->fetch_post_get('bundle_id');
    $this->bundle_class = $this->market->bundle_class;

    if ($id) {
      try {
        $this->bundle = new $this->bundle_class($id);
        $this->catalogue_id = $this->bundle->get('catalogue_id');
      } catch (Exception $e) {
        echo '<div>Wrong bundle ID</div>';
        return false;
      }
    }
    $this->ui_config = new nc_netshop_market_admin_ui(
            $this->market->catalogue_id, $this->place, $this->current_action
    );
  }

  /**
   * @return nc_ui_view
   */
  protected function action_index()
  {
    $add_link = "market.{$this->place}.bundle.add({$this->market->catalogue_id})";
    $this->ui_config->add_create_button($add_link);
    $this->ui_config->locationHash .= "({$this->market->catalogue_id})";

    $bundles = $this->market->get_bundles_list();

    if ($bundles) {

      $domain = $this->nc_core->catalogue->get_by_id($this->market->catalogue_id, 'Domain');
      $view = $this->view('bundles_list')
              ->with('bundles', $bundles)
              ->with('domain', $domain);
    } else {
      $message = constant("NETCAT_MODULE_NETSHOP_MARKET_".strtoupper($this->place)."_NO_BUNDLES");
      $view = parent::view('empty_list')->with('message', $message);
    }

    return $view;
  }

  protected function action_edit()
  {
    $bundle = !empty($this->bundle) ? $this->bundle : new $this->bundle_class($this->bundle_id);

    if ($this->bundle_id) {
      //
    }

    $view = $this->view('bundle_edit')
            ->with('default_types', $bundle->get_default_types())
            ->with('bundle', $bundle)
            ->with('bundle_id', !empty($this->bundle_id) ? $this->bundle_id : 0)
    ;

    $this->ui_config->add_save_and_cancel_buttons();
    $this->ui_config->locationHash .=
            ($this->bundle_id ? ".bundle.edit({$this->bundle_id})" : ".bundle.add({$this->market->catalogue_id})"
            );

    return $view;
  }

  protected function action_edit_fields()
  {
    $bundle = !empty($this->bundle) ? $this->bundle : new $this->bundle_class($this->bundle_id);
    if ($this->bundle_id) {

      $xml_fields = $bundle->get_xml_fields();
      $goods_tables = $bundle->get_goods_tables();
      foreach ($goods_tables as $goods_table) {
        $netcat_fields = $this->market->get_netcat_fields($goods_table);

        $map_values = $bundle->get_map_values($this->bundle_id, $goods_table);

        //todo replace
        $component_name = nc_db()->get_var("SELECT `Class_Name` FROM `Class` WHERE `Class_ID` = {$goods_table}");

        $goods[] = array('goods_table' => $goods_table, 'xml_fields' => $xml_fields, 'netcat_fields' => $netcat_fields, 'map_values' => $map_values, 'component_name' => $component_name);
      }
      $view = $this->view('bundle_fields_edit')
              ->with('bundle', $bundle)
              ->with('goods', $goods)
              ->with('bundle_id', !empty($this->bundle_id) ? $this->bundle_id : 0)
      ;

      $this->ui_config->add_save_and_cancel_buttons();
      $this->ui_config->locationHash .= ".bundle.edit_fields({$this->bundle_id})";

      return $view;
    }
  }

  protected function action_save()
  {
    $data = (array) $this->input->fetch_post('data');

    $is_new = (empty($data['bundle_id'])) ? true : false;
    $bundle = new $this->bundle_class($data);
    try {
      $bundle->save();
      if ($is_new) {
        $bundle->try_defaults($this->market);
      }
      parent::redirect_to_index_action("&place=".$this->place);
      return true;
    } catch (nc_record_exception $e) {
      $view = $this->view('error_message');
      $view->message = NETCAT_MODULE_NETSHOP_UNABLE_TO_SAVE_RECORD;
      return $view;
    }
  }

  protected function action_save_fields()
  {
    $data = (array) $this->input->fetch_post('data');
    $map_fields = (array) $this->input->fetch_post('map_fields');
    $bundle = new $this->bundle_class($data);

    if ($bundle && is_array($map_fields)) {
      $bundle->delete_map($bundle->get('bundle_id'));
      $bundle->save_map($bundle->get('bundle_id'), $map_fields);

    }
    parent::redirect_to_index_action("&place=".$this->place);
  }
  
  protected function action_remove()
  {
    $id = (int) $this->input->fetch_post('bundle_id');
    try {
      $bundle = new $this->bundle_class($id);
      $bundle->delete();
      $bundle->delete_map($id);
    } catch (Exception $e) {

    }
    parent::redirect_to_index_action("&place=".$this->place);
  }
  
  protected function action_order()
  {
    $place = str_replace('_order', '', $this->place);
    $this->ui_config->locationHash = 'module.netshop.market.' . $place . '.order(' . parent::determine_site_id() . ')';
    $this->ui_config->actionButtons[] = array(
      "id" => "submit_form",
      "caption" => NETCAT_MODULE_NETSHOP_SAVE,
      "action" => "mainView.submitIframeForm()"
    );
    
    return parent::view('market/' . $place . '/order');
  }

  protected function action_save_order()
  {
    $fields = (array) $this->input->fetch_post('fields');
    if (!isset($fields['YandexMarketOnlinePayment'])) {
	    $fields['YandexMarketOnlinePayment'] = 0;
    }
    $catalogue_id = $this->input->fetch_get('catalogue_id');
    foreach ($fields as $key => $value) {
      if (gettype($value) == 'array') {
        $value = json_encode($value);
      }
      $this->nc_core->set_settings($key, $value, 'netshop', $catalogue_id);
    }

    parent::redirect_to_index_action('&place=' . $this->place . '&action=order&fields_saved=true');
  }

}
