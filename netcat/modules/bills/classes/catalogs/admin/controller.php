<?php


class nc_bills_catalogs_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('catalogs', NETCAT_MODULE_BILLS_CATALOGS);

        $this->ui_config->tabs = array(
            array(
                'id'       => 'statuses',
                'caption'  => NETCAT_MODULE_BILLS_STATUSES,
                'location' => "module.bills.catalogs.statuses",
                'group'    => "admin",
            ),
            array(
                'id'       => 'services',
                'caption'  => NETCAT_MODULE_BILLS_SERVICES,
                'location' => "module.bills.catalogs.services",
                'group'    => "admin",
            ),
        );
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        return $this->view('index');
    }

    /**
     * @return nc_ui_view
     */
    protected function action_statuses() {
        return $this->view('statuses');
    }

    /**
     * @return nc_ui_view
     */
    protected function action_services() {
        return $this->view('services');
    }

    protected function before_action() {
        $action = $this->current_action;
        if ($action != 'index') {
            $this->ui_config->locationHash .= '.' . $action;
            $this->ui_config->activeTab = $action;
        }
    }

}