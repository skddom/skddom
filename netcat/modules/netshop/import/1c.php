<?php

/**
 * Интерфейс для автоматического импорта данных из 1C
 *
 * param source_id
 * param key
 * CommerceML data is in $HTTP_RAW_POST_DATA
 */
// LOGGING FUNCTION -- FOR DEBUGGING PURPOSES ONLY

$log_1c = false;

function nc_netshop_log_1c($msg) {
    if (!$GLOBALS['log_1c']) return;
    static $fp;
    if (!$fp) {
        $fp = fopen($GLOBALS['TMP_FOLDER'] . "1c.log", "a");
    }
    fputs($fp, $msg . "\n");
}

// -----------

@ignore_user_abort(true);

// FILE INCLUSION SECTION
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");
$MODULE_VARS = $nc_core->modules->get_module_vars();

if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}

require_once($MODULE_FOLDER . "netshop/old/kxlib.php");
require_once($MODULE_FOLDER . "netshop/function.inc.php");

// CHECK REQUIRED PARAMS
nc_netshop_log_1c("\n" . strftime('%Y-%m-%d %H:%M:%S') . ' request from ' . $_SERVER['REMOTE_ADDR']);

if (!int($_GET["source_id"])) {
    nc_netshop_log_1c('No source ID');
    die("No source id supplied");
}

if (!$HTTP_RAW_POST_DATA) {
    nc_netshop_log_1c('No data supplied');
    die("No data");
}


$source_id = intval($_GET["source_id"]);
extract(row("SELECT * FROM `Netshop_ImportSources` WHERE `source_id` = '" . $source_id . "'"));

if (!$catalogue_id) {
    nc_netshop_log_1c("Possibly wrong source_id: cannot get settings for the source");
    die("Wrong source_id?");
}

// check KEY
$netshop = nc_netshop::get_instance($catalogue_id);
$our_key = $netshop->is_netshop_v1_in_use($catalogue_id) ?
    $MODULE_VARS["netshop"]["SECRET_KEY"] :
    $netshop->get_setting('1cSecretKey');

if ($our_key && md5("$our_key$_GET[source_id]") != $_GET["key"]) {
    nc_netshop_log_1c("Wrong key");
    die("WRONG KEY");
}

$sql = "UPDATE `Netshop_ImportSources` SET `last_update` = NOW() WHERE `source_id` = $source_id";
q($sql);

// disable items not in the commerceml file?
if ($nonexistant == 'disable') {
    $res = q("SELECT DISTINCT c.Class_ID, c.Class_Name
      FROM `Class` as c, `Field` as f
      WHERE c.Class_ID = f.Class_ID
      AND f.Field_Name LIKE 'Price%'
      AND c.Class_Group = 'Netshop'
      ORDER BY c.Priority, c.Class_ID");

    while (list($id, $name) = mysqli_fetch_row($res)) {
        q("UPDATE `Message" . $id . "` SET `Checked` = 0 WHERE `ImportSourceID` = '" . $source_id . "'");
    }
}

// Here's an example how you should never write code:
$silent_1c_import = true;

// put data to the file
$filename = uniqid("import");
$filedir = $TMP_FOLDER;
file_put_contents($filedir . $filename, $HTTP_RAW_POST_DATA);

ob_start();
// include parse engine
require("./commerceml.php");

$out = ob_get_contents();

if (trim($out)) {
    nc_netshop_log_1c($out);
} else {
    nc_netshop_log_1c("DONE");
}