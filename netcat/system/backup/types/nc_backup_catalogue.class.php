<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'nc_backup_subdivision.class.php';

class nc_backup_catalogue extends nc_backup_subdivision {

    //--------------------------------------------------------------------------

    protected $name = TOOLS_SYSTABLE_SITES;

    protected $export_steps = array(
        'site'        => TOOLS_COPYSUB_SITE,
        'subdivision' => CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS,
        'templates'   => CONTROL_TEMPLATE,
        'components'  => NETCAT_SETTINGS_COMPONENTS,
        'users'       => SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST,
        'data'        => TOOLS_DATA_BACKUP_STEP_DATA,
    );

    protected $import_steps = array(
        'components'  => NETCAT_SETTINGS_COMPONENTS,
        'templates'   => CONTROL_TEMPLATE,
        'site'        => TOOLS_COPYSUB_SITE,
        'subdivision' => CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS,
        'users'       => SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST,
        'data'        => TOOLS_DATA_BACKUP_STEP_DATA,
    );

    //--------------------------------------------------------------------------
    // EXPORT
    //--------------------------------------------------------------------------

    protected function init() {
        // Фильтруем этапы (если пользователь что-то отключил)
        $options = array('subdivision', 'templates', 'components', 'users', 'data');
        $this->cross_data($options);
        foreach ($options as $key) {
            if (empty($this->cross_data[$key])) {
                unset($this->export_steps[$key]);
            }
        }
    }

    //--------------------------------------------------------------------------

    protected function export_step_site() {
        $this->export_process();
        $this->save_result($this, true);
    }

    //--------------------------------------------------------------------------

    protected function export_step_subdivision() {
        $sub_ids = $this->get_col('Subdivision', array('Catalogue_ID'=>$this->id(), 'Parent_Sub_ID'=>0), 'Subdivision_ID');

        foreach ($sub_ids as $sub_id) {
            $this->backup->subdivision->export($sub_id, $this->export_id(), true);
        }
    }

    //--------------------------------------------------------------------------

    protected function export_step_users() {
        $this->backup->user->export($this->id(), $this->export_id(), true);
    }

    //--------------------------------------------------------------------------

    protected function export_process() {
        $data = $this->export_data('Catalogue', 'Catalogue_ID', array('Catalogue_ID' => $this->id()));
    }

    //--------------------------------------------------------------------------

    public function export_form() {
        $catalogues     = array();
        $data           = $this->get_data('Catalogue', null, 'Catalogue_ID,Catalogue_Name', 'Catalogue_ID');
        $catalogues[''] = '';
        $catalogues    += $this->make_list($data, 'Catalogue_ID', 'Catalogue_Name');

        $ui = $this->nc_core->ui;

        return $ui->form->add_row(CONTROL_CONTENT_CATALOUGE_ONESITE)->select('id', $catalogues)
            . $ui->form->add_row('&nbsp;')->checkbox('subdivision', true, CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS)
            . $ui->form->add_row('&nbsp;')->checkbox('templates', true, CONTROL_TEMPLATE)
            . $ui->form->add_row('&nbsp;')->checkbox('components', true, NETCAT_SETTINGS_COMPONENTS)
            . $ui->form->add_row('&nbsp;')->checkbox('users', true, SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST)
            . $ui->form->add_row('&nbsp;')->checkbox('data', true, TOOLS_DATA_BACKUP_STEP_DATA);
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

    protected function import_step_site() {}

    //--------------------------------------------------------------------------

    // protected function import_step_subdivision() {}

    //--------------------------------------------------------------------------

    // protected function import_step_templates() {}

    //--------------------------------------------------------------------------

    // protected function import_step_components() {}

    //--------------------------------------------------------------------------

    protected function import_step_users() {}

    //--------------------------------------------------------------------------

    // protected function import_step_data() {}

    //--------------------------------------------------------------------------
}