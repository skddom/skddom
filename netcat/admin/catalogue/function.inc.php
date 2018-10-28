<?php

/* $Id: function.inc.php 8323 2012-11-01 14:14:25Z vadim $ */
if (!class_exists("nc_System"))
    die("Unable to load file.");
$systemTableName = "Catalogue";
$systemTableID = GetSystemTableID($systemTableName);

##############################################
# Вывод списка сайтов
##############################################

/**
 * Show all catalogue
 *
 */
function ShowCatalogueList() {
    global $db;
    global $EDIT_DOMAIN, $HTTP_ROOT_PATH, $DOMAIN_NAME, $ADMIN_PATH, $ADMIN_TEMPLATE, $SUB_FOLDER;
    $nc_core = nc_Core::get_object();
    $all_sites = $nc_core->catalogue->get_all();
    if (!empty($all_sites)) {
        echo "
		<form method='post' action='index.php'>

		<table border='0' cellpadding='0' cellspacing='0' width='99%' class='border-bottom'>
		<tr>
		<td>ID</td>
		<td width='60%'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SITE . "</td>
		<td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SUBSECTIONS . "</td>
		<td>
		  <div class='icons icon_prior' title='".CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_PRIORITY."'></div></td>
		<td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_GOTO . "</td>
		<td>
		  <div class='icons icon_delete' title='".CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE."'></div></td>
 		</tr>";


        foreach ($all_sites as $site) {
            $scheme = $nc_core->catalogue->get_scheme_by_id($site['Catalogue_ID']);

            print "<tr>";
            print "<td>" . "<font>" . $site['Catalogue_ID'] . "</font></td>";
            print "<td>" . "<a href='{$ADMIN_PATH}subdivision/full.php?CatalogueID={$site['Catalogue_ID']}'>" . (!$site['Checked'] ? "<font color='cccccc'>" : "<font>") . $site['Catalogue_Name'] . "</a></font></td>";
            print "<td>" . "<a href='" . $ADMIN_PATH . "subdivision/?CatalogueID=" . $site['Catalogue_ID'] . "'>" . (!$site['Checked'] ? "<font color=cccccc>" : "") . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_LIST . " (" . HighLevelChildrenNumber($site['Catalogue_ID']) . ")</a></td>\n";

            print "<td>" . nc_admin_input_simple("Priority" . $site['Catalogue_ID'], ($site['Priority'] ? $site['Priority'] : 0), 3, "class='s' maxlength='5'") . "</td>\n";


            print "<td>";

            //setup
            print "<a href='index.php?phase=2&CatalogueID=" . $site['Catalogue_ID'] . "&type=2'><div class='icons icon_settings" . (!$site['Checked'] ? "_disabled" : "") . "' title='".CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_TOOPTIONS."'></div></a>";

            //edit
            print ((!GetSubClassCount($site['Title_Sub_ID'])) ? "<img src=" . $ADMIN_PATH . "images/emp.gif width=18 height=18 style='margin:0px 2px 0px 2px;'>" : "<a target=_blank href='{$scheme}://" . $EDIT_DOMAIN . $SUB_FOLDER . $HTTP_ROOT_PATH . "?catalogue=" . $site['Catalogue_ID'] . "&sub=" . $site['Title_Sub_ID'] . (nc_strlen(session_id()) > 0 ? "&" . session_name() . "=" . session_id() . "" : "") . "'><div class='icons icon_pencil" . (!$site['Checked'] ? "_disabled" : "") . "' title='".CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_EDIT."'></div></a>");

            //browse
            print "<a href='{$scheme}://" . ($site['Domain'] ? (strchr($site['Domain'], ".") ? $site['Domain'] : $site['Domain'] . "." . $DOMAIN_NAME) : $DOMAIN_NAME) . $SUB_FOLDER . (nc_strlen(session_id()) > 0 ? "?" . session_name() . "=" . session_id() . "" : "") . "' target=_blank><div class='icons icon_preview" . (!$site['Checked'] ? "_disabled" : "") . "' title='".CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SHOW."'></div></a>";

            print "</td>";
            print "<td>" . nc_admin_checkbox_simple("Delete" . $site['Catalogue_ID'], $site['Catalogue_ID']) . "</td>\n";
            print "</tr>\n";
        }
        echo "
 		</table>
		<br />
    " . $nc_core->token->get_input() . "
 		<input type=hidden name=phase value='4' />
 		<input class='hidden' type='submit' />
 		</form>
	  ";
    } else {
        echo CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_NONE . "<br /><br />";
    }

    return 0;
}

##############################################
# Форма добавления/изменения сайта
##############################################


