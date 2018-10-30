<?

/* $Id: header.inc.php 7307 2012-06-26 13:40:15Z alive $ */

$main_section = "settings";
$item_id = 12;

error_reporting(E_ALL ^ E_NOTICE);

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER."catalogue/function.inc.php");
require_once ($ADMIN_FOLDER."siteinfo/function.inc.php");

$UI_CONFIG = new ui_config_catalogue('seo', +$CatalogueID);

$Delimeter = " &gt ";
$Title1 = SECTION_SECTIONS_INSTRUMENTS_SITEINFO;
$Title2 = NETCAT_MODULE_AUDITOR_TITLE;

BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/siteinfo/");
$perm->ExitIfNotAccess(NC_PERM_SEO, NC_PERM_ACTION_VIEW, 0, 0, 1);

//LoadModuleEnv();
//$MODULE_VARS = $nc_core->modules->get_module_vars();
?>