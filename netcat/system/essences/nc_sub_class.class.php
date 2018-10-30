<?php

class nc_Sub_Class extends nc_Essence {

    protected $db;
    private $_current_id;

    const MAX_KEYWORD_LENGTH = 64;

    protected $ctpl_cache;
    protected $subdivision_cache;
    protected $subdivision_first_checked_cache;

    /**
     * Constructor function
     */
    public function __construct() {
        // load parent constructor
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();
        // system db object
        if (is_object($nc_core->db)) {
            $this->db = $nc_core->db;
        }

        $this->register_event_listeners();
    }

    /**
     * Обработчики для обновления и сброса кэша
     */
    protected function register_event_listeners() {
        $event = nc_core::get_object()->event;
        $clear_cache = array($this, 'clear_cache');
        $event->add_listener(nc_event::AFTER_INFOBLOCK_UPDATED, $clear_cache);
        $event->add_listener(nc_event::AFTER_INFOBLOCK_ENABLED, $clear_cache);
        $event->add_listener(nc_event::AFTER_INFOBLOCK_DISABLED, $clear_cache);
        $event->add_listener(nc_event::AFTER_INFOBLOCK_DELETED, $clear_cache);
    }

    /**
     * Get subclass data with system table flag from `Class` table
     *
     * @param int $id Subdivision_ID, if not set - use current value in query
     * @param bool $reset reset stored data in the static variable
     *
     * @return array subclass data associative array
     */
    public function get_by_subdivision_id($id = 0, $reset = false) {
        // system superior object
        $nc_core = nc_Core::get_object();

        // validate parameters
        $id = (int)$id;
        if (!$id && is_object($nc_core->subdivision)) {
            $id = $nc_core->subdivision->get_current("Subdivision_ID");
        }

        if (!$id) {
            return false;
        }

        // check initialized object
        if (empty($this->subdivision_cache[$id]) || $reset) {

            $this->subdivision_cache[$id] = $this->db->get_results(
                "SELECT b.*, c.`System_Table_ID` AS sysTbl
                   FROM `Sub_Class` AS b
                   LEFT JOIN `Class` AS c ON b.`Class_ID` = c.`Class_ID`
                  WHERE b.`Subdivision_ID` = '" . $id . "'
                  ORDER BY b.`Checked`, b.`Priority`",
                ARRAY_A);

            if (!empty($this->subdivision_cache[$id])) {
                foreach ($this->subdivision_cache[$id] as $v) {
                    $this->data[$v['Sub_Class_ID']] = $v;
                    $this->data[$v['Sub_Class_ID']]['_nc_final'] = 0;
                }
            }
        }

        return $this->subdivision_cache[$id];
    }

    /**
     * Get first 'checked' subclass ID in a subdivision
     */
    public function get_first_checked_id_by_subdivision_id($id = 0, $reset = false) {
        $id = intval($id);

        if (!isset($this->subdivision_first_checked_cache[$id]) || $reset) {
            $this->subdivision_first_checked_cache[$id] = false;

            $subclasses = $this->get_by_subdivision_id($id, $reset);
            if ($subclasses) {
                foreach ($subclasses as $subclass) {
                    if ($subclass['Checked']) {
                        $this->subdivision_first_checked_cache[$id] = $subclass['Sub_Class_ID'];
                        break;
                    }
                }
            }
        }

        return $this->subdivision_first_checked_cache[$id];
    }

    /**
     * Set current subclass data by the id
     *
     * @param int $id subclass id
     * @param bool $reset reset stored data in the static variable
     *
     * @return array|false current cc id that was set
     */

    public function set_current_by_id($id, $reset = false) {

        // validate
        $id = intval($id);
        if (!$id) {
            return ($this->current = array());
        }
        try {
            //if ($id) {
            $this->current = $this->get_by_id($id, "");
            // set additional data
            $this->_current_id = $id;
            // return result
            return $this->current;
            //}
        } catch (Exception $e) {
            nc_print_status($e->getMessage(), 'error');
        }

        // reject
        return false;
    }

