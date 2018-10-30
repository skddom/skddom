<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER . "vars.inc.php");
require_once ($ADMIN_FOLDER . "function.inc.php");
require_once ($ADMIN_FOLDER . "report/function.inc.php");

$UI_CONFIG = new ui_config_tool(SECTION_ABOUT_TITLE, SECTION_ABOUT_TITLE, 'i_netcat_big.gif', 'help.about');

if (!isset($phase) || !$phase) $phase = 1;

BeginHtml(SECTION_ABOUT_TITLE, SECTION_ABOUT_HEADER, "http://".$DOC_DOMAIN."/");

switch ($phase) {
    case 1:
        echo "<br />";
        
        printf(SECTION_ABOUT_BODY, $SYSTEM_COLOR, $SYSTEM_NAME, $VERSION_ID);
        
		if ($DEVELOPER_NAME) {
			echo "<br /><br />";
			printf("<i>".SECTION_ABOUT_DEVELOPER.": ".($DEVELOPER_URL ? '<a href='.htmlspecialchars($DEVELOPER_URL, ENT_QUOTES).'>' : '').htmlspecialchars($DEVELOPER_NAME, ENT_QUOTES).($DEVELOPER_URL ? '</a>' : '')."</i>");
			echo "<br />";
		}
        
        echo nc_report_status();
        
	break;
    case 2:
        ob_clean();
        phpinfo();
        exit;
	break;
}

EndHtml();
?>