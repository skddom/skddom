<?php

/* $Id: get_forums.php 5125 2011-08-04 08:00:08Z denis $ */

$_POST["NC_HTTP_REQUEST"] = true;

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . ( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER . "vars.inc.php");
require_once ($ADMIN_FOLDER . "function.inc.php");

if (!$perm->isAccess(NC_PERM_MODULE, 0, 0, 0, 1)) {
    trigger_error("Permission denied", E_USER_ERROR);
}

if (!isset($parent_id)) {
    trigger_error("Wrong params", E_USER_ERROR);
}

$parent_id+= 0;

$groups = $db->get_results("SELECT `ID`, `Name`
  FROM `Forum_Groups`
  WHERE `Subdivision_ID` = '" . $parent_id . "'
  ORDER BY `Priority` DESC, `ID`", ARRAY_A);

$forums = $db->get_results("SELECT fsub.`Subdivision_ID`, fsub.`Group_ID`, sub.`Subdivision_Name`, sub.`Checked`
  FROM `Forum_Subdivisions` AS fsub
  LEFT JOIN `Subdivision` AS sub ON fsub.`Subdivision_ID` = sub.`Subdivision_ID`
  WHERE sub.`Parent_Sub_ID` = '" . $parent_id . "' AND fsub.`Type` = 'forum'
  ORDER BY fsub.`Group_ID`, sub.`Priority`", ARRAY_A);

if (!empty($forums)) {

    $json_groups = array();
    $json_forums = array();

    $group_name = "";
    $groups_count = count($groups);
    $result = "<div style='margin:0 0 7px 25px'>";

    foreach ($forums as $value) {
        if ($value['Group_ID'])
            continue;

        $json_forums[] = "'treeForum" . $value['Group_ID'] . "-" . $value['Subdivision_ID'] . "'";

        $result.= "<div style='margin:0 0 7px'>" .
                "<img dragLabel='" . $value['Subdivision_ID'] . ". " . $value['Subdivision_Name'] . "' id='treeForum" . $value['Group_ID'] . "-" . $value['Subdivision_ID'] . "' src='" . $ADMIN_TEMPLATE . "img/i_folder" . (!$value['Checked'] ? "_disabled" : "") . ".png' style='margin-bottom:-3px; width:18px; height:15; border:none;'>" .
                "<span style='margin-left:5px; color:gray; font-size:90%'>" . $value['Subdivision_ID'] . ".</span> " .
                "<a href='#' onclick='nc_forum2Obj.loadInfo(" . $value['Subdivision_ID'] . "); return false;' style='text-decoration:none; color:#505050'>" .
                $value['Subdivision_Name'] .
                "</a>" .
                "<div id='info" . $value['Subdivision_ID'] . "' style='display:none; margin:5px 0 0'>" .
                "<textarea id='infoDescription" . $value['Subdivision_ID'] . "' onchange='nc_forum2Obj.setChangeInfo(" . $value['Subdivision_ID'] . ", 1)' onblur='nc_forum2Obj.saveInfo(" . $value['Subdivision_ID'] . ")' style='width:50%' row='3'></textarea>" .
                "</div>" .
                "</div>";
    }

    for ($i = 0; $i < $groups_count; $i++) {
        $json_groups[] = "treeGroup-" . $groups[$i]['ID'];

        if ($groups[$i]['Name'] != $group_name) {
            $group_name = $groups[$i]['Name'];
            $result.= "<div style='margin-bottom:7px; color:grey'><b><span id='treeGroupID-" . $groups[$i]['ID'] . "'>" . $groups[$i]['ID'] . ".</span> <span id='treeGroup-" . $groups[$i]['ID'] . "'>" . $group_name . "</span></b></div>";
            $result.= "<div style='margin:0 0 5px 20px'>";
        }

        foreach ($forums as $value) {
            if ($value['Group_ID'] != $groups[$i]['ID'])
                continue;

            $json_forums[] = "treeForum" . $value['Group_ID'] . "-" . $value['Subdivision_ID'] . "";

            $result.= "<div style='margin:0 0 7px'>" .
                    "<img dragLabel='" . $value['Subdivision_ID'] . ". " . $value['Subdivision_Name'] . "' id='treeForum" . $value['Group_ID'] . "-" . $value['Subdivision_ID'] . "' src='" . $ADMIN_TEMPLATE . "img/i_folder" . (!$value['Checked'] ? "_disabled" : "") . ".png' style='margin-bottom:-3px; width:18px; height:15; border:none;'>" .
                    "<span style='margin-left:5px; color:gray; font-size:90%'>" . $value['Subdivision_ID'] . ".</span> " .
                    "<a href='#' onclick='nc_forum2Obj.loadInfo(" . $value['Subdivision_ID'] . "); return false;' style='text-decoration:none; color:#505050'>" .
                    $value['Subdivision_Name'] .
                    "</a>" .
                    "<div id='info" . $value['Subdivision_ID'] . "' style='display:none; margin:5px 0 0'>" .
                    "<textarea id='infoDescription" . $value['Subdivision_ID'] . "' onchange='nc_forum2Obj.setChangeInfo(" . $value['Subdivision_ID'] . ", 1)' onblur='nc_forum2Obj.saveInfo(" . $value['Subdivision_ID'] . ")' style='width:50%' row='3'></textarea>" .
                    "</div>" .
                    "</div>";
        }

        if ($groups[$i + 1]['Name'] != $group_name || ($i + 1) == $groups_count) {
            $result.= "</div>";
        }
    }

    $result.= "</div>";

    if (!$json_forums)
        $json_forums = array();
    if (!$json_groups)
        $json_groups = array();
    if (!$nc_core->NC_UNICODE) {
        $result = $nc_core->utf8->win2utf($result);
    }
    $res = array('html' => $result, 'forums' => $json_forums, 'groups' => $json_groups);
} else {
    $res = array('html' => "<div style='margin:0 0 7px 25px'>" . NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_NO_FORUMS_FOUND . "</div>", 'forums' => array(), 'groups' => array());
}

echo json_encode($res);
?>