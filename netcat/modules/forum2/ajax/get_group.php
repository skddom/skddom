<?php

/* $Id: get_group.php 4077 2010-10-22 13:08:28Z denis $ */

$_POST["NC_HTTP_REQUEST"] = true;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");

if (!$perm->isAccess(NC_PERM_MODULE, 0, 0, 0, 1)) {
    trigger_error("Permission denied", E_USER_ERROR);
}

if (!isset($group_id)) {
    trigger_error("Wrong params", E_USER_ERROR);
}

$group_id+= 0;

$data = $db->get_row("SELECT * FROM `Forum_Groups` WHERE `ID` = '".$group_id."'", ARRAY_A);

if (!empty($data)) {
    echo "{'id':'".$data['ID']."', 'name':'".$data['Name']."', 'description':'".$data['Description']."', 'priority':'".$data['Priority']."'}";
} else {
    echo "{'id':'', 'name':'', 'description':'', 'priority':'0'}";
}
?>