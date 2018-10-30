<?

$main_section = "settings";
$module_name = "netshop";

// error_reporting(E_ALL ^ E_NOTICE);

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");


if (is_file($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER."netshop/en.lang.php");
}

$Delimeter = " &gt ";
$Title1 = "<a href=".$ADMIN_PATH."modules/>".NETCAT_MODULES."</a>".$Delimeter.NETCAT_MODULE_NETSHOP_TITLE;
$Title2 = NETCAT_MODULE_NETSHOP_TITLE;

$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
//LoadModuleEnv();
$MODULE_VARS = $nc_core->modules->get_module_vars();

BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/");