function CatalogueForm($CatalogueID, $phase, $action, $type, $bar_action = null) {

    # type = 1 - это insert
    # type = 2 - это update
    global $ROOT_FOLDER, $HTTP_FILES_PATH, $DOMAIN_NAME;
    global $SUB_FOLDER, $HTTP_ROOT_PATH, $ADMIN_PATH;
    global $systemTableID, $systemTableName, $admin_mode;
    global $FILES_FOLDER, $INCLUDE_FOLDER, $MODULE_FOLDER, $ADMIN_FOLDER;
    global $UI_CONFIG;

    if (!$bar_action) {
        $bar_action = 'edit';
    }

    $textare_resize_enabled = true;

    // В настройках дизайна не показывать кнопки ресайза для textarea
    if ($bar_action == 'design') {
        $textare_resize_enabled = false;
    }

    $nc_core = nc_Core::get_object();
    $db = $nc_core->db;
    $lm_type = $nc_core->page->get_field_name('last_modified_type');
    $sm_field = $nc_core->page->get_field_name('sitemap_include');
    $sm_change_field = $nc_core->page->get_field_name('sitemap_changefreq');
    $sm_priority_field = $nc_core->page->get_field_name('sitemap_priority');
    $lang_field = $nc_core->page->get_field_name('language');

    $CatalogueID = intval($CatalogueID);

    $params = array('Catalogue_Name', 'Domain', 'Template_ID', 'Read_Access_ID',
            'Write_Access_ID', 'Edit_Access_ID', 'Subscribe_Access_ID',
            'Checked_Access_ID', 'Delete_Access_ID', 'Moderation_ID', 'Checked',
            'Priority', 'Mirrors', 'Robots', 'Cache_Access_ID', 'Cache_Lifetime', 'TitleSubIDName',
            'TitleSubIDKeyword', 'TitleTemplateID', 'E404SubIDName', 'E404SubIDKeyword',
            'E404TemplateID', 'CommentsEditRules', 'CommentAccessID', 'CommentsDeleteRules', 'DisplayType',
            'last_modified_type', 'AllowIndexing', $sm_field, $sm_change_field, $sm_priority_field, 'ncOfflineText');
    foreach ($params as $v)
        $$v = $nc_core->input->fetch_get_post($v);

    $st = new nc_Component(0, 1);
    foreach ($st->get_fields(0, 0) as $v) {
        $v = 'f_' . $v;
        $$v = $nc_core->input->fetch_get_post($v);
    }

    $showFields = false;

    if ($type == 1) {
        $mandatoryFields = array();
        foreach($st->get_fields() as $f) {
            if ($f['not_null']) {
                $mandatoryFields[] = $f['name'];
            }
        }

        $showFields = count($mandatoryFields) > 0;
    }


    if ($nc_core->modules->get_by_keyword('calendar', 0)) {
        echo nc_set_calendar(0);
    }

    echo "<form id='adminForm' class='nc-form' enctype='multipart/form-data' method='post' name='adminForm' action='" . $action . "'>";

    if ($type == 1) {
        if ($Priority == "" && $Checked == "")
            $Checked = 1;
        if ($Priority == "")
            $Priority = $db->get_var("SELECT MAX(`Priority`)+1 FROM `Catalogue`");
        foreach ($params as $v)
            $Array[$v] = $$v;

        $Array['Read_Access_ID'] = 1;
        $Array['Write_Access_ID'] = 3;
        $Array['Edit_Access_ID'] = 3;
        $Array['Checked_Access_ID'] = 3;
        $Array['Delete_Access_ID'] = 3;
    }
    else if ($type == 2) {
        try {
            $Array = $nc_core->catalogue->get_by_id($CatalogueID);
        } catch (Exception $e) {
            nc_print_status(CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOCATALOGUE, 'info');
            EndHtml();
            exit();
        }
    }

    //по умолчанию: публикация объекта сразу после добавления
    if (!$Array["Moderation_ID"])
        $Array["Moderation_ID"] = 1;
    if (!$Array[$lm_type])
        $Array[$lm_type] = 1;
    if (!$Array[$sm_change_field])
        $Array[$sm_change_field] = 'daily';
    if (!$Array[$sm_priority_field])
        $Array[$sm_priority_field] = 0.5;

    $fieldsets = new nc_admin_fieldset_collection();

    $access_actions = array('Read', 'Write', 'Edit', 'Checked', 'Delete');

    foreach ($access_actions as $access_action) {
        $Array["_db_{$access_action}_Access_ID"] = $Array[$access_action . "_Access_ID"];
    }

    $fields_hack = array(
            $nc_core->page->get_field_name('last_modified'),
            $nc_core->page->get_field_name('last_modified_type'),
            'Moderation_ID', 'Cache_Access_ID', 'Cache_Lifetime', 'DisallowIndexing', 'Template_ID');

    if ($nc_core->modules->get_by_keyword('search')) {
        $fields_hack[] = $nc_core->page->get_field_name('sitemap_include');
        $fields_hack[] = $nc_core->page->get_field_name('sitemap_changefreq');
        $fields_hack[] = $nc_core->page->get_field_name('sitemap_priority');
    }

    foreach ($fields_hack as $field_name) {
        $Array['_db_' . $field_name] = $Array[$field_name];
    }

    $Array['_db_inherit_'.$sm_change_field] = $Array['_db_'.$sm_change_field];
    $Array['_db_inherit_Template_ID'] = $Array['Template_ID'];

    $bar_all = $bar_action == 'all';
    $display = array (
            'edit' => $bar_all || $bar_action == 'edit' || $bar_action == 'wizard',
            'design' => $bar_action == 'design' || $bar_action == 'wizard',
            'seo' => $bar_action == 'seo' || $bar_action == 'wizard',
            'system' => $bar_action == 'system' || $bar_action == 'wizard',
            'fields' => $bar_action == 'fields' || $bar_action == 'wizard' || $showFields);

    $p_div_bar_action = '';
    $s_div_bar_action = '';

    if ($bar_action == 'all') {
        $p_div_bar_action = "<div style='display: none;'>";
        $s_div_bar_action = '</div>';
    }

    $fieldsets->set_prefix("
        $p_div_bar_action
        <div id='nc_seo_edit_info'".($bar_action != 'edit' ? " style='display:none;'" : "")." class='nc_admin_settings_info'>
            <div class='nc_admin_settings_info_actions'>
                <div>
                    <span>" . CLASS_TAB_CUSTOM_ADD . ":</span> {$Array['Created']}
                </div>
                " . ($Array['LastUpdated'] ?
                "<div>
                    <span>" . CLASS_TAB_CUSTOM_EDIT . ":</span> {$Array['LastUpdated']}
                </div>" : "") . "
            </div>

            <div class='nc_admin_settings_info_priority'>
                <div>
                    " . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_PRIORITY . ":
                </div>

                <div>
                    " . nc_admin_input_simple('Priority', intval($Array["Priority"]), 3, '', "maxlength='5'") . "
                </div>
            </div>

            <div class='nc_admin_settings_info_checked'>
                <div>
                    " . nc_admin_checkbox_simple('Checked', 1, CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ON, $Array["Checked"] == 1 || !$CatalogueID, 'turnon') . "
                </div>
            </div>

            <div class='nc_admin_settings_info_ssl'>
                <div>
                    " . nc_admin_checkbox_simple('ncHTTPS', 1, CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_HTTPS_ENABLED, $Array["ncHTTPS"] == 1, 'ncHTTPS') . "
                </div>
            </div>
        </div>$s_div_bar_action");

    $fieldsets->set_suffix(
        $nc_core->token->get_input() . "
        <input type='hidden' name='CatalogueID' value='$CatalogueID' />
        <input type='hidden' name='phase' value='$phase' />
        <input type='hidden' name='type' value='$type' />
        <input type='hidden' name='posting' value='1' />
        <input type='hidden' name='action' value='$bar_action' />
        <input type='submit' class='hidden' />
        </form><br />"
        . ($textare_resize_enabled ? nc_admin_js_resize(): '')
    );

    $fieldsets->new_fieldset('main_info', CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MAININFO)->show($display['edit']);
    $fieldsets->new_fieldset('template', '')->add(nc_subdivision_form_design($Array, $CatalogueID, false))->show($display['design']);
    //$fieldsets->new_fieldset('mobile', CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SETTINGS)->show($display['edit'] && $bar_action != 'all');
    $fieldsets->new_fieldset('seo', '')->add(nc_subdivision_form_seo($Array, false))->show($display['seo']);
    $fieldsets->new_fieldset('access', CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ACCESS)->add(nc_subdivision_show_access($Array, false))->show($display['system']);

    if (nc_module_check_by_keyword("cache")) {
        $fieldsets->new_fieldset('cache', CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE)->add(nc_subdivision_show_cache($Array, false))->show($display['system']);
    }

    if (nc_module_check_by_keyword("comments")) {
        $fieldsets->new_fieldset('comments', CONTROL_CONTENT_CATALOGUE_FUNCS_COMMENTS)->add(nc_subdivision_show_comments($Array, false))->show($display['system']);
    }

    $fieldsets->new_fieldset('demo_mode', CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE)->add(
        nc_admin_checkbox(CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE_CHECKBOX, 'DemoMode', $Array["DemoMode"])
    )->show($display['system']);

    ob_start();
    echo nc_admin_input(CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NAME, 'Catalogue_Name', $Array["Catalogue_Name"], 32) . "
   " . nc_admin_input(CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DOMAIN, 'Domain', $Array["Domain"], 32) . "
   $p_div_bar_action
   <br />" . nc_admin_textarea_simple('Mirrors', $Array["Mirrors"],CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MIRRORS, 4, 10, '', '', 'no_cm') . "<br /><br />
   " . nc_admin_textarea_simple('Robots', $Array["Robots"] ,CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS, 4, 10) . "<br /><br />
    " . nc_admin_textarea_simple( 'ncOfflineText', $Array["ncOfflineText"],CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_OFFLINE, 4, 10) . "<br /><br />

    <table border='0' cellpadding=0 cellspacing=0 width=100%>
        <tr>
            <td>
                " . CONTROL_CONTENT_SUBDIVISION_FUNCS_CATALOGUEFORM_LANG . ":<br/>
                " . nc_admin_input_simple('language', $Array[$lang_field], 50) . "<br/>
            </td>
        </tr>";

    if ($type == 2) {
        $subdivisions = $db->get_results("SELECT Subdivision_ID as value,
                                             CONCAT(Subdivision_ID, '. ', Subdivision_Name) as description,
                                             Parent_Sub_ID as parent
                                        FROM Subdivision
                                       WHERE Catalogue_ID='" . $CatalogueID . "'
                                    ORDER BY Subdivision_ID", ARRAY_A);
        echo "
        <tr>
            <td>
                <br />
                <table border='0' cellspacing='0' width='100%' class='border-bottom'>
                    <col width='40%'/><col/>
                    <tr>
                        <td>
                            " . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE . "
                        </td>
                    <td>";
        if (!empty($subdivisions)) {
            echo "<select name='TitleSubID'>";
            echo nc_select_options($subdivisions, $Array["Title_Sub_ID"]);
            echo "</select>";
        } else {
            echo CONTROL_USER_NOONESECSINSITE;
        }

        echo "              </td>
                    </tr>
                    <tr>
                        <td>
                            " . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND . "
                        </td>
                        <td>";
        if (!empty($subdivisions)) {
            echo "<select name='E404SubID'>";
            echo nc_select_options($subdivisions, $Array["E404_Sub_ID"]);
            echo "</select>";
        } else {
            echo CONTROL_USER_NOONESECSINSITE;
        }
	    
	echo "           </td>
	    </tr>
	    <tr>
		<td>
		    " . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DEFAULT_CLASS . "
		</td>
		<td>";
	$sql = "SELECT `Class_ID` as value, " .
		"IF (`IsAuxiliary`=1,
			CONCAT(`Class_ID`, '. ', `Class_Name`, ' " . $db->escape(CONTROL_CLASS_AUXILIARY) . "'),
			CONCAT(`Class_ID`, '. ', `Class_Name`))
		    as description, " .
		"`Class_Group` as optgroup, `IsAuxiliary` as `is_auxiliary` " .
		"FROM `Class` " .
		"WHERE `ClassTemplate` = 0 AND File_Mode = 0 " .
		"ORDER BY `Class_Group`, `Priority`, `Class_ID`";
	$classesV4 = (array)$db->get_results($sql, ARRAY_A);
	$sql = "SELECT `Class_ID` as value, " .
		"IF (`IsAuxiliary`=1,
			CONCAT(`Class_ID`, '. ', `Class_Name`, ' " . $db->escape(CONTROL_CLASS_AUXILIARY) . "'),
			CONCAT(`Class_ID`, '. ', `Class_Name`))
		    as description, " .
		"`Class_Group` as optgroup, `IsAuxiliary` as `is_auxiliary` " .
		"FROM `Class` " .
		"WHERE `ClassTemplate` = 0 AND File_Mode = 1 " .
		"ORDER BY `Class_Group`, `Priority`, `Class_ID`";
	$classesV5 = (array)$db->get_results($sql, ARRAY_A);
	$classInfo .= "<div id='nc_class_select'>";
	if (!empty($classesV4) || !empty($classesV5)) {
	    $classInfo.= "<select id='ClassID' name='Default_Class_ID'>";
	    $classInfo.= "<option value=0> -- </option>";
	    $show_group_label = !empty($classesV4) && !empty($classesV5);
	    if (!empty($classesV5)) {
	        $classInfo.= $show_group_label ? "<option disabled='disabled'>" . CONTROL_CLASS . " v5</option>\n" : '';
	        $classInfo.= nc_select_options($classesV5, $Array["Default_Class_ID"]);
	    }
	    if (!empty($classesV4)) {
	        $classInfo.= $show_group_label ? "<option disabled='disabled'>" . CONTROL_CLASS . " v4</option>\n" : '';
	        $classInfo.= nc_select_options($classesV4, $Array["Default_Class_ID"]);
	    }
	    $classInfo.= "</select>";
	} else {
	    $classInfo.= CONTROL_CLASS_NONE;
	}
	echo $classInfo;    

        echo "</td>
</tr>
</table><br><br></td></tr>";

    }
    echo "</table>$s_div_bar_action";

    $fieldsets->main_info->add(ob_get_clean());

    $templates = $db->get_results("SELECT Template_ID as value,
                                        CONCAT(Template_ID, '. ', Description) as description,
                                        Parent_Template_ID as parent
                                   FROM Template
                               ORDER BY Priority, Template_ID", ARRAY_A);

    ob_start();
    echo "<tr><td>";
    if ($type == 1) {
        echo $p_div_bar_action;
        echo WIZARD_SITE_STEP_TWO_DESCRIPTION . "<br/><br/>";

        if ($Array["Title_Sub_ID"]) {
            $title_sub = $db->get_row("SELECT Subdivision_Name, EnglishName FROM Subdivision WHERE Subdivision_ID = '" . intval($Array["Title_Sub_ID"]) . "'", ARRAY_A);
        }

        if ($Array["E404_Sub_ID"]) {
            $e404_sub = $db->get_row("SELECT Subdivision_Name, EnglishName FROM Subdivision WHERE Subdivision_ID = '" . intval($Array["E404_Sub_ID"]) . "'", ARRAY_A);
        }

        echo "<legend><h3>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE . "</h3></legend>\n";
        echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_NAME . ":<br>\n";
        echo nc_admin_input_simple('TitleSubIDName', ($title_sub['Subdivision_Name'] ? $title_sub['Subdivision_Name'] : CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE)) . "<br><br>\n";
        echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD . ":<br>\n";
        echo nc_admin_input_simple('TitleSubIDKeyword', ($title_sub['EnglishName'] ? $title_sub['EnglishName'] : "index")) . "<br><br>\n";

        if (!empty($templates)) {
            echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE . ":<br>\n";
            echo "<select name='TitleTemplateID'>\n";
            echo "<option value='0'>" . CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_N . "</option>";
            echo nc_select_options($templates, $Array["Title_Sub_ID"]);
            echo "</select><br>\n";
        } else {
            echo CONTROL_TEMPLATE_NONE;
        }

        echo "<legend><h3>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND . "</h3></legend>\n";
        echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_NAME . ":<br>\n";
        echo nc_admin_input_simple('E404SubIDName', ($title_sub['Subdivision_Name'] ? $title_sub['Subdivision_Name'] : CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND)) . "<br><br>\n";
        echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD . ":<br>\n";
        echo nc_admin_input_simple('E404SubIDKeyword', ($title_sub['EnglishName'] ? $title_sub['EnglishName'] : "404")) . "<br><br>\n";

        if (!empty($templates)) {
            echo CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE . ":<br>\n";
            echo "<select name='E404TemplateID'>\n";
            echo "<option value='0'>" . CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_N . "</option>";
            echo nc_select_options($templates, $Array["E404_Sub_ID"]);
            echo "</select><br>\n";
        } else {
            echo CONTROL_TEMPLATE_NONE;
        }
        echo $s_div_bar_action;
    }
    $fieldsets->template->add(ob_get_clean());

    $display_type_fieldset = new nc_admin_fieldset(CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE);
    ob_start();

    echo nc_get_modal_radio('DisplayType', array(
        array(
            'attr' => array('value' => 'traditional'),
            'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_TRADITIONAL),
        array(
            'attr' => array('value' => 'shortpage'),
            'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_SHORTPAGE),
        array(
            'attr' => array('value' => 'longpage_vertical'),
            'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_VERTICAL)), $Array['DisplayType']);


    $display_type_fieldset->add(ob_get_clean());
    $fieldsets->template->add($display_type_fieldset->result());

    ob_start();

    echo nc_get_modal_radio('ncMobile', array(
            array(
                    'attr' => array(
                            'value' => '0'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SIMPLE),
            array(
                    'attr' => array(
                            'value' => '1'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE),
            array(
                    'attr' => array(
                            'value' => '2'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ADAPTIVE)), $Array['ncMobile'] ? 1 : ($Array['ncResponsive'] ? 2 : 0));


    require_once($ADMIN_FOLDER . "related/format.inc.php");
    $field = new field_relation_catalogue();
    echo "

    <span id='nc_mobilesrc'>
        <br />
        <span id='mobility_text'>
        <font>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR . ":</font>
        <span id='cs_ncMobileSrc_caption' style='font-weight:bold;'>" . ($Array['ncMobileSrc'] ? listQuery($field->get_object_query($Array['ncMobileSrc']), $field->get_full_admin_template()) : '[нет]') . "</span>
        </span>
        <input id='cs_ncMobileSrc_value' name='ncMobileSrc' type='hidden' value='" . $Array['ncMobileSrc'] . "'>&nbsp;&nbsp;
        <span class='mobility_notMobile' style='display: none; color: #aaa'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR_NOTICE . "</span>
        <span class='moblilty_links'>
        <a href='#' onclick='window.open(\"" . $ADMIN_PATH . "related/select_catalogue.php?cs_type=rel_catalogue&amp;cs_field_name=ncMobileSrc\", \"nc_popup_ncMobileSrc\", \"width=800,height=500,menubar=no,resizable=no,scrollbars=yes,toolbar=no,resizable=yes\"); return false;'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CHANGE . "</a>&nbsp;&nbsp;
        <a href='#' onclick='document.getElementById(\"cs_ncMobileSrc_value\").value=\"\";document.getElementById(\"cs_ncMobileSrc_caption\").innerHTML = \"" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_NONE . "\";return false;'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_DELETE . "</a></span> <br /><br />
        " . nc_admin_checkbox(CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_REDIRECT, 'ncMobileRedirect', $Array["ncMobileRedirect"], "class='ncMobileIdentity'") . "
    </span>
    <br />" . CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CRITERION . "


    <script type='text/javascipt'>
    function nc_mobile_change() {
        if (\$nc('input[name=ncMobile]').filter(':checked').val() == 1) {
            \$nc('.ncMobileIdentity').each(function() {
                \$nc(this).removeAttr('disabled');
            })
            \$nc('.moblilty_links, #cs_ncMobileSrc_caption').css('display', '');
            \$nc('.mobility_notMobile').css('display', 'none');
            \$nc('#mobility_text').css('color', '#505050');
        } else {
            \$nc('.ncMobileIdentity').each(function() {
                \$nc(this).attr('disabled', 'disabled');
            });
            \$nc('.moblilty_links, #cs_ncMobileSrc_caption').css('display', 'none');
            \$nc('.mobility_notMobile').css('display', '');
            \$nc('#mobility_text').css('color', '#aaa');
        }
    }

    \$nc(document).ready(function() {
        nc_mobile_change();
        \$nc('input[name=ncMobile]').change(function(){
            nc_mobile_change();
        });
    });

    </script>";

    echo nc_get_modal_radio('ncMobileIdentity', array(
            array(
                    'attr' => array(
                            'value' => '1',
                            'class' => 'ncMobileIdentity'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_USERAGENT),

            array(
                    'attr' => array(
                            'value' => '2',
                            'class' => 'ncMobileIdentity',
                            'id' => 'ncMobileCatalogue'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SCREEN_RESOLUTION),

            array(
                    'attr' => array(
                            'value' => '3',
                            'class' => 'ncMobileIdentity'),
                    'desc' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_ALL_CRITERION)
            ), +$Array['ncMobileIdentity']);

    $mobile_fieldset = new nc_admin_fieldset(CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SETTINGS);
    $mobile_fieldset->add(ob_get_clean());
    $fieldsets->template->add($mobile_fieldset->result());

    if ($type == 1)
        $action = "add";
    if ($type == 2) {
        $action = "change";
        $message = $CatalogueID;
    }

    require $ROOT_FOLDER . "message_fields.php";

    if ($fldCount) {
        $fieldsets->new_fieldset('ext_fields', CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_EXTFIELDS);
        ob_start();

        if ($type == 2) {
            $fieldQuery = join($fld, ",");
            $fldValue = $db->get_row("SELECT " . $fieldQuery . " FROM `Catalogue` WHERE `Catalogue_ID`='" . $CatalogueID . "'", ARRAY_N);
        }

        echo "<table border='0' cellpadding='6' cellspacing='0' width='100%'><tr><td><font>";
        require $ROOT_FOLDER . "message_edit.php";
        echo "</td></tr></table>";
        $fieldsets->ext_fields->add(ob_get_clean())->show($display['fields']);
    }

    echo $fieldsets->to_string();

    if ($type == 1) {
        $UI_CONFIG->actionButtons[] = array("id" => "submit",
                "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE,
                "action" => "mainView.submitIframeForm()"
        );
    } elseif ($type == 2) {
        $UI_CONFIG->actionButtons[] = array("id" => "submit",
                "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE,
                "align" => "right",
                "action" => "mainView.submitIframeForm()"
        );
    }

    return 0;
}

##############################################
# Обновление информации в БД после добавления/изменения сайта
##############################################

function ActionCatalogueCompleted($CatalogueID, $type) {
    global $nc_core, $db, $ROOT_FOLDER, $admin_mode;
    global $systemTableID, $systemTableName;
    global $FILES_FOLDER, $INCLUDE_FOLDER;
    global $FILECHMOD, $DIRCHMOD, $ADMIN_FOLDER, $MODULE_FOLDER;
    global $CatalogueID;

    $is_there_any_files = getFileCount(0, $systemTableID);
    $lm_type = $nc_core->page->get_field_name('last_modified_type');
    if ($type == 1)
        $action = "add";
    if ($type == 2) {
        $CatalogueID = intval($CatalogueID);
        $action = "change";
        $message = $CatalogueID;
    }

	$sm_field = $nc_core->page->get_field_name('sitemap_include');
    $sm_change_field = $nc_core->page->get_field_name('sitemap_changefreq');
    $sm_priority_field = $nc_core->page->get_field_name('sitemap_priority');

    $params = array('Catalogue_Name', 'Domain', 'Template_ID', 'Read_Access_ID',
            'Write_Access_ID', 'Edit_Access_ID', 'Subscribe_Access_ID',
            'Checked_Access_ID', 'Delete_Access_ID', 'Moderation_ID', 'Checked',
            'Priority', 'Mirrors', 'Robots', 'Cache_Access_ID', 'Cache_Lifetime', 'TitleSubIDName', 'DisplayType',
            'TitleSubIDKeyword', 'TitleTemplateID', 'E404SubIDName', 'E404SubIDKeyword', 'TitleSubID', 'E404SubID',
            'E404TemplateID', 'CommentsEditRules', 'CommentAccessID', 'CommentsDeleteRules',
            'posting', 'last_modified_type',
            'DisallowIndexing', 'ncOfflineText', 'ncMobile', 'ncMobileSrc', 'ncMobileRedirect', 'ncMobileIdentity',
            'DemoMode', 'Default_Class_ID');
    if ($nc_core->modules->get_by_keyword('search')) {
        $params = array_merge($params, array($sm_field, $sm_change_field, $sm_priority_field));
    }

    foreach ($params as $v)
        global $$v;

    $st = new nc_Component(0, 1);
    foreach ($st->get_fields() as $v) {
        $name = 'f_' . $v['name'];
        global $$name;
        if ($v['type'] == 6) {
            global ${$name . "_old"};
            global ${"f_KILL" . $v['id']};
        }
        if ($v['type'] == 8) {
            global ${$name . "_day"};
            global ${$name . "_month"};
            global ${$name . "_year"};
            global ${$name . "_hours"};
            global ${$name . "_minutes"};
            global ${$name . "_seconds"};
        }
    }

    $Checked = intval($Checked);

    $Mirrors = str_replace(array("http://", "https://", "/"), "", $Mirrors);

    $Priority += 0;
    $Template_ID = intval($Template_ID);
    $posting = 1;

    // prepare template custom settings
    $settings = $nc_core->template->get_custom_settings($Template_ID);
    if ($settings) {
        $a2f = new nc_a2f($settings, 'TemplateSettings');
        if ($a2f->has_errors()) {
            $warnText = $a2f->get_validation_errors();
            $posting = 0;
        }
        $a2f->save_from_request_data('TemplateSettings');
        $TemplateSettings = $a2f->get_values_as_string();
    } else {
        $TemplateSettings = "";
    }


    require $ROOT_FOLDER . "message_fields.php";

    if ($posting == 0) {
        nc_print_status($warnText, 'error');
        CatalogueForm($CatalogueID, 3, "index.php", $type, $action);
        return false;
    }

    require $ROOT_FOLDER . "message_put.php";

    if (nc_module_check_by_keyword("comments")) {
        include_once ($MODULE_FOLDER . "comments/function.inc.php");
    }

    switch ($ncMobile) {
    case 2:
        $ncMobile = 0;
        $ncResponsive = 1;
        break;

    case 1:
        $ncMobile = 1;
        $ncResponsive = 0;
        break;

    default:
        $ncMobile = 0;
        $ncResponsive = 0;
        break;
    }

    if (!$Robots) {
        $Robots = "# NetCat Robots file\nUser-agent: *\nDisallow: /install/\nSitemap: /sitemap.xml";
        if (nc_module_check_by_keyword('search')) {
            $nc_search_robots = new nc_search_robots();
            $Robots = $nc_search_robots->fill_autogenerated_section($CatalogueID, $Robots);
        }
    }

    if ($type == 1) {

        $insert = "INSERT INTO `Catalogue` (";

        for ($i = 0; $i < $fldCount; $i++) {
            if ($fldTypeOfEdit[$i] == 3)
                continue;
            $insert.= $fld[$i] . ",";
        }

        if (nc_module_check_by_keyword("cache")) {
            $insert.= "`Cache_Access_ID`, `Cache_Lifetime`,";
        }
        $insert.= "`Catalogue_Name`, `Domain`, `" . $nc_core->page->get_field_name('language') . "`, `Template_ID`,  `Read_Access_ID`, `Write_Access_ID`, `Edit_Access_ID`, `Checked_Access_ID`, `Delete_Access_ID`, `Subscribe_Access_ID`, `Moderation_ID`, `Checked`, `Priority`, `Created`, `Mirrors`, `Robots`, `" . $lm_type . "`, `TemplateSettings` , `ncOfflineText`, `ncMobile`, `ncMobileSrc`, `ncMobileRedirect`, `ncMobileIdentity`, `ncResponsive`, `Default_Class_ID`) ";
        $insert.= "VALUES (";

        for ($i = 0; $i < $fldCount; $i++) {
            if ($fldTypeOfEdit[$i] == 3 || ( $fldTypeOfEdit[$i] == 2 && !nc_field_check_admin_perm()))
                continue; // поле недоступно никому или доступно администратору но нет прав администратора
            if (isset(${$fld[$i].'Defined'}) && ${$fld[$i].'Defined'} == true) {
              $insert.= ${$fld[$i].'NewValue'} . ",";
            } else {
              $insert .= $fldValue[$i] . ",";
            }
        }

        if (nc_module_check_by_keyword("cache")) {
            $insert .= "'" . intval($Cache_Access_ID) . "',";
            $insert .= "'" . intval($Cache_Lifetime) . "',";
        }

        $insert.= "'" . $db->escape($Catalogue_Name) . "',";
        $insert.= "'" . $db->escape($Domain) . "',";
        $insert.= "'" . $db->escape(($nc_core->input->fetch_get_post('language') != '' ? $nc_core->input->fetch_get_post('language') : MAIN_LANG)) . "',";
        $insert.= "'" . $db->escape($Template_ID) . "',";
        $insert.= "'" . intval($Read_Access_ID) . "',";
        $insert.= "'" . intval($Write_Access_ID) . "',";
        $insert.= "'" . intval($Edit_Access_ID) . "',";
        $insert.= "'" . intval($Checked_Access_ID) . "',";
        $insert.= "'" . intval($Delete_Access_ID) . "',";
        $insert.= "'" . intval($Subscribe_Access_ID) . "',";
        $insert.= "'" . intval($Moderation_ID) . "',";
        $insert.= "'" . intval($Checked) . "',";
        $insert.= "'" . intval($Priority) . "',";
        $insert.= "'" . date("Y-m-d H:i:s") . "',";
        $insert.= "'" . $db->escape($Mirrors) . "',";
        $insert.= "'" . $db->escape($Robots) . "',";
        $insert.= "'" . intval($last_modified_type) . "',";
        $insert.= "'" . $db->prepare($TemplateSettings) . "',";
        $insert.= "'" . $db->escape($ncOfflineText) . "',";
        $insert.= "'" . intval($ncMobile) . "',";
        $insert.= "'" . intval($ncMobileSrc) . "',";
        $insert.= "'" . intval($ncMobileRedirect) . "',";
        $insert.= "'" . intval($ncMobileIdentity) . "',";
        $insert.= "'" . intval($ncResponsive) . "',";
        $insert.= "'" . ($nc_core->input->fetch_get_post('ncHTTPS') ? '1' : '0') . "',";
	$insert.= "'" . intval($Default_Class_ID);
        $insert.= ")";

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SITE_CREATED, 0);

        $db->query($insert);
        if ($db->is_error)
            throw new nc_Exception_DB_Error($db->last_query, $db->last_error);
        $CatalogueID = $db->insert_id;

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_SITE_CREATED, $CatalogueID);

        $message = $CatalogueID;

        //постобработка файлов с учетом нового $message
        $nc_core->files->field_save_file_afteraction($message);

        if (nc_module_check_by_keyword("comments")) {
            if ($CommentAccessID > 0) {
                // add comment relation
                $CommentRelationID = nc_comments::addRule($db, array($message), $CommentAccessID, $CommentsEditRules, $CommentsDeleteRules);
                // update inserted data
                $db->query("UPDATE `Catalogue` SET `Comment_Rule_ID` = '" . (int) $CommentRelationID . "' WHERE `Catalogue_ID` = '" . (int) $message . "'");
            }
        }


        // проверка названия раздела
        if (!$TitleSubIDName || !$E404SubIDName) {
            nc_print_status(CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_NAME, 'error');
            return false;
        }

        // проверка символов для ключевого слова
        if (!$nc_core->subdivision->validate_hidden_url($TitleSubIDKeyword) || !$nc_core->subdivision->validate_hidden_url($E404SubIDKeyword)) {
            nc_print_status(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID, 'error');
            return false;
        }

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_CREATED, $CatalogueID, 0);

        // Добавление раздела для титульной страницы
        $db->query("INSERT INTO `Subdivision`
      SET `Catalogue_ID` = '" . intval($CatalogueID) . "',
      `Parent_Sub_ID` = 0,
      `Subdivision_Name` = '" . $db->escape($TitleSubIDName) . "',
      `Template_ID` = '" . intval($TitleTemplateID) . "',
      `Checked` = 0,
      `EnglishName` = '" . $db->escape($TitleSubIDKeyword) . "',
      `Hidden_URL` = '/" . $db->escape($TitleSubIDKeyword) . "/',
      `Priority` = 0");

        $title_sub_id = $db->insert_id;

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_CREATED, $CatalogueID, $title_sub_id);

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_CREATED, $CatalogueID, 0);

        // Добавление раздела для титульной страницы
        $db->query("INSERT INTO `Subdivision`
      SET `Catalogue_ID` = '" . $CatalogueID . "',
      `Parent_Sub_ID` = 0,
      `Subdivision_Name` = '" . $db->escape($E404SubIDName) . "',
      `Template_ID` = '" . intval($E404TemplateID) . "',
      `Checked` = 0,
      `EnglishName` = '" . $db->escape($E404SubIDKeyword) . "',
      `Hidden_URL` = '/" . $db->escape($E404SubIDKeyword) . "/',
      `Priority` = 1");

        $e404_sub_id = $db->insert_id;

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_CREATED, $CatalogueID, $e404_sub_id);

        // для этого апдейта не нужно вызывать трансляцию события
        $db->query("UPDATE `Catalogue` SET `Title_Sub_ID` = '" . $title_sub_id . "', `E404_Sub_ID` = '" . $e404_sub_id . "' WHERE `Catalogue_ID` = '" . $CatalogueID . "'");
    }

    if ($type == 2) {
        $cur_checked = $db->get_var("SELECT `Checked` FROM `Catalogue` WHERE `Catalogue_ID` = '" . $CatalogueID . "'");
        if (nc_module_check_by_keyword("comments")) {
            // get rule id
            $CommentData = nc_comments::getRuleData($db, array($CatalogueID));
            $CommentRelationID = $CommentData['ID'];
            // do something
            switch (true) {
                case $CommentAccessID > 0 && $CommentRelationID:
                    // update comment rules
                    nc_comments::updateRule($db, array($CatalogueID), $CommentAccessID, $CommentsEditRules, $CommentsDeleteRules);
                    break;
                case $CommentAccessID > 0 && !$CommentRelationID:
                    // add comment relation
                    $CommentRelationID = nc_comments::addRule($db, array($CatalogueID), $CommentAccessID, $CommentsEditRules, $CommentsDeleteRules);
                    break;
                case $CommentAccessID <= 0 && $CommentRelationID:
                    // delete comment rules
                    nc_comments::dropRuleCatalogue($db, $CatalogueID);
                    $CommentRelationID = 0;
                    break;
            }
        }

        $update = " UPDATE `Catalogue` SET ";

        for ($i = 0; $i < $fldCount; $i++) {
            if ($fldTypeOfEdit[$i] == 3)
                continue; // поле недоступно никому
            if (isset(${$fld[$i].'Defined'}) && ${$fld[$i].'Defined'} == true) {
              $update .= $fld[$i] . "=" . ${$fld[$i].'NewValue'} . ",";
            } else {
              $update .= $fld[$i] . "=" . $fldValue[$i] . ",";
            }
        }

        $update.= "`Catalogue_Name` = '" . $db->escape($Catalogue_Name) . "',";
        $update.= "`Domain` = '" . $db->escape($Domain) . "',";
        $update.= "`Template_ID` = " . intval($Template_ID) . ",";
        $update.= "`Read_Access_ID` = '" . intval($Read_Access_ID) . "',";
        $update.= "`Write_Access_ID` = '" . intval($Write_Access_ID) . "',";
        $update.= "`Edit_Access_ID` = '" . intval($Edit_Access_ID) . "',";
        $update.= "`Checked_Access_ID` = '" . intval($Checked_Access_ID) . "',";
        $update.= "`Delete_Access_ID` = '" . intval($Delete_Access_ID) . "',";
        $update.= "`Subscribe_Access_ID` = '" . intval($Subscribe_Access_ID) . "',";
        if (nc_module_check_by_keyword("cache")) {
            $update.= "`Cache_Access_ID` = '" . intval($Cache_Access_ID) . "',";
            $update.= "`Cache_Lifetime` = '" . intval($Cache_Lifetime) . "',";
        }
        if (nc_module_check_by_keyword("comments")) {
            $update.= "`Comment_Rule_ID` = '" . intval($CommentRelationID) . "',";
        }
        $update.= "`Moderation_ID` = '" . intval($Moderation_ID) . "',";
        $update.= "`Checked` = '" . intval($Checked) . "',";
        $update.= "`Priority` = '" . intval($Priority) . "',";
        $update.= "`Mirrors` = '" . $db->escape($Mirrors) . "',";
        $update.= "`Robots` = '" . $db->escape($Robots) . "',";
        $update.= "`Title_Sub_ID` = '" . intval($TitleSubID) . "',";
        $update.= "`E404_Sub_ID` = '" . intval($E404SubID) . "',";
        $update.= "`" . $lm_type . "` = '" . intval($last_modified_type) . "',";
        $update.= "`DisallowIndexing`= '" . intval($DisallowIndexing) . "',";
        $update.= "`" . $nc_core->page->get_field_name('language') . "` = '" . $nc_core->input->fetch_get_post('language') . "',";
        if ($nc_core->modules->get_by_keyword('search')) {
            $update.= "`" . $sm_field . "` = '" . $nc_core->input->fetch_get_post('sitemap_include') . "',";
            $update.= "`" . $sm_change_field . "` = '" . $nc_core->input->fetch_get_post('sitemap_changefreq') . "',";
            $update.= "`" . $sm_priority_field . "` = '" . str_replace(',', '.', sprintf("%.1f", doubleval($nc_core->input->fetch_get_post('sitemap_priority')))) . "',";
        }
        $update.= "`TemplateSettings` = '" . $db->prepare($TemplateSettings) . "',";
        $update.= "`ncOfflineText` = '" . $db->escape($ncOfflineText) . "',";
        $update.= "`ncMobile` = '" . intval($ncMobile) . "',";
        $update.= "`ncMobileSrc` = '" . intval($ncMobileSrc) . "',";
        $update.= "`ncMobileRedirect` = '" . intval($ncMobileRedirect) . "',";
        $update.= "`ncMobileIdentity` = '" . intval($ncMobileIdentity) . "',";
        $update.= "`ncResponsive` = '" . intval($ncResponsive) . "',";
        $update.= "`DisplayType` = '" . $db->escape($DisplayType) . "',";
        $update.= "`DemoMode` = '" . (isset($DemoMode) && $DemoMode ? '1' : '0') . "',";
        $update.= "`ncHTTPS` = '" . ($nc_core->input->fetch_get_post('ncHTTPS') ? '1' : '0') . "',";
        $update.= "`Default_Class_ID` = '" . intval($Default_Class_ID) . "'";
        $update.= " WHERE `Catalogue_ID` = " . $CatalogueID;

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SITE_UPDATED, $CatalogueID);
        if ($cur_checked != $Checked) {
            $nc_core->event->execute($Checked ? nc_Event::BEFORE_SITE_ENABLED : nc_Event::BEFORE_SITE_DISABLED, $CatalogueID);
        }

        $db->query($update);
        if ($db->is_error)
            throw new nc_Exception_DB_Error($db->last_query, $db->last_error);

        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_SITE_UPDATED, $CatalogueID);

        // произошло включение / выключение
        if ($cur_checked != $Checked) {
            $nc_core->event->execute($Checked ? nc_Event::AFTER_SITE_ENABLED : nc_Event::AFTER_SITE_DISABLED, $CatalogueID);
        }
    }

    return true;
}

function CheckIfDelete() {
    global $db;

    $nc_core = nc_Core::get_object();

    $input = $nc_core->input->fetch_get_post();
    if (!empty($input)) {
        foreach ($input as $key => $val) {
            if (nc_substr($key, 0, 6) == "Delete" && $val)
                return true;
        }
    }

    return false;
}

/*
 * Подтверждение удаления сайта
 */

function AscIfDelete($phase, $action) {
    global $db;
    global $UI_CONFIG;
    $ask = false;

    print "<form method='post' action='" . $action . "'>";
    print "<ul>";

    $nc_core = nc_Core::get_object();

    $input = $nc_core->input->fetch_get_post();

    if (!empty($input)) {
        foreach ($input as $key => $val) {
            if (nc_substr($key, 0, 6) == "Delete" && $val) {
                $ask = true;
                $cat_id = intval(nc_substr($key, 6, nc_strlen($key) - 6));

                $Catalogue_Name = $db->get_var("SELECT `Catalogue_Name` FROM `Catalogue` WHERE `Catalogue_ID` = '" . $cat_id . "'");

                print "<li>" . $Catalogue_Name . "</li>";
                print "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
                $cat_counter++;
            }
        }
    }

    if (!$ask)
        return false;

    if ($cat_counter > 1) {
        $post_f1 = CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_I;
        $post_f2 = CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_U;
    }

    print "</ul>";
    nc_print_status(sprintf(CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE, $post_f1, $post_f2), 'info');

    print "<br/><br/>
  " . $nc_core->token->get_input() . "
	<input type='hidden' name='phase' value='" . $phase . "' />
	</form>";

    $UI_CONFIG->actionButtons[] = array(
        "id" => "submit",
        "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CONFIRMDELETE,
        "action" => "mainView.submitIframeForm()",
        "red_border" => true,
    );

    return true;
}

/*
 * Обновление приоритета у сайта
 */

function UpdateCataloguePriority() {
    global $nc_core, $db;

    if (!empty($_POST)) {
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 8) == "Priority") {
                $cat_id = intval(substr($key, 8, strlen($key) - 8));
                $val += 0;

                // execute core action
                $nc_core->event->execute(nc_Event::BEFORE_SITE_UPDATED, $cat_id);

                $db->query("UPDATE `Catalogue` SET `Priority` = '" . $val . "', `LastUpdated` = `LastUpdated` WHERE `Catalogue_ID` = '" . $cat_id . "'");

                // execute core action
                $nc_core->event->execute(nc_Event::AFTER_SITE_UPDATED, $cat_id);
            }
        }
    }
}

/*
 * Удаления сайта
 */

function DeleteCatalogue() {
    global $db, $UI_CONFIG, $nc_core;

    $deleted_ids = array();

    if (!empty($_POST)) {
        foreach ($_POST as $key => $val) {
            if (nc_substr($key, 0, 6) == "Delete" && $val) {
                $val += 0;
                CascadeDeleteCatalogue($val);
                DeleteSystemTableFiles("Catalogue", $val);
                $UI_CONFIG->treeChanges['deleteNode'][] = "site-" . $val;
                $deleted_ids[] = $val;
            }
        }
    }

    $UI_CONFIG->deleteNavBarCatalogue = $deleted_ids;

    $nc_core->catalogue->load_all();
    nc_print_status(CONTROL_CONTENT_CATALOUGE_SUCCESS_DELETE, "ok");
}

/*
 * Страница информации о сайте
 */

function ShowMenu($CatalogueID, $phase1, $action1, $phase2, $action2) {
    global $db, $perm;
    global $EDIT_DOMAIN, $HTTP_ROOT_PATH, $DOMAIN_NAME, $ADMIN_PATH, $SUB_FOLDER;
    global $UI_CONFIG;

    $CatalogueID = intval($CatalogueID);
    $scheme = nc_Core::get_object()->catalogue->get_scheme_by_id($CatalogueID);

    $is_admin = $perm->isCatalogueAdmin($CatalogueID);

    $Array = $db->get_row("SELECT * FROM `Catalogue` WHERE `Catalogue_ID`='" . $CatalogueID . "'");
    if (!$Array) {
        nc_print_status(CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR, 'error');
        EndHtml();
        exit();
    }

    $countChild = HighLevelChildrenNumber($CatalogueID);
    $ModerationType = ($Array->Moderation_ID == 2) ? CLASSIFICATOR_TYPEOFMODERATION_MODERATION : CLASSIFICATOR_TYPEOFMODERATION_RIGHTAWAY;
    $UserGroupName = array(
            1 => CLASSIFICATOR_USERGROUP_ALL,
            2 => CLASSIFICATOR_USERGROUP_REGISTERED,
            3 => CLASSIFICATOR_USERGROUP_AUTHORIZED
    );

    //  In MySQL 4.1, TIMESTAMP display format changes to be the same as DATETIME.
    if (nc_strpos($Array->LastUpdated[4], '-')) {
        $Array->LastUpdated = nc_substr($Array->LastUpdated, 0, 4) . "-" . nc_substr($Array->LastUpdated, 4, 2) . "-" . nc_substr($Array->LastUpdated, 6, 2) . " " . nc_substr($Array->LastUpdated, 8, 2) . ":" . nc_substr($Array->LastUpdated, 10, 2) . ":" . nc_substr($Array->LastUpdated, 12, 2);
    }

    echo "<br />
	<table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td>
	<table border='0' cellpadding='0' cellspacing='1' width='100%'><tr><td>
 	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='border-bottom'>
 	<tr><td width='50%'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_CREATED . ":</td><td>" . $Array->Created . "</td></tr>
 	<tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_UPDATED . ":</td><td>" . $Array->LastUpdated . "</td></tr>
 	</table>
	</td></tr><tr><td>
 	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='border-bottom'>
  <tr><td width='50%'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SECTIONSCOUNT . ":</td>";
    echo "<td>" . $countChild;

    if ($countChild)
        echo " ( <a href='" . $ADMIN_PATH . "subdivision/index.php?CatalogueID=" . $CatalogueID . "&amp;ParentSubID=0'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_LIST . "</a>
          " . ($is_admin ? ", <a href='" . $ADMIN_PATH . "subdivision/index.php?phase=2&amp;ParentSubID=0&amp;CatalogueID=" . $CatalogueID . "'>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADD . "
          </a> )" : ")") . "";

    echo "</td></tr>
 	<tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SITESTATUS . ":</td><td>" . ($Array->Checked ? CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ON : CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_OFF ) . "</td></tr>
 	</table>
	</td></tr><tr><td>
 	<table border='0' cellpadding='0' cellspacing='0' width='100%' class='border-bottom'>
 	<tr><td width=50%>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_READACCESS . ":</td><td>" . $UserGroupName[$Array->Read_Access_ID] . " " . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS . "</td></tr>
  <tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDACCESS . ":</td><td>" . $UserGroupName[$Array->Write_Access_ID] . " " . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS . "</td></tr>
 	<tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDITACCESS . ":</td><td>" . $UserGroupName[$Array->Edit_Access_ID] . " " . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS . "</td></tr>
 	<tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBEACCESS . ":</td><td>" . $UserGroupName[$Array->Subscribe_Access_ID] . " " . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS . "</td></tr>
 	<tr><td>" . CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_PUBLISHACCESS . ":</td><td>" . $ModerationType . "</td></tr>
 	</table>
	</td></tr></table></td></tr></table>";

    $UI_CONFIG->actionButtons[] = array(
        "id" => "delete",
        "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_DELETE,
        "location" => "site.delete(" . $CatalogueID . ")",
        "red_border" => true,
    );
    $UI_CONFIG->actionButtons[] = array("id" => "preview",
            "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_VIEW,
            "action" => "urlDispatcher.load('{$scheme}://" . ($Array->Domain ? (strstr($Array->Domain, ".") ? $Array->Domain : $Array->Domain . "." . $DOMAIN_NAME) : $DOMAIN_NAME) . $SUB_FOLDER . (nc_strlen(session_id()) > 0 ? "?" . session_name() . "=" . session_id() . "" : "") . "', '1')"
    );
}

