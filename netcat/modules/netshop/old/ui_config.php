<?php

/* $Id: ui_config.php 6208 2012-02-10 10:21:43Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");

/** @todo OBSOLETE, REMOVE */

/**
 * Класс для облегчения формирования UI в модулях
 */
class ui_config_module_netshop extends ui_config_module {

    public function __construct($active_tab = 'admin', $toolbar_action = 'setup') {

        // $this->ui_config_module('netshop', $active_tab, "netshop-{$toolbar_action}");
        $this->ui_config_module('netshop', $active_tab);

        if ($active_tab = 'admin') {

            // $this->toolbar[] = array(
            //     'id'       => "import",
            //     'caption'  => NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML,
            //     'location' => "module.netshop.import",
            //     'group'    => "grp1"
            // );

            $this->toolbar[] = array(
                'id' => "sources",
                'caption' => NETCAT_MODULE_NETSHOP_SOURCES,
                'location' => "module.netshop.sources",
                'group' => "grp1"
            );
            $this->toolbar[] = array(
                'id' => "setup",
                'caption' => NETCAT_MODULE_NETSHOP_SETUP,
                'location' => "module.netshop.setup",
                'group' => "grp1"
            );

            // $this->toolbar[] = array(
            //     'id'       => "forms",
            //     'caption'  => NETCAT_MODULE_NETSHOP_FORMS,
            //     'location' => "module.netshop.forms",
            //     'group'    => "grp1"
            // );


            $this->locationHash = "module.netshop.$toolbar_action";
            $this->activeToolbarButtons[] = $toolbar_action;
        }
    }

    /**
     *
     */
    public function add_submit_button($caption) {
        $this->actionButtons[] = array(
            "id" => "submit_form",
            "caption" => $caption,
            "action" => "mainView.submitIframeForm()"
        );
    }

    public function add_create_button($location) {
        $this->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_NETSHOP_BUTTON_ADD,
            "location" => "#module.netshop.$location",
            "align" => "left");
    }

    /**
     * Для форм редактирования
     */
    public function add_save_and_cancel_buttons($save_button_caption = NETCAT_MODULE_NETSHOP_BUTTON_SAVE) {
        $this->actionButtons[] = array(
            "id" => "history_back",
            "caption" => NETCAT_MODULE_NETSHOP_BUTTON_BACK,
            "action" => "history.back(1)",
            "align" => "left"
        );
        $this->add_submit_button($save_button_caption);
    }

    /**
     *
     */

    // public function add_templates_toolbars($active_button) {

    //     $this->tabs = array(
    //         array(
    //             'id' => 'template',
    //             'caption' => NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATES,
    //             'location' => 'module.netshop.mailer.template',
    //             'group' => "admin",
    //         ),
    //         array(
    //             'id' => 'customer_order',
    //             'caption' => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER,
    //             'location' => 'module.netshop.mailer.customer_order',
    //             'group' => "admin",
    //         ),
    //     );
    //     $this->toolbar   = false;
    //     $this->activeTab = $active_button;
    // }

    public function add_templates_toolbars($type, $active_button, $catalogue_id = 0) {
        $this->toolbar = array(
            array(
                'id' => $type . '_order',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER,
                'location' => 'module.netshop.mailer.' . $type . '_mail(' . $catalogue_id . ',order)',
                'group' => "admin",
            ),

        );

        $db = nc_Core::get_object()->db;

        $sql = "SELECT `ShopOrderStatus_ID`, `ShopOrderStatus_Name` FROM `Classificator_ShopOrderStatus` ORDER BY `ShopOrderStatus_Priority` ASC";
        $result = (array)$db->get_results($sql, ARRAY_A);

        foreach ($result as $row) {
            $this->toolbar[] = array(
                'id' => $type . '_status_' . $row['ShopOrderStatus_ID'],
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_ORDER_STATUS . ' "' . $row['ShopOrderStatus_Name'] . '"',
                'location' => 'module.netshop.mailer.' . $type . '_mail(' . $catalogue_id . ',status_' . $row['ShopOrderStatus_ID'] . ')',
                'group' => "admin",
            );
        }

        $this->activeToolbarButtons[] = $active_button;

        /* @todo fix all this. */
        $this->activeTab = $active_button;
    }

}