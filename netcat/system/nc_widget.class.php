<?php

class nc_Widget extends nc_System {

    protected $core;

    public function __construct() {
        parent::__construct();
        $this->core = nc_Core::get_object();
        $this->widgetclasses = array();
        $this->widgetclasses_keys = array();
        $this->widgetclasses_loaded = false;
        $this->widgets = array();
        $this->widgets_keys = array();
        $this->widgets_loaded = false;
        $this->core->register_macrofunc('NC_WIDGET_SHOW', 'show_macrofunc', $this);
    }

    public function validate_keyword($keyword) {
        return nc_preg_match("/^[_a-z0-9-]+$/i", $keyword);
    }

    public function load($load = 'widgetclasses') {
        switch ($load) {
            case 'widgetclasses':
                $widgetclasses = $this->core->db->get_results("
                    SELECT 
                            wc.`Widget_Class_ID`,
                            wc.`Name`,
                            wc.`Keyword`,
                            wc.`Description`,
                            wc.`Category`,
                            wc.`InDevelop`,
                            wc.`Template`,
                            wc.`Settings`,
                            wc.`AddForm`,
                            wc.`EditForm`,
                            wc.`User_ID`,
                            wc.`LastUser_ID`,
                            wc.`Created`,
                            wc.`LastUpdated`,
                            wc.`WidgetDisallow`,
                            wc.`Update`,
                            wc.`File_Path`,
                            wc.`File_Mode`,
                    count(f.`Field_ID`) AS `Fields` 
                    FROM `Widget_Class` AS wc
                    LEFT JOIN `Field` AS f 
                    ON wc.`Widget_Class_ID` = f.`Widget_Class_ID`
                    GROUP BY wc.`Widget_Class_ID`
                    ");
                if (!empty($widgetclasses)) {
                    $wc_edit = new nc_widget_editor($this->core->WIDGET_TEMPLATE_FOLDER, $this->core->db);
                    foreach ($widgetclasses as $wc) {
                        if ($wc->File_Mode) {
                            $wc_edit->load($wc->Widget_Class_ID, $wc->File_Path)->fill_fields();
                        }
                        $this->widgetclasses[$wc->Widget_Class_ID] = array(
                                'Widget_Class_ID' => $wc->Widget_Class_ID,
                                'Name' => $wc->Name,
                                'Keyword' => $wc->Keyword,
                                'Description' => $wc->Description,
                                'Category' => $wc->Category,
                                'InDevelop' => $wc->InDevelop,
                                'Template' => $wc->File_Mode ? $wc_edit->get_field('Template') : $wc->Template,
                                'Settings' => $wc->File_Mode ? $wc_edit->get_field('Settings') : $wc->Settings,
                                'AddForm' => $wc->File_Mode ? $wc_edit->get_field('AddForm') : $wc->AddForm,
                                'EditForm' => $wc->File_Mode ? $wc_edit->get_field('EditForm') : $wc->EditForm,
                                'User_ID' => $wc->User_ID,
                                'LastUser_ID' => $wc->LastUser_ID,
                                'Created' => $wc->Created,
                                'LastUpdated' => $wc->LastUpdated,
                                'WidgetDisallow' => $wc->WidgetDisallow,
                                'Update' => $wc->Update,
                                'File_Path' => $wc->File_Path,
                                'File_Mode' => $wc->File_Mode,
                                'Fields' => $wc->Fields);

                        if (!in_array($wc->Keyword, $this->widgetclasses_keys)) {
                            $this->widgetclasses_keys[$wc->Keyword] = (int) $wc->Widget_Class_ID;
                        }
                    }
                    $this->widgetclasses_loaded = true;
                }
                break;
            case 'widgets':
                $widgets = $this->core->db->get_results("SELECT * FROM `Widget`");
                if (!empty($widgets)) {
                    foreach ($widgets as $w) {
                        $this->widgets[$w->Widget_ID] = array(
                                'Widget_ID' => $w->Widget_ID,
                                'Widget_Class_ID' => $w->Widget_Class_ID,
                                'Name' => $w->Name,
                                'Keyword' => $w->Keyword,
                                'Result' => $w->Result,
                                'User_ID' => $w->User_ID,
                                'LastUser_ID' => $w->LastUser_ID,
                                'Created' => $w->Created,
                                'LastUpdated' => $w->LastUpdated);

                        if (!in_array($w->Keyword, $this->widgets_keys)) {
                            $this->widgets_keys[$w->Keyword] = (int) $w->Widget_ID;
                        }
                    }
                    $this->widgets_loaded = true;
                }
                break;
        }
    }

    public function get_where($id, $widget = 0) {
        return " WHERE " . (is_int($id) ? ($widget ? "`Widget_ID`='" . $id . "'" : "`Widget_Class_ID`='" . $id . "'") : "`Keyword`='" . $this->core->db->escape($id) . "'");
    }

    public function get_id($id, $widget = 0) {
        return (is_int($id) ? $id : ($widget ? $this->widgets_keys[$id] : $this->widgetclasses_keys[$id]));
    }

    public function assistant($action, $params = '', $widget = 0) {
        global $AUTH_USER_ID;
        switch ($action) {
            case 'select':
                if ($params[1] == '') {
                    $result = ($widget ? (is_int($params[0]) ? $this->widgets[$params[0]] : $this->widgets[$this->widgets_keys[$params[0]]]) : (is_int($params[0]) ? $this->widgetclasses[$params[0]] : $this->widgetclasses[$this->widgetclasses_keys[$params[0]]]));
                } else {
                    $result = ($widget ? (is_int($params[0]) ? $this->widgets[$params[0]][$params[1]] : $this->widgets[$this->widgets_keys[$params[0]]][$params[1]]) : (is_int($params[0]) ? $this->widgetclasses[$params[0]][$params[1]] : $this->widgetclasses[$this->widgetclasses_keys[$params[0]]][$params[1]]));
                }
                return $result;
                break;
            case 'add':
                if (!$widget) {
                    $i = 0;
                    foreach ($params[1] as $p) {
                        $this->widgetclasses[$params[0]][$p] = $params[2][$i];
                        $i++;
                    }
                    $this->widgetclasses[$params[0]]['User_ID'] = $AUTH_USER_ID;
                    $this->widgetclasses[$params[0]]['LastUser_ID'] = $AUTH_USER_ID;
                    $this->widgetclasses[$params[0]]['Created'] = date('Y-m-d H:i:s');
                    $this->widgetclasses[$params[0]]['LastUpdated'] = date('Y-m-d H:i:s');
                } else {
                    $i = 0;
                    foreach ($params[1] as $p) {
                        $this->widgets[$params[0]][$p] = $params[2][$i];
                        $i++;
                    }
                    $this->widgets[$params[0]]['User_ID'] = $AUTH_USER_ID;
                    $this->widgets[$params[0]]['LastUser_ID'] = $AUTH_USER_ID;
                    $this->widgets[$params[0]]['Created'] = date('Y-m-d H:i:s');
                    $this->widgets[$params[0]]['LastUpdated'] = date('Y-m-d H:i:s');
                }
                break;
            case 'edit':
                if (!$widget) {
                    foreach ($params[1] as $p => $value) {
                        if (is_int($params[0])) {
                            $this->widgetclasses[$params[0]][$p] = $value;
                        } else {
                            $this->widgetclasses[$this->widgetclasses_keys[$params[0]]][$p] = $value;
                        }
                    }
                } else {
                    foreach ($params[1] as $p => $value) {
                        if (is_int($params[0])) {
                            $this->widgets[$params[0]][$p] = $value;
                        } else {
                            $this->widgets[$this->widgets_keys[$params[0]]][$p] = $value;
                        }
                    }
                }
                break;
            case 'drop':
                if (!$widget) {
                    if (is_int($params)) {
                        unset($this->widgetclasses[$params]);
                    } else {
                        unset($this->widgetclasses[$this->widgetclasses_keys[$params]]);
                    }
                    unset($this->widgetclasses_keys[$params]);
                } else {
                    if (is_int($params)) {
                        unset($this->widgets[$params]);
                    } else {
                        unset($this->widgets[$this->widgets_keys[$params]]);
                    }
                    unset($this->widgets_keys[$params]);
                }
                break;
            case 'list':
                if (!$widget) {
                    foreach ($this->widgetclasses as $wc) {
                        if ($wc['File_Mode'] == +$_REQUEST['fs']) {
                            $result[$wc['Widget_Class_ID']] = array('Name' => $wc['Name'], 'Keyword' => $wc['Keyword'], 'Description' => $wc['Description'], 'Category' => $wc['Category'], 'InDevelop' => $wc['InDevelop'], 'Template' => $wc['Template'], 'Settings' => $wc['Settings'], 'AddForm' => $wc['AddForm'], 'EditForm' => $wc['EditForm'], 'User_ID' => $wc['User_ID'], 'LastUser_ID' => $wc['LastUser_ID'], 'Created' => $wc['Created'], 'LastUpdated' => $wc['LastUpdated'], 'WidgetDisallow' => $wc['WidgetDisallow'], 'Update' => $wc['Update'], 'Fields' => $wc['Fields']);
                        }
                    }
                } else {
                    foreach ($this->widgets as $w) {
                        if ($wc->File_Mode == +$_REQUEST['fs']) {
                            $result[$w['Widget_Class_ID']] = array('Widget_Class_ID' => $w['Widget_Class_ID'], 'Name' => $w['Name'], 'Keyword' => $w['Keyword'], 'Result' => $w['Result'], 'User_ID' => $w['User_ID'], 'LastUser_ID' => $w['LastUser_ID'], 'Created' => $w['Created'], 'LastUpdated' => $w['LastUpdated']);
                        }
                    }
                }
                return $result;
        };
    }

    /**
     * Добавление нового виджета-компонента
     * @param array поля виджет-компонентв
     * @param bool импортируем ли?
     * @return integer id винджет-компонента
     */
    public function add_widgetclass($params, $import = 0, $base_widgetclass_id = '') {
        global $AUTH_USER_ID;
        $File_Mode = +$_REQUEST['fs'];
        $nc_core = $this->core;
        $db = $this->core->db;

        if (!$this->widgetclasses_loaded)
            $this->load();

        if ($this->widgetclasses_keys[$params['Keyword']] && $import) {
            $params['Keyword'] = $params['Keyword'] . "_import";
            return $this->add_widgetclass($params, 1);
        }

        if ($this->widgetclasses_keys[$params['Keyword']] && !$import) {
            return false;
        }

        $this->core->event->execute('addWidgetClassPrep', 0);

        if($File_Mode) {
            $fs_params = array(
                    'Template' => $params['Template'],
                    'Settings' => $params['Settings'],
                    'AddForm' => $params['AddForm'],
                    'EditForm' => $params['EditForm']
            );
            $params['Template'] = $params['Settings'] = $params['AddForm'] = $params['EditForm'] = '';
        }

        foreach ($params as $p => $value) {
            $query_key[] = "`" . $p . "`";
            $query_key_assist[] = $p;
            if ($p == 'Keyword' && $import) {
                $query_par[] = "'" . $db->escape(addslashes($params[$p])) . "'";
                $query_par_assist[] = $db->escape(addslashes($params[$p]));
            } else {
                $query_par[] = "'" . $db->escape(addslashes($params[$p])) . "'";
                $query_par_assist[] = $db->escape(addslashes($params[$p]));
            }
        }
        $query = "INSERT INTO `Widget_Class` (" . implode(',', $query_key) . ",`User_ID`,`LastUser_ID`,`Created`,`LastUpdated`) VALUES (" . implode(',', $query_par) . ",'" . $AUTH_USER_ID . "','" . $AUTH_USER_ID . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "')";

        $db->query($query);
        $insert_id = $db->insert_id;
        $this->assistant('add', array($db->insert_id, $query_key_assist, $query_par_assist));

        if ($base_widgetclass_id > 0) {
            $fields = $db->get_results("SELECT * FROM `Field` WHERE `Widget_Class_ID`='" . $base_widgetclass_id . "'");
            $query = "INSERT INTO `Field` (`Class_ID`, `Field_Name`, `Description`, `TypeOfData_ID`, `Format`, `NotNull`, `Priority`, `DoSearch`, `DefaultState`, `Inheritance`, `System_Table_ID`, `Widget_Class_ID`, `TypeOfEdit_ID`, `Checked`) VALUES ";
            foreach ($fields as $f) {
                $query_arr[] = "(" . $f->Class_ID . ", '" . $f->Field_Name . "', '" . $f->Description . "', " . $f->TypeOfData_ID . ", '" . $f->Format . "', " . $f->NotNull . ", " . $f->Priority . ", " . $f->DoSearch . ", '" . $f->DefaultState . "', " . $f->Inheritance . ", " . $f->System_Table_ID . ", " . $insert_id . ", " . $f->TypeOfEdit_ID . ", " . $f->Checked . ")";
            }
            $query .= implode(",", $query_arr);
            $db->query($query);
        }


        if($File_Mode) {
            $widget_editor = new nc_widget_editor($nc_core->WIDGET_TEMPLATE_FOLDER, $nc_core->db);
            $widget_editor->load($insert_id);
            $widget_editor->save_new($fs_params);
        }

        $this->core->event->execute('addWidgetClass', $db->insert_id);

        return $insert_id;
    }

    /**
     * Изменение существующего виджета-компонента
     * @param mixed номер виджет-компонента или его keyword
     * @param array поля виджет-компонента
     * @return bool
     */
    public function edit_widgetclass($id, $params) {
        global $AUTH_USER_ID;

        $nc_core = $this->core;
        $db = $nc_core->db;

        if (!$this->widgetclasses_loaded) {
            $this->load();
        }

        $File_Mode = $this->widgetclasses[$id]['File_Mode'];

        if ($File_Mode) {
            $widget_editor = new nc_widget_editor($nc_core->WIDGET_TEMPLATE_FOLDER, $db);
            $widget_editor->load($id);
            $widget_editor->save_fields(null, true);
            $fs_params = $params;
            $params['AddForm'] = $params['EditForm'] = '';
        }

        foreach ($params as $p => $value) {
            $query[] = "`" . $p . "`='" . $this->core->db->escape(addslashes($params[$p])) . "'";
        }

        $this->core->event->execute('editWidgetClassPrep', $this->get_id($id));

        $query = "UPDATE `Widget_Class` SET " . implode(',', $query) . $this->get_where($id) . "";
        $this->assistant('edit', array($id, $File_Mode ? $fs_params : $params));
        $db->query($query);
        $this->core->event->execute('editWidgetClass', $this->get_id($id));

        if (!$this->widgets_loaded) {
            $this->load('widgets');
        }

        foreach ($this->widgets as $w) {
            if ($w['Widget_Class_ID'] == $id) {
                $this->generate(intval($w['Widget_Class_ID']), '', 1, intval($w['Widget_ID']));
            }
        }

        return true;
    }

    /**
     * Удаление виджета-компонента
     * @param mixed номер виджет-компонента или его keyword
     * @return bool
     */
    public function drop_widgetclass($id) {
        if (!$this->widgetclasses_loaded)
            $this->load();
        $this->core->db->query("DELETE FROM Widget_Class" . $this->get_where($id) . "");
        $this->assistant('drop', $id);

        $widgets = $this->core->db->get_results("SELECT `Widget_ID` FROM `Widget` WHERE `Widget_Class_ID`='" . intval($id) . "'", ARRAY_A);
        $this->core->event->execute('dropWidgetClassPrep', $this->get_id($id));
        if ($widgets) {
            foreach ($widgets as $w) {
                $this->drop_widget(intval($w['Widget_ID']));
            }
        }

        $this->core->event->execute('dropWidgetClass', $this->get_id($id));
        return true;
    }

    /**
     * Получение информации о виджете-компоненте
     * @param mixed номер виджет-компонента или его keyword
     * @param string выводит одно поле по названию
     * @return array поля виджет-компонента
     */
    public function get_widgetclass($id, $item = '') {
        if (!$this->widgetclasses_loaded)
            $this->load();
        return $this->assistant('select', array($id, $item));
    }

    /**
     * Список всех виджет-компонентов
     * @return array все виджет-компоненты
     */
    public function list_widgetclass() {
        if (!$this->widgetclasses_loaded)
            $this->load();
        return $this->assistant('list');
    }

    /**
     * Добавление нового виджета
     * @param mixed номер виджет-компонента или его keyword
     * @param string выводит одно поле по названию
     * @return integer
     */
    public function add_widget($params, $fields = 0) {
        global $AUTH_USER_ID;
        $db = $this->core->db;
        if (!$this->widgets_loaded)
            $this->load('widgets');
        $query = "INSERT INTO `Widget` (`Widget_Class_ID`,`Name`,`Keyword`,`User_ID`,`LastUser_ID`,`Created`,`LastUpdated`) VALUES ('" . $params['Widget_Class_ID'] . "','" . $params['Name'] . "','" . $params['Keyword'] . "','" . $AUTH_USER_ID . "','" . $AUTH_USER_ID . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "')";

        $this->core->event->execute('addWidgetPrep', intval($params['Widget_Class_ID']), 0);

        $db->query($query);
        $widget_id = $db->insert_id;

        foreach ($params as $p => $value) {
            $query_key_assist[] = $p;
            $query_par_assist[] = $db->escape(addslashes($params[$p]));
        }
        $this->assistant('add', array($widget_id, $query_key_assist, $query_par_assist), 1);

        if ($fields) {
            foreach ($fields as $id => $value) {
                $db->query("INSERT INTO `Widget_Field` (`Widget_ID`, `Field_ID`, `Value`) VALUES ('" . $widget_id . "', '" . $id . "', '" . $value . "')");
                $generate_where[] = "`Field_ID`='" . $id . "'";
                $generate_values[$id] = $value;
            }

            $field_names = $db->get_results("SELECT `Field_Name`, `Field_ID` FROM `Field` WHERE (" . implode(' OR ', $generate_where) . ")");
            foreach ($field_names as $fn) {
                $generate_fields[$fn->Field_Name] = $generate_values[$fn->Field_ID];
            }
        }

        $this->generate(intval($params['Widget_Class_ID']), $generate_fields, 1, $widget_id);

        $this->core->event->execute('addWidget', intval($params['Widget_Class_ID']), $widget_id);
        return $widget_id;
    }

    /**
     * Редактирование виджета
     * @param mixed номер виджета или его keyword
     * @param array поля виджета
     * @param bool есть ли поля для редактирования?
     * @return bool
     */
    public function edit_widget($widget_id, $params, $fields = 0) {
        global $AUTH_USER_ID;
        $db = $this->core->db;
        if (!$this->widgets_loaded)
            $this->load('widgets');
        $query = "UPDATE `Widget` SET `Widget_Class_ID`='" . $params['Widget_Class_ID'] . "', `Name`='" . $params['Name'] . "', `Keyword`='" . $params['Keyword'] . "'" . $this->get_where($widget_id, 1) . "";

        $this->core->event->execute('editWidgetPrep', intval($params['Widget_Class_ID']), $this->get_id($widget_id, 1));

        $db->query($query);
        $this->assistant('edit', array($widget_id, $params));

        if ($fields) {
            foreach ($fields as $id => $value) {
                $db->query("UPDATE `Widget_Field` SET `Value`='" . $value . "' WHERE `Widget_ID`='" . intval($widget_id) . "' AND `Field_ID`='" . $id . "'");
                $generate_where[] = "`Field_ID`='" . $id . "'";
                $generate_values[$id] = $value;
            }
        }
        if ($generate_where) {
            $field_names = $db->get_results("SELECT `Field_Name`, `Field_ID` FROM `Field` WHERE (" . implode(' OR ', $generate_where) . ")");
            foreach ($field_names as $fn) {
                $generate_fields[$fn->Field_Name] = $generate_values[$fn->Field_ID];
            }
        }
        $this->generate(intval($params['Widget_Class_ID']), $generate_fields, 1, $widget_id);

        $this->core->event->execute('editWidget', intval($params['Widget_Class_ID']), $this->get_id($widget_id, 1));
        return true;
    }

    /**
     * Удаление виджета
     * @param mixed номер виджета или его keyword
     * @return bool
     */
    public function drop_widget($id) {
        if (!$this->widgets_loaded)
            $this->load('widgets');
        $new_id = is_int($id) ? "`Widget_ID`='" . $id . "'" : "`Keyword`='" . $this->core->db->escape($id) . "'";
        $wc_event_id = $this->core->db->get_var("SELECT `Widget_Class_ID` FROM `Widget` WHERE " . $new_id);
        $where = "WHERE `Widget_ID`='" . $this->core->db->get_var("SELECT `Widget_ID` FROM `Widget` WHERE " . $new_id);
        $this->core->event->execute('dropWidgetPrep', $wc_event_id, $this->get_id($id, 1));
        $this->core->db->query("DELETE FROM `Widget` " . $where . "'");
        $this->core->db->query("DELETE FROM `Widget_Field` " . $where . "'");
        $this->assistant('drop', $id, 1);
        $this->core->event->execute('dropWidget', $wc_event_id, $this->get_id($id, 1));
        return true;
    }

    /**
     * Получение информации о виджете
     * @param integer номер виджета
     * @return array поля виджета
     */
    public function get_widget($id) {
        $db = $this->core->db;
        if (!$this->widgets_loaded)
            $this->load('widgets');
        return $this->assistant('select', array($id, $item), 1);
    }

    /**
     * Генерация виджета
     * @param mixed номер виджета или его keyword
     * @param array поля виджета
     * @param bool обновлять ли экземпляр виджета
     * @param integer id обновляемого виджета
     * @return string html-код виджета
     */
    public function generate($id, $fields = '', $static = 0, $widget_id = 0) {
        $nc_core = $this->core;
        $db = $nc_core->db;
        $params = $this->get_widgetclass($id);
        $res = $db->get_results("SELECT `Field_Name`, `DefaultState` FROM `Field` WHERE `Widget_Class_ID`='" . $params['Widget_Class_ID'] . "'");
        if ($res)
            foreach ($res as $r) {
                $new_fields .= "\$f_" . $r->Field_Name . "= '" . $r->DefaultState . "';";
            }
        if ($fields != '') {
            foreach ($fields as $f => $val) {
                $new_fields .= "\$f_" . $f . "= '" . $val . "';";
            }
        }

        $result = '';
        eval($new_fields);

        if ($params['File_Mode']) {
            $widget_view = new nc_widget_view($nc_core->WIDGET_TEMPLATE_FOLDER, $db);
            $widget_view->load($params['Widget_Class_ID']);

            include $widget_view->get_field_path('Settings');
            ob_start();
            include $widget_view->get_field_path('Template');
            $result .= ob_get_clean();
        } else {
            eval($params['Settings']);
            eval("\$result .= \"" . $params['Template'] . "\";");
        }

        if ($static) {
            $db->query("UPDATE `Widget` SET `LastUpdated` = NOW(), `Result`='" . $this->core->db->escape($result) . "'" . $this->get_where($widget_id, 1) . "");
        }

        return $result;
    }

    /**
     * Получить код виджета
     * @param mixed номер виджета или его keyword
     * @param bool показ для макрофункции?
     * @return string html-код виджета
     */
    public function show($id, $for_macrofunc = 0) {
        $db = $this->core->db;
        $where = is_int($id) ? "`Widget_ID` = " . $id : "`Keyword` = '" . $db->escape($id) . "'";

        $SQL = "SELECT `w`.`Result`,
                       `w`.`Widget_Class_ID`,
                       `w`.`Widget_ID`,
                       `w`.`LastUpdated`,
                       `wc`.`Update`,
                       `wc`.`WidgetDisallow`
                     FROM `Widget` AS w,
                          `Widget_Class` AS wc
                         WHERE `wc`.`Widget_Class_ID` = `w`.`Widget_Class_ID`
                           AND `w`." . $where . "
                             LIMIT 1";
        $result = $db->get_row($SQL, ARRAY_A);

        // запрет на встраивание в текст виджета
        if ($for_macrofunc && $result['WidgetDisallow']) {
            return "";
        }

        $ret_val = $result['Result'];
        // виджет надо обновить
        if ((intval($result['Update'] != 0)) && (time() - intval($result['Update']) * 60 >= strtotime($result['LastUpdated']))) {
            $fields = $db->get_results("SELECT `f`.`Field_Name`, `wf`.`Value`
                                  FROM `Widget_Field` AS wf, `Field` AS f
                                  WHERE `wf`.`Field_ID` = `f`.`Field_ID` AND `wf`.`Widget_ID`= " . $result['Widget_ID'] . "", ARRAY_A);
            foreach ($fields as $f) {
                $new_fields[$f['Field_Name']] = $f['Value'];
            }
            $ret_val = $this->generate(intval($result['Widget_Class_ID']), $new_fields, 1, intval($result['Widget_ID']));
        }
        return $ret_val;
    }

    /**
     * Метод, соответсвующий NC_WIDGET_SHOW
     * @param mixed номер виджета или его keyword
     * @return string html-код виджета
     */
    public function show_macrofunc($id) {
        return $this->show($id, 1);
    }

}

?>