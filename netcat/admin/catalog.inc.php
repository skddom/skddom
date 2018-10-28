<?php

/**
 * Удаляет каталог и все его содержимое
 * 
 * @global type $nc_core
 * @global type $db
 * @global type $MODULE_FOLDER
 * @param type $catalogueId
 * @return type
 */
function CascadeDeleteCatalogue($catalogueId) {
    global $nc_core, $db, $MODULE_FOLDER;

    $catalogueId = (int) $catalogueId;

    $subClasses = (array) $db->get_results("SELECT `Subdivision_ID`, `Sub_Class_ID`, `Class_ID` FROM `Sub_Class` WHERE `Catalogue_ID` = {$catalogueId}", ARRAY_A);

    // execute core action
    $nc_core->event->execute(nc_Event::BEFORE_SITE_DELETED, $catalogueId);

    $subdivisionsToDelete = array();

    //delete messages
    foreach ($subClasses as $subClass) {
        $subdivisionId = (int) $subClass['Subdivision_ID'];
        $subClassId = (int) $subClass['Sub_Class_ID'];
        $classId = (int) $subClass['Class_ID'];

        if (!in_array($subdivisionId, $subdivisionsToDelete)) {
            $subdivisionsToDelete[] = $subdivisionId;
        }

        $subClassesToDelete[] = $subClassId;

        $messages = (array) $db->get_results("SELECT `Message_ID` FROM `Message{$classId}` WHERE `Sub_Class_ID` = {$subClassId}", ARRAY_A);

        $messagesToDelete = array();
        foreach ($messages as $message) {
            $messagesToDelete[] = (int) $message['Message_ID'];
        }

        if (count($messagesToDelete) > 0) {
            // execute core action
            $nc_core->event->execute(nc_Event::BEFORE_OBJECT_DELETED, $catalogueId, $subdivisionId, $subClassId, $classId, $messagesToDelete);
            // delete messages
            foreach ($messagesToDelete as $messageId) {
                DeleteMessageFiles($classId, $messageId);
                $db->query("DELETE FROM `Message{$classId}` WHERE `Message_ID` = {$messageId}");
            }
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_OBJECT_DELETED, $catalogueId, $subdivisionId, $subClassId, $classId, $messagesToDelete);
        }

        //delete subclass
        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_DELETED, $catalogueId, $subdivisionId, $subClassId);

        $db->query("DELETE FROM `Sub_Class` WHERE `Sub_Class_ID` = {$subClassId}");
        DeleteSubClassDirAlways($subdivisionId, $subClassId);

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_DELETED, $catalogueId, $subdivisionId, $subClassId);
    }

    $subDivisions = (array) $db->get_results("SELECT `Subdivision_ID` FROM `Subdivision` WHERE `Catalogue_ID` = {$catalogueId}", ARRAY_A);
    foreach ($subDivisions as $subDivision) {
        if (!in_array($subDivision['Subdivision_ID'], $subdivisionsToDelete)) {
            array_push($subdivisionsToDelete, $subDivision['Subdivision_ID']);
        }
    }

    if (count($subdivisionsToDelete) > 0) {
        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_DELETED, $catalogueId, $subdivisionsToDelete);

        foreach ($subdivisionsToDelete as $subdivisionId) {
            $db->query("DELETE FROM `Subdivision` WHERE `Subdivision_ID` = {$subdivisionId}");
            DeleteSubdivisionDir($subdivisionId);
        }

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_DELETED, $catalogueId, $subdivisionsToDelete);
    }

    if (nc_module_check_by_keyword('comments')) {
        include_once ($MODULE_FOLDER . 'comments/function.inc.php');
        // delete comment rules
        nc_comments::dropRule($db, array($catalogueId));
        // delete comments
        nc_comments::dropComments($db, $catalogueId, 'Catalogue');
    }

    // delete catalogue
    $db->query("DELETE FROM `Catalogue` WHERE `Catalogue_ID` = {$catalogueId}");

    // execute core action
    $nc_core->event->execute(nc_Event::AFTER_SITE_DELETED, $catalogueId);

    return;
}

