<?php

/**
 * Импорт/Экспорт данных раздела. Данные берутся из таблиц "Message…"
 */
class nc_backup_data extends nc_backup_base {

    //--------------------------------------------------------------------------

    protected $name = TOOLS_DATA_BACKUP_STEP_DATA;

    /** @var  nc_db_table */
    protected $sub_class_table;

    /** @var  nc_db_table */
    protected $field_table;

    protected $file_field_ids = array(6, 11);

    //-------------------------------------------------------------------------

    protected function init() {
        $this->sub_class_table = nc_db_table::make('Sub_Class');
        $this->field_table     = nc_db_table::make('Field');
    }

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function make_message_table($class_id) {
        return nc_db_table::make('Message' . intval($class_id), 'Message_ID');
    }

    //-------------------------------------------------------------------------

    protected function export_process() {
        global $SUB_FOLDER, $HTTP_FILES_PATH, $DOCUMENT_ROOT;

        $id        = $this->id;
        $sub_class = $this->sub_class_table->where_id($id)->get_row();

        if (!$sub_class) {
            return false;
        }

        $class_id      = $sub_class['Class_ID'];
        $sub_id        = $sub_class['Subdivision_ID'];
        $message_table = $this->make_message_table($class_id);

        // Data
        $data = $message_table->where('Sub_Class_ID', $id)->index_by_id()->get_result();
        $this->dumper->register_dict_field('Sub_Class_ID', 'Subdivision_ID');
        $this->dumper->export_data($message_table->get_table(), 'Message_ID', $data);


        // Files
        $dir = $SUB_FOLDER . $HTTP_FILES_PATH . "{$sub_id}/{$id}/";
        if (file_exists($DOCUMENT_ROOT . $dir)) {
            $this->dumper->export_files($dir);
        }

        // Files for simple FileSystem (fs1)
        $simple_fs_fields = array();
        $fields           = $this->field_table->where('Class_ID', $class_id)->get_result();
        foreach ($fields as $field) {
            if (in_array($field['TypeOfData_ID'], $this->file_field_ids) && strpos($field['Format'], 'fs1') ) {
                $simple_fs_fields[$field['Field_Name']] = $field;
            }
        }

        if ($simple_fs_fields) {
            foreach ($data as $row_id => $row) {
                foreach ($simple_fs_fields as $key => $field) {
                    $field_id = $field['Field_ID'];
                    if ($val = $row[$key]) {
                        $field_value = explode(':', $val);
                        // Simple FileSystem
                        $ext = substr($field_value[0], strrpos($field_value[0], "."));
                        $file = $SUB_FOLDER . $HTTP_FILES_PATH . $field_id . "_" . $row_id . $ext;
                        $this->dumper->export_files($file);
                    }
                }
            }
        }

        return true;
    }

    //--------------------------------------------------------------------------

    public function export_form() {
        // $catalogue_id = (int)$this->nc_core->input->fetch_get_post('catalogue_id');

        // $view         = $this->nc_core->ui->view($this->nc_core->ADMIN_FOLDER . 'views/backup/data_export_form');
        // $view->with('action_url', $this->nc_core->SUB_FOLDER . $this->nc_core->HTTP_ROOT_PATH . 'action.php?ctrl=admin.backup&action=');
        // $catalogues   = array();
        // $subdivisions = array();

        // if ($catalogue_id) {
        //     $data         = $this->get_data('Subdivision', array('Catalogue_ID'=>$catalogue_id), 'Subdivision_ID,Parent_Sub_ID,Subdivision_Name', 'Priority');
        //     $subdivisions = $this->make_tree_list($data, 'Subdivision_ID', 'Parent_Sub_ID', 'Subdivision_Name');
        // }
        // else {
        //     $data       = $this->get_data('Catalogue', null, 'Catalogue_ID,Catalogue_Name', 'Catalogue_ID');
        //     $catalogues[''] = '';
        //     $catalogues += $this->make_list($data, 'Catalogue_ID', 'Catalogue_Name');
        // }

        // $view->with('catalogue_id', $catalogue_id);
        // $view->with('catalogues',   $catalogues);
        // $view->with('subdivisions', $subdivisions);

        // return $view->make();
    }

    //--------------------------------------------------------------------------
    // IMPORT
    //--------------------------------------------------------------------------

    protected function import_process() {
        // $id = $this->id();

        // $class_id = current($this->current_dict('Class_ID'));
        // $this->new_id = $this->dict('Class_ID', $class_id); // From global dict

        // if ( ! $this->new_id) return;

        // $this->import_data('Message' . $this->new_id);

        // $this->import_file();

        // $this->result('redirect', $this->nc_core->ADMIN_PATH . 'class/index.php?fs=1&phase=4&ClassID=' . $this->new_id);
    }

    //--------------------------------------------------------------------------

    // переименовываем папку компонента
    // protected function before_extract($path, $file) {
    //     return $path . ($this->save_ids ? $file : $this->new_id);
    // }

    //--------------------------------------------------------------------------
}