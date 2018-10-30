<?php
// make user's undivine
@ignore_user_abort(true);

// load system
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");

if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}

@set_time_limit(0);

include_once($INCLUDE_FOLDER . "index.php");

// system superior object
$nc_core = nc_Core::get_object();

require_once('cml2_catalog.inc.php');
$CML2_Catalog_Export = new CML2_Catalog_Export($source_id, $nc_core);

$CML2_Catalog_Export->export(true);