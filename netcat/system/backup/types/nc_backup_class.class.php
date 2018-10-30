<?php


class nc_backup_class extends nc_backup_driver {

    //--------------------------------------------------------------------------

    protected $name = SECTION_CONTROL_CLASS;

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function export_process() {
        // Компонент
        $data = $this->export_data('Class', 'Class_ID', array('Class_ID' => $this->id()));

        if (!$data) return false;

        // Шаблоны компонента
        $this->export_data('Class', 'Class_ID', array('ClassTemplate' => $this->id()));

        // Поля компонента
        $this->export_data('Field', 'Field_ID', array('Class_ID' => $this->id()));

        // Таблица данных компонента
        $this->export_table('Message' . $this->id());

        // Файлы шаблона (v5)
        if ($data[0]['File_Mode']) {
            $file = trim($data[0]['File_Path'], '/');
            $this->export_file('/netcat_template/class', $file);
        }
    }

    //--------------------------------------------------------------------------

    public function export_form() {
        $data    = $this->get_data('Class', array('System_Table_ID'=>0, 'ClassTemplate'=>0, 'File_Mode'=>1), 'Class_ID,Class_Name,Class_Group', 'Class_ID');
        $options = $this->make_group_list($data, 'Class_ID', 'Class_Group', 'Class_Name');

        return $this->nc_core->ui->form->add_row(SECTION_CONTROL_CLASS)->select('id', $options);
    }

    //--------------------------------------------------------------------------
    // IMPORT
    //--------------------------------------------------------------------------

    protected function import_process() {
        $id = $this->id();

        $this->import_data('Class');
        $this->import_data('Field');

        $this->new_id = $this->dict('Class_ID', $id);

        $this->import_table('Message' . $id, 'Message' . $this->new_id);

        $this->import_file();

        // $this->result('redirect', $this->nc_core->ADMIN_PATH . 'class/index.php?fs=1&phase=4&ClassID=' . $this->new_id);
    }

    //--------------------------------------------------------------------------

    // Меняем ClassTemplate (Class_ID) для шаблонов компонента
    protected function before_insert_class($data) {
        $data['ClassTemplate'] = $this->dict('Class_ID', $data['ClassTemplate']);
        return $data;
    }

    //--------------------------------------------------------------------------

    // Меняем Class_ID для полей компонента
    protected function before_insert_field($data) {
        $data['Class_ID'] = $this->dict('Class_ID', $data['Class_ID']);
        return $data;
    }

    //--------------------------------------------------------------------------

    // Обновляем путь к директории с файлами шаблона
    protected function after_insert_class($data, $insert_id) {
        if ($this->save_ids) return;

        $update = array(
            'File_Path' => ($data['ClassTemplate'] ? "/" . $data['ClassTemplate'] : '' ) . "/{$insert_id}/",
        );
        $this->update('Class', $update, array('Class_ID' => $insert_id));
    }

    //--------------------------------------------------------------------------

    // переименовываем папку компонента
    protected function before_extract($path, $file) {
        return $path . ($this->save_ids ? $file : $this->new_id);
    }

    //--------------------------------------------------------------------------

    // переименовываем папки шаблонов
    protected function after_extract($path, $file) {
        if ($this->save_ids) return;

        $dir = $this->nc_core->DOCUMENT_ROOT . $path . $this->new_id . DIRECTORY_SEPARATOR;
        $class_files = scandir($dir);

        foreach ($class_files as $f) {
            if ( ! is_dir($dir . $f)) continue;
            $new_name = $this->dict('Class_ID', $f);
            if ($new_name && $new_name != $f) {
                rename($dir . $f, $dir . $new_name);
            }
        }
    }

    //--------------------------------------------------------------------------
}