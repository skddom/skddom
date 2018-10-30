<?php


class nc_bills_settings_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('settings', NETCAT_MODULE_BILLS_SETTINGS);
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $this->ui_config->actionButtons[] = array(
            "id" => "submit_form",
            "caption" => NETCAT_MODULE_BILLS_SAVE,
            "action" => "mainView.submitIframeForm()"
        );

        $company = new nc_bills_company();
        try {
            $company->load_where('owner', 1);
        } catch (nc_record_exception $e) {

        }

        $nc_bills = nc_bills::get_instance();

        $settings = array(
            'vat' => null,
            'key' => null,
            'logo' => null,
            'director_sign' => null,
            'accountant_sign' => null,
            'stamp' => null,
        );

        foreach($settings as $key => $value) {
            $method = 'get_' . $key;
            $settings[$key] = $nc_bills->$method();
        }

        return $this->view('index', array(
            'company' => $company,
            'settings' => $settings,
            'status' => nc_core('input')->fetch_get('status'),
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_save() {
        $nc_core = nc_core();

        $data = $nc_core->input->fetch_post();

        $settings_data = $data['settings'];

        $nc_bills = nc_bills::get_instance();
        $nc_bills->set_vat($settings_data['vat']);
        $nc_bills->set_key($settings_data['key']);

        $files = array(
            'logo',
            'director_sign',
            'accountant_sign',
            'stamp',
        );

        foreach($files as $file) {
            if (isset($_FILES['settings']['tmp_name'][$file]) && $_FILES['settings']['tmp_name'][$file]) {
                $method = 'set_' . $file;
                $nc_bills->$method($_FILES['settings']['tmp_name'][$file], $_FILES['settings']['name'][$file]);
            } else if (isset($settings_data['delete'][$file])) {
                $method = 'delete_' . $file;
                $nc_bills->$method();
            }
        }

        $company_data = $data['company'];

        $company = new nc_bills_company();
        try {
            $company->load_where('owner', 1);
        } catch (nc_record_exception $e) {

        }
        if (!$company->get_id()) {
            $company->set('owner', 1);
        }


        $company->set_values($company_data);
        $company->save();

        $company_id = $company->get_id();

        $sql = "DELETE FROM `Bills_Company` WHERE `Company_ID` <> {$company_id} AND `Owner` = 1";
        nc_core('db')->query($sql);

        $redirect = nc_module_path('bills') . 'admin/?controller=settings&action=index&status=ok';
        header("Location: " . $redirect);

        return null;
    }

}