##############################################
# Проверка домена на корректность
##############################################

function getDomainsFromDatabase() {
    return nc_Core::get_object()->db->get_col("SELECT Domain FROM Catalogue");
}

function getCurrentDomainFromDatabase() {
    return nc_Core::get_object()->catalogue->get_by_id($CatalogueID, 'Domain');
}

function checkDomain($domain, $CatalogueID, $result_array=false) {
    $result        = '';
    $data          = array(
        'text' => '',
        'link' => '',
    );
    $uniqueFileUrl = createUniqueFile();

    switch (true) {
        case !isFieldSet($domain):
            $data['text'] = CONTROL_CONTENT_CATALOUGE_ERROR_DOMAIN_NOT_SET;
            break;

        case !$uniqueFileUrl:
            $data['text'] = NETCAT_ADMIN_NOTICE_RIGHTS.' '.nc_Core::get_object()->HTTP_FILES_PATH;
            break;

        case !isDomainCorrect($domain, $uniqueFileUrl):
            $data['text'] = CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN;
            $data['link'] = nc_Core::get_object()->ADMIN_PATH."catalogue/index.php?phase=2&type=2&CatalogueID={$CatalogueID}";

        default:
            deleteUniqueFile($uniqueFileUrl);
            break;
    }

    if ($result_array) {
        return $data;
    }

    if ($data['link']) {
        return " ( <a style='vertical-align:top' href='{$data['link']}'>{$data['text']}</a>";
    }

    return $data['text'] ? ' (' . $data['text'] . ')' : '';
}

