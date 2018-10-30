<?php

class ui_config_module_sitesecure extends ui_config_module {

    public $headerText = SKYLAB_MODULE_SITESECURE;

    //public $headerImage = '';


    function ui_config_module_sitesecure($view, $params) {
        $this->tabs[] = array(
            'id' => "main",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_MAIN_TAB,
            'location' => "module.sitesecure.main",
            'group' => "admin"
        );
        $this->tabs[] = array(
            'id' => "settings",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_SETTINGS_TAB,
            'location' => "module.sitesecure.settings",
            'group' => "admin"
        );
        $this->tabs[] = array(
            'id' => "info",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_INFO_TAB,
            'location' => "module.sitesecure.info",
            'group' => "admin"
        );
        $this->activeTab = $view;
        $this->locationHash = "module.sitesecure." . $view . ($params ? "(" . $params . ")" : "");
        $this->treeMode = "modules";

        $module_settings = nc_Core::get_object()->modules->get_by_keyword('sitesecure');
        $this->treeSelectedNode = "module-" . $module_settings['Module_ID'];
    }

    public function add_main_toolbar() {
        $this->toolbar[] = array(
            'id' => "main",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_REVIEW_TAB,
            'location' => "module.sitesecure.main",
            'group' => "admin"
        );

        $this->toolbar[] = array(
            'id' => "alerts",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_ALERTS_TAB,
            'location' => "module.sitesecure.alerts",
            'group' => "admin"
        );

        $this->toolbar[] = array(
            'id' => "seal",
            'caption' => SKYLAB_MODULE_SITESECURE_ADMIN_TEMPLATE_SEAL_TAB,
            'location' => "module.sitesecure.seal",
            'group' => "admin"
        );

        $this->activeToolbarButtons[] = $this->activeTab;
        $this->activeTab = 'main';
    }

}
