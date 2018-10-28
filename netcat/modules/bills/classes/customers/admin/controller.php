<?php


class nc_bills_customers_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('customers', NETCAT_MODULE_BILLS_CUSTOMERS);
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $this->ui_config->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_BILLS_ADD,
            "location" => "#module.bills.customers.add",
        );

        $query = "SELECT * FROM `%t%` WHERE `Owner` = 0 ORDER BY `Company_ID`";

        try {
            $customers = nc_record_collection::load('nc_bills_company', $query);
        } catch (nc_record_exception $e) {
            $customers = array();
        }

        return $this->view('index', array(
            'customers' => $customers,
            'status' => nc_core('input')->fetch_get('status'),
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        $this->ui_config->locationHash .= '.add';
        $this->ui_config->actionButtons[] = array(
            "id" => "back",
            "align" => "left",
            "caption" => NETCAT_MODULE_BILLS_BACK,
            "action" => "history.go(-1)",
        );
        $this->ui_config->actionButtons[] = array(
            "id" => "submit_form",
            "caption" => NETCAT_MODULE_BILLS_SAVE,
            "action" => "mainView.submitIframeForm()"
        );

        return $this->view('edit', array(
            'company' => new nc_bills_company(),
            'type' => $this->current_action,
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_edit() {
        $id = (int)nc_core()->input->fetch_get('id');

        $this->ui_config->locationHash .= '.edit(' . $id . ')';
        $this->ui_config->actionButtons[] = array(
            "id" => "back",
            "align" => "left",
            "caption" => NETCAT_MODULE_BILLS_BACK,
            "action" => "history.go(-1)",
        );

        $company = new nc_bills_company();
        try {
            $company->load($id);
        } catch (nc_record_exception $e) {

        }

        if ($company->get_id()) {
            $this->ui_config->actionButtons[] = array(
                "id" => "submit_form",
                "caption" => NETCAT_MODULE_BILLS_SAVE,
                "action" => "mainView.submitIframeForm()"
            );
        } else {
            $company = null;
        }

        return $this->view('edit', array(
            'company' => $company,
            'type' => $this->current_action,
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_save() {
        $data = nc_core()->input->fetch_post();

        $company = new nc_bills_company();
        $company->set_values($data);
        $company->set('owner', 0);
        $company->save();

        $redirect = nc_module_path('bills') . 'admin/?controller=customers&status=ok';
        header("Location: " . $redirect);

        return null;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_remove() {
        $id = (int)nc_core()->input->fetch_get('id');

        $company = new nc_bills_company();
        try {
            $company->load($id);
            $company->delete();
        } catch (nc_record_exception $e) {

        }


        $redirect = nc_module_path('bills') . 'admin/?controller=customers';
        header("Location: " . $redirect);

        return null;
    }

}