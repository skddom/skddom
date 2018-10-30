<?php

/* $Id: ui.php 8366 2012-11-07 16:30:14Z aix $ */

/**
 * 
 */
class nc_search_admin_ui extends ui_config {

    public $headerText = NETCAT_MODULE_SEARCH_TITLE;
    public $headerImage = 'i_module_search_big.gif';

    /**
     *
     */
    public function __construct($view, $params) {
        $this->tabs = array(
                array(
                        'id' => 'info',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_INFO,
                        'location' => "module.search.info",
                ),
                array(
                        'id' => 'indexing',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_INDEXING,
                        'location' => "module.search.indexing",
                ),
                array(
                        'id' => 'lists',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_LISTS,
                        'location' => "module.search.queries",
                ),
                array(
                        'id' => 'settings',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_SETTINGS,
                        'location' => "module.search.generalsettings",
                ),
        );

        $this->activeTab = $view;
        $this->locationHash = "module.search.$view".($params ? "($params)" : "");
        $this->treeMode = "modules";

        $nc_core = nc_Core::get_object();
        $module_settings = $nc_core->modules->get_by_keyword('search');
        $this->treeSelectedNode = "module-$module_settings[Module_ID]";
    }

    /**
     *
     */
    public function add_lists_toolbar() {
        $this->toolbar = array(
                array(
                        'id' => 'queries',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_QUERIES,
                        'location' => 'module.search.queries',
                        'group' => "admin",
                ),
                array(
                        'id' => 'brokenlinks',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_BROKENLINKS,
                        'location' => 'module.search.brokenlinks',
                        'group' => "admin",
                ),
                array(
                        'id' => 'synonyms',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS,
                        'location' => 'module.search.synonyms',
                        'group' => "admin",
                ),
                array(
                        'id' => 'stopwords',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_STOPWORDS,
                        'location' => 'module.search.stopwords',
                        'group' => "admin",
                ),
                array(
                        'id' => 'events',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG,
                        'location' => 'module.search.events',
                        'group' => "admin",
                ),
        );

        list($active_button) = explode("_", $this->activeTab);
        $this->activeToolbarButtons[] = $active_button;
        $this->activeTab = 'lists';
    }

    /**
     *
     */
    public function add_settings_toolbar() {
        $this->toolbar = array(
                array(
                        'id' => 'generalsettings',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_GENERAL_SETTINGS,
                        'location' => 'module.search.generalsettings',
                        'group' => "admin",
                ),
                array(
                        'id' => 'templates',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_VIEW_SETTINGS,
                        'location' => 'module.search.templates',
                        'group' => "admin",
                ),
                array(
                        'id' => 'fields',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_FIELDS,
                        'location' => 'module.search.fields',
                        'group' => "admin",
                ),
                array(
                        'id' => 'rules',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_RULES_SETTINGS,
                        'location' => 'module.search.rules',
                        'group' => "admin",
                ),
                array(
                        'id' => 'extensions',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS,
                        'location' => 'module.search.extensions',
                        'group' => "admin",
                ),
                array(
                        'id' => 'systemsettings',
                        'caption' => NETCAT_MODULE_SEARCH_ADMIN_SYSTEM_SETTINGS,
                        'location' => 'module.search.systemsettings',
                        'group' => "admin",
                ),
        );

        list($active_button) = explode("_", $this->activeTab);
        $this->activeToolbarButtons[] = $active_button;
        $this->activeTab = 'settings';
    }

    /**
     *
     */
    public function add_submit_button($caption) {
        $this->actionButtons[] = array("id" => "submit_form",
                "caption" => $caption,
                "action" => "mainView.submitIframeForm()");
    }

    /**
     *
     */
    public function add_back_button($caption = NETCAT_MODULE_SEARCH_ADMIN_BACK) {
        $this->actionButtons[] = array("id" => "history_back",
                "caption" => $caption,
                "action" => "history.back(1)",
                "align" => "left");
    }

    /**
     *
     */
    public function add_location_parameters($param_string) {
        if (strpos($this->locationHash, "(")) {
            $this->locationHash = substr($this->locationHash, -1)."$param_string)";
        } else {
            $this->locationHash .= "($param_string)";
        }
    }

}
