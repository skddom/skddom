<?php


/**
 *
 */
class nc_netshop_payment_admin_controller extends nc_netshop_admin_table_controller {

    /** @var  nc_netshop_payment_admin_ui */
    protected $ui_config;

    protected $data_type = 'payment';

    /**
     *
     */
    protected function init() {
        parent::init();

        $this->bind('change_priority', array('id', 'priority'));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $table = new nc_netshop_payment_table();
        $methods = $table->for_site($this->site_id)->order_by('Priority')->as_array()->get_result();

        if (count($methods)) {
            $view = $this->view('method_list');
            $view->fields = $table->get_fields();
            $view->methods = $methods;
        } else {
            $view = $this->view('empty_list');
            $view->message = NETCAT_MODULE_NETSHOP_SETTINGS_NO_PAYMENT_METHODS_ON_SITE;
        }
        $this->ui_config->add_create_button("payment.add($this->site_id)");

        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        return $this->basic_table_edit_action(0, 'method_edit');
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_edit($id) {
        return $this->basic_table_edit_action($id, 'method_edit');
    }

    /**
     * @param $id
     * @param $priority
     */
    protected function action_change_priority($id, $priority) {
        $table_class = $this->get_db_table_class();
        /** @var nc_netshop_table $table */
        $table = new $table_class();
        $table->set('Priority', (int)$priority)->where_id($id)->update();
        exit;
    }

}