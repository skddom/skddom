<?php

class nc_infoblock_controller extends nc_ui_controller {

    protected $is_naked = false;

    /**
     *
     */
    protected function init() {
        $this->is_naked = true;
        $GLOBALS['isNaked'] = 1; // для nc_print_status() внутри Permission::ExitIfNotAccess() :(

        $this->bind('get_component_template_settings', array('component_id'));
    }

    /**
     * @param $result
     * @return string
     */
    protected function after_action($result) {
        if ($this->is_naked) {
            return $result;
        }

        return BeginHtml() . $result . EndHtml();
    }

    /**
     * @param $message
     * @return bool
     */
    protected function make_error_message($message) {
        return nc_print_status($message, 'error', array(), true);
    }

    /**
     * @param nc_a2f $a2f
     * @return string
     */
    protected function render_a2f(nc_a2f $a2f) {
        return $a2f->render(
            false,
            array(
                'checkbox' => '<div class="nc-field"><label>%VALUE %CAPTION</label></div>',
                'default' => '<div class="nc-field"><span class="nc-field-caption">%CAPTION:</span>%VALUE</div>',
            ),
            false,
            false
        );
    }

    /**
     * @param $infoblock_id
     * @param array $data
     * @return nc_db_table
     */
    protected function update($infoblock_id, array $data) {
        $nc_core = nc_core::get_object();
        $site_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Catalogue_ID');
        $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');

        if (isset($data['Checked']) && count($data) == 1 && $nc_core->sub_class->get_by_id($infoblock_id, 'Checked') != $data['Checked']) {
            $event = array(nc_event::BEFORE_INFOBLOCK_ENABLED, nc_event::AFTER_INFOBLOCK_ENABLED);
        }
        else {
            $event = array(nc_event::BEFORE_INFOBLOCK_UPDATED, nc_event::AFTER_INFOBLOCK_UPDATED);
        }

        $nc_core->event->execute($event[0], $site_id, $subdivision_id, $infoblock_id);
        $result = nc_db_table::make('Sub_Class')->where_id($infoblock_id)->update($data);
        $nc_core->event->execute($event[1], $site_id, $subdivision_id, $infoblock_id);

        return $result;
    }

    /**
     * @param $infoblock_id
     * @param int $action
     */
    protected function check_infoblock_permissions($infoblock_id, $action = NC_PERM_ACTION_ADMIN) {
        /** @var Permission $perm */
        global $perm;
        $perm->ExitIfNotAccess(NC_PERM_ITEM_CC, $action, $infoblock_id, null, 1);
    }

    /**
     * @param $subdivision_id
     * @param int $action
     */
    protected function check_subdivision_permissions($subdivision_id, $action = NC_PERM_ACTION_SUBCLASSADD) {
        /** @var Permission $perm */
        global $perm;
        $perm->ExitIfNotAccess(NC_PERM_ITEM_SUB, $action, $subdivision_id, null, 1);
    }

