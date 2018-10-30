<?php

/* $Id: admin.php 4290 2011-02-23 15:32:35Z denis $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
include_once ($MODULE_FOLDER."stats/openstat/admin.inc.php");
include_once ($ADMIN_FOLDER."function.inc.php");

include_once ($ADMIN_FOLDER."modules/ui.php");
include_once ($MODULE_FOLDER."stats/ui_config.php");
require_once ($ADMIN_FOLDER."catalogue/function.inc.php");

if (!$sub_view) {
    $sub_view = 'reports';
}

if (isset($_GET['catalog_page'])) {
    $UI_CONFIG = new ui_config_catalogue('stat', $catalog_page, '', '', 'openstat');
    $phase = $db->get_var("SELECT `Counter_Id` FROM `Stats_Openstat_Counters` WHERE `Catalogue_Id` = '".$db->escape($catalog_page + 0)."' OR `Catalogue_Id`='0'");
    $sub_view = 'reports';
} else {
    $UI_CONFIG = new ui_config_module_stats('openstat', $sub_view, $phase);
    $UI_CONFIG->add_openstat_toolbar($sub_view);
}


if (is_file($MODULE_FOLDER."stats/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."stats/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER."stats/en.lang.php");
}


$Delimeter = " &gt ";
$Title1 = NETCAT_MODULE_STATS;
$Title2 = "<a href=".$ADMIN_PATH."modules/>".NETCAT_MODULES."</a>";

// check permission
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();


$phase+=0;


BeginHtml($Title1, $Title2.$Delimeter.$Title1, "http://".$DOC_DOMAIN."/settings/modules/stats/openstat");


// check permission
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);


$db->get_results("SELECT * FROM `Stats_Openstat_Counters` order by `Counter_Id`");
$counters = @array_combine($db->get_col(NULL, 0), $db->get_results(NULL));

switch ($sub_view) {
    case 'templates' :
        show_templates($phase);
        break;

    case 'counters' :
        show_counters($phase);
        break;

    case 'reports' :
    default:
        show_reports($phase);
        break;
}


EndHtml();
?>