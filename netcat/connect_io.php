<?php

$NETCAT_FOLDER = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;

// unset for security reasons
$SYSTEM_FOLDER = "";
// include the config file
require_once $NETCAT_FOLDER . 'vars.inc.php';

// if vars.inc.php not updated set default value for $SYSTEM_FOLDER
if (!$SYSTEM_FOLDER) {
    global $SYSTEM_FOLDER;
    $SYSTEM_FOLDER = $ROOT_FOLDER . 'system' . DIRECTORY_SEPARATOR;
}

// PHP version must be >= 5.3
if (version_compare(phpversion(), '5.3', '<')) {
    echo "<b style='color:#A00'>PHP 5.3 or higher required!</b>";
    exit;
}

// short_open_tag must be on
if (!ini_get('short_open_tag')) {
    echo "<b style='color:#A00'>short_open_tag value must be on!</b>";
    exit;
}

// include all new system classes and get nc_core object
require_once $INCLUDE_FOLDER . 'unicode.inc.php';
require_once $SYSTEM_FOLDER . 'index.php';

// set db for compatibility
/** @var nc_Core $nc_core */
$db = $nc_core->db;

global $perm;
$perm = null;