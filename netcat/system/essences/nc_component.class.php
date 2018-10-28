<?php

class nc_Component extends nc_Essence {

    const MAX_KEYWORD_LENGTH = 64;

    protected $db;
    protected $_class_id, $_system_table_id;
    protected $_fields, $_field_count;
    // массив полей, попадающих в запрос, и переменные, им соответствующие
    protected $_fields_query, $_fields_vars, $_fields_vars_columns;
    protected $_joins;
    // все используемые поля всех компонентов
    protected static $all_fields;
    protected static $event_fields = array();

    protected $ids_by_keyword = array();
    protected $reserved_keywords = array('sys', 'table', 'Catalogue', 'Subdivision', 'User', 'Template');

    protected $last_modified_type = 'content';

    /**
     * Для системных таблиц:
     *   $user_table = new nc_component(0, 3)
     *   или
     *   $user_table = new nc_component('User');
     *
     *
     * @param int|string $class_id integer or 'Catalogue|Subdivision|User|Template'
     * @param int $system_table_id
     */
    public function __construct($class_id = 0, $system_table_id = 0) {
        parent::__construct();

        $this->essence = "Class";

        $nc_core = nc_Core::get_object();

        if (is_object($nc_core->db)) {
            $this->db = $nc_core->db;
        }

        $system_table_id_by_name = $nc_core->get_system_table_id_by_name($class_id);
        if ($system_table_id_by_name) {
            $system_table_id = $system_table_id_by_name;
            $class_id = 0;
        }

        $class_id = intval($class_id);
        $system_table_id = intval($system_table_id);

        // загружаем конкретный компонент
        if ($class_id || $system_table_id) {
            $this->_class_id = $class_id;
            $this->_system_table_id = $system_table_id;
        }

        $this->register_event_listeners();
    }

    /**
     * Обработчики для обновления и сброса кэша
     */
    protected function register_event_listeners() {
        $event = nc_core::get_object()->event;
        $clear_cache = array($this, 'clear_cache');
        $event->add_listener(nc_event::AFTER_COMPONENT_UPDATED, $clear_cache);
        $event->add_listener(nc_event::AFTER_COMPONENT_TEMPLATE_UPDATED, $clear_cache);
    }

    /**
     * @param $id
     * @param string $item
     * @param bool $reset
     * @return null|string|array
     * @throws nc_Exception_Class_Doesnt_Exist
     */
    public function get_by_id($id_or_keyword, $item = '', $reset = false) {
        $nc_core = nc_Core::get_object();

        $id = $keyword = null;
        if (ctype_digit((string)$id_or_keyword)) {
            $id = (int)$id_or_keyword;
        }
        else if (preg_match('/^\w+$/', $id_or_keyword)) {
            $keyword = $id_or_keyword;
            if (isset($this->ids_by_keyword[$keyword])) {
                $id = $this->ids_by_keyword[$keyword];
            }
        }

        if (!$id && !$keyword) {
            return;  //в этом случае был бы возвращен null, но в кэш загружены все компоненты без своих шаблонов
        }

        $res = array();

        if (isset($this->data[$id]) && !$reset) {
            $res = $this->data[$id];
        }

        if (empty($res)) {
            $nc_core->clear_cache_on_low_memory();

            if (!$id) {
                $res = $nc_core->db->get_results(
                    "SELECT `template`.*
                       FROM `Class` AS `template`
                            LEFT JOIN `Class` AS `parent`
                            ON (`template`.`ClassTemplate` = `parent`.`Class_ID`)
                      WHERE (`template`.`Keyword` = '$keyword' AND `template`.`ClassTemplate` = 0)
                         OR `parent`.`Keyword` = '$keyword'",
                    ARRAY_A);
            }
            else {
                $res = $nc_core->db->get_results("SELECT * FROM `Class` WHERE `Class_ID` = $id OR `ClassTemplate` = $id", ARRAY_A);
            }

            if (empty($res)) {
                throw new nc_Exception_Class_Doesnt_Exist($id ?: $keyword);
            }

            for ($i = 0; $i < $nc_core->db->num_rows; $i++) {
                if (false && $res[$i]['File_Mode']) { //for debug
                    $class_editor = new nc_tpl_component_editor($nc_core->CLASS_TEMPLATE_FOLDER, $nc_core->db);
                    $class_editor->load($res[$i]['Class_ID'], $res[$i]['File_Path'], $res[$i]['File_Hash']);
                    $class_editor->fill_fields();
                    $res[$i] = array_merge($res[$i], $class_editor->get_fields());
                }
                $this->data[$res[$i]['Class_ID']] = $res[$i];
                $this->data[$res[$i]['Class_ID']]['_nc_final'] = 0;
                $this->data[$res[$i]['Class_ID']]['Real_Class_ID'] = $res[$i]['Class_ID'];

                if ($res[$i]['ClassTemplate'] == 0) {
                    if (!$id) {
                        $id = $res[$i]['Class_ID'];
                    }
                    if ($res[$i]['Keyword']) {
                        $this->ids_by_keyword[$res[$i]['Keyword']] = $res[$i]['Class_ID'];
                    }
                }
            }
        }

        if (!$this->data[$id]['_nc_final'] && $this->data[$id]['ClassTemplate']) {
            $component_id = $this->data[$id]['ClassTemplate'];
            // визуальные настройки наследуются [целиком] от компонента, если не заданы
            if (!$this->data[$id]['CustomSettingsTemplate']) {
                $this->data[$id]['CustomSettingsTemplate'] = $this->get_by_id($component_id, 'CustomSettingsTemplate');
            }

            if (!$this->data[$component_id]['File_Mode']) {
                $macrovars = array('%Prefix%' => 'FormPrefix',
                    '%Record%' => 'RecordTemplate',
                    '%Suffix%' => 'FormSuffix',
                    '%Full%' => 'RecordTemplateFull',
                    '%Settings%' => 'Settings',
                    '%TitleTemplate%' => 'TitleTemplate',
                    '%Order%' => 'SortBy',
                    '%AddForm%' => 'AddTemplate',
                    '%AddCond%' => 'AddCond',
                    '%AddAction%' => 'AddActionTemplate',
                    '%EditForm%' => 'EditTemplate',
                    '%EditCond%' => 'EditCond',
                    '%EditAction%' => 'EditActionTemplate',
                    '%DeleteForm%' => 'DeleteTemplate',
                    '%DeleteCond%' => 'DeleteCond',
                    '%DeleteAction%' => 'DeleteActionTemplate',
                    '%SearchForm%' => 'FullSearchTemplate',
                    '%Search%' => 'SearchTemplate',
                    '%CheckAction%' => 'CheckActionTemplate');

                foreach ($macrovars as $var => $field) {
                    if (strpos($this->data[$id][$field], $var) !== false) {
                        $this->data[$id][$field] = str_replace($var, $this->get_by_id($component_id, $field), $this->data[$id][$field]);
                    }
                }
            }
        }

        $this->data[$id]['_nc_final'] = 1;

        if ($item && is_array($this->data[$id])) {
            return array_key_exists($item, $this->data[$id]) ? $this->data[$id][$item] : "";
        }
        return $this->data[$id];
    }