    /**
     * @param $id
     * @param string $item
     * @param int $nc_ctpl
     * @param int $reset
     * @param string $type
     * @return null|string|array
     * @throws Exception
     */
    public function get_by_id($id, $item = "", $nc_ctpl = 0, $reset = 0, $type = '') {
        $storage = &$this->ctpl_cache;

        // validate parameters
        $id = intval($id);
        //$nc_ctpl = intval($nc_ctpl);
        // check initialized object
        if (empty($storage[$id][$nc_ctpl]) || $reset) {
            if (!empty($this->data[$id]) && !$reset) {
                $storage[$id][$nc_ctpl] = $this->data[$id];
            }
            else {
                nc_core::get_object()->clear_cache_on_low_memory();
                $this->data[$id] = $this->db->get_row("SELECT * FROM `" . $this->essence . "` WHERE `" . $this->essence . "_ID` = '" . $id . "'", ARRAY_A);
                if (empty($this->data[$id])) {
                    //return false;
                    throw new Exception("Sub_Class with id  " . $id . " does not exist");
                }
                $real_value = array('Read_Access_ID', 'Write_Access_ID', 'Edit_Access_ID', 'Delete_Access_ID', 'Checked_Access_ID', 'Moderation_ID', 'Cache_Access_ID', 'Cache_Lifetime');
                foreach ($real_value as $v) {
                    $this->data[$id]['_db_' . $v] = $this->data[$id][$v];
                }
                $storage[$id][$nc_ctpl] = $this->data[$id];
            }

            $storage[$id][$nc_ctpl] = $this->inherit($storage[$id][$nc_ctpl], $nc_ctpl, $type);
        }
        else {
            // Если указан другой тип шаблона, чем тот, что уже был найден, нужно подобрать шаблон заново
            if ($type && $type != $storage[$id][$nc_ctpl]['Type']) {
                $properties_to_reset = array(
                    'FormPrefix', 'FormSuffix', 'RecordTemplate', 'RecordTemplateFull',
                    'TitleTemplate', 'TitleList', 'UseAltTitle', 'AddTemplate', 'EditTemplate',
                    'AddActionTemplate', 'EditActionTemplate', 'SearchTemplate',
                    'FullSearchTemplate', 'SubscribeTemplate', 'Settings',
                    'AddCond', 'EditCond', 'DeleteCond', 'CheckActionTemplate',
                    'DeleteActionTemplate', 'CustomSettingsTemplate',
                    'ClassDescription', 'DeleteTemplate', 'ClassTemplate',
                    'Type', 'File_Mode', 'File_Path', 'File_Hash', 'Real_Class_ID'
                );

                foreach ($properties_to_reset as $k) {
                    unset($storage[$id][$nc_ctpl][$k]);
                }

                $storage[$id][$nc_ctpl] = $this->inherit($storage[$id][$nc_ctpl], $nc_ctpl, $type);
            }
        }

        // if item requested return item value
        if ($item && is_array($storage[$id][$nc_ctpl])) {
            return array_key_exists($item, $storage[$id][$nc_ctpl]) ? $storage[$id][$nc_ctpl][$item] : "";
        }

        return $storage[$id][$nc_ctpl];
    }

