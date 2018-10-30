<?php

class nc_Message extends nc_essence {

    protected $db;

    /**
     * Constructor function
     */
    public function __construct() {
        // load parent constructor
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();
        // system db object
        if (is_object($nc_core->db)) $this->db = $nc_core->db;

        $this->register_event_listeners();
    }

    /**
     * Обработчики для обновления и сброса кэша
     */
    protected function register_event_listeners() {
        $event = nc_core::get_object()->event;
        $on_change = array($this, 'update_cache_on_change');
        $event->add_listener(nc_event::AFTER_OBJECT_CREATED, $on_change);
        $event->add_listener(nc_event::AFTER_OBJECT_UPDATED, $on_change);
        $event->add_listener(nc_event::AFTER_OBJECT_ENABLED, $on_change);
        $event->add_listener(nc_event::AFTER_OBJECT_DISABLED, $on_change);
        $event->add_listener(nc_event::AFTER_OBJECT_DELETED, $on_change);
    }


    public function get_current($item = "") {
        // dummy
        return false;
    }

    public function set_current_by_id($id, $reset = false) {
        // dummy
        return false;
    }

    public function get_by_id($class_id, $id, $item = "", $reset = false) {
        // validate parameters
        $class_id = intval($class_id);
        $id = intval($id);

        // check inited object
        if (empty($this->data[$class_id][$id]) || $reset) {
            nc_core::get_object()->clear_cache_on_low_memory();
            $this->data[$class_id][$id] = $this->db->get_row("SELECT * FROM `".$this->essence.$class_id."`
      WHERE `".$this->essence."_ID` = '".$id."'", ARRAY_A);
        }

        // if item requested return item value
        if ($item && is_array($this->data[$class_id][$id])) {
            return array_key_exists($item, $this->data[$class_id][$id]) ? $this->data[$class_id][$id][$item] : "";
        }

        return $this->data[$class_id][$id];
    }

    /**
     * Функция для удаления объектов компонентов
     * @param mixed `identificators` - идентификаторы объектов подлежащих удалению в виде массива, или цифры
     * @param int `class_id` номер компонента, в котором производится удаление
     * @param bool `trash` параметр, определяющий, перед будет ли объект помещен в корзину перед удалением. 1 - объект будет помещен в корзину, затем удален, 0 - объект будет удален.
     * @return int `affected` - затронутые объекты or false
     */
    public function delete_by_id($identificators, $class_id = 0, $trash = false) {
        $nc_core = nc_Core::get_object();
        $MODULE_FOLDER = $nc_core->MODULE_FOLDER;

        // validate parameters
        $affected = 0;
        $classID = intval($class_id);

        // Приводим первый параметр к массиву
        if (!is_array($identificators)) {
            $messages_to_delete = array(intval($identificators));
        } else {
            $messages_to_delete = array_map("intval", $identificators);
        }

        if (empty($messages_to_delete)) return 0;

        // Выясняем данные по всем объектам для удаления - номера разделов,
        // компонентов в разделе и номера сайтов, а тажке формируем массив номеров удаляемых объектов

        $temp_array = $this->db->get_results("SELECT m.* , m.`Message_ID` AS `m_id`, m.`Subdivision_ID` as `sub_id`, m.`Sub_Class_ID` as `cc_id`, s.`Catalogue_ID` as `cat_id`
    FROM `Message".$classID."` as m, `Subdivision` as s
    WHERE m.`Subdivision_ID` = s.`Subdivision_ID` AND  m.`Message_ID` IN (".join(',', $messages_to_delete).")", ARRAY_A);

        $message_to_delete = array();
        $message_to_delete_ids = array();
        $message_to_delete_data = array();

        if (!empty($temp_array)) {
            foreach ($temp_array as $v) {
                // группируем объекты по сайты/разделу/сс
                $message_to_delete[$v['cat_id']][$v['sub_id']][$v['cc_id']][] = intval($v['m_id']);
                $message_to_delete_data[$v['cat_id']][$v['sub_id']][$v['cc_id']][intval($v['m_id'])] = $v;
                $message_to_delete_ids[] = $v['m_id'];
            }
        } else {
            return 0;
        }

        $nc_core->event->execute(nc_Event::BEFORE_OBJECT_DELETED, $messages_to_delete);

        try {
            if ($trash) {
                $trashing_result_arr = $nc_core->trash->add($messages_to_delete, $classID);
            }
        } catch (nc_Exception_Trash_Full $e) {
            $trash = 0;
        } catch (nc_Exception_Trash_Folder_Fail $e) {
            $trash = 0;
        }

        // Удаляем комментарии
        if (nc_module_check_by_keyword("comments")) {
            include_once ($MODULE_FOLDER."comments/function.inc.php");
            // get need ids
            $comments_temp = $this->db->get_results("SELECT `Message_ID`, `Sub_Class_ID` FROM `Message".$classID."`
      WHERE `Message_ID` IN (".join(',', $message_to_delete_ids).") OR `Parent_Message_ID` IN (".join(',', $message_to_delete_ids).")", ARRAY_A);

            // compile arrays
            $temp_messages = array();
            $temp_ccs = array();
            foreach ((array) $comments_temp AS $comments_temp_value) {
                if (!in_array($comments_temp_value['Message_ID'], $temp_messages))
                        $temp_messages[] = $comments_temp_value['Message_ID'];
                if (!in_array($comments_temp_value['Sub_Class_ID'], $temp_ccs))
                        $temp_ccs[] = $comments_temp_value['Sub_Class_ID'];
            }

            // delete comments
            nc_comments::dropComments($this->db, $temp_ccs, "Sub_Class", $temp_messages);

            // delete comment rules
            nc_comments::dropRuleMessage($this->db, $temp_messages, $temp_ccs);
            // clear
            unset($comments_temp, $temp_ccs, $temp_messages);
        }

        // delete related files
        // поочередно удаляем файлы у всех перечисленных к удалению объектов
        // если они не отправляются в корзину, иначе файлам ставим метку deleted
        if (!$trash) {
            foreach ($message_to_delete_ids as $id) {
                DeleteMessageFiles($classID, $id);
            }
        } else {
            $nc_core->trash->TrashMessageFiles($classID, $message_to_delete_ids);
        }

        // Удаляем сами объекты
        $this->db->query("DELETE FROM `Message".$classID."` WHERE `Message_ID` IN (".join(',', $message_to_delete_ids).")");
        if ($this->db->is_error) {
            throw new nc_Exception_DB_Error($this->db->last_query, $this->db->last_error);
        } else {
            $affected = $this->db->rows_affected;
        }

        // execute core action
        if ($message_to_delete) {
            foreach ($message_to_delete as $site_id => $site) {
                foreach ($site as $sub_id => $sub) {
                    foreach ($sub as $cc_id => $messages_in_cc) {
                        $nc_core->event->execute(nc_Event::AFTER_OBJECT_DELETED, $site_id, $sub_id, $cc_id,
                                $classID, $messages_in_cc, $message_to_delete_data[$site_id][$sub_id][$cc_id]);
                    }
                }
            }
        }

        $children_ids = $this->db->get_col("SELECT `Message_ID` FROM `Message".$classID."` WHERE `Parent_Message_ID` IN  (".join(',', $message_to_delete_ids).")");

        if (!empty($children_ids)) {
            // delete related files
            if ($this->db->is_error) {
                throw new nc_Exception_DB_Error($this->db->last_query, $this->db->last_error);
            }

            $affected += $this->db->rows_affected;

            // execute core action
            $affected += $this->delete_by_id($children_ids, $classID, $trash);
        }

        return $affected;
    }

    /**
     * Создание объекта
     * @param $infoblock_id
     * @param array $object_properties
     * @return int ID созданного объекта
     * @throws Exception
     */
    public function create($infoblock_id, array $object_properties) {
        $nc_core = nc_core::get_object();
        $now = new nc_db_expression("NOW()");

        $infoblock_id = (int)$infoblock_id;
        $component_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Class_ID');
        $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');
        $site_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Catalogue_ID');
        $user_id = $GLOBALS['AUTH_USER_ID'];

        $table = nc_db_table::make('Message' . $component_id, 'Message_ID');
        if (!isset($priority)) {
            $priority = 1 + $nc_core->db->get_var(
                "SELECT MAX(`Priority`)
                   FROM `Message$component_id`
                  WHERE `Sub_Class_ID` = $infoblock_id
                    AND `Parent_Message_ID` = 0"
            );
        }

        $component = $nc_core->get_component($component_id);
        $files = array();
        foreach ($component->get_fields(NC_FIELDTYPE_FILE) as $file_field) {
            $file_field_name = $file_field['name'];
            if (isset($object_properties[$file_field_name])) {
                $files[$file_field_name] = $object_properties[$file_field_name];
                unset($object_properties[$file_field_name]);
            }
        }

        $multiple_files = array();
        foreach ($component->get_fields(NC_FIELDTYPE_MULTIFILE) as $file_field) {
            $file_field_name = $file_field['name'];
            if (isset($object_properties[$file_field_name])) {
                $multiple_files[$file_field_name] = $object_properties[$file_field_name];
                unset($object_properties[$file_field_name]);
            }
        }

        unset($object_properties['Sub_Class_ID']);
        $object_properties = $object_properties + array(
                'User_ID' => $user_id,
                'Subdivision_ID' => $subdivision_id,
                'Sub_Class_ID' => $infoblock_id,
                'Priority' => $priority,
                'Checked' => 1,
                'IP' => $_SERVER['REMOTE_ADDR'],
                'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
                'Created' => $now,
            );

        $nc_core->event->execute(nc_event::BEFORE_OBJECT_CREATED, $site_id, $subdivision_id, $infoblock_id, $component_id, null);
        $object_id = $table->insert($object_properties);

        if (!$object_id) {
            throw new Exception("Unable to create an object\n" . $nc_core->db->last_error);
        }

        foreach ($files as $file_field => $file_path) {
            $nc_core->files->field_save_file($component_id, $file_field, $object_id, $file_path);
        }

        // @todo save NC_FIELDTYPE_MULTIFILE values
        // foreach ($multiple_files as $file_field => $file_path) {
        // }

        $nc_core->event->execute(nc_event::AFTER_OBJECT_CREATED, $site_id, $subdivision_id, $infoblock_id, $component_id, $object_id);

        return $object_id;
    }

    /**
     * Копирование объекта
     *
     * @param int $component_id
     * @param int $object_id
     * @param int $destination_infoblock_id
     * @return int ID созданного объекта
     * @throws Exception
     * @throws nc_Exception_Class_Doesnt_Exist
     */
    public function duplicate($component_id, $object_id, $destination_infoblock_id) {
        $nc_core = nc_core::get_object();

        $component_id = $nc_core->component->get_by_id($component_id, 'Class_ID');
        $object_id = (int)$object_id;

        $component = $nc_core->get_component($component_id);

        $object_properties = $nc_core->db->get_row(
            "SELECT * FROM `Message{$component_id}` WHERE `Message_ID` = $object_id",
            ARRAY_A
        );

        if (!$object_properties) {
            throw new Exception("Object $object_id does not exist in component $component_id");
        }

        unset($object_properties['Message_ID'], $object_properties['Sub_Class_ID'], $object_properties['Subdivision_ID']);

        $nc_core->file_info->cache_object_data($component_id, $object_properties);
        foreach ($component->get_fields(NC_FIELDTYPE_FILE) as $file_field) {
            $file_field_name = $file_field['name'];
            $file_data = $nc_core->file_info->get_file_info($component_id, $object_id, $file_field_name, false);
            $object_properties[$file_field_name] = $file_data['url'];
        }
        $multifile_field_values = nc_get_multifile_field_values($component_id, $object_id);
        if ($multifile_field_values) {
            /** @var nc_multifield $file_data */
            foreach ($multifile_field_values as $file_field_name => $file_data) {
                $object_properties[$file_field_name] = array();
                foreach ($file_data->records as $file) {
                    $object_properties[$file_field_name][] = $file['Path'];
                }
            }
        }

        return $this->create($destination_infoblock_id, $object_properties);
    }

    /**
     * Создаёт запись-«заглушку».
     * Если в папке компонента есть RecordMockData.html, он должен вернуть массив
     * со значениями полей (для каждого поля может быть задан массив, тогда значение
     * поля будет выбрано из значений массива случайным образом)
     * @param $infoblock_id
     * @return int ID записи
     * @throws Exception
     */
    public function create_mock($infoblock_id) {
        $nc_core = nc_core::get_object();
        $component_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Class_ID');

        $fields = $nc_core->get_component($component_id)->get_fields();
        $object_properties = array();
        foreach ($fields as $field) {
            if ($field['not_null'] && !$field['default']) {
                switch ($field['type']) {
                    case NC_FIELDTYPE_BOOLEAN:
                    case NC_FIELDTYPE_INT:
                    case NC_FIELDTYPE_FLOAT:
                        $value = 0;
                        break;
                    case NC_FIELDTYPE_DATETIME:
                        $value = date("Y-m-d H:i:s");
                        break;
                    default:
                        $value = '';
                        break;
                }
                $object_properties[$field['name']] = $value;
            }
        }

        if ($nc_core->component->get_by_id($component_id, 'File_Mode')) {
            $component_template_id = $nc_core->sub_class->get_by_id($infoblock_id, 'ClassTemplate');
            $mock_data = $this->get_mock_data_file($component_template_id);
            if ($mock_data) {
                foreach ($mock_data as $key => $value) {
                    if (is_array($value)) {
                        $mock_data[$key] = $value[rand(0, sizeof($value)-1)];
                    }
                }
                $object_properties = array_merge($object_properties, $mock_data);
            }
        }

        return $this->create($infoblock_id, $object_properties);
    }

    /**
     * @param $component_template_id
     * @return array|null
     */
    protected function get_mock_data_file($component_template_id) {
        $nc_core = nc_core::get_object();
        $path =
            $nc_core->CLASS_TEMPLATE_FOLDER .
            $nc_core->component->get_by_id($component_template_id, 'File_Path') .
            'RecordMockData.html';

        if (file_exists($path)) {
            $mock_data = include $path;
            if (is_array($mock_data)) {
                return $mock_data;
            }
        }

        $component_id = $nc_core->component->get_by_id($component_template_id, 'ClassTemplate');
        if ($component_id) {
            return $this->get_mock_data_file($component_id);
        }

        return null;
    }

    /**
     * @param $site_id
     * @param $subdivision_id
     * @param $infoblock_id
     * @param $component_id
     * @param $object_id
     */
    public function update_cache_on_change($site_id, $subdivision_id, $infoblock_id, $component_id, $object_id) {
        nc_core::get_object()->file_info->clear_object_cache($component_id, $object_id);
        foreach ((array)$object_id as $id) {
            unset($this->data[$component_id][$id]);
        }
    }
}
