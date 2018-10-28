<?php

/* $Id */
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require ($ADMIN_FOLDER."report/function.inc.php");


$Delimeter = " &gt ";
$main_section = "report";
$item_id = 1;
$Title1 = "Отчет о состоянии";

if (!isset($phase) || !$phase) $phase = 1;



BeginHtml($Title1, $Title1, "http://".$DOC_DOMAIN."/reports/general/");
$perm->ExitIfNotAccess(NC_PERM_REPORT, 0, 0, 0, 1);

switch ($phase) {
    case 1: #общее сведения
        $UI_CONFIG = new ui_config_tool("Отчет о состоянии", "Отчет о состоянии", "", "");
        echo nc_report_status();
        break;
    case 2:
        $UI_CONFIG = new ui_config_tool("Отчет о состоянии", "Отчет о состоянии", "", "");
        phpinfo();
        break;
}

EndHtml();
?>