    /**
     * @param $cc_env
     * @return null
     */
    protected function inherit($cc_env) {
        $nc_ctpl = (int) func_get_arg(1);
        $type    = (int) func_get_arg(2);
        // system superior object
        $nc_core = nc_Core::get_object();

        if (empty($cc_env)) {
            global $perm;
            // error message
            if ($perm instanceof Permission && $perm->isSupervisor()) {
                // backtrace info
                $debug_backtrace_info = debug_backtrace();
                // choose error
                if (isset($debug_backtrace_info[2]['function']) && $debug_backtrace_info[2]['function'] == "nc_objects_list") {
                    // error info for the supervisor
                    trigger_error(sprintf(NETCAT_FUNCTION_OBJECTS_LIST_CC_ERROR, $debug_backtrace_info[2]['args'][1]), E_USER_WARNING);
                }
                else {
                    // error info for the supervisor
                    trigger_error(sprintf(NETCAT_FUNCTION_LISTCLASSVARS_ERROR_SUPERVISOR, $cc), E_USER_WARNING);
                }
            }

            return null;
        }

        $nc_tpl_in_cc = 0;
        if ($cc_env['Class_Template_ID'] && !$nc_ctpl) {
            $nc_tpl_in_cc = $cc_env['Class_Template_ID'];
        }

        $class_env = $nc_core->component->get_for_cc($cc_env['Sub_Class_ID'], $cc_env['Class_ID'], $nc_ctpl, $nc_tpl_in_cc, $type);

        foreach ((array)$class_env AS $key => $val) {
            if (!array_key_exists($key, $cc_env) || $cc_env[$key] == "") {
                $cc_env[$key] = $val;
            }
        }

        if ($cc_env["NL2BR"] == -1) {
            $cc_env["NL2BR"] = $class_env["NL2BR"];
        }

        if ($cc_env["AllowTags"] == -1) {
            $cc_env["AllowTags"] = $class_env["AllowTags"];
        }

        if ($cc_env["UseCaptcha"] == -1) {
            $cc_env["UseCaptcha"] = $class_env["UseCaptcha"];
        }

        if ($cc_env['MinRecordsInInfoblock'] === null) {
            $cc_env['MinRecordsInInfoblock'] = $class_env['MinRecordsInInfoblock'];
        }

        if ($cc_env['MaxRecordsInInfoblock'] === null) {
            $cc_env['MaxRecordsInInfoblock'] = $class_env['MaxRecordsInInfoblock'];
        }

        if ($nc_core->modules->get_by_keyword("cache")) {
            if ($cc_env["CacheForUser"] == -1) {
                $cc_env["CacheForUser"] = $class_env["CacheForUser"];
            }
        }

        if ($class_env['CustomSettingsTemplate']) {
            $a2f = new nc_a2f($class_env['CustomSettingsTemplate'], 'CustomSettings');
            $a2f->set_value($cc_env['CustomSettings']);
            $cc_env["Sub_Class_Settings"] = $a2f->get_values_as_array();
        }

        $cc_env['sysTbl'] = intval($class_env['System_Table_ID']);

        $sub_env = $nc_core->subdivision->get_by_id($cc_env["Subdivision_ID"]);

        $inherited_params = array('Read_Access_ID', 'Write_Access_ID', 'Edit_Access_ID', 'Checked_Access_ID',
            'Delete_Access_ID', 'Subscribe_Access_ID', 'Moderation_ID');
        if ($nc_core->modules->get_by_keyword("cache")) {
            $inherited_params[] = 'Cache_Access_ID';
            $inherited_params[] = 'Cache_Lifetime';
        }

        foreach ($inherited_params as $v) {
            if (!$cc_env[$v]) {
                $cc_env[$v] = $sub_env[$v];
            }
        }

        $cc_env['Subdivision_Name'] = $sub_env['Subdivision_Name'];
        $cc_env['Hidden_URL'] = $sub_env['Hidden_URL'];

        $Domain = $nc_core->catalogue->get_by_id($cc_env['Catalogue_ID'], 'Domain');
        $cc_env['Hidden_Host'] = $Domain ? $Domain : $nc_core->DOMAIN_NAME;

        return $cc_env;
    }

    /**
     * @param $str
     * @return int
     */
    public function validate_english_name($str) {
        // Check string length: database scheme stores up to 64 characters.
        if (mb_strlen($str) > self::MAX_KEYWORD_LENGTH) {
            return 0;
        }
        // validate Hidden_URL
        return nc_preg_match('/^[\w' . NETCAT_RUALPHABET . '-]+$/', $str);
    }


    /**
     * Проверяет, является ли EnglishName уникальным для инфоблока в указанном разделе
     *
     * @param $subdivision_id
     * @param $infoblock_id
     * @param $english_name
     * @return bool
     */
    public function is_english_name_unique_in_subdivision($subdivision_id, $infoblock_id, $english_name) {
        $db = nc_db();
        return !$db->get_var(
            "SELECT 1
               FROM `Sub_Class`
              WHERE `EnglishName` = '" . $db->escape($english_name) . "'
                AND `Subdivision_ID` = " . (int)$subdivision_id . "
                AND `Sub_Class_ID` != " . (int)$infoblock_id
        );
    }

    /**
     * @param $id
     * @param string $item
     * @return array|string
     */
    public function get_mirror($id, $item = '') {
        $res = array();
        foreach ($this->data as $v) {
            if ($v['SrcMirror'] == $id) {
                if ($item) {
                    return array_key_exists($item, $v) ? $v[$item] : "";
                }
                $res = $v;
            }
        }

        return $res;
    }


