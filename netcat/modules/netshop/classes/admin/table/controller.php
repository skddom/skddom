<?php

/**
 * Типовой контроллер страниц административного интерфейса модуля для работы
 * с классами-наследниками nc_db_table.
 * Определяет типовые действия: add, edit, toggle, remove
 *
 * Должна быть задана переменная $data_type.
 * Исходя из $data_type по умолчанию будут использованы следующие классы:
 *  — nc_netshop_DATATYPE_admin_ui — класс для формирования UI_CONFIG
 *  — nc_netshop_table_DATATYPE — в действиях add(), remove(), edit()
 *
 * Серая магия:
 *  — К view автоматически добавляются переменные: catalogue_id, current_url, netshop
 *    при использовании basic_table_edit_action() — record
 *
 */

abstract class nc_netshop_admin_table_controller extends nc_netshop_admin_controller {

    /** @var string
     * Может быть не задан, тогда табличные CRUD-действия не работают.
     * Если не задан, должен быть переопределён класс get_script_path()
     */
    protected $data_type = '';

    /** @var string|null   Если не задан, определяется на основании $data_type */
    protected $ui_config_class = null;

    /** @var string  Если не задан — равен $data_type */
    protected $ui_config_base_path = '';

    /**
     *
     */
    protected function init() {
        parent::init();

        $this->bind('toggle', array('id', 'Checked'));
        $this->bind('remove', array('id'));
        $this->bind('edit', array('id'));
    }

    /**
     *
     */
    protected function before_action() {
        if ($this->ui_config_class) {
            $ui_config_class = $this->ui_config_class;
        }
        else {
            $ui_config_class = 'nc_netshop_' . $this->data_type . '_admin_ui';
        }

        $this->ui_config = new $ui_config_class($this->site_id, $this->current_action);
        if ($this->ui_config_base_path) {
            $active_tab = array_pop(explode(".", $this->ui_config_base_path));
            $this->ui_config->activeTab = $active_tab;
            $this->ui_config->set_location_hash($this->ui_config_base_path);
        }
    }

    /**
     * @return bool|string
     */
    protected function get_db_table_class() {
        return 'nc_netshop_' . $this->data_type . '_table';
    }

    /**
     * Обработчик для «типовых» форм добавления и сохранения объектов (действия:
     * добавление, редактирование, сохранение).
     *
     * @param int $id
     * @param string $view
     * @return nc_ui_view
     */
    protected function basic_table_edit_action($id = 0, $view = 'form', $save_mail_attachment_form = null) {
        $id = (int)$id;
        $existing_record = array();

        /** @var nc_db_table $table */
        $table_class = $this->get_db_table_class();
        $table = new $table_class();

        $submitted_data = $this->input->fetch_post('data');
        if (is_array($submitted_data)) {
            if ($table->validate($submitted_data)) {
                $this->set_site_id((int)$submitted_data['Catalogue_ID']);
                $submitted_id = (int)$submitted_data[$table->get_primary_key()];

                $table->set($submitted_data);

                if ($submitted_id) {
                    $table->where_id($submitted_id)->update();
                    if ($save_mail_attachment_form) {
                        nc_mail_attachment_form_save($save_mail_attachment_form . '_' . $submitted_id);
                    }
                }
                else {
                    $insert_id = $table->insert();
                    if ($save_mail_attachment_form) {
                        nc_mail_attachment_form_save($save_mail_attachment_form . '_' . $insert_id,
                            $save_mail_attachment_form . '_0');
                    }
                }

                $this->redirect_to_index_action();
            }
            else { // validation failed: show a form with the values provided by user
                // @todo some kind of hint for the user of what happened (show errors)
                $existing_record = $submitted_data;
            }
        }
        else { // nothing was submitted
            if ($id) {
                $existing_record = $table->as_array()->where_id($id)->get_row();
                if ((int)$existing_record['Catalogue_ID']) {
                    $this->set_site_id((int)$existing_record['Catalogue_ID']);
                }
            }
            else {
                $existing_record = array(
                    'Catalogue_ID' => $this->site_id,
                    'Checked' => 1
                );
            }
        }

        $view         = $this->view($view);
        $view->form   = $table->make_form($existing_record);
        $view->record = $existing_record;

        $controller_action = ($id ? "edit($id)" : "add($this->site_id)");

        $controller_path_part = ($this->ui_config_base_path ? $this->ui_config_base_path : $this->data_type);
        $this->ui_config->set_location_hash("$controller_path_part.$controller_action");
        $this->ui_config->add_save_and_cancel_buttons();

        return $view;
    }


    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        return $this->basic_table_edit_action(0);
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_edit($id) {
        return $this->basic_table_edit_action($id);
    }

    /**
     * @param $id
     * @param $checked
     * @return string
     */
    protected function action_toggle($id, $checked) {
        // FIXME: после обновления шаблонов параметр должен быть POST only
        // (см также init() выше)
        if (!$checked) {
            $checked = (int)$this->input->fetch_post('enable');
        }

        $table_class = $this->get_db_table_class();
        /** @var nc_netshop_table $table */
        $table = new $table_class();
        $table->set('Checked', (int)$checked)->where_id($id)->update();
        $this->redirect_to_index_action();
    }

    /**
     * @param $id
     */
    protected function action_remove($id) {
        $table_class = $this->get_db_table_class();
        /** @var nc_netshop_table $table */
        $table = new $table_class();
        $table->where_id($id)->delete();
        $this->redirect_to_index_action();
    }

}