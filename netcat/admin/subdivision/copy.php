<?php

/* $Id */
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );

include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER."subdivision/function.inc.php");

$UI_CONFIG = new ui_config_tool(TOOLS_COPYSUB,
                TOOLS_COPYSUB,
                'i_copysub_big.gif',
                'tools.copy('.($copy_type ? $copy_type : 'site' ).','.( $catalogue_id ? $catalogue_id : 0 ).','.( $sub_id ? $sub_id : 0 ).')');
$UI_CONFIG->treeMode = 'sitemap';

$Title1 = TOOLS_COPYSUB;

if (!isset($phase)) $phase = 0;

if (in_array($phase, array(1))) {
    if (!$nc_core->token->verify()) {
        BeginHtml($Title1, $Title1, "");
        nc_print_status(NETCAT_TOKEN_INVALID, 'error');
        EndHtml();
        exit;
    }
}

switch ($phase) {
    case 0:
        BeginHtml($Title1, $Title1, "");
        print nc_copy_form();
        break;
    case 1:
        BeginHtml($Title1, $Title1, "");
        try {
            nc_copy_completed();
            nc_print_status(TOOLS_COPYSUB_COPY_SUCCESS, 'ok');
        } catch (Exception $e) {
            nc_print_status($e->getMessage(), 'error');
        }
        // перезагрузка левого фрейма
        print "<script>top.frames['treeIframe'].window.location.reload(); </script>";
        print nc_copy_form();
        break;
}

EndHtml ();

