<?php


/**
 *
 */
class nc_netshop_settings_admin_controller extends nc_netshop_admin_controller {

    /** @var  nc_netshop_settings_admin_ui */
    protected $ui_config;

    protected $ui_config_class = 'nc_netshop_settings_admin_ui';

    /**
     *
     */
    protected function init() {
        parent::init();
        $this->bind('settings_save', array('settings', 'next_action'));
        $this->bind('get_order_component_template_select', array('order_component_id'));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $this->ui_config->add_submit_button();
        $view = $this->view('org_settings');
        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_module() {
        $this->ui_config->add_submit_button();
        $view = $this->view('module_settings');
        return $view;
    }

   /**
     * @param $settings
     * @param $next_action
     */
    protected function action_settings_save($settings, $next_action) {
        $nc_core = $nc_core = nc_core::get_object();

        $old_item_index_fields = $this->netshop->get_setting('ItemIndexFields');

        foreach ($settings as $k=>$v) {
            $nc_core->set_settings($k, $v, 'netshop', $this->site_id);
        }

        // Переиндексирование товаров, если было изменено значение ItemIndexFields
        if (isset($settings['ItemIndexFields']) && $settings['ItemIndexFields'] != $old_item_index_fields) {
            $this->netshop->reload();
            $this->netshop->itemindex->reindex_site();
        }

        $this->redirect_to_index_action($next_action);
    }

}