<?php


class nc_bills_information_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('information', NETCAT_MODULE_BILLS_INFORMATION);
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $bills = nc_bills::get_instance();

        return $this->view('index', array(
            'total' => $bills->get_bills_count_total(),
            'paid' => $bills->get_bills_count_paid(),
            'not_paid' => $bills->get_bills_count_not_paid(),
        ));
    }

}