    /**
     * Создаёт инфоблок.
     *
     * @param int|string  Идентификатор или ключевое слово компонента
     * @param array $properties  Свойства инфоблока
     *      Subdivision_ID — должно быть указано
     *      EnglishName —
     *          Если не указано, транслитерируется из Sub_Class_Name.
     *          Если уже существует, добавляется суффикс "-1", "-2" и т.п.
     *      Priority — если не указано, то следующий по порядку приоритет в родительском разделе
     * @param array $custom_settings  Пользовательские настройки компонента для инфоблока
     * @return int  ID
     * @throws Exception когда не найден родительский раздел или компонент, при ошибке создания инфоблока
     */
    public function create($component, array $properties, array $custom_settings = null) {
        $nc_core = nc_core::get_object();

        $subdivision_id = $properties['Subdivision_ID'] = (int)$properties['Subdivision_ID'];
        $site_id = $nc_core->subdivision->get_by_id($subdivision_id, 'Catalogue_ID');

        // преобразование ключевого слова в ID; гарантирует существование компонента
        $component_id = $nc_core->component->get_by_id($component, 'Class_ID');

        if (empty($properties['EnglishName'])) {
            unset($properties['EnglishName']);
        }

        // Значения по умолчанию
        $now = new nc_db_expression("NOW()");
        $default_name = $nc_core->component->get_by_id($component_id, 'Class_Name');
        $default_keyword =
            nc_array_value($properties, 'Sub_Class_Name')
            ?: $nc_core->component->get_by_id($component_id, 'Keyword')
            ?: 'object';

        $defaults = array(
            'Class_ID' => $component_id,
            'Catalogue_ID' => $site_id,
            'Sub_Class_Name' => $default_name,
            'EnglishName' => strtolower(nc_transliterate($default_keyword, true)),
            'Class_Template_ID' => 0,
            'LastUpdated' => $now,
            'Created' => $now,
            'Checked' => 1,
        );

        foreach ($defaults as $field => $default_value) {
            if (!isset($properties[$field])) {
                $properties[$field] = $default_value;
            }
        }

        // Установка EnglishName
        $properties['EnglishName'] = $this->get_available_english_name($subdivision_id, $properties['EnglishName']);

        if (!$this->validate_english_name($properties['EnglishName'])) {
            throw new Exception(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID);
        }

        // Установка приоритета
        if (isset($properties['Priority'])) {
            // задан приоритет, сдвигаем имеющиеся инфоблоки «вниз»
            $properties['Priority'] = (int)$properties['Priority'];
            $nc_core->db->query(
                "UPDATE `Sub_Class`
                    SET `Priority` = `Priority` + 1
                  WHERE `Subdivision_ID` = $subdivision_id
                    AND `Priority` >= " . $properties['Priority']
            );
        }
        else {
            $properties['Priority'] = 1 + $nc_core->db->get_var(
                "SELECT MAX(`Priority`) FROM `Sub_Class` WHERE `Subdivision_ID` = " . (int)$subdivision_id
            );
        }

        // Пользовательские настройки макета дизайна в разделе
        $component_custom_settings = $nc_core->component->get_by_id($properties['Class_Template_ID'] ?: $component_id, 'CustomSettingsTemplate');
        if ($component_custom_settings) {
            $a2f = new nc_a2f($component_custom_settings, 'CustomSettings');
            $a2f->set_initial_values();

            if (isset($properties['CustomSettings'])) {
                $a2f->set_values($properties['CustomSettings']);
            }

            if ($custom_settings) {
                $a2f->set_values($custom_settings);
            }

            if (!$a2f->validate($a2f->get_values_as_array())) {
                throw new Exception($a2f->get_validation_errors());
            }

            $a2f->save($custom_settings);

            $properties['CustomSettings'] = $a2f->get_values_as_string();
        }

        $nc_core->event->execute(nc_event::BEFORE_INFOBLOCK_CREATED, $site_id, $subdivision_id, null);
        $infoblock_id = nc_db_table::make('Sub_Class')->insert($properties);

        if (!$infoblock_id) {
            throw new Exception("Unable to create infoblock\n" . $nc_core->db->last_error);
        }

        $nc_core->event->execute(nc_event::AFTER_INFOBLOCK_CREATED, $site_id, $subdivision_id, $infoblock_id);

        return $infoblock_id;
    }

