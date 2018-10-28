<?php


/**
 *
 */
class nc_netshop_delivery_method_admin_controller extends nc_netshop_admin_table_controller {

    /** @var  nc_netshop_delivery_admin_ui */
    protected $ui_config;
    protected $ui_config_class = 'nc_netshop_delivery_admin_ui';
    protected $ui_config_base_path = 'delivery.method';
    protected $data_type = 'delivery_method';
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
        $table = new nc_netshop_delivery_method_table();
        $methods = $table->for_site($this->site_id)->order_by('Priority')->as_array()->get_result();

        if (count($methods)) {
            $view = $this->view('method_list');
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

        $this->ui_config->set_delivery_location_suffix("method($this->site_id)");
        $this->ui_config->add_create_button("delivery.method.add($this->site_id)");

        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        $this->ui_config->set_delivery_location_suffix("method.add($this->site_id)");
        return $this->get_edit_view(0);
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_edit($id) {
        $this->ui_config->set_delivery_location_suffix("method.edit($id)");
        return $this->get_edit_view($id);
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function get_edit_view($id) {
        $this->prepare_delivery_service_settings_for_saving();
        return $this->basic_table_edit_action($id, 'method_edit')
                    ->with('delivery_service_types', $this->get_delivery_service_types());
    }

    /**
     * @return array
     */
    protected function get_delivery_service_types() {
        $types = array();
        $netshop = nc_netshop::get_instance($this->site_id);

        $delivery_service_ids = nc_db()->get_col(
            "SELECT `ShopDeliveryService_ID` FROM `Classificator_ShopDeliveryService` WHERE `Checked` = 1"
        ) ?: array();

        foreach ($delivery_service_ids as $id) {
            $handler = $netshop->delivery->get_delivery_service_by_id($id);
            if ($handler) {
                $types[$id] = $handler->get_delivery_type();
            }
        }

        return $types;
    }

    /**
     * Перенос дополнительных настроек службы расчёта доставки из массива в сериализованную строку
     */
    protected function prepare_delivery_service_settings_for_saving() {
        $data = $this->input->fetch_post('data');
        $delivery_service_id = nc_array_value($data, 'ShopDeliveryService_ID');
        if (!$delivery_service_id) {
            return;
        }

        // Сброс группы пунктов вывоза, если выбран способ с неподходящим типом доставки
        if (strlen(nc_array_value($data, 'DeliveryPointGroup'))) {
            $site_id = nc_array_value($data, 'Catalogue_ID');
            $handler = nc_netshop::get_instance($site_id)->delivery->get_delivery_service_by_id($delivery_service_id);
            if (!$handler || $handler->get_delivery_type() !== nc_netshop_delivery::DELIVERY_TYPE_PICKUP) {
                $data['DeliveryPointGroup'] = '';
            }
        }

        // Сохранение кастомных настроек для класса расчёта доставки
        // (значения сохраняются через serialize — поддерживаемый в nc_record способ)
        $delivery_service_settings = nc_array_value($this->input->fetch_post('delivery_service_settings'), $delivery_service_id);
        if ($delivery_service_settings !== null) {
            $data['ShopDeliveryService_Settings'] = serialize($delivery_service_settings);
        }

        // Передача данных в basic_table_edit_action() :-/
        $_POST['data'] = $data;
        $this->input->set('_POST', 'data', $data);
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