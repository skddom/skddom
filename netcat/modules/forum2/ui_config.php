<?php

/* $Id: ui_config.php 4290 2011-02-23 15:32:35Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");

/**
 * Класс для облегчения формирования UI в модулях
 */
class ui_config_module_forum2 extends ui_config_module {

    function ui_config_module_forum2($active_tab = 'admin', $toolbar_action = 'settings') {
        global $db;
        global $MODULE_FOLDER;

        $this->ui_config_module('forum2', $active_tab);

        if ($active_tab = 'admin') {

            $this->toolbar[] = array(
                    'id' => "settings",
                    'caption' => NETCAT_MODULE_FORUM2_ADMIN_TEMPLATE_SETTINGS_TAB,
                    'location' => "module.forum2.settings",
                    'group' => "forum2"
            );
            $this->toolbar[] = array(
                    'id' => "converter",
                    'caption' => NETCAT_MODULE_FORUM2_ADMIN_TEMPLATE_CONVERTER_TAB,
                    'location' => "module.forum2.converter",
                    'group' => "forum2"
            );

            if ($toolbar_action)
                    $this->locationHash = "module.forum2.".$toolbar_action;
            $this->activeToolbarButtons[] = $toolbar_action;
        }
    }

}
?>