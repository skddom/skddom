<?php
/* $Id: nc_forum2_admin.class.php 4469 2011-04-12 07:55:22Z denis $ */

class nc_forum2_admin {

    protected $db, $UI_CONFIG;
    protected $MODULE_FOLDER, $MODULE_PATH, $ADMIN_TEMPLATE;

    public function __construct() {
        // system superior object
        $nc_core = nc_Core::get_object();

        // global variables
        global $UI_CONFIG;

        // global variables to internal
        $this->db = &$nc_core->db;
        $this->UI_CONFIG = $UI_CONFIG;
        $this->ADMIN_TEMPLATE = $nc_core->ADMIN_TEMPLATE;
        $this->MODULE_FOLDER = $nc_core->MODULE_FOLDER;
        $this->MODULE_PATH = str_replace($nc_core->DOCUMENT_ROOT, "", $nc_core->MODULE_FOLDER)."forum2/";
        // superglobal variable
        $this->POST = $_POST;

        // this function must be called only from cache/admin.php file
        $debug_backtrace = debug_backtrace();
        // get file from calling this method
        $deb_value = $debug_backtrace[0];
        // validate file permission
        /* if (
          !( str_replace( array("/", "\\"), "/", $deb_value['file']) == str_replace( array("/", "\\"), "/", $this->MODULE_FOLDER."forum2/admin.php") )
          ) {
          throw new Exception (NETCAT_MODULE_FORUM2_CLASS_UNRECOGNIZED_OBJECT_CALLING);
          } */

        return;
    }