function createUniqueFile() {
    $DOCUMENT_ROOT = nc_Core::get_object()->DOCUMENT_ROOT;
    $HTTP_FILES_PATH = nc_Core::get_object()->HTTP_FILES_PATH;

    $fileUrl = $HTTP_FILES_PATH.md5(time());

    if (@file_put_contents($DOCUMENT_ROOT.$fileUrl, '') === false) {
        return false;
    }

    return $fileUrl;
}

function deleteUniqueFile($fileUrl) {
    @unlink(nc_Core::get_object()->DOCUMENT_ROOT.$fileUrl);
    return true;
}

function isDomainCorrect($domain, $fileUrl) {
    if (!preg_match("~[a-z0-9_-а-яё]+\.[а-яёa-z]+~", $domain) || preg_match("~\.loc$~", $domain)) {
        return false;
    }
    return isFieldSet($domain) && checkFile($domain, $fileUrl);
}

function isFieldSet($domain) {
    return !empty($domain);
}

function checkFile($domain, $fileUrl) {
    return $fileUrl ? (extension_loaded('curl') ? checkFileByCurl($domain, $fileUrl) : checkFileByGetHeaders($domain, $fileUrl)) : false;
}

function checkFileByCurl($domain, $fileUrl) {
    if (function_exists('curl_init')) {
        $curlConnection = curl_init();

        curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlConnection, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($curlConnection, CURLOPT_HEADER, 1);
        curl_setopt($curlConnection, CURLOPT_NOBODY, true);
        curl_setopt($curlConnection, CURLOPT_TIMEOUT, '30');
        curl_setopt($curlConnection, CURLOPT_URL, nc_Core::get_object()->catalogue->get_url_by_host_name($domain) . $fileUrl);

        $responseHeaders = curl_exec($curlConnection);

        curl_close($curlConnection);

        return isHeaderOk($responseHeaders);
    }

    return false;
}

