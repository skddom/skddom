<?php

/* $Id: update_tree.php 3612 2009-12-03 12:51:42Z vadim $ */

$_POST["NC_HTTP_REQUEST"] = true;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");

if (!$perm->isAccess(NC_PERM_MODULE, 0, 0, 0, 1)) {
    trigger_error("Permission denied", E_USER_ERROR);
}

if (
        $type == "forum" && (!isset($from_id) || !isset($to_id) || !isset($obj_id) ) ||
        $type == "group" && (!isset($from_id) || !isset($to_id) )
) {
    trigger_error("Wrong params", E_USER_ERROR);
}

$from_id+= 0;
$to_id+= 0;
$obj_id+= 0;

// forum moved
if ($type == "forum") {
    /**
     * from_id - source group
     *   to_id - destination group
     *  obj_id - dragged object
     */
    $parents = $db->get_results("SELECT `ID`, `Subdivision_ID` FROM `Forum_Groups`
    WHERE `ID` IN (".$from_id.", ".$to_id.")", ARRAY_A);

    if (empty($parents)) die(0);

    $from_parent_id = 0;
    $to_parent_id = 0;

    foreach ($parents as $row) {
        if ($row['ID'] == $from_id) $from_parent_id = $row['Subdivision_ID'];
        if ($row['ID'] == $to_id) $to_parent_id = $row['Subdivision_ID'];
    }

    if ($from_parent_id == $to_parent_id || $from_id == 0) {
        $db->query("UPDATE `Forum_Subdivisions`
      SET `Group_ID` = '".$to_id."'
      WHERE `Subdivision_ID` = '".$obj_id."'".($from_id ? " AND `Group_ID` = '".$from_id."'" : ""));
    }

    if ($db->rows_affected) {
        echo $db->get_var("SELECT `Subdivision_ID` FROM `Forum_Groups` WHERE `ID` = '".$to_id."'");
    } else {
        echo 0;
    }
}

// group moved
if ($type == "group") {
    /**
     * from_id - dragged source object
     *   to_id - destination target object
     */
    $groups = $db->get_results("SELECT `ID`, `Subdivision_ID`, `Priority` FROM `Forum_Groups`
    WHERE `ID` IN (".$from_id.", ".$to_id.")", ARRAY_A);

    if (empty($groups) || $groups[0]['Subdivision_ID'] != $groups[1]['Subdivision_ID'])
            die(0);

    foreach ($groups as $row) {
        if ($row['ID'] == $from_id) $from_priority = $row['Priority'];
        if ($row['ID'] == $to_id) $to_priority = $row['Priority'];
    }

    //if ($from_priority==$to_priority) die(0);

    if ($to_priority <= $from_priority) {
        // Close gap at the position of the dragged object and make space at the new position
        $db->query("UPDATE `Forum_Groups`
       SET `Priority` = `Priority` - 1
       WHERE `Priority` BETWEEN '".$from_priority."' AND ('".$to_priority."' - 1)");

        // Change dragged object's priority
        $db->query("UPDATE `Forum_Groups`
       SET Priority = (".$to_priority." - 1)
       WHERE ID = '".$from_id."'");
    } else {
        // Close gap at the position of the dragged object and make space at the new position
        $db->query("UPDATE `Forum_Groups`
       SET `Priority` = `Priority` + 1
       WHERE `Priority` BETWEEN '".$to_priority."' AND ('".$from_priority."' - 1)");

        // Change dragged object's priority
        $db->query("UPDATE `Forum_Groups`
       SET `Priority` = '".$to_priority."'
       WHERE `ID` = '".$from_id."'");
    }

    if ($db->rows_affected) {
        echo $groups[0]['Subdivision_ID'];
    } else {
        echo 0;
    }
}
?>