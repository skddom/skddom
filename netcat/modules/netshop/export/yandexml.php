<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");
require_once($MODULE_FOLDER . "netshop/function.inc.php");
require_once('yandexml.inc.php');

header("Content-type: text/xml");
$nc_core = nc_core();
$catalogue = $nc_core->catalogue->get_by_host_name($HTTP_HOST);
$catalogue = $catalogue["Catalogue_ID"];
if (!$catalogue) $catalogue = 1;

if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
    $modules_lang = "Russian";
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
    $modules_lang = "English";
}

$netshop = nc_netshop::get_instance($catalogue);

if (1 || $netshop->is_netshop_v1_in_use()) { //заглушка, пока не реализован второй вариант
    $export = new YML_Export_V1();
} else {
    $export = new YML_Export_V2($netshop);
}

$export->ExportYML();