    /**
     * Возвращает доступное ключевое слово инфоблока в указанном разделе, добавляя
     * при необходимости суффикс "-1", "-2" и т. п.
     * @param $subdivision_id
     * @param $desired_english_name
     * @return string
     */
    public function get_available_english_name($subdivision_id, $desired_english_name) {
        $english_name = substr($desired_english_name, 0, self::MAX_KEYWORD_LENGTH);
        $suffix = 1;

        $nc_core = nc_core::get_object();
        while ($nc_core->db->get_var(
            "SELECT 1
               FROM `Sub_Class`
              WHERE `Subdivision_ID` = " . (int)$subdivision_id . "
                AND `EnglishName` = '" . $nc_core->db->escape($english_name) . "'"
        )) {
            $english_name = substr($desired_english_name, 0, self::MAX_KEYWORD_LENGTH - 1 - strlen($suffix)) . '-' . ($suffix++);
        }

        return $english_name;
    }

    /**
     * Копирование инфоблока
     * @param int $source_infoblock_id
     * @param int $destination_subdivision_id
     * @param bool $copy_objects
     * @return int ID созданного инфоблока
     * @throws Exception
     */
    public function duplicate($source_infoblock_id, $destination_subdivision_id, $copy_objects = true) {
        $nc_core = nc_core::get_object();

        $source_infoblock_id = (int)$source_infoblock_id;
        $destination_subdivision_id = (int)$destination_subdivision_id;

        $source_infoblock_properties = $nc_core->db->get_row(
            "SELECT * FROM `Sub_Class` WHERE `Sub_Class_ID` = $source_infoblock_id",
            ARRAY_A
        );

        if (!$source_infoblock_properties) {
            throw new Exception("Infoblock $source_infoblock_id does not exist");
        }

        $component_id = $source_infoblock_properties['Class_ID'];

        $new_infoblock_data = $source_infoblock_properties;
        unset($new_infoblock_data['Sub_Class_ID'], $new_infoblock_data['Priority']);
        if ($destination_subdivision_id) {
            $new_infoblock_data['Subdivision_ID'] = $destination_subdivision_id;
        }

        $new_infoblock_id = $this->create($component_id, $new_infoblock_data);

        if ($copy_objects) {
            $object_ids = $nc_core->db->get_col("SELECT `Message_ID` FROM `Message{$component_id}` WHERE `Sub_Class_ID` = '$source_infoblock_id'");
            foreach ($object_ids as $object_id) {
                $nc_core->message->duplicate($component_id, $object_id, $new_infoblock_id);
            }
        }

        if (nc_module_check_by_keyword('requests')) {
            nc_requests_form_settings_infoblock::duplicate_settings_for_all_form_types($source_infoblock_id, $new_infoblock_id);
        }

        return $new_infoblock_id;
    }

    /**
     * @param $infoblock_id
     */
    public function create_mock_objects($infoblock_id) {
        $nc_core = nc_core::get_object();
        $component_id = $this->get_by_id($infoblock_id, 'Class_ID');
        $min_records = (int)$this->get_by_id($infoblock_id, 'MinRecordsInInfoblock');
        $num_records = nc_db()->get_var(
            "SELECT COUNT(*) FROM `Message{$component_id}` WHERE `Sub_Class_ID` = " . (int)$infoblock_id
        );
        for ($i = $num_records; $i < $min_records; $i++) {
            $nc_core->message->create_mock($infoblock_id);
        }
    }

    /**
     * Возвращает свойства первого инфоблока в разделе из указанного компонента
     *
     * @param $subdivision_id
     * @param $component_id_or_keyword
     * @param null|string $property
     * @return mixed
     * @throws nc_Exception_Class_Doesnt_Exist
     */
    public function get_first_subdivision_infoblock_by_component_id($subdivision_id, $component_id_or_keyword, $property = null) {
        $nc_core = nc_core::get_object();

        $component_id = $nc_core->component->get_by_id($component_id_or_keyword, 'Class_ID');

        $infoblocks_in_subdivision = $this->get_by_subdivision_id($subdivision_id);
        foreach ($infoblocks_in_subdivision as $infoblock) {
            if ($infoblock['Class_ID'] == $component_id) {
                return $property ? $infoblock[$property] : $infoblock;
            }
        }

        return null;
    }

    /**
     *
     */
    public function clear_cache() {
        unset($this->data, $this->ctpl_cache, $this->subdivision_cache, $this->subdivision_first_checked_cache);
    }
}