    /**
     * Возвращает шаблон компонента по ключевому слову этого шаблона
     * @param string|int $component_id  ID или ключевое слово компонента
     * @param string|int $template_keyword   ID или ключевое слово шаблона компонента
     * @param null|string $item  Возвращаемое свойство
     * @param bool $reset  Сброс кэша
     * @return array|mixed|null
     */
    public function get_component_template_by_keyword($component_id, $template_keyword, $item = '', $reset = false) {
        if (ctype_digit((string)$template_keyword)) { // число считаем идентификатором
            return $this->get_by_id($template_keyword, $item, $reset);
        }

        // загрузка + получение ID компонента по ключевому слову
        $component_id = $this->get_by_id($component_id, 'Class_ID', $reset);

        foreach ($this->data as $row) {
            if ($row['ClassTemplate'] == $component_id && $row['Keyword'] == $template_keyword) {
                if ($item) {
                    return isset($row[$item]) ? $row[$item] : null;
                }
                else {
                    return $row;
                }
            }
        }

        return null;
    }

    /**
     * Возвращает шаблоны компонентов указанного типа для компонента
     * @param int $component_id
     * @param string|array|null $template_types
     * @param bool $reset
     * @return array
     */
    public function get_component_templates($component_id, $template_types = null, $reset = false) {
        // загрузка + получение ID компонента по ключевому слову
        $component_id = $this->get_by_id($component_id, 'Class_ID', $reset);

        $result = array();
        foreach ($this->data as $row) {
            if ($row['ClassTemplate'] == $component_id) {
                $type_match =
                    !$template_types ||
                    (is_array($template_types) && in_array($row['Type'], $template_types)) ||
                    ($row['Type'] == $template_types);
                if ($type_match) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    /**
     * Проверяет, есть ли у компонента или хотя бы одного его шаблона флаг IsOptimizedForMultipleMode
     * @param int $component_id
     * @return bool
     */
    public function is_optimized_for_multiple_mode($component_id) {
        if ($this->get_by_id($component_id, 'IsOptimizedForMultipleMode')) {
            return true;
        }
        $parent_id = $this->get_by_id($component_id, 'ClassTemplate');
        if (!$parent_id) { // Это компонент, а не шаблон. Проверяем шаблоны компонента
            $templates = $this->get_component_templates($component_id, 'useful');
            foreach ($templates as $template) {
                if ($template['IsOptimizedForMultipleMode']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $sub_class_id
     * @param $class_id
     * @param $nc_ctpl
     * @param int $nc_tpl_in_cc
     * @param string $type
     * @return bool|null|string|array
     */
    public function get_for_cc($sub_class_id, $class_id, $nc_ctpl, $nc_tpl_in_cc = 0, $type = '') {
        $nc_core = nc_Core::get_object();

        $class_id = intval($class_id);

        $this->get_by_id($class_id);

        if (!$type) {
            if ($nc_core->admin_mode) {
                $type = 'admin_mode';
            }
            if ($nc_core->inside_admin) {
                $type = 'inside_admin';
            }
            if ($nc_core->get_page_type() == 'rss') {
                $type = 'rss';
            }
            if ($nc_core->get_page_type() == 'xml') {
                $type = 'xml';
            }
            if ($nc_ctpl === 'title') {
                $type = 'title';
                $nc_ctpl = 0;
            }
        }

        // выбор по шаблону nc_ctpl переданному в s_list
        if ($nc_ctpl && $nc_ctpl !== 'title') {
            foreach ($this->data as $id => $v) {
                if ($v['Class_ID'] == $nc_ctpl || ($v['ClassTemplate'] == $class_id && $v['Keyword'] == $nc_ctpl)) {
                    return $this->get_by_id($v['Class_ID']);
                }
            }
        }

        // поиск по типу специального шаблона компонента
        if ($type) {
            foreach ($this->data as $id => $v) {
                if ($v['ClassTemplate'] == $class_id && $v['Type'] == $type) {
                    return $this->get_by_id($v['Class_ID']);
                }
            }
        }

        // выбор по шаблону в инфоблоке источнике для s_list
        if ($nc_tpl_in_cc) {
            foreach ($this->data as $id => $v) {
                if ($v['Class_ID'] == $nc_tpl_in_cc) {
                    return $this->get_by_id($v['Class_ID']);
                }
            }
        }

        // выбор по номеру компонента если никакие шаблоны не подошли
        foreach ($this->data as $id => $v) {
            if (!$nc_ctpl && $v['Class_ID'] == $class_id) {
                return $this->get_by_id($v['Class_ID']);
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function get_fields_query() {
        if (empty($this->_fields_query)) {
            $this->make_query();
        }

        return join(', ', $this->_fields_query);
    }

    /**
     * @return string
     */
    public function get_fields_vars() {
        if (empty($this->_fields_vars)) {
            $this->make_query();
        }

        return join(', ', $this->_fields_vars);
    }

    /**
     * @throws nc_Exception_DB_Error
     */
    protected function _load_fields() {
        // загрузка их статических данных
        // если их нет - то взять из базы
        $cache_key = $this->_class_id . '-' . $this->_system_table_id;
        if (!isset(self::$all_fields[$cache_key])) {
            $result = $this->db->get_results(
                    "SELECT `Field_ID` as `id`,
                            `Field_Name` as `name`,
                            `TypeOfData_ID` as `type`,
                            `Format` as `format`,
                            `Description` AS `description`,
                            `NotNull` AS `not_null`,
                            `DefaultState` as `default`,
                            `TypeOfEdit_ID` AS `edit_type`,
                            `System_Table_ID` AS `system_table_id`,
                            `Class_ID` AS `class_id`,
                            IF(`TypeOfData_ID` IN (" . NC_FIELDTYPE_SELECT . ", " . NC_FIELDTYPE_MULTISELECT . "),
                               SUBSTRING_INDEX(`Format`, ':', 1),
                               '') AS `table`,
                            " . (!$this->_system_table_id ? "`DoSearch`" : "1") . " AS `search`,
                            `Inheritance` AS `inheritance`,
                            `Extension` as `extension`,
                            `InTableView` AS `in_table_view`
                       FROM `Field`
                      WHERE `Checked` = 1  AND " .
                    ($this->_system_table_id
                        ? " `System_Table_ID` = '" . $this->_system_table_id . "'"
                        : " `Class_ID` = '" . $this->_class_id . "'") . "
                      ORDER BY `Priority`",
                    ARRAY_A) ?: array();

            if ($this->db->is_error) {
                throw new nc_Exception_DB_Error($this->db->last_query, $this->db->last_error);
            }

            if ($result && $result['extension']) {
                $result['extension'] = str_replace('%ID', $result['id'], $result['extension']);
            }

            self::$all_fields[$cache_key] = $result;
        }

        $this->_fields = self::$all_fields[$cache_key];
        $this->_field_count = count($this->_fields);
    }

    /**
     * @return mixed
     */
    public function get_joins() {
        return $this->_joins;
    }

    /**
     * @param $res
     * @return array
     */
    public function get_old_vars($res) {
        $old_vars = array();
        foreach ($this->_fields_vars_columns as $variable => $column) {
            if (!isset($res[$variable])) {
                $old_vars[$variable] = $res[$column];
            }
        }

        return $old_vars;
    }

    /**
     *
     */
    public function make_query() {
        $nc_core = nc_Core::get_object();

        $this->_load_fields();

        if ($this->_system_table_id == 3) {
            $this->_fields_query = array('a.`User_ID`', 'a.`PermissionGroup_ID`');
            $this->_fields_vars = array('$f_User_ID', '$f_PermissionGroup_ID');
        } else {
            $sub_folder = $nc_core->db->escape($nc_core->SUB_FOLDER);

            $this->_fields_query = array('a.`Message_ID`', 'a.`User_ID`', 'a.`IP`', 'a.`UserAgent`',
                'a.`LastUser_ID`', 'a.`LastIP`', 'a.`LastUserAgent`',
                'a.`Priority`', 'a.`Parent_Message_ID`', 'a.`ncTitle`', 'a.`ncKeywords`',
                'a.`ncDescription`', 'a.`ncSMO_Title`', 'a.`ncSMO_Description`', 'a.`ncSMO_Image`', 'sub.`Subdivision_ID`',
                'CONCAT(\'' . $sub_folder . '\', sub.`Hidden_URL`) AS `Hidden_URL`',
                'cc.`Sub_Class_ID`', 'cc.`EnglishName`');
            $this->_fields_vars = array('$f_Message_ID', '$f_User_ID', '$f_IP', '$f_UserAgent',
                '$f_LastUser_ID', '$f_LastIP', '$f_LastUserAgent',
                '$f_Priority', '$f_Parent_Message_ID', '$f_ncTitle', '$f_ncKeywords',
                '$f_ncDescription', '$f_ncSMO_Title', '$f_ncSMO_Description', '$f_ncSMO_Image', '$f_Subdivision_ID',
                '$f_Hidden_URL',
                '$f_Sub_Class_ID', '$f_EnglishName');

            $this->_joins .=
                " LEFT JOIN `Subdivision` AS sub ON sub.`Subdivision_ID` = a.`Subdivision_ID`
                  LEFT JOIN `Sub_Class` AS cc ON cc.`Sub_Class_ID` = a.`Sub_Class_ID` ";
        }

        $this->_fields_query[] = 'a.`Checked`';
        $this->_fields_query[] = 'a.`Created`';
        $this->_fields_query[] = 'a.`Keyword`';
        $this->_fields_query[] = 'a.`LastUpdated` + 0 AS LastUpdated';

        $this->_fields_vars[] = '$f_Checked';
        $this->_fields_vars[] = '$f_Created';
        $this->_fields_vars[] = '$f_Keyword';
        $this->_fields_vars[] = '$f_LastUpdated';


        if (!$this->_system_table_id && $nc_core->admin_mode && $nc_core->AUTHORIZE_BY !== 'User_ID') {
            $this->_fields_query[] = "uAdminInterfaceAdd.`" . $nc_core->AUTHORIZE_BY . "` AS f_AdminInterface_user_add ";
            $this->_fields_query[] = "uAdminInterfaceChange.`" . $nc_core->AUTHORIZE_BY . "` AS f_AdminInterface_user_change ";

            $this->_fields_vars[] = '$f_AdminInterface_user_add';
            $this->_fields_vars[] = '$f_AdminInterface_user_change';

            $this->_joins .= " LEFT JOIN `User` AS uAdminInterfaceAdd ON a.`User_ID` = uAdminInterfaceAdd.`User_ID`
                               LEFT JOIN `User` AS uAdminInterfaceChange ON a.`LastUser_ID` = uAdminInterfaceChange.`User_ID` ";
        }


        for ($i = 0; $i < $this->_field_count; $i++) {
            $field = & $this->_fields[$i];
            // skip "multifile" fields
            if ($field['type'] == NC_FIELDTYPE_MULTIFILE) {
                continue;
            }

            switch ($field['type']) {
                // list field
                case NC_FIELDTYPE_SELECT:
                    $table = $field['table'];
                    $this->_joins .= " LEFT JOIN `Classificator_" . $table . "` AS tbl" . $field['id'] . " ON a.`" . $field['name'] . "` = tbl" . $field['id'] . "." . $table . "_ID ";

                    $this->_fields_query[] = "tbl" . $field['id'] . "." . $table . "_Name AS " . $field['name'];
                    $this->_fields_query[] = "tbl" . $field['id'] . "." . $table . "_ID AS " . $field['name'] . "_id";
                    $this->_fields_query[] = "tbl" . $field['id'] . ".`Value` AS " . $field['name'] . "_value ";

                    $this->_fields_vars[] = "\$f_" . $field['name'];
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_id";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_value";
                    break;

                // date field
                case NC_FIELDTYPE_DATETIME:
                    $format = explode(";", $field['format']);
                    if (!empty($format[0]) && in_array($format[0], array('event_date', 'event_time'))) {
                        switch($format[0]) {
                            case "event_date":
                                $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%Y-%m-%d') as `" . $field['name'] . "`";
                                break;
                            case "event_time":
                                $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%H:%i:%s') as `" . $field['name'] . "`";
                                break;
                        }
                    } else {
                        $this->_fields_query[] = "a." . $field['name'];
                    }

                    $this->_fields_vars[] = "\$f_" . $field['name'];

                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%Y') as `" . $field['name'] . "_year`";
                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%m') as `" . $field['name'] . "_month`";
                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%d') as `" . $field['name'] . "_day`";
                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%H') as `" . $field['name'] . "_hours`";
                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%i') as `" . $field['name'] . "_minutes`";
                    $this->_fields_query[] = "DATE_FORMAT(a.`" . $field['name'] . "`,'%s') as `" . $field['name'] . "_seconds`";

                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_year";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_month";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_day";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_hours";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_minutes";
                    $this->_fields_vars[] = "\$f_" . $field['name'] . "_seconds";

                    break;

                // MultiList
                case NC_FIELDTYPE_MULTISELECT:
                    $this->_fields_query[] = "a." . $field['name'];
                    $this->_fields_vars[] = "\$f_" . $field['name'];
                    break;

                default:
                    $this->_fields_query[] = "a." . $field['name'];
                    $this->_fields_vars[] = "\$f_" . $field['name'];
                    break;
            }
        }

        $this->_fields_vars_columns = array();
        foreach ($this->_fields_vars as $i => $var) {
            $field_name = preg_replace('/^\\$(?:f_)?/', "", $var);
            $field_query = $this->_fields_query[$i];
            if (stripos($field_query, " as ")) {
                $field_column = preg_replace('/^.+\sAS\s+`?(\w+).?$/is', '$1', $field_query);
            } else {
                $field_column = preg_replace('/^.*?([\w_]+?)[`]?$/i', '$1', $field_query);
            }
            $this->_fields_vars_columns[$field_name] = $field_column;
        }
    }

    /**
     * @param int $type
     * @param int $output_all
     * @return array
     *    Если output_all = true, то массив со следующими элементами для каждого поля:
     *      id
     *      name
     *      type
     *      format
     *      description
     *      not_null
     *      default
     *      edit_type
     *      table   (таблица классификатора)
     *      search
     *      inheritance
     *      extension
     *      in_table_view
     *   Если output_all = false, то массив id поля => name (не description) поля
     */
    public function get_fields($type = 0, $output_all = 1) {
        $this->_load_fields();

        if (!$type && $output_all) {
            return $this->_fields;
        }

        $result = array();
        for ($i = 0; $i < $this->_field_count; $i++) {
            if ($type ? ($this->_fields[$i]['type'] == $type) : 1) {
                if ($output_all) {
                    $result[] = $this->_fields[$i];
                } else {
                    $result[$this->_fields[$i]['id']] = $this->_fields[$i]['name'];
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает массив с информацией о полях, у которых имя (name) начинается
     * с указанного префикса
     * @param string $prefix    Искомый префикс
     * @param null $type        Аналогично методу get_fields()
     * @param bool $output_all  Аналогично методу get_fields()
     * @return array
     */
    public function get_fields_by_name_prefix($prefix, $type = null, $output_all = true) {
        $fields = $this->get_fields($type, $output_all);

        if ($output_all) {
            $result = array();
            $prefix_length =  strlen($prefix);
            foreach ($fields as $f) {
                if (substr($f['name'], 0, $prefix_length) === $prefix) {
                    $result[] = $f;
                }
            }
            return $result;
        }
        else {
            return preg_grep("/^" . preg_quote($prefix) . "/", $fields);
        }
    }

    /**
     * Получить все настройки поля с именем $field_name или
     * параметр $parameter_name поля $field_name
     *
     * $component->get_field('City')  → array
     * $component->get_field('City', 'description') → string
     *
     * @param string $field_name
     * @param string|null $parameter_name
     * @return null|array|string
     */
    public function get_field($field_name, $parameter_name = null) {
        $this->_load_fields();

        foreach ($this->_fields as $field) {
            if ($field['name'] == $field_name) {
                if ($parameter_name) {
                    return isset($field[$parameter_name]) ? $field[$parameter_name] : null;
                } else {
                    return $field;
                }
            }
        }
        return null;
    }

    /**
     * Возвращает информацию для поля ncSMO_Image
     */
    public function get_smo_image_field() {
        if ($this->_system_table_id && $this->_system_table_id != 2) {
            return array();
        }

        return array(
            'id' => 'ncSMO_Image',
            'name' => 'ncSMO_Image',
            'type' => NC_FIELDTYPE_FILE,
            'format' => '10485760:image/*:fs2',
            'description' => NETCAT_MODERATION_STANDART_FIELD_NC_SMO_IMAGE,
            'not_null' => 0,
            'default' => '',
            'edit_type' => 1,
            'table' => null,
            'search' => 0,
        );
    }

    /**
     * Проверяет, существует ли поле с указанным именем в компоненте
     * @param string $field_name
     * @param int $field_type
     * @return bool
     */
    public function has_field($field_name, $field_type = null) {
        $this->_load_fields();
        $t = $this->get_field($field_name, 'type');
        if ($field_type) {
            return $t == $field_type;
        }
        else {
            return (bool)$t;
        }
    }

    /**
     * Возвращает имя поля с типа дата с форматом event или event_date,
     * если таковое существует, или false
     */
    public function get_date_field() {
        $key = $this->_class_id . "-" . $this->_system_table_id;

        if (!isset(self::$event_fields[$key])) {
            self::$event_fields[$key] = false;

            foreach ($this->get_fields() as $field) {
                if ($field['type'] != NC_FIELDTYPE_DATETIME) {
                    continue;
                }

                $format = nc_field_parse_format($field['format'], NC_FIELDTYPE_DATETIME);
                if ($format['type'] == 'event' || $format['type'] == 'event_date') {
                    self::$event_fields[$key] = $field['name'];
                    break;
                }
            }
        }

        return self::$event_fields[$key];
    }

    /**
     * @param $srchPat
     * @return array
     */
    public function get_search_query($srchPat, $cc = null) {

        if ($cc != null) {
            $this->_fields = array_values($this->get_additional_search_fields($cc));
            $this->_field_count = count($this->_fields);
            $srchPatName = "srchPatAdd";
        } else {
            $this->_load_fields();
            $srchPatName = "srchPat";
        }
        // return if search params not set
        if (empty($srchPat)) {
            return array("query" => "", "link" => "");
        }

        $search_param = array();
        if (isset($srchPat['OR']) && $srchPat['OR'] == '1') {
            $search_param[] = "srchPat[OR]=1";
        }

        $search_string = $fullSearchStr = '';
        $or_and = '';
        for ($i = 0, $j = 0; $i < $this->_field_count; $i++) {
            $field = & $this->_fields[$i];
            if ($search_string > '') {
                $or_and = ((isset($srchPat['OR']) && $srchPat['OR'] == '1') ? 'OR' : 'AND');
            }
            if ($field['search']) {
                switch ($field['type']) {
                    case NC_FIELDTYPE_STRING: // Char
                        if ($srchPat[$j] == "") {
                            break;
                        }
                        $search_string .= " " . $or_and . " a." . $field['name'] . " LIKE '%" . $this->db->escape($srchPat[$j]) . "%'";
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . rawurlencode($srchPat[$j]);
                        break;
                    case NC_FIELDTYPE_INT: // Int
                        if (trim($srchPat[$j]) != "") {
                            $search_string .= " " . $or_and . " ";
                            if (trim($srchPat[$j + 1]) != "") {
                                $search_string .= "(";
                            }
                            $search_string .= "a." . $field['name'] . ">=" . trim(intval($srchPat[$j]));
                            $search_param[] = "".$srchPatName."[" . $j . "]=" . trim(intval($srchPat[$j]));
                        }
                        $j++;
                        if (trim($srchPat[$j]) != "") {
                            if (trim($srchPat[$j - 1]) != "") {
                                $search_string .= " AND ";
                            } else {
                                $search_string .= " " . $or_and . " ";
                            }
                            $search_string .= " a." . $field['name'] . "<=" . trim(intval($srchPat[$j]));
                            if (trim($srchPat[$j - 1]) != "") {
                                $search_string .= ")";
                            }
                            $search_param[] = "".$srchPatName."[" . $j . "]=" . trim(intval($srchPat[$j]));
                        }
                        break;
                    case NC_FIELDTYPE_TEXT: // Text
                        if ($srchPat[$j] == "") {
                            break;
                        }
                        $srch_str = $this->db->escape($srchPat[$j]);
                        $search_string .= " " . $or_and . " a." . $field['name'] . " LIKE '%" . $srch_str . "%'";
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . rawurlencode($srchPat[$j]);
                        break;
                    case NC_FIELDTYPE_SELECT: // List
                        if ($srchPat[$j] == "") {
                            break;
                        }
                        $srchPat[$j] += 0;
                        $search_string .= " " . $or_and . " a." . $field['name'] . "=" . $srchPat[$j];
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        break;
                    case NC_FIELDTYPE_BOOLEAN: // Boolean
                        if ($srchPat[$j] == "") {
                            break;
                        }
                        $srchPat[$j] += 0;
                        $search_string .= " " . $or_and . " a." . $field['name'] . "=" . $srchPat[$j];
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        break;
                    case NC_FIELDTYPE_FILE: // File
                        if ($srchPat[$j] == "") {
                            break;
                        }
                        $srch_str = $this->db->escape($srchPat[$j]);
                        $search_string .= " " . $or_and . " SUBSTRING_INDEX(a." . $field['name'] . ",':',1) LIKE '%" . $srch_str . "%'";
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        break;
                    case NC_FIELDTYPE_FLOAT: // Float
                        if (trim($srchPat[$j]) != "") {
                            $search_string .= " " . $or_and . " ";
                            if (trim($srchPat[$j + 1]) != "") {
                                $search_string .= "(";
                            }
                            $srchPat[$j] = str_replace(',', '.', floatval($srchPat[$j]));
                            $search_string .= "a." . $field['name'] . ">=" . $srchPat[$j];
                            $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        }
                        $j++;

                        if (trim($srchPat[$j]) != "") {
                            if (trim($srchPat[$j - 1]) != "") {
                                $search_string .= " AND ";
                            } else {
                                $search_string .= " " . $or_and . " ";
                            }
                            $srchPat[$j] = str_replace(',', '.', floatval($srchPat[$j]));
                            $search_string .= " a." . $field['name'] . "<=" . $srchPat[$j];
                            if (trim($srchPat[$j - 1]) != "") {
                                $search_string .= ")";
                            }
                            $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        }
                        break;
                    case NC_FIELDTYPE_DATETIME: // DateTime
                        $date_from['d'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_from['m'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_from['Y'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%04d", $srchPat[$j]) : false);
                        $j++;
                        $date_from['H'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_from['i'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_from['s'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['d'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['m'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['Y'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%04d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['H'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['i'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                        $j++;
                        $date_to['s'] = ($srchPat[$j] && ($search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);

                        $date_format_from = ($date_from['Y'] ? '%Y' : '') . ($date_from['m'] ? '%m' : '') . ($date_from['d'] ? '%d' : '') . ($date_from['H'] ? '%H' : '') . ($date_from['i'] ? '%i' : '') . ($date_from['s'] ? '%s' : '');
                        $date_format_to = ($date_to['Y'] ? '%Y' : '') . ($date_to['m'] ? '%m' : '') . ($date_to['d'] ? '%d' : '') . ($date_to['H'] ? '%H' : '') . ($date_to['i'] ? '%i' : '') . ($date_to['s'] ? '%s' : '');

                        if ($date_format_from || $date_format_to) {
                            $search_string .= " " . $or_and . " (";
                        }
                        if ($date_format_from) {
                            $search_string .= " DATE_FORMAT(a." . $field['name'] . ",'" . $date_format_from . "')>=" . $date_from['Y'] . $date_from['m'] . $date_from['d'] . $date_from['H'] . $date_from['i'] . $date_from['s'];
                        }
                        if ($date_format_to) {
                            if ($date_format_from) {
                                $search_string .= " AND ";
                            }
                            $search_string .= " DATE_FORMAT(a." . $field['name'] . ",'" . $date_format_to . "')<=" . $date_to['Y'] . $date_to['m'] . $date_to['d'] . $date_to['H'] . $date_to['i'] . $date_to['s'] ;
                        }
                        if ($date_format_from || $date_format_to) {
                            $search_string .= ")";
                        }
                        break;

                    case NC_FIELDTYPE_MULTISELECT: // MultiList
                        if ($srchPat[$j] == "") {
                            $j++;
                            break;
                        }

                        $id = array(); // массив с id искомых элементов

                        if (is_array($srchPat[$j])) {
                            foreach ((array)$srchPat[$j] as $v) {
                                if (!$v) {
                                    break;
                                }
                                $id[] = intval($v);
                            }
                        } else {
                            $temp_id = explode('-', $srchPat[$j]);
                            foreach ((array)$temp_id as $v) {
                                if (!$v) {
                                    break;
                                }
                                $id[] = intval($v);
                            }
                        }
                        $j++; //второй параметр - это тип поиска

                        if (empty($id)) {
                            break;
                        }

                        $search_string .= " " . $or_and . " (";
                        switch ($srchPat[$j]) {
                            case 1: //Полное совпадение
                                $search_string .= "a." . $field['name'] . " LIKE CONCAT(',' ,  '" . join(',', $id) . "', ',') ";
                                break;

                            case 2: //Хотя бы один. Выбор между LIKE и REGEXP выпал в сторону первого
                                foreach ($id as $v)
                                    $search_string .= "a." . $field['name'] . " LIKE CONCAT('%,', '" . $v . "', ',%') OR ";
                                $search_string .= "0 "; //чтобы "закрыть" последний OR
                                break;
                            case 0: // как минимум выбранные - частичное совпадение - по умолчанию
                            default:
                                $srchPat[$j] = 0;
                                $search_string .= "a." . $field['name'] . "  REGEXP  \"((,[0-9]+)*)";
                                $prev_v = -1;
                                foreach ($id as $v) {
                                    /*
                                      example:
                                      &srchPat[2][]=1&srchPat[2][]=3
                                      (a.test REGEXP "((,[0-9]+)*)(,1,)([0-9]*)((,[0-9]+)*)(,2,)([0-9]*)((,[0-9]+)*)"
                                    */
                                    $search_string .= "(," . $v . ")(,[0-9]+)*";
                                    $prev_v = $v;
                                }
                                $search_string .= '"';
                                break;
                        }
                        $search_string .= ")";

                        $search_param[] = "".$srchPatName."[" . ($j - 1) . "]=" . join('-', $id);
                        $search_param[] = "".$srchPatName."[" . $j . "]=" . $srchPat[$j];
                        break;
                }
                $j++;
            }
        }

        if (!empty($search_string)) {
            $fullSearchStr = " AND( " . $search_string . ")";
        }
        if (!empty($search_param)) {
            $search_params['link'] = join('&amp;', $search_param);
        }
        $search_params['query'] = $fullSearchStr;

        return $search_params;
    }

    /**
     * @param $catalogue
     * @param $sub
     * @param $cc
     * @param int $eval
     * @return string
     */
    public function add_form($catalogue, $sub, $cc, $eval = 0) {
        // в форме добавления могут использоваться различные глобальные переменные... :(
        extract($GLOBALS);
        list($catalogue, $sub, $cc, $eval) = func_get_args();
        $nc_core = nc_Core::get_object();
        $classID = $class_id = $this->_class_id;

        $File_Mode = nc_get_file_mode('Class', $this->_class_id);
        if ($File_Mode) {
            $sub_class_settings = $nc_core->sub_class->get_by_id($cc);
            $file_class = new nc_tpl_component_view($nc_core->CLASS_TEMPLATE_FOLDER, $nc_core->db);
            $file_class->load($sub_class_settings['Real_Class_ID'], $sub_class_settings['File_Path'], $sub_class_settings['File_Hash']);
            $nc_parent_field_path = $file_class->get_parent_field_path('AddTemplate');
            $nc_field_path = $file_class->get_field_path('AddTemplate');
            if (filesize($nc_field_path)) {
                ob_start();
                include $nc_field_path;
                return ob_get_clean();
            }
        }

        $alter_form = $nc_core->component->get_by_id($this->_class_id, 'AddTemplate');

        if ($alter_form) {
            $result = $alter_form;
        } else {
            $this->_load_fields();
            $result = nc_fields_form('add', $this->_fields, $this->_class_id);
        }
        if ($eval && !$File_Mode) {
            $addForm = null;
            eval(nc_check_eval("\$addForm = \"" . $result . "\"; "));
            return $addForm;
        }
        return $result;
    }

    /**
     * @param int $short
     * @return string
     */
    public function search_form($short = 1, $filter_additional_fields = NULL) {
        $nc_core = nc_Core::get_object();
        $alter_form = $nc_core->component->get_by_id($this->_class_id, $short ? 'FullSearchTemplate' : 'SearchTemplate');
        if ($alter_form) {
            return $alter_form;
        }

        $result = nc_fields_form('search', $this->_fields, 0, $filter_additional_fields);

        return $result;
    }

    /**
     * Добавление нового компонента ( шаблона компонента )
     *
     * @param string $class_name - имя компонента
     * @param string $class_group - группа компонента
     * @param array $params - массив параметров компонента
     * @param int $class_template - номер класса, если идёт создание шаблона
     * @param string $type - тип шаблона компонента
     *
     * @throws nc_Exception_DB_Error|nc_Exception_Class_Invalid_Keyword
     * @return int номер созданного компонент
     */
    public function add($class_name, $class_group, $params, $class_template = 0, $type = 'useful') {
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;
        $class_name = $db->escape($class_name);
        $class_group = $db->escape($class_group);
        $type = $db->escape($type);
        $class_template = intval($class_template);

        $File_Mode = nc_get_file_mode('Class', $class_template);

        if ($File_Mode) {
            $class_editor = new nc_tpl_component_editor($nc_core->CLASS_TEMPLATE_FOLDER, $db);
            $class_editor->load($class_template);
            if (is_array($params)) {
                $template_content = array_merge((array)$nc_core->input->fetch_post(), $params);
            }
        }

        // все параметры компонента
        $params_int = array('AllowTags', 'RecordsPerPage', 'NL2BR', 'UseCaptcha', 'CacheForUser', 'IsAuxiliary', 'IsOptimizedForMultipleMode');
        if (!$class_template) {
            $params_int[] = 'System_Table_ID';
        }
        $params_int_null = array('MinRecordsInInfoblock', 'MaxRecordsInInfoblock');
        $params_text = array('FormPrefix', 'FormSuffix', 'RecordTemplate', 'SortBy', 'RecordTemplateFull',
            'TitleTemplate', 'AddTemplate', 'EditTemplate', 'AddActionTemplate', 'EditActionTemplate', 'SearchTemplate',
            'FullSearchTemplate', 'SubscribeTemplate', 'Settings', 'AddCond', 'EditCond', 'SubscribeCond',
            'DeleteCond', 'CheckActionTemplate', 'DeleteActionTemplate', 'CustomSettingsTemplate',
            'ClassDescription', 'DeleteTemplate', 'TitleList');

        if ($File_Mode) {
            $params_text = $class_editor->get_clear_fields($params_text);
            $params['File_Mode'] = 1;
            $params_text[] = 'File_Mode';
        }

        // проверка ключевого слова
        $keyword = trim(nc_array_value($params, 'Keyword'));
        $keyword_validation_result = $this->validate_keyword($keyword, null, $class_template);

        if ($keyword_validation_result !== true) {
            throw new nc_Exception_Class_Invalid_Keyword($keyword_validation_result);
        }

        // добавление имени, группы, ключевого слова
        $query = array("`Class_Name`", "`Class_Group`", "`Keyword`");
        $values = array("'$class_name'", "'$class_group'", "'" . $db->escape($keyword) . "'");

        // добавление шаблона компонента
        if ($class_template) {
            $query[] = "`ClassTemplate`";
            $values[] = "'" . $class_template . "'";
            // System Table ID в любом случае берётся от компонента
            $query[] = "`System_Table_ID`";
            $values[] = "'" . $this->get_by_id($class_template, 'System_Table_ID') . "'";
        }
        // тип шаблона компонента
        if ($type) {
            $query[] = "`Type`";
            $values[] = "'" . $type . "'";
        }
        // добавление всех параметров компонента
        foreach ($params_int as $v) {
            $value = isset($params[$v]) ? intval($params[$v]) : 0;

            $query[] = "`" . $v . "`";
            $values[] = "'" . $value . "'";
        }

        foreach ($params_text as $v) {
            $value = isset($params[$v]) ? $params[$v] : '';

            $query[] = "`" . $v . "`";
            $values[] = "'" . $db->prepare($value) . "'";
        }

        foreach ($params_int_null as $v) {
            $value = isset($params[$v]) && strlen(trim($params[$v])) ? (int)trim($params[$v]) : 'NULL';

            $query[] = "`" . $v . "`";
            $values[] = $value;
        }

        if (!$class_template) {
            $nc_core->event->execute(nc_Event::BEFORE_COMPONENT_CREATED, 0);
        } else {
            $nc_core->event->execute(nc_Event::BEFORE_COMPONENT_TEMPLATE_CREATED, $class_template, 0);
        }

        // собственно добавление
        $SQL = "INSERT INTO `Class` (" . join(', ', $query) . ") VALUES (" . join(', ', $values) . ") ";
        $db->query($SQL);

        if ($db->is_error) {
            throw new nc_Exception_DB_Error($db->last_query, $db->last_error);
        }

        $new_class_id = $db->insert_id;

        if ($File_Mode) {
            $class_editor->save_new_class($new_class_id, $template_content);
        }

        // трансляция события создания компонента
        if (!$class_template) {
            CreateMessageTable($new_class_id, $db);
            $nc_core->event->execute(nc_Event::AFTER_COMPONENT_CREATED, $new_class_id);
        } else {
            $nc_core->event->execute(nc_Event::AFTER_COMPONENT_TEMPLATE_CREATED, $class_template, $new_class_id);
        }


        return $new_class_id;
    }

    /**
     * @param $id
     * @param array $params
     * @return bool
     * @throws nc_Exception_DB_Error|nc_Exception_Class_Invalid_Keyword
     */
    public function update($id, $params = array()) {
        $nc_core = nc_Core::get_object();
        $db = $this->db;

        $id = intval($id);
        if (!$id || !is_array($params)) {
            return false;
        }

        if ($params['action_type'] == 1) {
            $params_int = array(
                'CacheForUser',
                'IsAuxiliary',
                'IsOptimizedForMultipleMode',
            );
            $params_int_null = array();
            $params_text = array(
                'Class_Name',
                'Class_Group',
                'Keyword',
                'ObjectNameSingular',
                'ObjectNamePlural',
            );

            // Проверка ключевого слова
            $keyword = trim(nc_array_value($params, 'Keyword'));
            $keyword_validation_result = $this->validate_keyword($keyword, $id);

            if ($keyword_validation_result !== true) {
                throw new nc_Exception_Class_Invalid_Keyword($keyword_validation_result);
            }

            $old_keyword = $this->get_by_id($id, 'Keyword');

        } else {

            $params_int = array(
                'AllowTags',
                'RecordsPerPage',
                'System_Table_ID',
                'NL2BR',
                'UseCaptcha',
                'UseAltTitle',
            );

            $params_int_null = array(
                'MinRecordsInInfoblock',
                'MaxRecordsInInfoblock',
            );

            $params_text = array(
                'FormPrefix',
                'FormSuffix',
                'RecordTemplate',
                'SortBy',
                'RecordTemplateFull',
                'TitleTemplate',
                'AddTemplate',
                'EditTemplate',
                'AddActionTemplate',
                'EditActionTemplate',
                'SearchTemplate',
                'FullSearchTemplate',
                'SubscribeTemplate',
                'Settings',
                'AddCond',
                'EditCond',
                'SubscribeCond',
                'DeleteCond',
                'CheckActionTemplate',
                'DeleteActionTemplate',
                'CustomSettingsTemplate',
                'ClassDescription',
                'DeleteTemplate',
                'TitleList');

            $keyword = $old_keyword = null;
        }

        $File_Mode = nc_get_file_mode('Class', $id);
        if ($File_Mode) {
            $class_editor = new nc_tpl_component_editor($nc_core->CLASS_TEMPLATE_FOLDER, $db);
            $class_editor->load($id);
            $class_editor->save_fields($only_isset_post = true);
            $params_text = $class_editor->get_clear_fields($params_text);
        }

        $query = array();

        foreach ($params as $k => $v) {
            $is_nullable_int = in_array($k, $params_int_null);
            if (!in_array($k, $params_int) && !in_array($k, $params_text) && !$is_nullable_int) {
                continue;
            }
            if ($is_nullable_int && !strlen(trim($v))) {
                if (strlen(trim($v))) {
                    $query[] = "`" . $db->escape($k) . "` = " . (int)trim($v);
                }
                else {
                    $query[] = "`" . $db->escape($k) . "` = NULL";
                }
            }
            else {
                $query[] = "`" . $db->escape($k) . "` = '" . $db->prepare($v) . "'";
            }
        }

        if (isset($params['DisableBlockMarkup']) && strlen($params['DisableBlockMarkup'])) {
            $query[] = "`DisableBlockMarkup` = " . (int)$params['DisableBlockMarkup'];
        }

        if (!empty($query)) {
            $ClassTemplate = $db->get_var("SELECT `ClassTemplate` FROM `Class` WHERE `Class_ID` = '" . $id . "' ");

            @$nc_core->event->execute(nc_Event::BEFORE_SYSTEM_TABLE_UPDATED, 3);

            if (!$ClassTemplate) {
                $nc_core->event->execute(nc_Event::BEFORE_COMPONENT_UPDATED, $id);
            } else {
                $nc_core->event->execute(nc_Event::BEFORE_COMPONENT_TEMPLATE_UPDATED, $ClassTemplate, $id);
            }

            $db->query("UPDATE `Class` SET " . join(",\n        ", $query) . " WHERE `Class_ID` = " . $id);
            if ($db->is_error) {
                throw new nc_Exception_DB_Error($db->last_query, $db->last_error);
            }


            if ($keyword != $old_keyword && $params['action_type'] == 1 && $File_Mode && isset($class_editor)) {
                $class_editor->update_keyword($keyword ?: $id);
            }


            if (!$ClassTemplate) {
                $nc_core->event->execute(nc_Event::AFTER_COMPONENT_UPDATED, $id);
            } else {
                $nc_core->event->execute(nc_Event::AFTER_COMPONENT_TEMPLATE_UPDATED, $ClassTemplate, $id);
            }

            @$nc_core->event->execute(nc_Event::AFTER_SYSTEM_TABLE_UPDATED, 3);
        }

        $this->data = array();
        return true;
    }

    public function get_system_table_id() {
        return $this->_system_table_id;
    }

    /**
     * @return array
     */
    public function get_standart_fields() {
        $db = nc_core('db');

        $sql = "SELECT `FieldsInTableView` FROM `Class` WHERE `Class_ID` = {$this->_class_id}";
        $in_table_fields = @json_decode($db->get_var($sql), true);

        if (!$in_table_fields) {
            $in_table_fields = array();
        }

        $standart_fields = array(
            'User_ID' => NETCAT_MODERATION_STANDART_FIELD_USER_ID,
            'User' => NETCAT_MODERATION_STANDART_FIELD_USER,
            'Priority' => NETCAT_MODERATION_STANDART_FIELD_PRIORITY,
            'Keyword' => NETCAT_MODERATION_STANDART_FIELD_KEYWORD,
            'ncTitle' => NETCAT_MODERATION_STANDART_FIELD_NC_TITLE,
            'ncKeywords' => NETCAT_MODERATION_STANDART_FIELD_NC_KEYWORDS,
            'ncDescription' => NETCAT_MODERATION_STANDART_FIELD_NC_DESCRIPTION,
            'ncSMO_Title' => NETCAT_MODERATION_STANDART_FIELD_NC_SMO_TITLE,
            'ncSMO_Description' => NETCAT_MODERATION_STANDART_FIELD_NC_SMO_DESCRIPTION,
            'ncSMO_Image' => NETCAT_MODERATION_STANDART_FIELD_NC_SMO_IMAGE,
            'IP' => NETCAT_MODERATION_STANDART_FIELD_IP,
            'UserAgent' => NETCAT_MODERATION_STANDART_FIELD_USER_AGENT,
            'Created' => NETCAT_MODERATION_STANDART_FIELD_CREATED,
            'LastUpdated' => NETCAT_MODERATION_STANDART_FIELD_LAST_UPDATED,
            'LastUser_ID' => NETCAT_MODERATION_STANDART_FIELD_LAST_USER_ID,
            'LastUser' => NETCAT_MODERATION_STANDART_FIELD_LAST_USER,
            'LastIP' => NETCAT_MODERATION_STANDART_FIELD_LAST_IP,
            'LastUserAgent' => NETCAT_MODERATION_STANDART_FIELD_LAST_USER_AGENT,
        );

        $result = array();

        foreach ($standart_fields as $field => $description) {
            $result[] = array(
                'id' => $field,
                'name' => $field,
                'description' => $description,
                'standart' => true,
                'in_table_view' => in_array($field, $in_table_fields),
            );
        }

        return $result;
    }

    public function get_additional_search_fields($cc)
    {
        $db = nc_core('db');
        $filter_additional_fields = array(
          'Message_ID' => array(
            'id' => 'Message_ID',
            'name' => 'Message_ID',
            'description' => NETCAT_FILTER_FIELD_MESSAGE_ID,
            'search' => 0,
            'type' => 2,
            'format' => 0,
          ),
          'Created' => array(
            'id' => 'Created',
            'name' => 'Created',
            'description' => NETCAT_FILTER_FIELD_CREATED,
            'search' => 0,
            'type' => 8,
            'format' => 'event;calendar',
          ),
          'LastUpdated' => array(
            'id' => 'LastUpdated',
            'name' => 'LastUpdated',
            'description' => NETCAT_FILTER_FIELD_LAST_UPDATED,
            'search' => 0,
            'type' => 8,
            'format' => 'event;calendar',
          )
        );
        $sql = "SELECT Field_Name FROM FieldFilter WHERE SubClass_ID='" . $cc . "' AND DoSearch='1'";
        $res = $db->get_results($sql, ARRAY_A);

        if (!empty($res)) {
            foreach ($res as $field) {
                $filter_additional_fields[$field['Field_Name']]['search'] = 1;
            }
        }
        return $filter_additional_fields;
    }

    /**
     * @param string $keyword
     * @param int|null $for_component_id    ID компонента или шаблона компонента
     * @param int|null $parent_component_id
     * @return bool|string   возвращает true или текст ошибки
     */
    public function validate_keyword($keyword, $for_component_id = null, $parent_component_id = null) {
        $keyword = trim($keyword);
        $length = strlen($keyword);
        $for_component_id = (int)$for_component_id;

        // Пустое ключевое слово — OK
        if ($length == 0) {
            return true;
        }

        // Длина больше 64 — не ОК
        if ($length > self::MAX_KEYWORD_LENGTH) {
            return sprintf(CONTROL_CLASS_KEYWORD_TOO_LONG, self::MAX_KEYWORD_LENGTH);
        }

        // Только цифры — не ОК
        if (preg_match('/^\d+$/', $keyword)) {
            return CONTROL_CLASS_KEYWORD_ONLY_DIGITS;
        }

        // В ключевом слове допустимы только a-z 0-9 _
        if (!preg_match('/^\w+$/', $keyword)) {
            return CONTROL_CLASS_KEYWORD_INVALID_CHARACTERS;
        }

        // Определяем родительский компонент (для шаблонов)
        if ($parent_component_id === null && $for_component_id) {
            $parent_component_id = (int)$this->get_by_id($for_component_id, 'ClassTemplate');
        }
        else {
            $parent_component_id = (int)$parent_component_id;
        }

        // Зарезервированные ключевые слова для компонента
        if (!$parent_component_id && in_array($keyword, $this->reserved_keywords)) {
            return sprintf(CONTROL_CLASS_KEYWORD_RESERVED, $keyword);
        }

        // Зарезервированные ключевые слова для шаблонов
        if ($parent_component_id && preg_match('/^(?:assets|images?|styles?|scripts?|fonts?|img|css|js)$/', $keyword)) {
            return sprintf(CONTROL_CLASS_KEYWORD_RESERVED, $keyword);
        }

        // Уникальность ключевого слова в пределах родителя
        $existing_component = $this->db->get_row(
            "SELECT `Class_ID`, `Class_Name`
               FROM `Class`
              WHERE `Keyword` = '$keyword'
                AND `ClassTemplate` = $parent_component_id
                AND `Class_ID` != '$for_component_id'", ARRAY_A); // $keyword безопасен

        // Ключевое слово уже используется
        if ($existing_component) {
            $message = $parent_component_id ? CONTROL_CLASS_KEYWORD_TEMPLATE_NON_UNIQUE : CONTROL_CLASS_KEYWORD_NON_UNIQUE;
            return sprintf($message, $keyword, htmlspecialchars($existing_component['Class_Name'] ?: $existing_component['Class_ID']));
        }

        // Претензий не имеем
        return true;
    }

    /**
     * Возвращает стандартные имена классов для шаблона компонента
     * ("tpl-component-КЛЮЧЕВОЕ_СЛОВО_ИЛИ_ID_КОМПОНЕНТА tpl-template-КЛЮЧЕВОЕ_СЛОВО_ИЛИ_ID_ШАБЛОНА")
     * @param int $template_id
     * @return string
     */
    public function get_css_class_name($template_id) {
        if (!$this->get_by_id($template_id, 'File_Mode')) {
            return '';
        }

        $component_id = $this->get_by_id($template_id, 'ClassTemplate') ?: $template_id;

        $component_string = trim($this->get_by_id($component_id, 'File_Path'), '/');
        $result = 'tpl-component-' . nc_camelcase_to_dashcase($component_string);

        if ($template_id != $component_id) {
            $template_string = $this->get_by_id($template_id, 'Keyword') ?: $template_id;
            $result .= ' tpl-template-' . nc_camelcase_to_dashcase($template_string);
        }

        return $result;
    }

    /**
     * Возвращает путь к картинке с эскизом списка объектов шаблона компонента
     * от корня сайта
     * @param int $component_id
     * @param bool $only_if_exists   проверять существование файла
     * @return string|null
     */
    public function get_list_preview_relative_path($component_id, $only_if_exists = true) {
        $nc_core = nc_core::get_object();
        if (!$this->get_by_id($component_id, 'File_Mode')) {
            return null;
        }

        $relative_path =
            $nc_core->SUB_FOLDER .
            $nc_core->HTTP_TEMPLATE_PATH .
            'class' .
            $nc_core->component->get_by_id($component_id, 'File_Path') .
            'Class.png';

        if ($only_if_exists && !file_exists($nc_core->DOCUMENT_ROOT . $relative_path)) {
            return null;
        }

        return $relative_path;
    }

    /**
     *
     */
    public function clear_cache() {
        parent::clear_cache();
        self::$event_fields = array();
        self::$all_fields = array();
    }

    /**
     * Проверяет, нужно (можно) ли добавлять дополнительную разметку (div.tpl-*) при выводе блока.
     * @param $component_template_id
     * @return bool
     */
    public function can_add_block_markup($component_template_id) {
        if (!$this->get_by_id($component_template_id, 'File_Mode')) {
            return false;
        }

        if ($this->get_by_id($component_template_id, 'DisableBlockMarkup')) {
            return false;
        }

        $template_type = $this->get_by_id($component_template_id, 'Type');
        if ($template_type == 'rss' || $template_type == 'xml') {
            return false;
        }

        $page_type = $nc_core = nc_core::get_object()->page->get_routing_result('format');
        if ($page_type == 'rss' || $page_type == 'xml') {
            return false;
        }

        return true;
    }

}