/**
 * Подсчитывает число подразделов сайта с заданным $CatalogueID
 * 
 * @global type $db
 * @global type $perm
 * @param type $CatalogueID
 * @param type $available
 * @return type
 */
function HighLevelChildrenNumber($CatalogueID, $available = '') {
    global $db, $perm;

    // часть sql-запроса, ограничивающая выборку только объектами, которые пользователь может видеть
    $security_limit = '';

    // id разделов, которые пользователь может администрировать
    $sub_admin = $perm->listItems('subdivision');

    // id шаблонов в разделе, которые пользователь может администрировать
    $cc_admin = $perm->listItems('subclass');

    // id сайтов, которые пользователь видит (на основе $site_admin, $sub_admin, $cc_admin)
    $allowed_sites = array();

    // id разделов, которые администрирует пользователь, на основе $sub_admin + $cc_admin
    $sub_and_cc_admin = $sub_admin;
    if (is_array($cc_admin) && !empty($cc_admin)) {
        $in_str = join(', ', $cc_admin);
        if ($in_str) {
            $res = $db->get_results("SELECT `Subdivision_ID`
        FROM `Sub_Class`
        WHERE `Sub_Class_ID` IN ($in_str)", ARRAY_A);
            if (!empty($res)) {
                foreach ($res as $row) {
                    $sub_and_cc_admin[] = $row['Subdivision_ID'];
                }
            }
        }
    }

    if (is_array($sub_and_cc_admin) && !empty($sub_and_cc_admin)) {
        // получить родительские разделы для разделов, которые пользователь может
        // модерировать или администрировать
        $res = $db->get_results("SELECT parent.`Subdivision_ID`
      FROM `Subdivision` as parent, `Subdivision` as allowed
      WHERE allowed.`Subdivision_ID` IN (" . join(",", array_unique($sub_and_cc_admin)) . ")
      AND allowed.`Hidden_URL` LIKE CONCAT(parent.`Hidden_URL`, '%')", ARRAY_A);

        // разделы, которые пользователь может видеть
        $allowed_subs = array();
        if (!empty($res)) {
            foreach ($res as $row) {
                // flatten array
                $allowed_subs[] = $row['Subdivision_ID'];
            }
        }

        // id разделов, которые являются дочерними для тех разделов, на которые
        // явно указаны права на администрирование -- эти права наследуются (as of 3.0)
        $sub_child_administrator = array();
        // права наследуются для дочерних узлов
        if (is_array($sub_admin) && !empty($sub_admin)) {
            $res = $db->get_results("SELECT child.`Subdivision_ID`, allowed.`Subdivision_ID` as Allowed_Subdivision_ID
          FROM `Subdivision` as child, `Subdivision` as allowed
          WHERE allowed.`Subdivision_ID` IN (" . join(",", array_unique($sub_admin)) . ")
          AND child.`Hidden_URL` LIKE CONCAT(allowed.`Hidden_URL`, '_%')", ARRAY_A);
        }

        if (!empty($res)) {
            foreach ($res as $row) {
                $allowed_subs[] = $row['Subdivision_ID'];
                $sub_child_administrator[$row['Subdivision_ID']] = $row['Allowed_Subdivision_ID'];
            }
        }

        if ($allowed_subs) {
            $qry_where = " AND a.Subdivision_ID IN (" . join(',', $allowed_subs) . ") ";
        }
    }

    return $db->get_var("SELECT COUNT(`Subdivision_ID`) FROM `Subdivision` AS a WHERE `Parent_Sub_ID` = 0
    AND `Catalogue_ID` = '" . intval($CatalogueID) . "'" . ($available && $qry_where ? $qry_where : ''));
}

/**
 * Проверяет домен $Domain на дубль среди сайтов системы, отличных от $CatalogueID
 * 
 * @global type $db
 * @param type $Domain
 * @param type $CatalogueID
 * @return type
 */
function IsAllowedDomain($Domain, $CatalogueID) {
    global $db;

    return !$db->get_var("SELECT `Catalogue_ID` FROM `Catalogue` WHERE `Domain` = '" . $db->escape($Domain) . "'
    AND `Catalogue_ID` <> '" . intval($CatalogueID) . "'");
}
