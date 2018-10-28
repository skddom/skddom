<?php

/* $Id: sub_class.inc.php 5946 2012-01-17 10:44:36Z denis $ */

function GetSubClassName($SubClassID) {
    global $db;

    $result = $db->get_var("SELECT `Sub_Class_Name` FROM `Sub_Class` WHERE `Sub_Class_ID` = '".(int) $SubClassID."'");

    return $result;
}

function GetSubdivisionBySubClass($SubClassID) {
    global $db;

    $result = $db->get_var("SELECT `Subdivision_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` = '".(int) $SubClassID."'");

    return $result;
}

function GetClassIDBySubClass($SubClassID) {
    global $db;

    $result = $db->get_var("SELECT `Class_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` = '".(int) $SubClassID."'");

    return $result;
}

/**
 * Delete all objects from SubClass
 *
 * @param int $SubClassID
 * @return null
 */
function SubClassClear($SubClassID) {
    global $nc_core, $db, $MODULE_FOLDER;
    // full info about this subclass
    $SubClassID = (int)$SubClassID;
    $res = $db->get_row("SELECT `Catalogue_ID`, `Subdivision_ID`, `Class_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` = '{$SubClassID}'", ARRAY_A);

    if (nc_module_check_by_keyword('comments')) {
        include_once $MODULE_FOLDER . 'comments/function.inc.php';
        // delete comment rules
        nc_comments::dropRuleSubClass($db, $SubClassID);
        // delete comments
        nc_comments::dropComments($db, $SubClassID, 'Sub_Class');
    }

    if ($db->get_var('SELECT System_Table_ID FROM Class WHERE Class_ID = ' . $res['Class_ID'])) {
        return null;
    }

    DeleteSubClassFiles($SubClassID, $res['Class_ID']);

    // delete from message
    $messages_id = $db->get_col("SELECT `Message_ID` FROM `Message{$res['Class_ID']}` WHERE `Subdivision_ID` = '{$res['Subdivision_ID']}' AND `Sub_Class_ID` = '{$SubClassID}'");
    if (!empty($messages_id)) {
        if ($db->query("SELECT * FROM `Message{$res['Class_ID']}` WHERE `Message_ID` IN (" . implode(', ', $messages_id) . ')')) {
            $messages_data = array_combine($db->get_col(NULL, 0), $db->get_results(NULL));
        }

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_OBJECT_DELETED, $res['Catalogue_ID'], $res['Subdivision_ID'], $SubClassID, $res['Class_ID'], $messages_id, $messages_data);

        $db->query("DELETE FROM `Message{$res['Class_ID']}` WHERE `Message_ID` IN (" . implode(', ', $messages_id) . ')');

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_OBJECT_DELETED, $res['Catalogue_ID'], $res['Subdivision_ID'], $SubClassID, $res['Class_ID'], $messages_id, $messages_data);
    }

    return null;
}

function DeleteSubClass($SubClassID) {
    global $nc_core, $db;

    $SubClassID+= 0;

    $data = $nc_core->sub_class->get_by_id($SubClassID);

    // execute core action
    $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_DELETED, $data['Catalogue_ID'], $data['Subdivision_ID'], $SubClassID);

    SubClassClear($SubClassID);
    $db->query("DELETE FROM `Sub_Class` WHERE `Sub_Class_ID` = '{$SubClassID}'");
    if ($db->last_error) {
        return false;
    }

    // execute core action
    $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_DELETED, $data['Catalogue_ID'], $data['Subdivision_ID'], $SubClassID);

    return null;
}

function IsAllowedSubClassEnglishName($EnglishName, $SubdivisionID, $SubClassID) {
    global $db;

    $select = "SELECT `EnglishName` FROM `Sub_Class` WHERE ";
    $select.= "`EnglishName` = '".$EnglishName."'";
    $select.= " AND `Subdivision_ID` = '".$SubdivisionID."'";
    $select.= " AND `Sub_Class_ID` <> '".$SubClassID."'";

    $Result = $db->query($select);
    if ($db->num_rows == 0) return 1;

    return 0;
}
?>