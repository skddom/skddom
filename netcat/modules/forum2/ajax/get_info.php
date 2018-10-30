<?php

/* $Id: get_info.php 3612 2009-12-03 12:51:42Z vadim $ */

$_POST["NC_HTTP_REQUEST"] = true;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");

if (!$perm->isAccess(NC_PERM_MODULE, 0, 0, 0, 1)) {
    trigger_error("Permission denied", E_USER_ERROR);
}

if (!isset($node_id)) {
    trigger_error("Wrong params", E_USER_ERROR);
}

$node_id+= 0;

echo $db->get_var("SELECT `Description` FROM `Forum_Subdivisions`
  WHERE `Subdivision_ID` = '".$node_id."'");
?>