<?php

class nc_backup_site extends nc_backup_base {

    //--------------------------------------------------------------------------

    protected $name = TOOLS_SYSTABLE_SITES;

    /** @var  nc_db_table */
    protected $site_table;
    /** @var  nc_db_table */
    protected $classificator_table;
    /** @var  nc_db_table */
    protected $multifield_table;
    /** @var  nc_db_table */
    protected $subdivision_table;
    /** @var  nc_db_table */
    protected $subclass_table;
    /** @var  nc_db_table */
    protected $template_table;
    /** @var  nc_db_table */
    protected $template_partial_table;
    /** @var  nc_db_table */
    protected $settings_table;
    /** @var  nc_db_table */
    protected $class_table;
    /** @var  nc_db_table */
    protected $field_table;
    /** @var  nc_db_table */
    protected $system_table;

    protected $template_paths            = array();
    protected $custom_settings_relations = array();
    protected $new_components            = array();
    protected $file_fields               = array();
    protected $relation_fields           = array();
    protected $relation_message_fields   = array();
    protected $simple_file_fields        = array();
    protected $imported_component_keywords = array();

    protected $exported_classificators = array();
    protected $preexisting_classificators = array();

    protected $not_imported_auxiliary_components = array();

    //--------------------------------------------------------------------------

    protected function init() {
        $this->site_table = nc_db_table::make('Catalogue');
        $this->classificator_table = nc_db_table::make('Classificator');
        $this->subdivision_table = nc_db_table::make('Subdivision');
        $this->multifield_table = nc_db_table::make('Multifield');
        $this->subclass_table = nc_db_table::make('Sub_Class');
        $this->template_table = nc_db_table::make('Template');
        $this->template_partial_table = nc_db_table::make('Template_Partial');
        $this->settings_table = nc_db_table::make('Settings');
        $this->class_table = nc_db_table::make('Class');
        $this->field_table = nc_db_table::make('Field');
        $this->system_table = nc_db_table::make('System_Table');
    }

    //-------------------------------------------------------------------------

    protected function reset() {
        parent::reset();
        $this->template_paths          = array();
        $this->new_components          = array();
        $this->file_fields             = array();
        $this->relation_fields         = array();
        $this->relation_message_fields = array();
        $this->simple_file_fields      = array();
        $this->imported_component_keywords = array();
    }

    //-------------------------------------------------------------------------

    protected function row_attributes($ids) {
        $titles = $this->site_table->select('Catalogue_ID, Catalogue_Name, Domain')->where_in_id((array)$ids)->index_by_id()->get_result();

        $result = array();
        foreach ($titles as $id => $row) {
            $result[$id] = array(
                'title'       => $row['Catalogue_Name'] . ' (' . $row['Domain'] . ')',
                'sprite'      => 'nc--site',
                'netcat_link' => $this->nc_core->ADMIN_PATH . "subdivision/full.php?CatalogueID={$id}"
            );
        }

        return $result;
    }

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function export_form() {
        $options    = array(''=>'');

        $result = $this->site_table
            ->select('Catalogue_ID, Catalogue_Name, Domain')
            ->order_by('Priority')
            ->order_by('Catalogue_ID')
            ->order_by('Catalogue_Name')
            ->index_by_id()
            ->as_object()
            ->get_result();


        foreach ($result as $site_id => $row) {
            $options[$site_id] = $site_id . '. ' . $row->Catalogue_Name . ' (' . $row->Domain . ')';
        }

        return $this->nc_core->ui->form->add_row(SECTION_CONTROL_CLASS)->select('id', $options);
    }

    //-------------------------------------------------------------------------

    protected function export_validation() {
        if (!$this->id) {
            $this->set_validation_error('Site not selected');
            return false;
        }
        return true;
    }

    //-------------------------------------------------------------------------

