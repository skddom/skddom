<?php

/* $Id: admin.php 4469 2011-04-12 07:55:22Z denis $ */

// get settings
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
require_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");

require_once ($MODULE_FOLDER."forum2/nc_forum2_admin.class.php");

// language constants
if (is_file($MODULE_FOLDER.'forum2/'.MAIN_LANG.'.lang.php')) {
    require_once($MODULE_FOLDER.'forum2/'.MAIN_LANG.'.lang.php');
} else {
    require_once($MODULE_FOLDER.'forum2/en.lang.php');
}

// load modules env
if (!isset($MODULE_VARS)) $MODULE_VARS = $nc_core->modules->get_module_vars();

// UI config
require_once ($ADMIN_FOLDER."modules/ui.php");
// default
if (!$page) $page = "settings";
require_once ($MODULE_FOLDER."forum2/ui_config.php");

$Title1 = NETCAT_MODULES;
$Title2 = NETCAT_MODULE_FORUM2;

// default phase
if (!isset($phase)) $phase = 1;

// UI functional
$UI_CONFIG = new ui_config_module_forum2('admin', $page);

// admin object
try {
    $nc_forum2_admin = new nc_forum2_admin();
} catch (Exception $e) {
    BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/forum2/");
    // got error
    nc_print_status($e->getMessage(), "error");
    EndHtml();
    exit;
}

switch ($phase) {
    // step 1: show settings form
    case 1:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/forum2/");
        // check permission
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 0);
        // show settings form
        $nc_forum2_admin->settings();
        break;

    // step 2: save settings
    case 2:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/forum2/");
        // check permission
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        // save settings
        $nc_forum2_admin->settingsSave();
        // successfully saved
        nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_SAVE_OK, "ok");
        // show settings form
        $nc_forum2_admin->settings();
        break;

    // step 3, 4: converter
    case 3:
    case 4:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/forum2/");
        // check permission
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        // catalogue if setted
        $conv_catalogue = $_POST['ConverterCatalogue'] ? $_POST['ConverterCatalogue'] : 0;
        // subdivision if setted
        $conv_subdivision = $_POST['ConverterSubdivision'] ? $_POST['ConverterSubdivision'] : 0;
        // show convert form
        $nc_forum2_admin->converter($phase, $conv_catalogue, $conv_subdivision);
        break;

    // step 5: converter done
    case 5:
        BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/forum2/");
        // check permission
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        // save convert
        if ($nc_forum2_admin->converterSave()) {
            // successfully converted
            nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_OK, "ok");
        }
        // show convert form
        $nc_forum2_admin->converter();
        break;
}

EndHtml();
?>