    /**
     * Устанавливает приоритет инфоблока $moved_infoblock_id таким образом,
     * чтобы он был непосредственно выше ($position = 'before') или ниже
     * ($position = 'after') относительно инфоблока $other_infoblock_id.
     * Метод исходит из допущения, что приоритеты инфоблоков в разделе уникальны.
     * @param int $moved_infoblock_id
     * @param string $position  'before' or 'after'
     * @param int $other_infoblock_id   Если 0, то размещает инфоблок первым вне зависимости от значения $position
     * @return bool
     */
    protected function place_infoblock($moved_infoblock_id, $position, $other_infoblock_id) {
        $moved_infoblock_id = (int)$moved_infoblock_id;
        $other_infoblock_id = (int)$other_infoblock_id;

        $nc_core = nc_core::get_object();
        $subdivision_id = $nc_core->sub_class->get_by_id($moved_infoblock_id, 'Subdivision_ID');
        $old_priority = $new_priority = $nc_core->sub_class->get_by_id($moved_infoblock_id, 'Priority');

        $all_infoblocks = $nc_core->sub_class->get_by_subdivision_id($subdivision_id, true);
        if (count($all_infoblocks) < 2) {
            return true;
        }

        if (!$other_infoblock_id) {
            // "other_infoblock_id=0" means "move to the top"
            $new_priority = $all_infoblocks[0]['Priority'];
        }
        elseif ($position == 'before') {
            foreach ($all_infoblocks as $infoblock) {
                if ($infoblock['Sub_Class_ID'] == $other_infoblock_id) {
                    $new_priority = $infoblock['Priority'];
                    break;
                }
            }
        }
        elseif ($position == 'after') {
            $infoblock = array('Priority' => 0);
            foreach ($all_infoblocks as $infoblock) {
                if ($infoblock['Sub_Class_ID'] == $other_infoblock_id) {
                    break;
                }
            }
            $new_priority = $infoblock['Priority'] + ($old_priority > $infoblock['Priority'] ? 1 : 0);
        }
        else {
            throw new InvalidArgumentException("\$position argument must be 'before' or 'after'");
        }

        if ($old_priority == $new_priority) {
            return true;
        }

        $nc_core->db->query(
            "UPDATE `Sub_Class`
                SET `Priority` = `Priority` - 1
              WHERE `Subdivision_ID` = $subdivision_id
                AND `Priority` >= $old_priority"
        );

        $nc_core->db->query(
            "UPDATE `Sub_Class`
                SET `Priority` = `Priority` + 1
              WHERE `Subdivision_ID` = $subdivision_id
                AND `Priority` >= $new_priority"
        );

        $this->update($moved_infoblock_id, array('Priority' => $new_priority));
        return true;
    }

    /**
     * @param int $component_id
     * @param array|null $values
     * @return string
     * @throws nc_Exception_Class_Doesnt_Exist
     */
    protected function get_custom_settings_html($component_id, $values = null) {
        $custom_settings_template = nc_core::get_object()->component->get_by_id($component_id, 'CustomSettingsTemplate');
        if ($custom_settings_template) {
            $a2f = new nc_a2f($custom_settings_template, 'custom_settings');
            $a2f->set_initial_values();
            if ($values) {
                $a2f->set_values($values);
            }
            return $this->render_a2f($a2f);
        }
        return '';
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    protected function action_update_custom_setting() {
        $nc_core = nc_core::get_object();

        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $key = $nc_core->input->fetch_post('key');
        $value = $nc_core->input->fetch_post('value');

        $this->check_infoblock_permissions($infoblock_id);

        $infoblock_data = $nc_core->sub_class->get_by_id($infoblock_id);

        $a2f = new nc_a2f($infoblock_data['CustomSettingsTemplate'], 'CustomSettings');
        $a2f->set_values($infoblock_data['CustomSettings']);
        $a2f->set_values(array($key => $value));

        if (!$a2f->validate($a2f->get_values_as_array())) {
            return $this->make_error_message($a2f->get_validation_errors());
        } else {
            $this->update($infoblock_id, array('CustomSettings' => $a2f->get_values_as_string()));
            return 'OK';
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function action_toggle() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $was_enabled = $nc_core->sub_class->get_by_id($infoblock_id, 'Checked');
        $this->update($infoblock_id, array('Checked' => (int)!$was_enabled));

        return 'OK';
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function action_change_order() {
        $nc_core = nc_core::get_object();

        $moved_infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $other_infoblock_id = (int)$nc_core->input->fetch_post('other_infoblock_id');
        $position = $nc_core->input->fetch_post('position');

        $this->check_infoblock_permissions($moved_infoblock_id);
        $this->check_infoblock_permissions($other_infoblock_id);

        $this->place_infoblock($moved_infoblock_id, $position, $other_infoblock_id);
        return 'OK';
    }

    /**
     * @return nc_ui_view
     */
    protected function action_show_settings_dialog() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $infoblock_data = $nc_core->sub_class->get_by_id($infoblock_id);
        $custom_settings = $this->get_custom_settings_html($infoblock_data['Class_Template_ID'] ? $infoblock_data['Class_Template_ID'] : $infoblock_data['Class_ID'], $infoblock_data['CustomSettings']);

        return $this->view('infoblock/settings_dialog', array(
            'infoblock_data' => $infoblock_data,
            'custom_settings' => $custom_settings,
        ));
    }

    /**
     *
     */
    protected function action_save() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $updated_properties = $nc_core->input->fetch_post('data') ?: array();
        if ($updated_properties) {
            $updated_properties = (array)$updated_properties;
        }
        $custom_settings = $nc_core->input->fetch_post('custom_settings') ?: array();

        if ($custom_settings || $nc_core->input->fetch_files('custom_settings')) {
            $a2f = new nc_a2f($nc_core->sub_class->get_by_id($infoblock_id, 'CustomSettingsTemplate'), 'custom_settings');
            $a2f->set_values($nc_core->sub_class->get_by_id($infoblock_id, 'CustomSettings'));
            $new_custom_settings = array_merge($a2f->get_values_as_array(), $custom_settings);
            if ($a2f->validate($new_custom_settings)) {
                $a2f->save_from_request_data('custom_settings');
                $updated_properties['CustomSettings'] = $a2f->get_values_as_string();
            } else {
                return $this->make_error_message($a2f->get_validation_errors());
            }
        }

        if ($updated_properties) {
            $this->update($infoblock_id, $updated_properties);
        }

        return 'OK';
    }

    /**
     * @return nc_ui_view
     */
    protected function action_show_delete_confirm_dialog() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post_get('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        return $this->view('infoblock/delete_confirm_dialog', array(
            'infoblock_id' => $infoblock_id,
        ));
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function action_delete() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $subdivision_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID');