    protected function export_process() {
        global $SUB_FOLDER, $HTTP_FILES_PATH, $DOCUMENT_ROOT;
        $nc_core = nc_core::get_object();

        $id   = $this->id;
        $site = $this->site_table->where_id($id)->get_row();

        if (!$site) {
            return false;
        }

        // Если задан параметр экспорта named_entities_path, «системные» сущности:
        //  — макеты, у которых указано ключевое слово,
        //  — компоненты и их шаблоны, у которых указано ключевое слово,
        //  — списки
        //  — виджеты (@todo)
        // будут экспортированы отдельно в указанную папку (в подпапки components/KEYWORD/,
        // templates/KEYWORD, lists/TABLE_NAME), а
        $export_settings = $this->dumper->get_export_settings();
        $named_entities_path = nc_array_value($export_settings, 'named_entities_path');
        $keyword_map = array();


        $this->exported_classificators = array();

        $this->dumper->register_dict_field('Catalogue_ID', 'Class_ID', 'Sub_Class_ID', 'Template_ID', 'Class_Template_ID', 'Subdivision_ID');

        // Catalogue
        $data = array($id => $site);
        $this->dumper->export_data('Catalogue', 'Catalogue_ID', $data);


        // Settings
        $data = $this->settings_table->where('Catalogue_ID', $id)->index_by_id()->get_result();
        $this->dumper->export_data('Settings', 'Settings_ID', $data);

        // System table fields
        $system_table_fields = $this->field_table->where('System_Table_ID', '!=', 0)->index_by_id()->get_result();
        $this->dumper->export_data('Field', 'Field_ID', $system_table_fields);

        $system_table_fields_by_table = array();
        foreach ($system_table_fields as $field) {
            $system_table_name = $nc_core->get_system_table_name_by_id($field['System_Table_ID']);
            $system_table_fields_by_table[$system_table_name][] = $field['Field_Name'];
        }
        foreach ($system_table_fields_by_table as $system_table => $system_table_fields) {
            $this->dumper->export_table_fields($system_table, $system_table_fields);
        }

        // Subdivisions
        $data = $this->subdivision_table->where('Catalogue_ID', $id)->where('Parent_Sub_ID', 0)->index_by_id()->get_result();
        $parent_ids = array_keys($data);
        while ($parent_ids) {
            $result     = $this->subdivision_table->where_in('Parent_Sub_ID', $parent_ids)->index_by_id()->get_result();
            $parent_ids = array_keys($result);
            $data      += $result;
        }
        $this->dumper->export_data('Subdivision', 'Subdivision_ID', $data);

        // Template Settings
        $template_settings = array();
        if ($site['TemplateSettings']) {
            $template_settings[0] = $site['TemplateSettings']; // zero index for catalogue (site)
        }
        foreach ($data as $sub_id => $sub) {
            if ($sub['TemplateSettings']) {
                $template_settings[$sub_id] = $sub['TemplateSettings'];
            }
        }


        // Sub_Class
        $sub_ids     = $this->dumper->get_dict('Subdivision_ID');
        $sub_classes = $this->subclass_table->where_in('Subdivision_ID', $sub_ids)->index_by_id()->get_result();
        $this->dumper->export_data('Sub_Class', 'Sub_Class_ID', $sub_classes);


        ##### TEMPLATES #####

        $tpl_settings_file_fields = array();
        $template_ids = $this->dumper->get_dict('Template_ID');
        unset($template_ids[0]);
        if ($template_ids) {
            do {
                $template_ids = array_unique($this->template_table->where_in_id($template_ids)->get_list('Parent_Template_ID'));
                $template_ids[0] = 0;
            } while(call_user_func_array('max', $template_ids));
            unset($template_ids[0]);
            $template_ids = array_keys($template_ids);

            // Template
            $templates = $this->template_table->where_in_id($template_ids)->index_by_id()->get_result();
            $data = $templates;
            while ($template_ids) {
                $result       = $this->template_table->where_in('Parent_Template_ID', $template_ids)->index_by_id()->get_result();
                $template_ids = array_keys($result);
                $data        += $result;
            }

            $all_data = $data;
            if ($named_entities_path) {
                foreach ($data as $template_id => $template_data) {
                    if ($template_data['Parent_Template_ID'] == 0 && $template_data['Keyword']) {
                        $template_export_settings = array_merge($export_settings, array(
                            'path' => $named_entities_path . '/templates/' . $template_data['Keyword'],
                            'file_name_suffix' => $template_data['Keyword'],
                        ));
                        $this->export_named_entity('template', $template_id, $template_export_settings);
                    }

                    if (preg_match('!^/\d*[a-zA-Z_]!', $template_data['File_Path'])) {
                        // это макет с ключевым словом или «подмакет» такого макета,
                        // он будет записан отдельно (предыдущий if)
                        unset(
                            $data[$template_id],
                            $templates[$template_id]
                        );
                        $keyword_map['Template'][trim($template_data['File_Path'], '/')] = $template_id;
                    }
                }
            }

            $this->dumper->export_data('Template', 'Template_ID', $data);

            // Custom settings files
            foreach ($all_data as $tpl_id => $tpl) {
                if ($tpl['CustomSettings']) {
                    $settings_array = false;
                    @eval($tpl['CustomSettings']);
                    if (is_array($settings_array)) {
                        foreach ($settings_array as $sfield_name => $settings_field) {
                            if ($settings_field['type'] == 'file') {
                                $tpl_settings_file_fields[$sfield_name] = $sfield_name;
                            }
                        }
                    }
                }
            }
            unset($all_data);

            // Export files
            foreach ($templates as $tpl) {
                if ($tpl['File_Mode']) {
                    $this->dumper->export_files(nc_core('HTTP_TEMPLATE_PATH') . 'template', $tpl['File_Path']);
                }
            }

            // Template partials
            $template_ids = array_keys($data);
            $data = $this->template_partial_table->where_in('Template_ID', $template_ids)->get_result();
            $this->dumper->export_data('Template_Partial', 'Template_Partial_ID', $data);
        }

        // Export template settings files
        if ($tpl_settings_file_fields && $template_settings) {
            foreach ($template_settings as $settings_string) {
                $TemplateSettings = false;
                @eval($settings_string);
                if ($TemplateSettings) {
                    foreach ($tpl_settings_file_fields as $sfield_name) {
                        if (!empty($TemplateSettings[$sfield_name])) {
                            $file = explode(':', $TemplateSettings[$sfield_name]);
                            $file = $HTTP_FILES_PATH . $file[3];
                            if (file_exists($DOCUMENT_ROOT . $file) && !is_dir($DOCUMENT_ROOT . $file)) {
                                $this->dumper->export_files($file);
                            }
                        }
                    }
                }
            }
        }
        unset($tpl_settings_file_fields);
        unset($template_settings);


        ##### COMPONENTS #####
        $db = nc_db();
        $file_fields = array();
        $multiple_file_fields = array();

        // Class
        $component_ids = $this->dumper->get_dict('Class_ID');

        $components = $this->class_table
                           ->where_in_id($component_ids)
                           ->index_by_id()
                           ->get_result();

        // Class templates
        $component_template_ids = $this->dumper->get_dict('Class_Template_ID');

        // Добавить в архив шаблоны компонентов, которые используются на сайте,
        // а также специальные шаблоны (шаблоны для административной части,
        // корзины, rss и т.д.)
        $component_templates = (array) $db->get_results(
            "SELECT *
               FROM `Class`
              WHERE `ClassTemplate` > 0
                AND (
                    `Class_ID` IN (" . join(',', $component_template_ids) . ")
                    OR (`ClassTemplate` IN (" . join(',', $component_ids) . ")
                        AND `Type` NOT IN ('useful', 'mobile', 'responsive')
                       )
                    )
              ",
            ARRAY_A,
            'Class_ID');

        $all_component_ids = $component_ids;
        $components_and_templates = $components + $component_templates;
        if ($named_entities_path) {
            // Экспорт компонентов в отдельную папку
            foreach ($components_and_templates as $component_id => $component_data) {
                $component_keyword = $component_data['Keyword'];
                if ($component_keyword) {
                    unset(
                        $components_and_templates[$component_id],
                        $component_ids[$component_id],
                        $components[$component_id],
                        $component_templates[$component_id]
                    );
                    if ($component_data['ClassTemplate'] == 0) {
                        $component_export_settings = array_merge($export_settings, array(
                            // на случай, если там если у компонента разные шаблоны на разных сайтах,
                            // чтобы не затереть шаблоны с другого сайта:
                            'remove_existing' => false,
                            'path' => $named_entities_path . '/components/' . $component_keyword,
                            'file_name_suffix' => $component_keyword,
                        ));
                        $this->export_named_entity('component', $component_id, $component_export_settings);

                        $keyword_map['Field'][$component_keyword] = $this->field_table
                            ->where('Class_ID', $component_id)
                            ->get_list('Field_Name', 'Field_ID');
                    }
                    $keyword_map['Class'][trim($component_data['File_Path'], '/')] = $component_id;
                }
            }
        }

        if (count($components_and_templates)) {
            $this->dumper->export_data('Class', 'Class_ID', $components_and_templates);
        }

        // For tables marked as 'auxiliary', save field names in dump_info['auxiliary_component_fields_$ID']
        // as a comma-separated string
        $db->query("SET group_concat_max_len=16384");
        $auxiliary_component_fields = $db->get_col("
            SELECT IFNULL(GROUP_CONCAT(`f`.`Field_Name` ORDER BY `f`.`Field_Name`), ''),
                   `c`.`Class_ID`
              FROM `Class` AS `c`  LEFT JOIN `Field` AS `f` USING (`Class_ID`)
             WHERE `c`.`Class_ID` IN (" . join(', ', $all_component_ids) . ")
               AND `c`.`IsAuxiliary` = 1
             GROUP BY `c`.`Class_ID`",
            0, 1);

        foreach ($auxiliary_component_fields as $aux_component_id => $aux_component_fields) {
            $this->dumper->set_dump_info("auxiliary_component_fields_" . $aux_component_id, $aux_component_fields);
        }

        // Field
        $fields_to_export = $this->field_table->where_in('Class_ID', $component_ids)->index_by_id()->get_result();
        $this->dumper->export_data('Field', 'Field_ID', $fields_to_export);
        unset($fields_to_export);

        $all_fields = $this->field_table->where_in('Class_ID', $all_component_ids)->get_result();

        // Classificators
        foreach ($all_fields as $field) {
            if ($field['TypeOfData_ID'] == NC_FIELDTYPE_SELECT || $field['TypeOfData_ID'] == NC_FIELDTYPE_MULTISELECT) {
                list($classificator_table) = explode(':', $field['Format']);

                if ($classificator_table && !isset($this->exported_classificators[$classificator_table])) {
                    // Do that only once for each classifier
                    $this->exported_classificators[$classificator_table] = true;
                    $classificator = $this->classificator_table->where('Table_Name', $classificator_table)->get_row();

                    if (!$classificator) {
                        trigger_error(__CLASS__ . ": classifier '$classificator_table' does not exist (field: '$field[Field_Name]')", E_USER_WARNING);
                        continue;
                    }

                    if ($named_entities_path) {
                        $classificator_export_settings = array_merge($export_settings, array(
                            'path' => $named_entities_path . '/lists/' . $classificator_table,
                            'file_name_suffix' => $classificator_table,
                        ));
                        $this->export_named_entity('classificator', $classificator['Classificator_ID'], $classificator_export_settings);
                    }
                    else {
                        $data = array($classificator['Classificator_ID'] => $classificator);
                        $this->dumper->export_data('Classificator', 'Classificator_ID', $data);

                        // Export data: Classificator_{Table_Name}
                        $c_table = 'Classificator_' . $classificator['Table_Name'];
                        $c_pk    = $classificator['Table_Name'] . '_ID';

                        $classificator_data_table = nc_db_table::make($c_table, $c_pk);
                        $data = $classificator_data_table->get_result();

                        $this->dumper->export_data($c_table, $c_pk, $data);

                        // Export table: Classificator_{Table_Name}
                        $this->dumper->export_table($c_table);
                    }
                }
            }
            elseif ($field['TypeOfData_ID'] == NC_FIELDTYPE_FILE) {
                $file_fields[$field['Class_ID']][$field['Field_ID']] = $field['Field_Name'];
            }
            elseif ($field['TypeOfData_ID'] == NC_FIELDTYPE_MULTIFILE) {
                $multiple_file_fields[$field['Class_ID']][$field['Field_ID']] = $field['Field_ID'];
            }
        }

        foreach ($components as $component_id => $component) {
            // Do not export the /sys/ (`User`) component templates
            if ($component['System_Table_ID']) { continue; }

            // Message*
            $this->dumper->export_table('Message' . $component_id);

            // Export component files
            if ($component['File_Mode']) {
                $this->dumper->export_files(nc_core('HTTP_TEMPLATE_PATH') . 'class', $component['File_Path'], false);
            }
        }

        // Export component template files (including /sys/*)
        foreach ($component_templates as $component_template_id => $component_template) {
            if ($component_template['File_Mode']) {
                // Убираем последнюю часть пути к шаблону компонента:
                $component_path_parts = explode('/', rtrim($component_template['File_Path'], '/'));
                $last_fragment = array_pop($component_path_parts);
                $folder = join('/', $component_path_parts);
                $this->dumper->export_files(nc_core('HTTP_TEMPLATE_PATH') . 'class' . $folder, $last_fragment, false);
            }
        }


        ##### DATA #####
        $system_component_ids = $this->class_table
                                     ->where_in_id($component_ids)
                                     ->where('System_Table_ID', '!=', 0)
                                     ->get_list('Class_ID');

        if ($sub_classes) {
            $file_info = nc_core::get_object()->file_info;
            $files_to_export = array('url', 'preview_url');

            foreach ($sub_classes as $sub_class_id => $sub_class) {
                $component_id      = $sub_class['Class_ID'];

                if (in_array($component_id, $system_component_ids)) {
                    continue;
                }

                $message_table = nc_db_table::make('Message' . $component_id, 'Message_ID');

                // Data
                $data = $message_table->where('Sub_Class_ID', $sub_class_id)
                            ->order_by('Parent_Message_ID')
                            ->index_by_id()->get_result();
                $this->dumper->export_data($message_table->get_table(), 'Message_ID', $data);
                $message_ids = array_keys($data);

                // Files
                if (isset($file_fields[$component_id])) {
                    $file_info->cache_object_list_data($component_id, $data);
                    $file_info->preload_filetable_values($component_id, $message_ids);
                    $filetable_data = array();

                    foreach ($data as $row) {
                        foreach ($file_fields[$component_id] as $field_id => $field_name) {
                            if (!$row[$field_name]) {
                                continue;
                            }

                            $message_id = $row['Message_ID'];
                            $file = $file_info->get_file_info($component_id, $message_id, $field_name, false, false);
                            foreach ($files_to_export as $f) { // export 'url' and 'preview_url'
                                // safety precautions in case file_info returns malformed data:
                                if (file_exists($DOCUMENT_ROOT . $file[$f]) && !is_dir($DOCUMENT_ROOT . $file[$f])) {
                                    $this->dumper->export_files($file[$f]);
                                }
                            }

                            if ($file['fs_type'] === NC_FS_PROTECTED) {
                                $filetable_values = $file_info->get_filetable_values($component_id, $message_id, $field_name, true);
                                if ($filetable_values) { $filetable_data[] = $filetable_values; }
                            }
                        }
                    }

                    $this->dumper->export_data('Filetable', 'ID', $filetable_data);

                    $file_info->clear_cache();
                }

                // Multiple files
                if (isset($multiple_file_fields[$component_id])) {
                    $data = $this->multifield_table
                        ->where_in('Message_ID', $message_ids)
                        ->where_in('Field_ID', $multiple_file_fields[$component_id])->get_result();
                    $this->dumper->export_data($this->multifield_table->get_table(), 'ID', $data);

                    foreach ($multiple_file_fields[$component_id] as $field_id) {
                        $this->dumper->export_files($SUB_FOLDER . $HTTP_FILES_PATH . 'multifile', $field_id);
                    }
                }
            }
        }

        if ($named_entities_path) {
            $this->dumper->set_dump_info('keywords', $keyword_map);
            $this->dumper->set_dump_info('required_lists', $this->exported_classificators);
        }

        return true;
    }

    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------