    public function settings() {
        // system superior object
        $nc_core = nc_Core::get_object();

        $FORUM2_VARS = &$nc_core->modules->get_vars("forum2");

        $SQL = "
            SELECT fsub.`Subdivision_ID`, 
                   sub.`Subdivision_Name`, 
                   sub.`Checked`
                FROM `Forum_Subdivisions` AS fsub
                  LEFT JOIN `Subdivision` AS sub ON fsub.`Subdivision_ID` = sub.`Subdivision_ID`
                WHERE `Type` = 'parent'";
        $forum_subs = $this->db->get_results($SQL, ARRAY_A);

        if (!empty($forum_subs)) {
?>
            <script type='text/javascript' src='<?=$this->MODULE_PATH
?>nc_forum2_admin.class.js'></script>
            <script type='text/javascript'>
                var nc_forum2Obj = new nc_Forum2({'MODULE_PATH':'<?=$this->MODULE_PATH
?>', 'ADMIN_TEMPLATE':'<?=$this->ADMIN_TEMPLATE
?>'});
            </script>
<?php
            echo "<fieldset>\n".
            "<legend>\n".
            "".NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_FORUM_LIST."\n".
            "</legend>\n".
            "<div style='margin:10px 0; _padding:0;'>\n";

            foreach ($forum_subs AS $value) {
                echo "<div style='margin-bottom:7px'>".
                "<img src='".$this->ADMIN_TEMPLATE."img/i_plus.png' id='plus".$value['Subdivision_ID']."' onclick='nc_forum2Obj.loadForums(".$value['Subdivision_ID'].")' style='margin:0 2px -4px 0; width:16px; height:16px; border:none;'>".
                "<img src='".$this->ADMIN_TEMPLATE."img/i_folder".(!$value['Checked'] ? "_disabled" : "").".png' style='margin-bottom:-3px; width:18px; height:15px; border:none;'>".
                "<span style='margin-left:5px; color:gray; font-size:90%'>".$value['Subdivision_ID'].".</span> ".
                "<a href='#' onclick='nc_forum2Obj.loadInfo(".$value['Subdivision_ID']."); return false;' style='text-decoration:none; color:#505050'>".
                $value['Subdivision_Name'].
                "</a>".
                "<div id='info".$value['Subdivision_ID']."' style='display:none; margin:5px 0 0'>".
                "<textarea id='infoDescription".$value['Subdivision_ID']."' onchange='nc_forum2Obj.setChangeInfo(".$value['Subdivision_ID'].", 1)' onblur='nc_forum2Obj.saveInfo(".$value['Subdivision_ID'].")' style='width:50%' row='3'></textarea>".
                "</div>".
                "</div>".
                "<div id='parent".$value['Subdivision_ID']."'></div>";
            }

            echo "</div>\n".
            "</fieldset>\n";
        } else {
            nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_FORUMS_DATA, "info");
        }

        $groups = $this->db->get_results("SELECT fg.*, sub.`Subdivision_Name` FROM `Forum_Groups` AS fg
      LEFT JOIN `Subdivision` AS sub ON fg.`Subdivision_ID` = sub.`Subdivision_ID`
      ORDER BY fg.`Subdivision_ID`, fg.`ID`", ARRAY_A);

        echo "<form method='post' id='GroupsForm' action='admin.php' style='padding:0; margin:0;'>\n".
        "<fieldset>\n".
        "<legend>\n".
        "".NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUPS_SETTINGS."\n".
        "</legend>\n";

        if (!empty($groups)) {
            echo "<div style='margin:10px 0; _padding:0;'>\n".
            "<select name='Group_ID' id='Group_ID' onchange='nc_forum2Obj.loadGroup(this.value);' style='width:50%'>\n".
            "<option value='0'>".NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_NEW."</option>\n";

            $group_count = count($groups);
            //$group_name = $groups[0]['Subdivision_Name'];
            $group_name = "";
            for ($i = 0; $i < $group_count; $i++) {
                if ($groups[$i]['Subdivision_Name'] != $group_name) {
                    $group_name = $groups[$i]['Subdivision_Name'];
                    echo "<optgroup label='".$group_name."'>";
                }
                echo "<option value='".$groups[$i]['ID']."'".($i == 0 ? " selected" : "").">".$groups[$i]['ID'].": ".$groups[$i]['Name']."</option>\n";
                if ($groups[$i + 1]['Subdivision_Name'] != $group_name || ($i + 1) == $group_count) {
                    echo "</optgroup>";
                }
            }
            echo "</select>\n".
            "</div>\n";
        }

        echo "<div id='GroupForumBlock' style='margin:10px 0; _padding:0;".(!empty($groups) ? " display:none;" : "")."'>\n".
        NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_FORUM.":<br/>".
        "<select name='Group_Forum' id='Group_Forum' style='width:50%'>";
        $forum_subs_count = count($forum_subs);
        for ($i = 0; $i < $forum_subs_count; $i++) {
            echo "<option value='".$forum_subs[$i]['Subdivision_ID']."'".($i == 0 ? " selected" : "").">".$forum_subs[$i]['Subdivision_ID'].". ".$forum_subs[$i]['Subdivision_Name']."</option>\n";
        }
        echo "</select>\n".
        "</div>\n";

        echo "<div style='margin:10px 0; _padding:0;'>\n".
        NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_NAME.":<br/>".
        "<input type='text' name='Group_Name' id='Group_Name' style='width:100%' value='".$groups[0]['Name']."'/>".
        "</div>\n";

        echo "<div style='margin:10px 0; _padding:0;'>\n".
        NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_DESCRIPTION.":<br/>".
        "<textarea name='Group_Description' id='Group_Description' style='width:100%; height:5em; line-height:1em'>".htmlentities($groups[0]['Description'], ENT_COMPAT, MAIN_ENCODING)."</textarea>".
        "</div>\n";

        echo "<div style='margin:10px 0; _padding:0;'>\n".
        NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_PRIORITY.":<br/>".
        "<input type='text' name='Group_Priority' id='Group_Priority' style='width:100%' value='".$groups[0]['Priority']."'/>".
        "</div>\n";


        echo "<div id='GroupDeleteBlock' style='margin:10px 0; _padding:0;'>\n".
        "<input type='checkbox' id='Group_Delete' name='Group_Delete' id='Group_Delete' value='1'/>&nbsp;".
        "<label for='Group_Delete'>".NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_GROUP_DELETE."</label><br/>".
        "</div>\n";

        // admin buttons
        $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_MODULE_FORUM2_ADMIN_SETTINGS_SAVE,
                "action" => "mainView.submitIframeForm('GroupsForm')"
        );

        echo "<input type='hidden' name='phase' value='2'>\n".
        "</fieldset>\n".
        "</form>\n";
    }

    public function settingsSave() {
        // system superior object
        $nc_core = nc_Core::get_object();

        $FORUM2_VARS = &$nc_core->modules->get_vars("forum2");

        $Group_ID = $nc_core->input->fetch_post('Group_ID');
        $Group_Forum = $nc_core->input->fetch_post('Group_Forum');
        $Group_Name = $nc_core->input->fetch_post('Group_Name');
        $Group_Description = $nc_core->input->fetch_post('Group_Description');
        $Group_Priority = $nc_core->input->fetch_post('Group_Priority');
        $Group_Delete = $nc_core->input->fetch_post('Group_Delete');

        if ($Group_Delete && $Group_ID) {
            $this->db->query("DELETE FROM `Forum_Groups` WHERE `ID` = '".intval($Group_ID)."'");
            // return changes status
            return $this->db->rows_affected;
        }

        if ($Group_ID == 0 && $Group_Forum && $Group_Name) {
            $this->db->query("INSERT INTO `Forum_Groups`
        (`Subdivision_ID`, `Name`, `Description`, `Priority`)
        VALUES
        ('".intval($Group_Forum)."', '".$this->db->escape($Group_Name)."', '".$this->db->escape($Group_Description)."', '".intval($Group_Priority)."')");
            // return changes status
            return $this->db->insert_id;
        }

        if ($Group_ID && $Group_Name) {
            $this->db->query("UPDATE `Forum_Groups` SET
        `Name` = '".$this->db->escape($Group_Name)."',
        `Description` = '".$this->db->escape($Group_Description)."',
        `Priority` = '".intval($Group_Priority)."'
        WHERE `ID` = '".intval($Group_ID)."'");
            // return changes status
            return $this->db->rows_affected;
        }

        // return changes status
        return false;
    }

    public function converter($phase = 3, $catalogue = 0, $subdivision = 0) {
        // system superior object
        $nc_core = nc_Core::get_object();

        $OLD_FORUM_VARS = &$nc_core->modules->get_vars("forum");

        $catalogue = intval($catalogue);
        $subdivision = intval($subdivision);

        $error = false;

        if (($phase == 4 && !$catalogue))
                $phase--; // || ($phase==3 && !$subdivision)

            echo "<form method='post' action='admin.php' id='ConvertForum2' style='padding:0; margin:0;'>\n".
        "<input type='hidden' name='phase' value='".($phase + 1)."'>\n".
        "<input type='hidden' name='page' value='converter'>\n".
        "<fieldset>\n".
        "<legend>\n".
        "".NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_DIALOG."</legend>\n";

        if ($phase == 3) {
            $catalogues = $this->db->get_results("SELECT `Catalogue_ID`, `Catalogue_Name` FROM `Catalogue`", ARRAY_A);
            if (!empty($catalogues)) {
                echo "<div style='padding:10px 0 5px; width:100%'>".
                "".NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_SELECT_CATALOGUE."<br>".
                "<select name='ConverterCatalogue' style='width:50%'>";
                foreach ($catalogues AS $value) {
                    echo "<option value='".$value['Catalogue_ID']."'>".$value['Catalogue_Name']."</option>";
                }
                echo "</select>";
            } else {
                nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_CATALOGUE_ERROR, "error");
                $error = true;
            }
        }

        if ($phase == 4) {
            $subdivisions = $this->db->get_results("SELECT sub.`Subdivision_ID` AS value,
          CONCAT(sub.`Subdivision_ID`, '. ', sub.`Subdivision_Name`) AS description, sub.`Parent_Sub_ID` AS parent
          FROM `Subdivision` AS sub
          LEFT JOIN `Sub_Class` AS cc ON sub.`Subdivision_ID` = cc.`Subdivision_ID`
          WHERE sub.`Catalogue_ID` = '".$catalogue."'
            AND cc.`Class_ID` = '".intval($OLD_FORUM_VARS['LIST_CATEGORIES_TABLE'])."'
          ORDER BY sub.`Subdivision_ID`", ARRAY_A);
            if (!empty($subdivisions)) {
                echo "<div style='padding:10px 0 5px'>".
                "".NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_SELECT_SUBDIVISION."<br>".
                "<input type='hidden' name='ConverterCatalogue' value='".$catalogue."'>".
                "<select name='ConverterSubdivision' style='width:50%'>";
                echo nc_select_options($subdivisions);
                echo "</select>";

                echo "<div style='padding:10px 0 5px'>".
                "".NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_SUBDIVISION_NEW_KEYWORD."<br>".
                "<input type='text' name='ConverterNewKeyword' style='width:50%' value=''>".
                "</div>";

                echo "<div style='padding:10px 0 5px'>".
                "".NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_SUBDIVISION_NEW_NAME."<br>".
                "<input type='text' name='ConverterNewName' style='width:50%' value=''>".
                "</div>";

                echo "</legend>\n".
                "</fieldset>\n";

                echo "<fieldset>\n".
                "<legend>\n".
                "".NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_SETTINGS."\n";

                echo "<div style='margin:10px 0; _padding:0;'>\n".
                "<input type='checkbox' id='PermissionForum' name='PermissionForum' value='1' checked/>&nbsp;".
                "<label for='PermissionForum'>".NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_SET_FORUM."</label><br/>".
                "</div>\n";

                echo "<div style='margin:10px 0; _padding:0;'>\n".
                "<input type='checkbox' id='PermissionGroups' name='PermissionGroups' value='1' checked onclick='if (this.checked) getElementById(\"PermissionSet\").style.display = \"block\"; else getElementById(\"PermissionSet\").style.display = \"none\";'/>&nbsp;".
                "<label for='PermissionGroups'>".NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_SET_GROUPS."</label><br/>".
                "</div>\n";
?>
                <div style='margin:10px 0; _padding:0;' id='PermissionSet'>
                    <table cellpadding='5' cellspacing='1' style=''>
                        <tr>
                            <td style=''><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_GROUP_USERS ?></td>
            <td style=''><b><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_GROUP_MODERATORS
?></b></td>
        </tr>
        <tr>
            <td style=''>
                <input id='pu1' type='checkbox' name='uRead' value='1' checked /> <label for='pu1'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_VIEW
?></label><br/>
                <input id='pu2' type='checkbox' name='uComment' value='1'> <label for='pu2'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_COMMENT
?></label><br/>
                <input id='pu3' type='checkbox' name='uAdd' value='1' checked /> <label for='pu3'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_ADD
?></label><br/>
                <input id='pu4' type='checkbox' name='uEdit' value='1' checked /> <label for='pu4'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_EDIT
?></label><br/>
                <input id='pu5' type='checkbox' name='uCheck' value='1'> <label for='pu5'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_CHECK
?></label><br/>
                <input id='pu6' type='checkbox' name='uDelete' value='1' checked /> <label for='pu6'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_DELETE
?></label><br/>
                <input id='pu7' type='checkbox' name='uSubscribe' value='1' checked /> <label for='pu7'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_SUBSCRIBE
?></label><br/>
                <input id='pu8' type='checkbox' name='uModerate' value='1'> <label for='pu8'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_MODERATE
?></label><br/>
                <input id='pu9' type='checkbox' name='uAdminister' value='1'> <label for='pu9'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_ADMINISTER
?></label>
            </td>
            <td style=''>
                <input id='pm1' type='checkbox' name='mRead' value='1' checked /> <label for='pm1'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_VIEW
?></label><br/>
                <input id='pm2' type='checkbox' name='mComment' value='1'> <label for='pm2'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_COMMENT
?></label><br/>
                <input id='pm3' type='checkbox' name='mAdd' value='1' checked /> <label for='pm3'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_ADD
?></label><br/>
                <input id='pm4' type='checkbox' name='mEdit' value='1' checked /> <label for='pm4'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_EDIT
?></label><br/>
                <input id='pm5' type='checkbox' name='mCheck' value='1' checked /> <label for='pm5'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_CHECK ?></label><br/>
                <input id='pm6' type='checkbox' name='mDelete' value='1' checked /> <label for='pm6'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_DELETE ?></label><br/>
                <input id='pm7' type='checkbox' name='mSubscribe' value='1' checked /> <label for='pm7'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_SUBSCRIBE ?></label><br/>
                <input id='pm8' type='checkbox' name='mModerate' value='1' checked /> <label for='pm8'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_MODERATE ?></label><br/>
                <input id='pm9' type='checkbox' name='mAdminister' value='1'> <label for='pm9'><?=NETCAT_MODULE_FORUM2_ADMIN_CONVERT_PERMISSION_ADMINISTER ?></label>
            </td>
        </tr>
    </table>
</div>
<?php
} else {
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_SUBDIVISION, "info");
$error = true;
}
}

echo "</legend>\n".
"</fieldset>\n";

// admin buttons
if (!$error) {
$this->UI_CONFIG->actionButtons[] = array(
    "id" => "submit",
    "caption" => constant("NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_SAVE_BUTTON_".$phase),
    "action" => "mainView.submitIframeForm('ConvertForum2')"
);
} else {
$this->UI_CONFIG->actionButtons[] = array(
    "id" => "submit",
    "caption" => NETCAT_MODULE_FORUM2_ADMIN_CONVERTER_RETURN_BUTTON,
    "location" => "module.forum2.converter(3)"
);
}

echo "</form><br/>\n";

return;
}

public function converterSave() {
// system superior object
$nc_core = nc_Core::get_object();

$FORUM2_VARS = &$nc_core->modules->get_vars("forum2");

// get objects
$parent_obj = nc_forum2_parent::get_object();
$forum_obj = nc_forum2_forum::get_object();
$topic_obj = nc_forum2_topic::get_object();
$reply_obj = nc_forum2_reply::get_object();

$catalogue = intval($nc_core->input->fetch_post('ConverterCatalogue'));
$subdivision = intval($nc_core->input->fetch_post('ConverterSubdivision'));
$new_keyword = $nc_core->input->fetch_post('ConverterNewKeyword');
$new_name = $nc_core->input->fetch_post('ConverterNewName');

$PermissionForum = intval($nc_core->input->fetch_post('PermissionForum'));
$PermissionGroups = intval($nc_core->input->fetch_post('PermissionGroups'));

if ($PermissionGroups) {
// users permissions
$uRead = intval($nc_core->input->fetch_post('uRead'));
$uComment = intval($nc_core->input->fetch_post('uComment'));
$uAdd = intval($nc_core->input->fetch_post('uAdd'));
$uEdit = intval($nc_core->input->fetch_post('uEdit'));
$uCheck = intval($nc_core->input->fetch_post('uCheck'));
$uDelete = intval($nc_core->input->fetch_post('uDelete'));
$uSubscribe = intval($nc_core->input->fetch_post('uSubscribe'));
$uModerate = intval($nc_core->input->fetch_post('uModerate'));
$uAdminister = intval($nc_core->input->fetch_post('uAdminister'));
// moderators permissions
$mRead = intval($nc_core->input->fetch_post('mRead'));
$mComment = intval($nc_core->input->fetch_post('mComment'));
$mAdd = intval($nc_core->input->fetch_post('mAdd'));
$mEdit = intval($nc_core->input->fetch_post('mEdit'));
$mCheck = intval($nc_core->input->fetch_post('mCheck'));
$mDelete = intval($nc_core->input->fetch_post('mDelete'));
$mSubscribe = intval($nc_core->input->fetch_post('mSubscribe'));
$mModerate = intval($nc_core->input->fetch_post('mModerate'));
$mAdminister = intval($nc_core->input->fetch_post('mAdminister'));
}

if (!$catalogue && !$subdivision) {
// catalogue and subdivision not defined
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_DATA_ERROR, "error");
// error
return false;
}

// get old forum parent by sub
$parent = $this->db->get_row("SELECT ff.*, sub.`EnglishName`, sub.`Subdivision_Name`
      FROM `Forum_forums` AS ff
      LEFT JOIN `Subdivision` AS sub ON ff.`Subdivision_ID` = sub.`Subdivision_ID`
      WHERE ff.`Subdivision_ID` = '".$subdivision."'", ARRAY_A);

// no data
if (empty($parent)) {
// data empty
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_PARENT_DATA, "error");
// error
return false;
}

// new keyword and name
$parent_keyword = ( $new_keyword ? $new_keyword : $parent['EnglishName'].time() );
$parent_name = ($new_name ? $new_name : $parent['Subdivision_Name']);

// create new forum parent
$parent_id = $parent_obj->create($catalogue, 0, $parent_keyword, $parent_name, $parent['Forum_enabled']);

// no data
if (!$parent_id) {
// data empty
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_CAN_NOT_CREATE_PARENT, "error");
// error
return false;
}

// convert user groups permissions
if ($PermissionGroups) {
// get old user groups
$user_perm_groups = $this->db->get_results("SELECT * FROM `Forum_permgroup`", ARRAY_A);

if (!empty($user_perm_groups)) {
// user groups temp array
$_user_perm_group_ids = array();
// walk
foreach ($user_perm_groups as $row) {
    // put ids in array
    $_user_perm_group_ids[] = $row['Group_ID'];
    // append
    $this->db->query("INSERT INTO `PermissionGroup`
            (`PermissionGroup_Name`)
            VALUES
            ('".$this->db->escape($row['Group_name'])."')");
    // old/new user groups relation array
    $_user_perm_group_rel[$row['Group_ID']] = $this->db->insert_id;
}
}

// get old user groups
$user_groups = $this->db->get_results("SELECT * FROM `Forum_usergroup`", ARRAY_A);

if (!empty($user_groups)) {
// walk
foreach ($user_groups as $row) {
    // append
    $this->db->query("INSERT INTO `User_Group`
            (`User_ID`, `PermissionGroup_ID`)
            VALUES
            ('".$row['User_ID']."', '".$_user_perm_group_rel[$row['Group_ID']]."')");
}
}
}

// update forum parent
$this->db->query("UPDATE `Forum_Subdivisions`
      SET `Description` = '".$this->db->escape($parent['Forum_description'])."'
      WHERE `Subdivision_ID` = '".$parent_id."'");

// get old categories/groups
$groups = $this->db->get_results("SELECT * FROM `Forum_categories`
      WHERE `Forum_ID` = '".$parent['Forum_ID']."'", ARRAY_A);

if (empty($groups)) {
// data empty
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_GROUPS_DATA, "error");
// error
return false;
}

// groups temp array
$_group_ids = array();
// groups array
foreach ($groups as $row) {
// put ids in array
$_group_ids[] = $row['Category_ID'];
// append
$this->db->query("INSERT INTO `Forum_Groups`
        (`Subdivision_ID`, `Name`, `Description`, `Priority`)
        VALUES
        ('".$parent_id."', '".$row['Category_name']."', '".$row['Category_description']."', '".$row['Category_sort']."')");
// old/new groups relation array
$_group_rel[$row['Category_ID']] = $this->db->insert_id;
}

// get old forums data
$forums = $this->db->get_results("SELECT * FROM `Forum_subdiv`
      WHERE `Category_ID` IN (".join(",", $_group_ids).")", ARRAY_A);

if (empty($forums)) {
// data empty
nc_print_status(NETCAT_MODULE_FORUM2_ADMIN_CONVERT_NO_FORUMS_DATA, "info");
}
else
foreach ($forums as $row) {
// forum keyword
$keyword = strtolower(nc_preg_replace("/[^a-z0-9]/is", "-", nc_transliterate($row['Subdiv_name'])));

// check subdivision EnglishName
if ($this->db->get_var("SELECT `Subdivision_ID` FROM `Subdivision` WHERE `EnglishName` = '".$this->db->escape($keyword)."'")) {
    $keyword.= time();
}

// create forum
$forum_id = $forum_obj->create($parent_id, $keyword, $row['Subdiv_name'], $row['Subdiv_enabled']);

// update forum group
$this->db->query("UPDATE `Forum_Subdivisions`
        SET `Group_ID` = '".$_group_rel[$row['Category_ID']]."',
        `Description` = '".$this->db->escape($row['Subdiv_description'])."'
        WHERE `Subdivision_ID` = '".$forum_id."'");

/**
 * perm_view      - Разрешает отображение раздела в общем списке разделов
 * perm_read      - Разрешает пользователю просматривать список топиков в разделе и читать их
 * perm_post      - Разрешает пользователю создавать свои топики в разделе
 * perm_reply     - Разрешает пользователю создавать ответы на топики
 * perm_edit      - Разрешает редактировать сообщения
 * perm_delete    - Разрешает удалять сообщения
 * perm_sticky    - Прикрепленные сообщения
 * perm_announce  - Объявления
 * perm_vote  	
 * perm_pcreate
 */
// convert forums (Subdivision) permissions
if ($PermissionForum) {
    $perm_remap_arr = array(
            'all' => 1,
            'reg' => 2,
            'private' => 3,
            'mod' => 3,
            'admin' => 3
    );

    $vis_perm_remap_arr = array(
            'all' => 'all',
            'reg' => 'auth',
            'private' => 'auth',
            'mod' => 'moders',
            'admin' => 'admins'
    );

    // topic component default vis
    $cc_settings_str = "";

    // topic component settings template
    $settings = $nc_core->component->get_by_id($topic_obj->get_class_id(), "CustomSettingsTemplate");

    // convert
    if ($settings) {
        // vis values
        $vis_values = array(
                "advertisement" => $vis_perm_remap_arr[$row['perm_announce']],
                "important" => $vis_perm_remap_arr[$row['perm_sticky']]
        );
        // component settings object
        $cc_settings_obj = new nc_a2f($settings, $vis_values, "CustomSettings");
        // component settings values string
        $cc_settings_str = $cc_settings_obj->get_values_as_string();
    }

    // execute core action
    $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_UPDATED, $catalogue, $forum_id);

    // update forum rules
    $this->db->query("UPDATE `Subdivision` SET
          `Read_Access_ID` = '".$perm_remap_arr[$row['perm_read']]."',
          `Write_Access_ID` = '".$perm_remap_arr[$row['perm_post']]."',
          `Edit_Access_ID` = '".$perm_remap_arr[$row['perm_edit']]."',
          `Delete_Access_ID` = '".$perm_remap_arr[$row['perm_delete']]."'
          WHERE `Subdivision_ID` = '".$forum_id."'");
    // execute core action
    $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_UPDATED, $catalogue, $forum_id);

    // execute core action
    $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_UPDATED, $catalogue, $forum_id, $topic_obj->get_subclass_id($forum_id));

    // topic vis
    $this->db->query("UPDATE `Sub_Class` SET
          `CustomSettings` = '".$this->db->prepare($cc_settings_str)."'
          WHERE `Sub_Class_ID` = '".$topic_obj->get_subclass_id($forum_id)."'");
    // execute core action
    $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_UPDATED, $catalogue, $forum_id, $topic_obj->get_subclass_id($forum_id));

    // execute core action
    $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_UPDATED, $catalogue, $forum_id, $reply_obj->get_subclass_id($forum_id));

    // reply create permissions
    $this->db->query("UPDATE `Sub_Class` SET
          `Write_Access_ID` = '".$perm_remap_arr[$row['perm_reply']]."'
          WHERE `Sub_Class_ID` = '".$reply_obj->get_subclass_id($forum_id)."'");
    // execute core action
    $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_UPDATED, $catalogue, $forum_id, $reply_obj->get_subclass_id($forum_id));
}

// convert user groups permissions
if ($PermissionGroups) {
    // users permission set
    $UserPermSet = intval(
                    $uRead * MASK_READ +
                    $uAdd * MASK_ADD +
                    $uEdit * MASK_EDIT +
                    $uDelete * MASK_DELETE +
                    $uCheck * MASK_CHECKED +
                    $uComment * MASK_COMMENT +
                    $uSubscribe * MASK_SUBSCRIBE +
                    $uModerate * MASK_MODERATE +
                    $uAdminister * MASK_ADMIN
    );

    // moderators permission set
    $ModerPermSet = intval(
                    $mRead * MASK_READ +
                    $mAdd * MASK_ADD +
                    $mEdit * MASK_EDIT +
                    $mDelete * MASK_DELETE +
                    $mCheck * MASK_CHECKED +
                    $mComment * MASK_COMMENT +
                    $mSubscribe * MASK_SUBSCRIBE +
                    $mModerate * MASK_MODERATE +
                    $mAdminister * MASK_ADMIN
    );

    // moderators group
    if ($_user_perm_group_rel[$row['Group_moderators']]) {
        // add forum users permission
        $this->db->query("INSERT INTO `Permission`
            (`User_ID`, `AdminType`, `Catalogue_ID`, `PermissionSet`, `PermissionGroup_ID`)
            VALUES
            (0, 3, '".$forum_id."', '".$ModerPermSet."', '".$_user_perm_group_rel[$row['Group_moderators']]."')");
    }

    // users group
    if ($_user_perm_group_rel[$row['Group_users']] && $row['Group_users'] != $row['Group_moderators']) {
        // add forum users permission
        $this->db->query("INSERT INTO `Permission`
            (`User_ID`, `AdminType`, `Catalogue_ID`, `PermissionSet`, `PermissionGroup_ID`)
            VALUES
            (0, 3, '".$forum_id."', '".$UserPermSet."', '".$_user_perm_group_rel[$row['Group_users']]."')");
    }
}

// get topics
$topics = $this->db->get_results("SELECT * FROM `Forum_topics".$row['Subdiv_ID']."`", ARRAY_A);
// determine topic cc
$topic_cc = $topic_obj->get_subclass_id($forum_id);

// insert topics
if (!empty($topics)) {
    foreach ($topics as $v) {
        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_OBJECT_CREATED, $catalogue, $forum_id, $topic_cc, $FORUM2_VARS['TOPIC_CLASS_ID'], 0);

        // put message
        $this->db->query("INSERT INTO `Message".$FORUM2_VARS['TOPIC_CLASS_ID']."`
            (`User_ID`, `Subdivision_ID`, `Sub_Class_ID`, `Priority`, `Checked`, `Parent_Message_ID`, `Created`, `LastUpdated`, `Keyword`, `Subject`, `Message`, `Type`, `Closed`, `Views`)
            VALUES
            ('".$v['Topic_creator_id']."', '".$forum_id."', '".$topic_cc."', 0, 1, 0, '".$v['Topic_regdate']."', '', '', '".$this->db->escape($v['Topic_subject'])."', '".$this->db->escape($v['Topic_message'])."', '".$v['Topic_type']."', '".$v['Topic_closed']."', '".$v['Topic_views']."')");

        // old/new relation array
        $_topic_rel[$v['Topic_ID']] = $this->db->insert_id;

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_OBJECT_CREATED, $catalogue, $forum_id, $topic_cc, $FORUM2_VARS['TOPIC_CLASS_ID'], $_topic_rel[$v['Topic_ID']]);
    }
}

// get topics
$replies = $this->db->get_results("SELECT * FROM `Forum_replies".$row['Subdiv_ID']."`", ARRAY_A);
// determine reply cc
$reply_cc = $reply_obj->get_subclass_id($forum_id);

// insert replies
if (!empty($replies)) {
    foreach ($replies as $v) {
        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_OBJECT_CREATED, $catalogue, $forum_id, $reply_cc, $FORUM2_VARS['REPLY_CLASS_ID'], 0);

        // put message
        $this->db->query("INSERT INTO `Message".$FORUM2_VARS['REPLY_CLASS_ID']."`
            (`User_ID`, `Subdivision_ID`, `Sub_Class_ID`, `Priority`, `Checked`, `Parent_Message_ID`, `Created`, `LastUpdated`, `Keyword`, `Topic_ID`, `Message`, `Subject`)
            VALUES
            ('".$v['Topic_creator_id']."', '".$forum_id."', '".$reply_cc."', 0, 1, 0, '".$v['Topic_regdate']."', '', '', '".$_topic_rel[$v['Parent_topic_ID']]."', '".$this->db->escape($v['Topic_message'])."', '".$this->db->escape($v['Topic_subject'])."')");

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_OBJECT_CREATED, $catalogue, $forum_id, $reply_cc, $FORUM2_VARS['REPLY_CLASS_ID'], $this->db->insert_id);
    }
}
}

return true;
}

}
?>