        $this->check_subdivision_permissions($subdivision_id, NC_PERM_ACTION_SUBCLASSDEL);

        require_once $nc_core->ADMIN_FOLDER . 'subdivision/subclass.inc.php';

        if (DeleteSubClass($infoblock_id) === false) {
            return $this->make_error_message(CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_DELETE);
        }
        else {
            return "\nReloadPage=1\n";
        }
    }

    /**
     * @return nc_ui_view
     */
    protected function action_show_new_infoblock_dialog() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');
        $position = $nc_core->input->fetch_get_post('position');

        $this->check_subdivision_permissions($subdivision_id);

        $components = (array)nc_db()->get_results(
            "SELECT *
              FROM `Class`
             WHERE `ClassTemplate` = 0
               AND `IsAuxiliary` = 0
             ORDER BY `Class_Group`, `Priority`, `Class_ID`",
            ARRAY_A
        );

        return $this->view('infoblock/new_infoblock_dialog', array(
            'subdivision_id' => $subdivision_id,
            'infoblock_id' => $infoblock_id,
            'position' => $position,
            'components' => $components,
        ));
    }


    /**
     * @return nc_ui_view
     */
    protected function action_show_new_infoblock_simple_dialog() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');
        $infoblock_id = (int)$nc_core->input->fetch_get_post('infoblock_id');
        $position = $nc_core->input->fetch_get_post('position');

        $this->check_subdivision_permissions($subdivision_id);

        $component_templates = (array)nc_db()->get_results(
            "SELECT *
              FROM `Class` AS component
             WHERE `File_Mode` = 1
               AND `IsAuxiliary` = 0
               AND (`IsOptimizedForMultipleMode` = 1 OR (
                        SELECT COUNT(*)
                          FROM `Class` AS template
                         WHERE template.ClassTemplate = component.Class_ID
                           AND template.IsOptimizedForMultipleMode = 1
                   ))
             ORDER BY `Class_Group`, `ClassTemplate` = 0, `Priority`, `Class_ID`",
            ARRAY_A
        );

        if (!$component_templates) {
            return $this->action_show_new_infoblock_dialog();
        }

        return $this->view('infoblock/new_infoblock_simple_dialog', array(
            'subdivision_id' => $subdivision_id,
            'infoblock_id' => $infoblock_id,
            'position' => $position,
            'component_templates' => $component_templates,
        ));
    }


    /**
     * @return string
     */
    protected function action_create() {
        $nc_core = nc_core::get_object();
        $subdivision_id = (int)$nc_core->input->fetch_get_post('subdivision_id');

        /** @var Permission $perm */
        $this->check_subdivision_permissions($subdivision_id);

        // Свойства создаваемого инфоблока
        $infoblock_properties = (array)$nc_core->input->fetch_post('data');
        $infoblock_properties['Subdivision_ID'] = $subdivision_id;
        $component_id = (int)nc_array_value($infoblock_properties, 'Class_ID');
        $custom_settings = $nc_core->input->fetch_post('custom_settings') ?: array();

        // Положение инфоблока относительно другого инфоблока
        $position_infoblock_id = (int)$nc_core->input->fetch_post('position_infoblock_id');
        if ($position_infoblock_id) {
            $other_infoblock_priority = $nc_core->sub_class->get_by_id($position_infoblock_id, 'Priority');
            $position = $nc_core->input->fetch_post('position');
            $infoblock_properties['Priority'] = $other_infoblock_priority + ($position == 'before' ? 0 : 1);
        }

        if (!nc_array_value($infoblock_properties, 'Sub_Class_Name')) {
            $infoblock_properties['Sub_Class_Name'] = $nc_core->component->get_by_id($component_id, 'Class_Name');
        }

        try {
            $infoblock_id = $nc_core->sub_class->create($component_id, $infoblock_properties, $custom_settings);
            $nc_core->sub_class->create_mock_objects($infoblock_id);

            $infoblock_english_name = $nc_core->sub_class->get_by_id($infoblock_id, 'EnglishName');
            return "\nReloadPage=1\nSetLocationHash=$infoblock_english_name\n";
        }
        catch (Exception $e) {
            return $this->make_error_message($e->getMessage());
        }
    }

    /**
     * @param int $component_id
     * @return string (JSON)
     */
    protected function action_get_component_template_settings($component_id) {
        $nc_core = nc_core::get_object();
        if (!$nc_core->user->get_by_id($GLOBALS['AUTH_USER_ID'], 'InsideAdminAccess')) {
            return $this->make_error_message(NETCAT_MODERATION_ERROR_NORIGHT);
        }

        $template_info = array();

        $template_info[] = array(
            'id' => 0,
            'name' => NETCAT_MODERATION_COMPONENT_NO_TEMPLATE,
            'preview' => $nc_core->component->get_list_preview_relative_path($component_id, true),
            'settings' => $this->get_custom_settings_html($component_id),
            'multiple_mode' => $nc_core->component->get_by_id($component_id, 'IsOptimizedForMultipleMode'),
        );

        $templates = $nc_core->component->get_component_templates($component_id, 'useful');
        if ($templates) {
            foreach ($templates as $template) {
                $template_id = $template['Class_ID'];
                $template_info[] = array(
                    'id' => $template_id,
                    'name' => $template['Class_Name'],
                    'preview' => $nc_core->component->get_list_preview_relative_path($template_id, true),
                    'settings' => $this->get_custom_settings_html($template_id),
                    'multiple_mode' => $template['IsOptimizedForMultipleMode'],
                );
            }
        }

        return nc_array_json($template_info);
    }

    /**
     *
     */
    protected function action_set_component_template() {
        $nc_core = nc_core::get_object();
        $infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $this->check_infoblock_permissions($infoblock_id);

        $template_id = (int)$nc_core->input->fetch_post('template_id');
        $this->update($infoblock_id, array('Class_Template_ID' => $template_id));

        ob_end_clean();
        header(
            'Location: ' .
            $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH .
            '?isNaked=1' .
            '&sub=' . $nc_core->sub_class->get_by_id($infoblock_id, 'Subdivision_ID') .
            '&cc_only=' . $infoblock_id .
            '&include_component_style_tag=1'
        );
        exit;
    }

    protected function action_copy() {
        $nc_core = nc_core::get_object();

        $reference_infoblock_id = (int)$nc_core->input->fetch_post('infoblock_id');
        $destination_subdivision_id = $nc_core->sub_class->get_by_id($reference_infoblock_id, 'Subdivision_ID');
        $copied_infoblock_id = (int)$nc_core->input->fetch_post('copied_infoblock_id');
        $position = $nc_core->input->fetch_post('position');

        if (!$copied_infoblock_id) {
            return 'ERROR: no source infoblock ID';
        }

        if (!$destination_subdivision_id) {
            return 'ERROR: no source subdivision ID';
        }

        $this->check_subdivision_permissions($destination_subdivision_id);
        $this->check_subdivision_permissions($copied_infoblock_id);

        $new_infoblock_id = $nc_core->sub_class->duplicate($copied_infoblock_id, $destination_subdivision_id);
        $this->place_infoblock($new_infoblock_id, $position, $reference_infoblock_id);

        return 'OK';
    }
}
