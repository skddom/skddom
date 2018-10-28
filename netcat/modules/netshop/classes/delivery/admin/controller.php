<?php


/**
 *
 */
class nc_netshop_delivery_admin_controller extends nc_netshop_admin_table_controller {

    /** @var  nc_netshop_delivery_admin_ui */
    protected $ui_config;

    /** @var string  */
    protected $data_type = 'delivery';

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
        $table = new nc_netshop_delivery_table();
        $methods = $table->for_site($this->site_id)->order_by('Priority')->as_array()->get_result();

        if (count($methods)) {
            $view = $this->view('deliverymethod_list');
            $view->fields = $table->get_fields();
            $view->methods = $methods;
            $view->delivery_services = nc_array_set_keys(
                $this->db->get_results(
                    "SELECT `ShopDeliveryService_ID` AS `id`, `ShopDeliveryService_Name` AS `name`
                       FROM `Classificator_ShopDeliveryService`",
                    ARRAY_A),
                'id', 'name'
            );
        } else {
            $view = $this->view('empty_list');
            $view->message = NETCAT_MODULE_NETSHOP_SETTINGS_NO_DELIVERY_METHODS_ON_SITE;
        }

        $this->ui_config->add_create_button("delivery.add($this->site_id)");

        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        return $this->basic_table_edit_action(0, 'form_with_condition');
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_edit($id) {
        return $this->basic_table_edit_action($id, 'form_with_condition');
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