function nc_copy_form() {
    global $UI_CONFIG, $db;
    $nc_core = nc_Core::get_object();

    $all_sites = $nc_core->catalogue->get_all();

    $copy_type = $nc_core->input->fetch_get_post('copy_type');
    if (!$copy_type) $copy_type = 'site';

    $catalogue_id = intval($nc_core->input->fetch_get_post('catalogue_id'));
    $sub_id = intval($nc_core->input->fetch_get_post('sub_id'));
    if ($catalogue_id) {
        $Result = $db->get_results("SELECT `Subdivision_ID` AS value,
                                    CONCAT(Subdivision_ID, '. ', Subdivision_Name) AS description,
                                    `Parent_Sub_ID` AS parent
                                    FROM `Subdivision`
                                    WHERE `Catalogue_ID` = '".$catalogue_id."'
                                    ORDER BY `Subdivision_ID`", ARRAY_A);
        if (!empty($Result)) $res_sub = nc_select_options($Result, $sub_id);
    }


    $dest_catalogue_id = intval($nc_core->input->fetch_get_post('dest_catalogue_id'));
    $dest_sub_id = intval($nc_core->input->fetch_get_post('dest_sub_id'));
    if ($dest_catalogue_id) {
        $Result = $db->get_results("SELECT `Subdivision_ID` AS value,
                                    CONCAT(Subdivision_ID, '. ', Subdivision_Name) AS description,
                                    `Parent_Sub_ID` AS parent
                                    FROM `Subdivision`
                                    WHERE `Catalogue_ID` = '".$dest_catalogue_id."'
                                    ORDER BY `Subdivision_ID`", ARRAY_A);
        if (!empty($Result))
                $res_dest_sub = nc_select_options($Result, $dest_sub_id);
    }


    $templates = array(
            'sub_name' => $nc_core->input->fetch_get_post('tmpl_sub_name'),
            'sub_keyword' => $nc_core->input->fetch_get_post('tmpl_sub_keyword'),
            'cc_name' => $nc_core->input->fetch_get_post('tmpl_cc_name'),
            'cc_keyword' => $nc_core->input->fetch_get_post('tmpl_cc_keyword')
    );

    $html .=" <form action='copy.php' method='post'>
             <input type='hidden' name='phase' value='1' /> ";

    $html .= "<script type='text/javascript'>
              var nc_ajax = null;
              try {
                nc_ajax = new XMLHttpRequest();
              }
              catch(e) {
                try {
                  nc_ajax = new ActiveXObject(\"Msxml2.XMLHTTP\");
                }
                catch(e) {
                    nc_ajax = new ActiveXObject(\"Microsoft.XMLHTTP\");
                  }
              }


              function nc_copy_resp_subs() {
                if (nc_ajax.readyState == 4) {
                   document.getElementById('sub_list').innerHTML = \"<select  name='sub_id' id='sub_id'>\" + nc_ajax.responseText +\"</select>\";
                }
              }

              function nc_copy_resp_destsubs() {
                if (nc_ajax.readyState == 4) {
                   document.getElementById('dest_sub_list').innerHTML = \"<select name='dest_sub_id' id='dest_sub_id'><option value='0'>".CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ROOT."</option>\" + nc_ajax.responseText +\"</select>\";
                }
              }


              function nc_copy_query ( dest) {
                var list = document.getElementById(dest ? 'dest_catalogue_id' : 'catalogue_id');
                var site = list.options[list.selectedIndex].value;
                nc_ajax.open('POST', '../user/index.php?phase=20', true);
                nc_ajax.onreadystatechange = dest ? nc_copy_resp_destsubs : nc_copy_resp_subs ;
                nc_ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded, charset=utf-8');
                nc_ajax.send('getsublist=' + site );
              }

              function nc_copy_change_type() {
                if (document.getElementById('copy_type').selectedIndex == 1 ) {
                  var list = document.getElementById('catalogue_id');
                  var site = list.options[list.selectedIndex].value;
                  document.getElementById('row3').style.display = 'block';
                  document.getElementById('dest_fl').style.display = 'block';
                  nc_ajax.open('POST', '../user/index.php?phase=20', true);
                  nc_ajax.onreadystatechange = nc_copy_resp_subs;
                  nc_ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded, charset=utf-8');
                  nc_ajax.send('getsublist=' + site );
                }
                else {
                  document.getElementById('row3').style.display = 'none';
                  document.getElementById('dest_fl').style.display = 'none';
                }
              }
            </script>";
    $html .= "<div id='copy_sub'><fieldset><legend>".TOOLS_COPYSUB_SOURCE."</legend>";

    $html .= "<div id='row1' class='div_row'>
              <div class='left_col'>".TOOLS_COPYSUB_ACTION."</div>
              <div class='right_col'>
                  <select  id='copy_type' name='copy_type' onchange='nc_copy_change_type(); return false;'>
                     <option value='site' ".( $copy_type == 'site' ? "selected='selected'" : "").">".TOOLS_COPYSUB_COPY_SITE."</option>
                     <option value='sub' ".( $copy_type == 'sub' ? "selected='selected'" : "").">".TOOLS_COPYSUB_COPY_SUB."</option>
                      </select>
               </div>
               <div style='clear:both;'></div>
            </div>";

    $html .= "<div id='row2'  class='div_row'>
              <div class='left_col' style='border-top: none;'>".TOOLS_COPYSUB_SITE."</div>
              <div class='right_col' style='border-top: none;'>
                  <select  id='catalogue_id' name='catalogue_id' onchange='nc_copy_query(0); return false;'>";
    foreach ($all_sites as $v) {
        $html .= "<option value='".$v['Catalogue_ID']."' ".( $catalogue_id == $v['Catalogue_ID'] ? "selected='selected'" : "").">".$v['Catalogue_ID'].". ".$v['Catalogue_Name']."</option>";
    }
    $html .="          </select>
               </div>
               <div style='clear:both;'></div>
            </div>";

    $html .= "<div id='row3' class='div_row' ".($copy_type == 'site' ? "style='display: none; '" : "" ).">
              <div class='left_col' style='border-top: none;'>".TOOLS_COPYSUB_SUB."</div>
              <div id='sub_list' class='right_col' style='border-top: none;'>
                ".( $res_sub ? "<select name='sub_id'>".$res_sub."</select>" : "")."
               </div>
               <div style='clear:both;'></div>
            </div>
            </fieldset>";



    $html .= "<fieldset id='dest_fl' ".($copy_type == 'site' ? "style='display: none; '" : "" )."><legend>".TOOLS_COPYSUB_DESTINATION."</legend>
             <div id='row_dest_1' class='div_row'>
              <div class='left_col'>".TOOLS_COPYSUB_SITE."</div>
              <div class='right_col' >
                  <select  id='dest_catalogue_id' name='dest_catalogue_id'  onchange='nc_copy_query(1); return false;'>";
    foreach ($all_sites as $v) {
        $html .= "<option value='".$v['Catalogue_ID']."' ".( $dest_catalogue_id == $v['Catalogue_ID'] ? "selected='selected'" : "").">".$v['Catalogue_ID'].". ".$v['Catalogue_Name']."</option>";
    }
    $html .="          </select>
               </div>
               <div style='clear:both;'></div>
            </div>


            <div id='row_dest_2' class='div_row'>
              <div class='left_col' style='border-top: none;'>".WIZARD_CLASS_FORM_SUBDIVISION_PARENTSUB."</div>
              <div id='dest_sub_list' class='right_col' style='border-top: none;'>
                <select name='dest_sub_id'><option value='0'>".CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ROOT."</option>".($res_dest_sub ? $res_dest_sub : "")."</select>
               </div>
               <div style='clear:both;'></div>
            </div>
            </fieldset> ";

    $html .= "<fieldset><legend>".TOOLS_COPYSUB_TEMPLATE_NAME."</legend>
              <div id='tmpl1' class='div_row'>
              <div class='left_col'>".REPORTS_LAST_NAME."</div>
              <div  class='right_col' >".
            nc_admin_input_simple('tmpl_sub_name', ( $templates['sub_name'] ? $templates['sub_name'] : "%NAME%"), 0, '')."
               </div>
               <div style='clear:both;'></div>
            </div>
            <div id='tmpl2' class='div_row'>
              <div class='left_col' style='border-top: none;'>".TOOLS_COPYSUB_KEYWORD_SUB."</div>
              <div  class='right_col' style='border-top: none;'>".nc_admin_input_simple('tmpl_sub_keyword', ($templates['sub_keyword'] ? $templates['sub_keyword'] : "%KEYWORD%-copy"), 0, '')."
               </div>
               <div style='clear:both;'></div>
            </div>
            <div id='tmpl3' class='div_row'>
              <div class='left_col' style='border-top: none;'>".TOOLS_COPYSUB_NAME_CC."</div>
              <div  class='right_col' style='border-top: none;'>".nc_admin_input_simple('tmpl_cc_name', ( $templates['cc_name'] ? $templates['cc_name'] : "%NAME%"), 0, '')."
               </div>
               <div style='clear:both;'></div>
            </div>
            <div id='tmpl4' class='div_row'>
              <div class='left_col' style='border-top: none;'>".TOOLS_COPYSUB_KEYWORD_CC."</div>
              <div  class='right_col' style='border-top: none;'>".nc_admin_input_simple('tmpl_cc_keyword', ( $templates['cc_keyword'] ? $templates['cc_keyword'] : "%KEYWORD%-copy"), 0, '')."
               </div>
               <div style='clear:both;'></div>
            </div></fieldset>
            ";


    $html .= "<fieldset><legend>".TOOLS_COPYSUB_SETTINGS."</legend>".
            nc_admin_checkbox_simple('with_child', 1, TOOLS_COPYSUB_COPY_WITH_CHILD, false, '', (!$phase ? " checked='checked'" : ""))."<br/>".
            nc_admin_checkbox_simple('with_сс', 1, TOOLS_COPYSUB_COPY_WITH_CC, false, '', (!$phase ? " checked='checked'" : ""))."<br/>".
            nc_admin_checkbox_simple('with_object', 1, TOOLS_COPYSUB_COPY_WITH_OBJECT, false, '', (!$phase ? " checked='checked'" : ""));

    $html .= "</fieldset></div>";

    if (!$res_dest_sub)
            $html .= "<script type='text/javascript'> nc_copy_query(1); </script>";

    $html .= $nc_core->token->get_input();
    $html .= " </form>";

    $UI_CONFIG->actionButtons[] = array("id" => "submit",
            "caption" => TOOLS_COPYSUB_COPY,
            "action" => "mainView.submitIframeForm()"
    );

    return $html;
}

function nc_copy_subdivision($sub_ids, $dest_catalogue_id, $dest_parent_sub_id, $with_child = 1, $with_сс = 1, $with_object = 1, $templates = array()) {
    $nc_core = nc_Core::get_object();
    global $db;

    if (!is_array($sub_ids)) $sub_ids = array($sub_ids);
    if (!$with_сс) $with_object = 0;
    if (!$templates['sub_name']) $templates['sub_name'] = '%NAME%';
    if (!$templates['sub_keyword']) $templates['sub_keyword'] = '%KEYWORD%';
    if (!$templates['cc_name']) $templates['cc_name'] = '%NAME%';
    if (!$templates['cc_keyword']) $templates['cc_keyword'] = '%KEYWORD%';


    // разделы для копирования
    $subdivisions = $db->get_results("SELECT * FROM `Subdivision`
                                    WHERE `Subdivision_ID` IN (".join(',', $sub_ids).")
                                    ORDER BY `Subdivision_ID` ", ARRAY_A);

    if (empty($subdivisions)) return 0;

    if ($dest_parent_sub_id && in_array($dest_parent_sub_id, $sub_ids)) {
        throw new Exception(TOOLS_COPYSUB_ERROR_LEVEL_COUNT);
    }

    // копирование в определенный раздел - можно вычислить сайт
    if ($dest_parent_sub_id) {
        $dest_subdivision = $db->get_row("SELECT `Catalogue_ID`, `Hidden_URL`, `EnglishName` FROM `Subdivision` WHERE `Subdivision_ID` = '".$dest_parent_sub_id."'", ARRAY_A);
        $dest_catalogue_id = $dest_subdivision['Catalogue_ID'];
        $hidden_url = $dest_subdivision['Hidden_URL'];
        $english_name = $dest_subdivision['EnglishName'];
        $child_english_names = $db->get_col("SELECT `EnglishName` FROM `Subdivision` WHERE `Parent_Sub_ID` = '".$dest_parent_sub_id."' ");
    } else {
        $hidden_url = '/';
        $child_english_names = $db->get_col("SELECT `EnglishName` FROM `Subdivision` WHERE `Parent_Sub_ID` = '0' AND `Catalogue_ID` = '".$dest_catalogue_id."' ");
    }

    if (!$child_english_names) $child_english_names = array();

    $fields = array_keys($subdivisions[0]);

    $query_fields_subdivisions = array();
    $insert_values_subdivisions = array();

    unset($fields[0]);
    foreach ($fields as $v) {
        $query_fields_subdivisions[] = "`".$v."`";
    }


    foreach ($subdivisions as $subdivision) {
        $rel[$subdivision['Subdivision_ID']] = 0; // связка между старыми и новыми id
        $subdivision['Subdivision_Name'] = str_replace('%NAME%', $subdivision['Subdivision_Name'], $templates['sub_name']);
        $subdivision['Subdivision_Name'] = str_replace('%KEYWORD%', $subdivision['EnglishName'], $subdivision['Subdivision_Name']);
        $subdivision['EnglishName'] = str_replace('%KEYWORD%', $subdivision['EnglishName'], $templates['sub_keyword']);
        $subdivision['Parent_Sub_ID'] = $dest_parent_sub_id;
        $subdivision['Catalogue_ID'] = $dest_catalogue_id;
        $subdivision['Created'] = $subdivision['LastUpdated'] = date("Y-m-d H:i:s");
        $subdivision['Hidden_URL'] = $hidden_url.$subdivision['EnglishName'].'/';

        if (!$nc_core->subdivision->validate_english_name($subdivision['EnglishName'])) {
            throw new Exception(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID."<br/>".$subdivision['EnglishName']);
        }
        //if ( $english_name == $subdivision['EnglishName'] ) {
        //  throw new Exception(TOOLS_COPYSUB_ERROR_KEYWORD_EXIST);
        //}
        if (in_array($subdivision['EnglishName'], $child_english_names)) {
            throw new Exception(TOOLS_COPYSUB_ERROR_KEYWORD_EXIST);
        }

        // нельзя скопировать раздел в подраздел этого же раздела
        if ($dest_parent_sub_id) {
            $childs = nc_get_subdivision_children($subdivision['Subdivision_ID']);
            if (!empty($childs) && in_array($dest_parent_sub_id, $childs)) {
                throw new Exception(TOOLS_COPYSUB_ERROR_LEVEL_COUNT);
            }
        }

        $query_values = array();
        foreach ($fields as $v) {
            $query_values[] = "'".$db->escape($subdivision[$v])."'";
        }
        $insert_values_subdivisions[$subdivision['Subdivision_ID']] = "(".join(',', $query_values).")";
    }

    $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_CREATED, $dest_catalogue_id, 0);

    foreach($insert_values_subdivisions as $Subdivision_ID => $insert_value_subdivisions) {
        $db->query("INSERT INTO `Subdivision` (".join(',', $query_fields_subdivisions).")  VALUES {$insert_value_subdivisions}");
        if ($db->is_error)
            throw new nc_Exception_DB_Error($db->last_query, $db->last_error);

        $inserted_id = $db->insert_id;

        $rel[$Subdivision_ID] = $inserted_id;

        if (!is_dir($nc_core->FILES_FOLDER.$inserted_id)) {
            @mkdir($nc_core->FILES_FOLDER.$inserted_id, $nc_core->DIRCHMOD);
            @chmod($nc_core->FILES_FOLDER.$inserted_id, $nc_core->DIRCHMOD);
        }
    }

    $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_CREATED, $dest_catalogue_id, $rel);

    // компоненты в разделе для копирования
    $sub_classes = !$with_сс ? null : $db->get_results("SELECT * FROM `Sub_Class`
                                                     WHERE `Subdivision_ID` IN (".join(',', $sub_ids).")
                                                     ORDER BY `Sub_Class_ID`", ARRAY_A);

    $query_fields_subclasses = array();
    $insert_values_subclasses = array();
    if (!empty($sub_classes)) {
        $fields = array_keys($sub_classes[0]);
        unset($fields[0]);
        foreach ($fields as $v) {
            $query_fields_subclasses[] = "`".$v."`";
        }

        foreach ($sub_classes as $sub_class) {
            $rel_cc[$sub_class['Sub_Class_ID']] = 0;
            $sub_class['Subdivision_ID'] = $rel[$sub_class['Subdivision_ID']];
            $sub_class['Sub_Class_Name'] = str_replace('%NAME%', $sub_class['Sub_Class_Name'], $templates['cc_name']);
            $sub_class['EnglishName'] = str_replace('%KEYWORD%', $sub_class['EnglishName'], $templates['cc_keyword']);
            $sub_class['Created'] = $sub_class['LastUpdated'] = date("Y-m-d H:i:s");
            $sub_class['Catalogue_ID'] = $dest_catalogue_id;

            if (!$nc_core->sub_class->validate_english_name($sub_class['EnglishName'])) {
                throw new Exception(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID."<br/>".$sub_class['EnglishName']);
            }

            $query_values = array();
            foreach ($fields as $v) {
                $query_values[] = "'".$db->escape($sub_class[$v])."'";
            }
            $insert_values_subclasses[$sub_class['Sub_Class_ID']] = "(".join(',', $query_values).")";
        }

        foreach ($sub_classes as $sub_class) {
            $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_CREATED, $dest_catalogue_id, $sub_class['Subdivision_ID'], 0);

            $db->query("INSERT INTO `Sub_Class` (".join(',', $query_fields_subclasses).")  VALUES {$insert_values_subclasses[$sub_class['Sub_Class_ID']]}");
            if ($db->is_error)
                throw new nc_Exception_DB_Error($db->last_query, $db->last_error);

            $inserted_id = $db->insert_id;

            $k = $sub_class['Sub_Class_ID'];
            $rel_cc[$k] = $inserted_id;

            $class_id = $sub_class['Class_ID'];
            $is_system_table = $db->get_var("SELECT `System_Table_ID` FROM `Class` WHERE `Class_ID` = '".$class_id."' ");
            $sub_id = $rel[$sub_class['Subdivision_ID']];

            $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_CREATED, $dest_catalogue_id, $sub_id, $rel_cc[$k]);

            if (!is_dir($nc_core->FILES_FOLDER.$sub_id."/".$rel_cc[$k])) {
                @mkdir($nc_core->FILES_FOLDER.$sub_id."/".$rel_cc[$k], $nc_core->DIRCHMOD);
                @chmod($nc_core->FILES_FOLDER.$sub_id."/".$rel_cc[$k], $nc_core->DIRCHMOD);
            }

            // объекты для копирования
            $messages = (!$with_object || $is_system_table) ? null : $db->get_results("SELECT * FROM `Message".$class_id."` WHERE `Sub_Class_ID` = '".$k."' ORDER BY `Message_ID`", ARRAY_A);

            $query_fields_messages = array();
            $insert_values_messages = array();
            if (!empty($messages)) {
                $fields = array_keys($messages[0]);
                unset($fields[0]);
                foreach ($fields as $v) {
                    $query_fields_messages[] = "`".$v."`";
                }
                $msg_ids = array();
                $rel_message = array();
                foreach ($messages as $message) {
                    $rel_message[$message['Message_ID']] = 0;
                    $message['Subdivision_ID'] = $rel[$message['Subdivision_ID']];
                    $message['Sub_Class_ID'] = $rel_cc[$message['Sub_Class_ID']];
                    $message['Created'] = $message['LastUpdated'] = date("Y-m-d H:i:s");
                    $msg_ids[] = $message['Message_ID'];

                    $query_values = array();
                    foreach ($fields as $v) {
                        $query_values[] = "'".$db->escape($message[$v])."'";
                    }
                    $insert_values_messages[$message['Message_ID']] = "(".join(',', $query_values).")";
                }

                // копирование файлов объекта
                $file_fields = array();
                $tmp = $db->get_results("SELECT `Field_ID` AS `id`, `Field_Name` AS `name` FROM `Field` WHERE `Class_ID`='".$class_id."' AND `TypeOfData_ID` = '6' ", ARRAY_A);
                if ($tmp) {
                    foreach ($tmp as $v) {
                        $file_fields[$v['id']] = $v['name'];
                    }
                    $filetable = $db->get_results("SELECT * FROM `Filetable`
                                   WHERE `Field_ID` IN (".join(',', array_keys($file_fields)).")
                                   AND `Message_ID` IN (".join(',', $msg_ids).") ", ARRAY_A);
                }

                // Поля типа "множественная загрузка" в компоненте
                $multifile_fields = $db->get_results("SELECT `Field_ID`, `Format`, `Field_Name`
                                    FROM `Field`
                                    WHERE Class_ID='".$class_id."'
                                    AND TypeOfData_ID='".NC_FIELDTYPE_MULTIFILE."'", ARRAY_A);

                foreach ($messages as $message) {
                    $message_id = $message['Message_ID'];

                    // событие
                    $nc_core->event->execute(nc_Event::BEFORE_OBJECT_CREATED, $dest_catalogue_id, $rel[$message['Subdivision_ID']], $rel_cc[$message['Sub_Class_ID']], $class_id, 0);

                    $db->query("INSERT INTO `Message".$class_id."` (".join(',', $query_fields_messages).")  VALUES {$insert_values_messages[$message_id]}");
                    if ($db->is_error)
                        throw new nc_Exception_DB_Error($db->last_query, $db->last_error);

                    $inserted_id = $db->insert_id;

                    $k = $message_id;
                    $rel_message[$k] = $inserted_id;

                    // событие
                    $nc_core->event->execute(nc_Event::AFTER_OBJECT_CREATED, $dest_catalogue_id, $rel[$message['Subdivision_ID']], $rel_cc[$message['Sub_Class_ID']], $class_id, $rel_message[$message_id]);

                    if (!empty($file_fields)) {
                        foreach ($file_fields as $field_id => $field_name) {
                            if (($value = $message[$field_name])) {
                                $is_copy = 0;

                                if (!empty($filetable))
                                    foreach ($filetable as $v) {
                                        if ($v['Message_ID'] == $message_id && $v['Field_ID'] == $field_id) {
                                            $file_path = rtrim($nc_core->FILES_FOLDER, '/').$v['File_Path'].$v['Virt_Name'];
                                            $new_name = md5($file_path.date("H:i:s d.m.Y").uniqid("netcat"));
                                            $new_file_path = $rel[$message['Subdivision_ID']]."/".$rel_cc[$message['Sub_Class_ID']];
                                            @copy($file_path, $nc_core->FILES_FOLDER.$new_file_path."/".$new_name);
                                            $to_filetable[] = "('".$db->escape($v['Real_Name'])."', '".$new_name."', '/".$new_file_path."/',
                                   '".$v['File_Type']."', '".$v['File_Size']."', '".$rel_message[$message_id]."',
                                   '".$field_id."', '".$v['Content_Disposition']."')";
                                            $is_copy = 1;
                                        }
                                    }
                                if ($is_copy) continue;

                                $file_data = explode(':', $value);
                                $file_name = $file_data[0];
                                $ext = nc_substr($file_name, nc_strrpos($file_name, ".")); // расширение файла
                                if ($file_data[3]) {
                                    $new_name = nc_get_filename_for_original_fs($file_name, $nc_core->FILES_FOLDER.$rel[$message['Subdivision_ID']]."/".$rel_cc[$message['Sub_Class_ID']]."/");
                                    @copy($nc_core->FILES_FOLDER.$file_data[3], $nc_core->FILES_FOLDER.$rel[$message['Subdivision_ID']]."/".$rel_cc[$message['Sub_Class_ID']]."/".$new_name);
                                    $dt = $file_data[0].":".$file_data[1].":".$file_data[2].":".$rel[$message['Subdivision_ID']]."/".$rel_cc[$message['Sub_Class_ID']]."/".$new_name;
                                    $db->query("UPDATE `Message".$class_id."` SET `".$field_name."` = '".$dt."' WHERE `Message_ID` = '".$rel_message[$message_id]."'  ");
                                } else {
                                    @copy($nc_core->FILES_FOLDER.$field_id."_".$message_id.$ext,
                                        $nc_core->FILES_FOLDER.$field_id."_".$rel_message[$message_id].$ext);
                                }
                            }
                        }
                    }

                    // проходим по каждому полю
                    foreach ((array)$multifile_fields as $field) {
                        $field_id = (int) $field['Field_ID'];

                        $settings_http_path = nc_standardize_path_to_folder($nc_core->HTTP_FILES_PATH . "/multifile/{$field_id}/");
                        $settings_path = nc_standardize_path_to_folder($nc_core->DOCUMENT_ROOT . '/' . $nc_core->SUB_FOLDER . '/' . $settings_http_path);

                        //получаем список файлов
                        $sql = "SELECT `Priority`, `Name`, `Size`, `Path`, `Preview` FROM `Multifield` WHERE `Field_ID` = {$field_id} AND `Message_ID` = {$message_id}";
                        $files = $db->get_results($sql, ARRAY_A);

                        foreach((array)$files as $file) {
                            foreach(array('Path', 'Preview') as $path) {
                                $file_path = $file[$path];

                                if ($file_path) {
                                    $parts = explode('/', nc_standardize_path_to_file($file_path));
                                    $file_name = array_pop($parts);

                                    $new_file_name = nc_get_filename_for_original_fs($file_name, $settings_path);

                                    @copy($settings_path . $file_name, $settings_path . $new_file_name);

                                    $file[$path] = $settings_http_path . $new_file_name;
                                }
                            }

                            $priority = (int)$file['Priority'];
                            $name = $db->escape($file['Name']);
                            $size = (int)$file['Size'];
                            $path = $db->escape($file['Path']);
                            $preview = $db->escape($file['Preview']);

                            $sql = "INSERT INTO `Multifield` (`Field_ID`, `Message_ID`, `Priority`, `Name`, `Size`, `Path`, `Preview`) VALUES " .
                                "({$field_id}, {$rel_message[$message_id]}, {$priority}, '{$name}', {$size}, '{$path}', '{$preview}')";
                            $db->query($sql);
                        }
                    }
                }
            }
        }
    }
    //при копировании разделов проставлялись нули, если поле RecordsPerPage было не заполнено

    $sub_cc_ids = join(', ', (array) $rel_cc);

    if (!empty($sub_cc_ids)) {
        $db->query("UPDATE `Sub_Class`
                    SET `RecordsPerPage` = NULL
                        WHERE `RecordsPerPage`= 0
                          AND `Sub_Class_ID` IN ($sub_cc_ids)");
    }

    // копирование файлов
    $system_fields = $nc_core->get_system_table_fields("Subdivision");

    $file_fields = array();
    if (!empty($system_fields))
            foreach ($system_fields as $v) {
            if ($v['type'] == 6) $file_fields[$v['id']] = $v['name'];
        }

    if (!empty($file_fields)) {
        $filetable = $db->get_results("SELECT * FROM `Filetable`
                                   WHERE `Field_ID` IN (".join(',', array_keys($file_fields)).")
                                   AND `Message_ID` IN (".join(',', $sub_ids).") ", ARRAY_A);

        foreach ($subdivisions as $subdivision) {
            $sub_id = $subdivision['Subdivision_ID'];
            foreach ($file_fields as $field_id => $field_name) {
                if (($value = $subdivision[$field_name])) {
                    $is_copy = 0;

                    if (!empty($filetable))
                            foreach ($filetable as $v) {
                            if ($v['Message_ID'] == $sub_id && $v['Field_ID'] == $field_id) {
                                $file_path = rtrim($nc_core->FILES_FOLDER, '/').$v['File_Path'].$v['Virt_Name'];
                                $new_name = md5($file_path.date("H:i:s d.m.Y").uniqid("netcat"));

                                @copy($file_path, $nc_core->FILES_FOLDER.$rel[$sub_id]."/".$new_name);
                                $to_filetable[] = " ('".$db->escape($v['Real_Name'])."', '".$new_name."', '/".$rel[$sub_id]."/',
                                   '".$v['File_Type']."', '".$v['File_Size']."', '".$rel[$sub_id]."',
                                   '".$field_id."', '".$v['Content_Disposition']."')";
                                // $db->debug();
                                $is_copy = 1;
                            }
                        }
                    if ($is_copy) continue;

                    $file_data = explode(':', $value);
                    $file_name = $file_data[0];
                    $ext = nc_substr($file_name, nc_strrpos($file_name, ".")); // расширение файла
                    if ($file_data[3]) {
                        $new_name = nc_get_filename_for_original_fs($file_name, $nc_core->FILES_FOLDER.$rel[$sub_id]."/");
                        @copy($nc_core->FILES_FOLDER.$file_data[3], $nc_core->FILES_FOLDER.$rel[$sub_id]."/".$new_name);
                        $dt = $file_data[0].":".$file_data[1].":".$file_data[3].":".$rel[$sub_id]."/".$new_name;
                        $db->query("UPDATE `Subdivision` SET `".$field_name."` = '".$dt."' WHERE `Subdivision_ID` = '".$rel[$sub_id]."'  ");
                        //   $db->debug();
                    } else {
                        @copy($nc_core->FILES_FOLDER.$field_id."_".$sub_id.$ext,
                                        $nc_core->FILES_FOLDER.$field_id."_".$rel[$sub_id].$ext);
                    }
                }
            }
        }
    }

    // update filetable
    if (!empty($to_filetable))
            $db->query("INSERT INTO `Filetable` (`Real_Name`, `Virt_Name`, `File_Path`,
                                         `File_Type`, `File_Size`, `Message_ID`, `Field_ID`, `Content_Disposition` )
                          VALUES ".join(',', $to_filetable)." ");


    // рекурсивное копирование подразделов
    if ($with_child)
            foreach ($rel as $k => $v) {
            $childs = $db->get_col("SELECT `Subdivision_ID` FROM `Subdivision` WHERE `Parent_Sub_ID` = '".$k."' ");
            if ($childs) {
                nc_copy_subdivision($childs, 0, $v, $with_child, $with_сс, $with_object, $templates);
            }
        }

    return 0;
}

function nc_copy_completed() {
    $nc_core = nc_Core::get_object();
    $db = $nc_core->db;

    $copy_type = $nc_core->input->fetch_get_post('copy_type');

    $catalogue_id = intval($nc_core->input->fetch_get_post('catalogue_id'));
    $sub_id = intval($nc_core->input->fetch_get_post('sub_id'));

    $dest_catalogue_id = intval($nc_core->input->fetch_get_post('dest_catalogue_id'));
    $dest_sub_id = intval($nc_core->input->fetch_get_post('dest_sub_id'));

    $with_child = intval($nc_core->input->fetch_get_post('with_child'));
    $with_сс = intval($nc_core->input->fetch_get_post('with_сс'));
    $with_object = intval($nc_core->input->fetch_get_post('with_object'));

    $templates = array(
            'sub_name' => $nc_core->input->fetch_get_post('tmpl_sub_name'),
            'sub_keyword' => $nc_core->input->fetch_get_post('tmpl_sub_keyword'),
            'cc_name' => $nc_core->input->fetch_get_post('tmpl_cc_name'),
            'cc_keyword' => $nc_core->input->fetch_get_post('tmpl_cc_keyword')
    );

    if (($copy_type == 'site' && !$catalogue_id) || ($copy_type == 'sub' && !$sub_id)) {
        throw new Exception(TOOLS_COPYSUB_ERROR_PARAM);
    }

    // копирование сайта
    if ($copy_type == 'site') {
        $catalogue = $db->get_row("SELECT * FROM `Catalogue` WHERE `Catalogue_ID` = '".$catalogue_id."' ", ARRAY_A);
        if (empty($catalogue))
                throw new Exception(TOOLS_COPYSUB_ERROR_SITE_NOT_FOUND);
        unset($catalogue['Catalogue_ID']);
        $catalogue['Domain'] = time().$catalogue['Domain'];
        $catalogue['Mirrors'] = '';
        foreach ($catalogue as $k => $v) {
            $query_fields[] = "`".$k."`";
            $query_values[] = "'".$db->escape($v)."'";
        }

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SITE_CREATED, 0);

        $db->query("INSERT INTO `Catalogue` (".join(',', $query_fields).") VALUES (".join(',', $query_values).") ");
        if ($db->is_error)
                throw new nc_Exception_DB_Error($db->last_query, $db->last_error);
        $new_catalogue_id = $db->insert_id;
        $nc_core->event->execute(nc_Event::AFTER_SITE_CREATED, $new_catalogue_id);

        if ($with_child) {
            $sub_ids = $db->get_col("SELECT `Subdivision_ID` FROM `Subdivision` WHERE `Parent_Sub_ID` = '0' AND `Catalogue_ID` = '".$catalogue_id."'  ");
            if ($sub_ids)
                    nc_copy_subdivision($sub_ids, $new_catalogue_id, 0, $with_child, $with_сс, $with_object, $templates);
        }
    }
    else {
        nc_copy_subdivision($sub_id, $dest_catalogue_id, $dest_sub_id, $with_child, $with_сс, $with_object, $templates);
    }

    return 0;
}