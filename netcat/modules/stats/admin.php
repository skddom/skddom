<?php

/* $Id: admin.php 6210 2012-02-10 10:30:32Z denis $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($MODULE_FOLDER."stats/admin.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER."catalogue/function.inc.php");


require_once ($ADMIN_FOLDER."modules/ui.php");
require_once ($MODULE_FOLDER."stats/ui_config.php");
if ($cat_id) {
    $UI_CONFIG = new ui_config_catalogue('stat', $cat_id, '', '', 'nc_stat');
} else {
    $UI_CONFIG = new ui_config_module_stats('nc_stat', '', $phase);
    $in_module_page = 1;
}

if (is_file($MODULE_FOLDER."stats/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."stats/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER."stats/en.lang.php");
}

$stats_cat_id = stats_IsOneCatalogue();

if ($stats_cat_id != FALSE) {
    $cat_id = $stats_cat_id;
    if ($phase == 0) {
        $phase = 9;
    }
}


$Delimeter = " &gt ";
$Title1 = NETCAT_MODULE_STATS;
$Title2 = "<a href=".$ADMIN_PATH."modules/>".NETCAT_MODULES."</a>";
$Title3 = "<a href='".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/stats/admin.php'>".$Title1."</a>";
if (isset($cat_id) && $cat_id != 0)
        $Title4 = $Title2.$Delimeter.$Title3.$Delimeter.$nc_core->catalogue->get_by_id($cat_id, "Catalogue_Name");

// check permission
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();

$phase+=0;

//stats_CreateReports();

if (isset($cat_id) && $cat_id != 0) {
    BeginHtml($Title1, $Title4, "http://".$DOC_DOMAIN."/settings/modules/stats/");
} else {
    BeginHtml($Title1, $Title2.$Delimeter.$Title1, "http://".$DOC_DOMAIN."/settings/modules/stats/");
}


// check permission
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

if ($in_module_page) {
    if ($nc_core->get_settings('NC_Stat_Enabled', 'stats') != 1) {
        nc_print_status(NETCAT_MODULE_STATS_ADMIN_TAB_NC_STAT_DISABLED, "error");
        EndHtml();
        exit;
    }
}

if ($date_start_y && $date_start_m && $date_start_d)
        $date_start = sprintf("%04d-%02d-%02d", $date_start_y, $date_start_m, $date_start_d);
if ($date_end_y && $date_end_m && $date_end_d)
        $date_end = sprintf("%04d-%02d-%02d", $date_end_y, $date_end_m, $date_end_d);

if (!$date_start) $date_start = date("Y-m-d");
if (!$date_end) $date_end = date("Y-m-d");

list($date_start_y, $date_start_m, $date_start_d) = explode("-", $date_start);
list($date_end_y, $date_end_m, $date_end_d) = explode("-", $date_end);


if ($phase != 0 && $cat_id) {
    Stats_NavBar();
}


switch ($phase) {
    case 0:
        stats_ShowCatalogues();
        break;
    case 1:
        stats_ShowReportAttendance($date_start, $date_end);
        break;
    case 2:
        stats_ShowReportPopularity($date_start, $date_end);
        break;
    case 3:
        stats_ShowReportReferer($date_start, $date_end);
        break;
    case 4:
        stats_ShowReportBrowser($date_start, $date_end);
        break;
    case 5:
        stats_ShowReportOS($date_start, $date_end);
        break;
    case 6:
        stats_ShowReportIP($date_start, $date_end);
        break;
    case 7:
        stats_ShowReportGeo($date_start, $date_end);
        break;
    case 8:
        stats_ClearStats();
        stats_ShowReportTotal();
        break;
    case 9:
        if ($cat_id) {
            stats_ShowReportTotal();
        } else {
            stats_ShowCatalogues();
        }
        break;
    case 10:
        stats_ShowReportPhrases($date_start, $date_end);
        break;
}


EndHtml();
?>