    public function call_event($event, $attr) {
        if (strpos($event, 'before_insert_classificator_') === 0) {
            return $this->event_before_insert_classificator_item($attr[0], substr($event, strlen('before_insert_classificator_')));
        }

        return parent::call_event($event, $attr);
    }

    protected function import_process() {

        $this->dumper->register_dict_field(array(
            'Parent_Sub_ID' => 'Subdivision_ID',
            'Parent_Template_ID' => 'Template_ID',
            'ClassTemplate' => 'Class_ID',
            'Class_Template_ID' => 'Class_ID',
            'Edit_Class_Template' => 'Class_ID',
            'Admin_Class_Template' => 'Class_ID',
        ));

        // Add fields for system tables
        $this->dumper->import_table_fields('Catalogue');
        $this->dumper->import_table_fields('Subdivision');
        $this->dumper->import_table_fields('Template');
        $this->dumper->import_table_fields('User');

        // Template
        $this->dumper->import_data('Template');
        $this->dumper->import_data('Template_Partial');

        $this->dumper->import_data('Catalogue');
        $this->new_id = $this->dumper->get_dict('Catalogue_ID', $this->id);

        // Class
        $this->dumper->import_data('Class');

        // Field
        $this->dumper->import_data('Field');

        // Classificator
        $this->dumper->import_data('Classificator');

        // Message*
        $class_ids = $this->dumper->get_dict('Class_ID');
        foreach ($class_ids as $old_id => $new_id) {
            if (isset($this->new_components[$new_id])) {
                $this->dumper->import_table('Message' . $old_id, 'Message' . $new_id);
            }
        }

        // Subdivision
        $this->dumper->import_data('Subdivision');

        // Update Catalogue
        $site = $this->site_table->where_id($this->new_id)->get_row();
        $site_update = array(
            'E404_Sub_ID'  => $site['E404_Sub_ID']  ? $this->dumper->get_dict('Subdivision_ID', $site['E404_Sub_ID'])  : null,
            'Title_Sub_ID' => $site['Title_Sub_ID'] ? $this->dumper->get_dict('Subdivision_ID', $site['Title_Sub_ID']) : null,
        );
        // Set current domain
        if ($this->site_table->count_all() == 1) {
            if ($_SERVER['HTTP_HOST']) {
                $site_update['Domain'] = $_SERVER['HTTP_HOST'];
            }
        }
        $this->site_table->where_id($this->new_id)->update($site_update);

        // Sub_Class
        $this->dumper->import_data('Sub_Class');

        // Custom settings relations
        if ($this->custom_settings_relations) {
            // Catalogue
            $result = $this->site_table->where_id($this->new_id)->get_value('TemplateSettings');
            if ($TemplateSettings = $this->make_custom_settings_array($result)) {
                $this->site_table->where_id($this->new_id)->update(array(
                    'TemplateSettings' => $TemplateSettings
                ));
            }
            // Subdivisions
            $sub_ids = $this->dumper->get_dict('Subdivision_ID');
            $sub_template_settings = $this->subdivision_table->where_in_id($sub_ids)->get_list('TemplateSettings');
            foreach ($sub_template_settings as $sub_id => $template_settings) {
                if ($TemplateSettings = $this->make_custom_settings_array($template_settings)) {
                    $this->subdivision_table->where_id($sub_id)->update(array(
                        'TemplateSettings' => $TemplateSettings
                    ));
                }
            }
        }

        // DATA
        foreach ($class_ids as $old_id => $new_id) {
            if (isset($this->new_components[$new_id]) || isset($this->not_imported_auxiliary_components[$new_id])) {
                $this->dumper->import_data("Message{$old_id}", "Message{$new_id}", array(
                    'Parent_Message_ID' => "Message{$new_id}.Message_ID",
                ));
            }
        }

        // DATA fields related to another message row
        foreach ($this->relation_message_fields as $message_id => $relation_message_fields) {
            $message_table = nc_db_table::make('Message' . $message_id, 'Message_ID');
            foreach ($relation_message_fields as $field_name => $field) {
                $old_class_id    = (int)$field['Format'];
                $new_class_id    = $this->dumper->get_dict('Class_ID', $old_class_id);
                // $new_message_ids = $this->dumper->get_dict("Message{$new_class_id}.Message_ID");

                $data = $message_table->select("`Message_ID`, `{$field_name}`")->index_by_id()->get_result();
                foreach ($data as $row_id => $row) {
                    $value = $this->dumper->get_dict("Message{$new_class_id}.Message_ID", $row[$field_name]);
                    $message_table->where_id($row_id)->update(array($field_name => $value));
                }

                $this->field_table->where_id($field['Field_ID'])->update(array('Format'=>$new_class_id));
            }
        }

        // Settings
        $this->dumper->import_data('Settings');

        // Files
        $HTTP_TEMPLATE_PATH = nc_core('HTTP_TEMPLATE_PATH');
        $HTTP_FILES_PATH = nc_core('HTTP_FILES_PATH');

        $this->dumper->import_files(array($HTTP_TEMPLATE_PATH . 'template',
                                          $HTTP_TEMPLATE_PATH . 'class',
                                          $HTTP_FILES_PATH));

        $this->dumper->import_data('Filetable');

        // Multifield
        $this->dumper->import_data('Multifield');

        // Execute AFTER_SITE_CREATED event handler
        nc_core::get_object()->event->execute(nc_event::AFTER_SITE_CREATED, $this->new_id);
    }

