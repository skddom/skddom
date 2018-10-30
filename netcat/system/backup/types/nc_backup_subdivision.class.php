<?php


class nc_backup_subdivision extends nc_backup_base {

    //--------------------------------------------------------------------------

    protected $name = CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS;

    /** @var  nc_db_table */
    protected $subdivision_table;
    /** @var  nc_db_table */
    protected $subclass_table;

    //--------------------------------------------------------------------------

    protected function init() {
        $this->subdivision_table = nc_db_table::make('Subdivision');
        $this->subclass_table    = nc_db_table::make('Sub_Class');
    }

    //-------------------------------------------------------------------------

    protected function row_attributes($ids) {
        $titles = $this->subdivision_table->select('Subdivision_ID, Subdivision_Name')->where_in_id((array)$ids)->index_by_id()->get_result();

        $result = array();
        foreach ($titles as $id => $row) {
            $result[$id] = array(
                'title'       => $row['Subdivision_Name'],
                'sprite'      => 'nc--folder',
                'netcat_link' => $this->nc_core->ADMIN_PATH . "subdivision/index.php?phase=5&SubdivisionID={$id}&view=edit"
            );
        }

        return $result;
    }

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function export_form() {
        // $options    = array(''=>'');
        // $options_v4 = array(''=>'');

        // $result = $this->class_table
        //     ->select('Class_ID, Class_Name, Class_Group, File_Mode')
        //     ->where('System_Table_ID', 0)->where('ClassTemplate', 0)
        //     ->order_by('File_Mode', 'DESC')->order_by('Class_Group')->order_by('Class_Name')
        //     ->as_object()->get_result();

        // foreach ($result as $row) {
        //     $group = $row->Class_Group . ($row->File_Mode ? '' : ' (v4)');
        //     $options[$group][$row->Class_ID] = $row->Class_ID . '. ' . $row->Class_Name;
        // }

        // return $this->nc_core->ui->form->add_row(SECTION_CONTROL_CLASS)->select('id', $options);
    }

    //-------------------------------------------------------------------------

    protected function export_validation() {
        if (!$this->id) {
            $this->set_validation_error('Subdivision not selected');
            return false;
        }
        return true;
    }

    //-------------------------------------------------------------------------

    protected function export_process() {
        $id          = $this->id;
        $subdivision = $this->subdivision_table->where_id($id)->get_row();

        if (!$subdivision) {
            return false;
        }

        // Subdivisions
        $data       = array($id => $subdivision);
        $parent_ids = array($id);
        while ($parent_ids) {
            $result     = $this->subdivision_table->where_in('Parent_Sub_ID', $parent_ids)->index_by_id()->get_result();
            $parent_ids = array_keys($result);
            $data      += $result;
        }
        $this->dumper->register_dict_field('Hidden_URL');
        $this->dumper->export_data('Subdivision', 'Subdivision_ID', $data);


        // Sub_Class
        $sub_ids = $this->dumper->get_dict('Subdivision_ID');
        $data    = $this->subclass_table->where_in('Subdivision_ID', $sub_ids)->index_by_id()->get_result();
        $this->dumper->register_dict_field('Class_ID');
        $this->dumper->export_data('Sub_Class', 'Sub_Class_ID', $data);

        // Class
        $class_ids = $this->dumper->get_dict('Class_ID');
        $this->dumper->set_dump_info('class_ids', array_values($class_ids));
        foreach ($class_ids as $class_id) {
            $this->backup->component->export($class_id);
        }

        // Data from Message
        $sub_class_ids = $this->dumper->get_dict('Sub_Class_ID');
        foreach ($sub_class_ids as $sub_class_id) {
            $this->backup->data->export($sub_class_id);
        }

        // $this->dumper->export_data('Template', 'Template_ID', $data);

        // $this->dumper->set_dump_info('file_mode', $component['File_Mode']);

        return true;
    }

    //-------------------------------------------------------------------------

    protected function import_process() {
        // $this->dumper->import_data('Subdivision');
        // $this->new_id = $this->dumper->get_dict('Subdivision_ID', $this->id);

        // $class_ids = $this->dumper->get_dump_info('sub_export', 'component');
        // foreach ($class_ids as $class_id) {
        //     $this->backup->component->import($class_id);
        // }

        // $this->dumper->import_data('Sub_Class');

        // $sub_class_ids = $this->dumper->get_dump_info('sub_export', 'data');
        // idebug($sub_class_ids);
        // idebug($this->dumper->get_dict('Sub_Class_ID'));
        // foreach ($sub_class_ids as $sub_class_id) {
        //     $this->backup->data->import($sub_class_id);
        // }
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_subdivision($row) {
        if ($row['Parent_Sub_ID']) {
            $row['Parent_Sub_ID'] = $this->dumper->get_dict('Subdivision_ID', $row['Parent_Sub_ID']);
        }

        return $row;
    }

    //-------------------------------------------------------------------------

    protected function event_before_insert_sub_class($row) {
        $row['Subdivision_ID'] = $this->dumper->get_dict('Subdivision_ID', $row['Subdivision_ID']);
        $row['Class_ID'] = $this->dumper->get_dict('Class_ID', $row['Class_ID']);
// idebug($row);
// exit;
        return $row;
    }
}