function checkFileByGetHeaders($domain, $fileUrl) {
    $responseHeaders = @get_headers(nc_Core::get_object()->catalogue->get_url_by_host_name($domain) . $fileUrl);
    return isHeaderOk($responseHeaders[0]);
}

function isHeaderOk($header) {
    return strpos($header, '200');
}

class ui_config_catalogue extends ui_config {

    /**
     * Constructor
     * @param string [site]
     * @param int catalogue id
     */
    function __construct($active_tab, $catalogue_id, $action = '', $parent_sub_id = '', $active_toolbar = null) {
        global $perm;

        $catalogue_id = +$catalogue_id;
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;

        $is_admin = $perm->isCatalogueAdmin($catalogue_id);
        $nc_stat_stat_module = ($nc_core->get_settings('NC_Stat_Enabled', 'stats') == 1) && $perm->isSupervisor();
        $openstat_stat_module = ($nc_core->get_settings('Openstat_Enabled', 'stats') == 1) && $perm->isSupervisor();

        $catalogue = $db->get_row("SELECT Catalogue_Name
  	  	                         FROM Catalogue
    		                    WHERE Catalogue_ID = $catalogue_id", ARRAY_A);

        if ($active_tab == 'edit' || $active_tab == 'map' || $active_tab == 'info' || $active_tab == 'seo' || $active_tab == 'stat') {
            global $HTTP_HOST;
            $scheme = $nc_core->catalogue->get_scheme_by_id($catalogue_id);

            if ($active_tab == 'edit') {
                $this->toolbar = array();
                $toolbar = array('design', 'edit', 'seo', 'system', 'fields');

                if (!$action) {
                    $action = 'edit';
                }

                foreach ($toolbar as $v) {
                    $this->toolbar[] = array(
                            'id' => $v,
                            'caption' => constant("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_" . strtoupper($v)),
                            'location' => "catalogue." . $v . "(" . $catalogue_id . ")",
                            'group' => "grp1");
                    if ($action == $v) {
                        $this->activeToolbarButtons[] = $v;
                    }
                }
            }

            $this->headerText = $catalogue["Catalogue_Name"];
            $this->headerImage = ( $active_tab == 'info' ) ? 'i_tool_siteinfo_big.gif' : 'i_site_big.gif';

            $this->tabs[] = array('id' => 'map',
                    'caption' => SITE_TAB_SITEMAP,
                    'location' => "site.map($catalogue_id)");

            if ($is_admin) {
                $this->tabs[] = array('id' => 'edit',
                        'caption' => SITE_TAB_SETTINGS,
                        'location' => "catalogue.design($catalogue_id)");

            }

            if ($perm->isSupervisor()) {
                $this->tabs[] = array('id' => 'seo',
                        'caption' => SITE_TAB_SEO,
                        'location' => "site.seo(" . $HTTP_HOST . ",$catalogue_id)");
            }

            if ($openstat_stat_module) {
                $this->tabs[] = array('id' => 'stat',
                        'caption' => SITE_TAB_STATS,
                        'location' => "site.stat.openstat(" . $catalogue_id . ")");
            } elseif ($nc_stat_stat_module) {
                $this->tabs[] = array('id' => 'stat',
                        'caption' => SITE_TAB_STATS,
                        'location' => "site.stat.nc_stat(" . $catalogue_id . ")");
            }

            // 5. Просмотр
            $catalogue_object = nc_Core::get_object()->catalogue->get_by_id($catalogue_id);
            $this->tabs[] = array('id' => 'view' . (!$catalogue_object['Domain'] ? '_site_not_active' : ''),
                'caption' => STRUCTURE_TAB_PREVIEW_SITE,
                'action' => "window.open('{$scheme}://" .
                    $catalogue_object['Domain'] .
                    "');"
            );

            if ($active_tab == 'info') {
                $this->toolbar = array(
                        array(
                                'id' => "info",
                                'caption' => SITE_TOOLBAR_INFO,
                                'location' => "site.info($catalogue_id)",
                                'group' => "grp1"),
                        array(
                                'id' => "sublist",
                                'caption' => SITE_TOOLBAR_SUBLIST,
                                'location' => "site.sublist($catalogue_id,0)",
                                'group' => "grp1"));
                if ($action == 'sublist') {
                    $this->locationHash = "site.$action($catalogue_id,$parent_sub_id)";
                    $this->activeToolbarButtons[] = "sublist";
                } else {
                    $this->activeToolbarButtons[] = "info";
                }
            } elseif ($active_tab == 'stat') {
                $this->toolbar = array();
                if ($openstat_stat_module) {
                    $this->toolbar[] = array(
                            'id' => "openstat",
                            'caption' => NETCAT_MODULE_STATS_ADMIN_TAB_OPENSTAT,
                            'location' => "site.stat.openstat(" . $catalogue_id . ")",
                            'group' => "stats");
                }
                if ($nc_stat_stat_module) {
                    $this->toolbar[] = array(
                            'id' => "nc_stat",
                            'caption' => NETCAT_MODULE_STATS_ADMIN_TAB_NC_STAT,
                            'location' => "site.stat.nc_stat(" . $catalogue_id . ")",
                            'group' => "stats");
                }
                $this->activeToolbarButtons[] = $active_toolbar;
            }
        }

        if ($active_tab == 'add') {
            $this->headerText = CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE;
            $this->headerImage = 'i_folder_big.gif';
            $this->tabs = array(
                    array(
                            'id' => 'add',
                            'caption' => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE,
                            'location' => "site.add()",
                            'align' => 'left'));
        }

        if ($active_tab == 'delete') {
            $this->headerText = CONTROL_CONTENT_CATALOUGE_SITEDELCONFIRM;
            $this->headerImage = 'i_folder_big.gif';
            $this->tabs = array(
                    array(
                            'id' => 'delete',
                            'caption' => CONTROL_CONTENT_CATALOUGE_SITEDELCONFIRM,
                            'location' => "site.delete($catalogue_id)"));
        }

        if ($action != 'sublist') {
            if($active_tab == 'edit') {
                $this->locationHash = "catalogue." . $action . "(" . $catalogue_id . ")";
            } else {
                $this->locationHash = "site." . $active_tab . ($active_toolbar ? "." . $active_toolbar : "") . "($catalogue_id)";
            }
        }

        $this->activeTab = $active_tab;
        $this->treeMode = 'sitemap';
        $this->treeSelectedNode = "site-" . $catalogue_id;
    }

}

class ui_list_catalogue extends ui_config {

    /**
     * Construct
     *
     *
     */
    function __construct() {
        $this->tabs[] = array('id' => 'list',
                'caption' => SECTION_INDEX_SITE_LIST,
                'location' => "site.list()");
        $this->activeTab = 'list';

        $this->locationHash = "site.list()";
        $this->headerText = SECTION_INDEX_SITE_LIST;
        $this->headerImage = 'i_folder_big.gif';
        $this->treeMode = 'sitemap';


        $this->actionButtons[] = array("id" => "submit",
                "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SAVE,
                "action" => "mainView.submitIframeForm()",
                "align" => "right");

        $this->actionButtons[] = array("id" => "add",
                "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE,
                "action" => "urlDispatcher.load('site.add()')",
                "align" => "left");
    }

}