    //-------------------------------------------------------------------------

    protected function make_custom_settings_array($row) {
        if (!$row) {
            return false;
        }

        $TemplateSettings = null;

        @eval($row);

        if (!$TemplateSettings) {
            return false;
        }

        foreach ($this->custom_settings_relations as $template_id => $rel_fields) {
            foreach ($rel_fields as $settings_field => $dict_field) {
                if (isset($TemplateSettings[$settings_field])) {
                    $TemplateSettings[$settings_field] = $this->dumper->get_dict($dict_field, $TemplateSettings[$settings_field]);
                }
            }
        }

        return '$TemplateSettings = ' . var_export($TemplateSettings, true) . ';';
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_catalogue($row) {
        $domain_exists = (bool) $this->site_table->where('Domain', $row['Domain'])->count_all();
        if ($domain_exists) {
            $row['Domain'] = 'domain-' . uniqid();
        }
        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_settings($row) {
        if (substr($row['Key'], 0, 13) == 'nc_shop_mode_') {
            $row['Key'] = 'nc_shop_mode_' . $row['Catalogue_ID'];
        }

        if ($row['Value']) {
            $module_settings = $this->backup->get_settings('module_settings');
            if (isset($module_settings[$row['Module']]['settings_dict_fields'][$row['Key']])) {
                $dict_field = $module_settings[$row['Module']]['settings_dict_fields'][$row['Key']];
                // Значение может быть как числом, так и списком идентификаторов через запятую.
                $ids = preg_split('/\s*,\s*/', $row['Value']);
                foreach ($ids as $key => $id) {
                    $ids[$key] = $this->dumper->get_dict($dict_field, $id);
                }
                $row['Value'] = join(',', $ids); // Если были пробелы, они будут потеряны
            }
        }

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_after_insert_template($row, $insert_id) {

        // Обновление пути к файлам макета
        $update = array(
            'File_Path' => ($row['Parent_Template_ID']
                                ? $this->template_paths[$row['Parent_Template_ID']]
                                : '/') .
                           ($row['Keyword'] ?: $insert_id) .
                           "/",
        );
        $this->template_paths[$insert_id] = $update['File_Path'];
        $this->template_table->where_id($insert_id)->update($update);


        // Пользовательские параметры — связь с другим объектом
        $dict_fields = array(
            'sub'   => 'Subdivision_ID',
            'cc'    => 'Sub_Class_ID',
            'class' => 'Class_ID',
            // user => UserID
        );

        if ($row['CustomSettings']) {
            $settings_array = array();
            eval($row['CustomSettings']);
            foreach ($settings_array as $settings_field => $settings) {
                if ($settings['type'] == 'rel' && isset($dict_fields[$settings['subtype']])) {
                    $dict_key = $dict_fields[$settings['subtype']];
                    $this->custom_settings_relations[$insert_id][$settings_field] = $dict_key;
                }
            }
        }
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_class($row) {
        // Для таблицы «Пользователи» пропускаем основной класс и шаблоны с
        // совпадающими ключевыми словами.
        $db = nc_db();
        $existing_component_id = null;

        if ($row['System_Table_ID']) {
            // таблица «Пользователи»
            $condition = $row['ClassTemplate'] == 0 ? "`ClassTemplate` = 0" : "`Keyword` = '$row[Keyword]'";
            $existing_component_id = $db->get_var(
                "SELECT `Class_ID`
                   FROM `Class`
                  WHERE `System_Table_ID` = 3
                    AND $condition"
            );
        }

        // Для «вспомогательных» и «служебных» компонентов, помеченных как
        // IsAuxiliary (но не их дополнительных шаблонов), попробуем найти
        // (по названию и набору полей) уже существующий компонент;
        // если таковой есть, не будем создавать новый компонент
        // (совместимость со старыми версиями)
        $auxiliary_component_fields = $this->dumper->get_dump_info('auxiliary_component_fields_' . $row['Class_ID']);
        if ($auxiliary_component_fields !== null && !$row['ClassTemplate']) {
            $db->query("SET group_concat_max_len=16384");
            $existing_component_id = $db->get_var(
                "SELECT `c`.`Class_ID`,
                        IFNULL(GROUP_CONCAT(`f`.`Field_Name` ORDER BY `f`.`Field_Name`), '') AS `Fields`
                   FROM `Class` AS `c`  LEFT JOIN `Field` AS `f` USING (`Class_ID`)
                  WHERE `c`.`Class_Name` = '" . $db->escape($row['Class_Name']) . "'
                    AND `c`.`Class_Group` = '" . $db->escape($row['Class_Group']) . "'
                    AND `c`.`IsAuxiliary` = 1
                  GROUP BY `c`.`Class_ID`
                 HAVING `Fields` = '" . $db->escape($auxiliary_component_fields) . "'"
            );
        }

        // компоненты и их шаблоны при совпадении по ключевому слову
        // с существующими не импортируются заново
        if (!$existing_component_id && $row['Keyword']) {
            $existing_component_id = $db->get_var(
                "SELECT `Class_ID`
                   FROM `Class`
                  WHERE `File_Path` = '$row[File_Path]'"
            );
        }

        if ($existing_component_id) {
            $this->dumper->set_dict('Class_ID', $row['Class_ID'], $existing_component_id);
            $this->not_imported_auxiliary_components[$existing_component_id] = $existing_component_id;
            $this->imported_component_keywords[$existing_component_id] =
                $db->get_var("SELECT `Keyword` FROM `Class` WHERE `Class_ID` = $existing_component_id")
                ?: $existing_component_id;
            return false;
        }

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_after_insert_class($row, $class_id) {
        $parent_class_folder = '';

        $this->imported_component_keywords[$class_id] = $row['Keyword'];
        if ($row['ClassTemplate']) {
            $parent_class_folder .= '/';

            if (!empty($this->imported_component_keywords[$class_id])) {
                $parent_class_folder .= $this->imported_component_keywords[$row['ClassTemplate']];
            }
            else {
                $parent_class_folder .= $row['ClassTemplate'];
            }
        }
        else {
            $this->new_components[$class_id] = $class_id;
        }

        $update = array(
            'File_Path' => "$parent_class_folder/" . ($row['Keyword'] ?: $class_id) . "/",
        );

        $this->class_table->where_id($class_id)->update($update);
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_multifield($row) {
        foreach (array('Path', 'Preview') as $f) {
            $row[$f] = explode('/', $row[$f]);
            $row[$f][3] = $this->dumper->get_dict('Field_ID', $row[$f][3]);
            $row[$f] = implode('/', $row[$f]);
        }

        $new_component_id = $this->get_component_id_by_field_id($row['Field_ID']);
        $row['Message_ID'] = $this->dumper->get_dict("Message{$new_component_id}.Message_ID", $row['Message_ID']);

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_filetable($row) {
        $file_path = explode('/', $row['File_Path']); // "/123/456/"
        $file_path[1] = $this->dumper->get_dict('Subdivision_ID', $file_path[1]);
        $file_path[2] = $this->dumper->get_dict('Sub_Class_ID', $file_path[2]);
        $row['File_Path'] = join('/', $file_path);

        $new_component_id = $this->get_component_id_by_field_id($row['Field_ID']);
        $row['Message_ID'] = $this->dumper->get_dict("Message{$new_component_id}.Message_ID", $row['Message_ID']);

        return $row;
    }

    // ------------------------------------------------------------------------

    protected function event_before_insert_field($row) {
        // skip fields of the components that were not imported
        if ($row['Class_ID'] || $row['System_Table_ID']) {
            $existing_field_id = $this->field_table
                ->where('Class_ID', $row['Class_ID'])
                ->where('System_Table_ID', $row['System_Table_ID'])
                ->where('Field_Name', $row['Field_Name'])
                ->get_value('Field_ID');

            if ($existing_field_id) {
                $this->dumper->set_dict('Field_ID', $row['Field_ID'], $existing_field_id);
                $this->store_field_info($row, $existing_field_id);
                return false;
            }
        }
        else if ($row['Widget_Class_ID']) {
            // виджеты в текущей версии не импортируются
            return false;
        }

        return $row;
    }

    // ------------------------------------------------------------------------

    protected function event_after_insert_field($row, $field_id) {
        $this->store_field_info($row, $field_id);
    }

    // ------------------------------------------------------------------------

    protected function store_field_info($row, $field_id) {
        $class_id   = $row['Class_ID'];
        $field_name = $row['Field_Name'];
        $row['Field_ID'] = $field_id;
        switch ($row['TypeOfData_ID']) {
            // File fields
            case NC_FIELDTYPE_FILE:
                $this->file_fields[$class_id][$field_name] = $row;
                break;

            // Relation fields
            case NC_FIELDTYPE_RELATION:
                if (is_numeric($row['Format'])) {
                    $this->relation_message_fields[$class_id][$field_name] = $row;
                } else {
                    $this->relation_fields[$class_id][$field_name] = $row;
                }
                break;
        }
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_classificator($row) {
        $exists = $this->classificator_table->where('Table_Name', $row['Table_Name'])->count_all();

        if ($exists) {
            $this->preexisting_classificators[strtolower($row['Table_Name'])] = $row['Table_Name'];
            $this->dumper->import_data('Classificator_' . $row['Table_Name']);
            return false;
        }

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_after_insert_classificator($row) {
        $table = 'Classificator_' . $row['Table_Name'];
        $this->dumper->import_table($table);
        $this->dumper->import_data($table);
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_classificator_item($row, $classificator_lowercase_name) {
        if (!isset($this->preexisting_classificators[$classificator_lowercase_name])) {
            // (This event handler is intended to process only items of the classifiers
            // which existed prior to the current import operation, see
            // event_before_insert_classificator() method)
            return $row;
        }

        // Proper (capitalized) table name:
        $classificator = $this->preexisting_classificators[$classificator_lowercase_name];
        $id_field = "{$classificator}_ID";
        $name_field = "{$classificator}_Name";

        // Check if there is a record with the same Name and Value
        $existing_id = nc_db_table::make("Classificator_{$classificator}")
                            ->where($name_field, $row[$name_field])
                            ->where('Value', $row['Value'])
                            ->get_value($id_field);

        if ($existing_id) {
            $this->dumper->set_dict($id_field, $row[$id_field], $existing_id);
            return false;
        }

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_message($component_id, $row) {
        $result = null;

        if (isset($this->relation_fields[$component_id])) {
            foreach ($this->relation_fields[$component_id] as $key => $field) {
                $new_val = null;
                $val     = $row[$key];
                $format  = strtolower(current(explode(':', $field['Format'], 2)));
                switch ($format) {
                    case 'subdivision':
                        $new_val = $this->dumper->get_dict('Subdivision_ID', $val);
                        break;
                    case 'subclass':
                    case 'sub_class':
                    case 'sub-class':
                        $new_val = $this->dumper->get_dict('Sub_Class_ID', $val);
                        break;
                    case 'catalogue':
                        $new_val = $this->dumper->get_dict('Catalogue_ID', $val);
                        break;
                }
                if ($new_val && $new_val != $val) {
                    $row[$key] = $new_val;
                    $result    = $row;
                }
            }
        }

        if (isset($this->file_fields[$component_id])) {
            foreach ($this->file_fields[$component_id] as $key => $field) {
                $val = $row[$key];
                $val = explode(':', $val);
                if (isset($val[3])) { // «Стандартная файловая система» (/netcat_files/12/34/file.ext)
                    $file = explode('/', $val[3]);
                    $file[0] = $this->dumper->get_dict('Subdivision_ID', $file[0]);
                    $file[1] = $this->dumper->get_dict('Sub_Class_ID', $file[1]);
                    $val[3]  = implode('/', $file);
                }
                $row[$key] = implode(':', $val);
            }
            $result = $row;
        }

        return $result;
    }

    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------

    protected function detect_type_by_path($path) {
        global $HTTP_TEMPLATE_PATH, $HTTP_FILES_PATH;

        $types = array(
            'class'    => $HTTP_TEMPLATE_PATH . 'class/',
            'template' => $HTTP_TEMPLATE_PATH . 'template/',
            'multifile' => $HTTP_FILES_PATH . 'multifile/',
            'files'    => $HTTP_FILES_PATH,
        );

        foreach ($types as $type => $type_path) {
            if (substr($path, 0, strlen($type_path)) == $type_path) {
                return $type;
            }
        }

        return null;
    }

    //-------------------------------------------------------------------------

    protected function event_before_copy_file($path, $file) {
        switch ($this->detect_type_by_path($path)) {
            case 'class':
                if (isset($this->not_imported_auxiliary_components[$file])) {
                    return false;
                }

                $full_path_parts = explode('/', $path . $file);
                $i = sizeof($full_path_parts) - 1;
                do {
                    if (ctype_digit($full_path_parts[$i])) {
                        $full_path_parts[$i] = $this->dumper->get_dict('Class_ID', $full_path_parts[$i]);
                    }
                    $i--;
                }
                while ($i && $full_path_parts[$i] != 'class');

                return implode('/', $full_path_parts);

            case 'template':
                return $path . $this->dumper->get_dict('Template_ID', $file);

            case 'multifile':
                return $path . $this->dumper->get_dict('Field_ID', $file);

            case 'files':
                $full_path = $path . $file;

                if (preg_match('@/(\d+)/(\d+)(/[^/]+)?$@', $full_path, $matches)) {
                    $full_path = explode('/', $full_path);

                    $i = count($full_path) - (isset($matches[3]) ? 2 : 1);
                    $full_path[$i] = $this->dumper->get_dict('Sub_Class_ID', $full_path[$i]);
                    $i--;
                    $full_path[$i] = $this->dumper->get_dict('Subdivision_ID', $full_path[$i]);

                    return implode('/', $full_path);
                }

                // «Простая файловая система»:
                // нужно убедиться, что файл находится в корне netcat_files
                $num_path_parts = substr_count($full_path, '/');
                if ($num_path_parts == 2 && preg_match('@^(preview_)?(\d+)_(\d+)(\.\w+)?$@', $file, $parts)) {
                    $old_field_id = $parts[2];
                    $new_field_id = $this->dumper->get_dict('Field_ID', $old_field_id);
                    $new_component_id = $this->get_component_id_by_field_id($new_field_id);
                    $new_object_id = $this->dumper->get_dict("Message{$new_component_id}.Message_ID", $parts[3]);

                    return $path . $parts[1] . $new_field_id . "_" . $new_object_id . nc_array_value($parts, 4, '');
                }

                return $full_path;

            default:
                return $path . $file;
        }
    }

    //-------------------------------------------------------------------------

    protected function event_after_copy_file($path) {
        global $DOCUMENT_ROOT;

        $rel_path = substr($path, strlen($DOCUMENT_ROOT));

        switch ($this->detect_type_by_path($rel_path)) {
//            case 'class':
//                $items = scandir($path);
//                foreach ($items as $file) {
//                    if (is_numeric($file) && is_dir($path . '/' . $file)) {
//                        $new_file = $this->dumper->get_dict('Class_ID', $file);
//                        if ($new_file != $file) {
//                            rename($path . '/' . $file, $path . '/' . $new_file);
//                        }
//                    }
//                }
//                break;

            case 'template':
                $items = scandir($path);
                foreach ($items as $file) {

                    if (is_numeric($file) && is_dir($path . '/' . $file)) {
                        if ($new_file = $this->dumper->get_dict('Template_ID', $file, false)) {
                            rename($path . '/' . $file, $path . '/' . $new_file);
                            $this->event_after_copy_file($path . '/' . $new_file);
                        }
                    }
                }
                break;
        }
    }

    // ------------------------------------------------------------------------

    protected function get_component_id_by_field_id($field_id) {
        static $cache = array();
        if (!isset($cache[$field_id])) {
            $cache[$field_id] = $this->field_table->where_id($field_id)->get_value('Class_ID');
        }
        return $cache[$field_id];
    }

    // ------------------------------------------------------------------------

    protected function export_named_entity($type, $id, $settings) {
        $backup = new nc_backup();
        $backup->export($type, $id, $settings);
    }

}
