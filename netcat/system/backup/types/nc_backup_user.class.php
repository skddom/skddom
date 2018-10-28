<?php


class nc_backup_user extends nc_backup_driver {

    //--------------------------------------------------------------------------

    protected $name       = TOOLS_SYSTABLE_USERS;
    protected $require_id = false;

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function export_process() {

        $where = $this->id() ? array('Catalogue_ID' => $this->id()) : null;
        $data  = $this->export_data('User', 'User_ID', $where);

        if (!$data) return false;

        $this->export_data('User_Group', 'ID', array('User_ID IN ('.implode(',', $this->dict('User_ID')).')'));

    }

    //--------------------------------------------------------------------------

    public function export_form() {
        $data    = $this->get_data('Catalogue', null, 'Catalogue_ID,Catalogue_Name', 'Catalogue_ID');
        $options = array(0=>CONTROL_USER_SELECTSITEALL);
        $options += $this->make_list($data, 'Catalogue_ID', 'Catalogue_Name');

        return $this->nc_core->ui->form->add_row(CONTROL_CONTENT_CATALOUGE_ONESITE)->select('id', $options);
    }

    //--------------------------------------------------------------------------
    // IMPORT
    //--------------------------------------------------------------------------

    protected function import_process() {
        $id = $this->id();

        $this->import_data('User');
        $this->import_data('User_Group');

        // $this->result('redirect', $this->nc_core->ADMIN_PATH . 'class/index.php?fs=1&phase=4&ClassID=' . $this->new_id);
    }

    //--------------------------------------------------------------------------

    protected function before_insert_user_group($data) {
        $data['User_ID'] = $this->dict('User_ID', $data['User_ID']);
        return $data;
    }

    //--------------------------------------------------------------------------
}