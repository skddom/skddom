<?

// $main_section = "settings";
// $item_id = 3;

// error_reporting(E_ALL ^ E_NOTICE);

// $NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
// include_once ($NETCAT_FOLDER."vars.inc.php");

chdir('admin/');

require_once 'admin/index.php';

// require_once ($ADMIN_FOLDER."function.inc.php");
// require_once ($ADMIN_FOLDER."classificator.inc.php");



// if (is_file($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php")) {
//     require_once($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php");
// } else {
//     require_once($MODULE_FOLDER."netshop/en.lang.php");
// }

// $UI_CONFIG = new ui_config_module_netshop('admin');

// $Delimeter = " &gt ";
// $Title1 = "<a href=".$ADMIN_PATH."modules/>".NETCAT_MODULES."</a>".$Delimeter.NETCAT_MODULE_NETSHOP_TITLE;
// $Title2 = NETCAT_MODULE_NETSHOP_TITLE;

// // check permission
// $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

// //LoadModuleEnv();
// $MODULE_VARS = $nc_core->modules->get_module_vars();

// BeginHtml($Title2, $Title1, "http://".$DOC_DOMAIN."/settings/modules/netshop/");

// if (!$GLOBALS["MODULE_VARS"]["netshop"]["SHOP_TABLE"]) {
//     nc_print_status(NETCAT_MODULE_NETSHOP_ERROR_NO_SETTINGS, 'error');
//     EndHtml();
//     die();
// }

// header("Location: ".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/netshop/sources.php");

// EndHtml ();
