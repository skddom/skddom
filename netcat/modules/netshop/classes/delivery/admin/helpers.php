<?php

class nc_netshop_delivery_admin_helpers {

    static protected $classifier_table_name = "Classificator_ShopDeliveryService";

    /**
     * Настройки служб автоматического расчёта доставки (для админки)
     *
     * @param int $site_id
     * @param array $data
     * @return string
     */
    static public function get_delivery_service_options($site_id, array $data) {

        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;

        $delivery_service_names = array(
            0 => NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_DONT_USE,
        );

        $delivery_service_list = $db->get_results(
            "SELECT `ShopDeliveryService_ID`, `ShopDeliveryService_Name`, `Value` AS 'class'
               FROM `" . self::$classifier_table_name . "`
              WHERE `Checked` = 1",
            ARRAY_N
        );

        /** @var nc_netshop_delivery_service[] $delivery_services */
        $delivery_services = array();

        foreach ($delivery_service_list as $delivery_service_data) {
            list($delivery_service_id, $delivery_service_name, $delivery_service_class) = $delivery_service_data;
            if (@class_exists($delivery_service_class) && is_subclass_of($delivery_service_class, "nc_netshop_delivery_service")) {
                $delivery_services[$delivery_service_id] = new $delivery_service_class(array());
                $delivery_service_names[$delivery_service_id] = $delivery_service_name;
            }
        }

        $netshop = nc_netshop::get_instance($site_id);
        $order_component_id = $netshop->get_setting('OrderComponentID');
        $order_fields = $nc_core->get_component($order_component_id)->get_fields();

        $current_mapping = nc_array_value($data, 'ShopDeliveryService_Mapping');

        /**
         * $current_mapping для полей заказа может быть в двух вариантах:
         *   с идентификатором поля, например: order_1234
         *   c названием поля, например: order_Weight
         * Приводим значение к варианту с названием поля
         */
        if (preg_match_all('/"order_(\d+)"/', $current_mapping, $matches)) {
            foreach ($matches[1] as $i => $order_field_id) {
                foreach ($order_fields as $order_field) {
                    if ($order_field['id'] == $order_field_id) {
                        $current_mapping = str_replace($matches[0][$i], '"order_' . $order_field['name'] . '"', $current_mapping);
                        break;
                    }
                }
            }
        }

        $shop_fields = nc_netshop_admin_helpers::get_shop_fields();

        $selected_delivery_service_id = nc_array_value($data, 'ShopDeliveryService_ID');

        $html = "<input type='hidden' name='data[ShopDeliveryService_Mapping]' value='" . htmlspecialchars($current_mapping, ENT_QUOTES) . "'/>";

        foreach ($delivery_services as $delivery_service_id => $delivery_service) {
            $html .= "<div class='nc-netshop-delivery-service nc-netshop-delivery-service--$delivery_service_id' style='display: none;'>";

            $delivery_service_name = $delivery_service_names[$delivery_service_id];

            // Блок соответствия полей заказа и настроек магазина свойствам службы доставки
            $mapped_fields = $delivery_service->get_mapped_fields();
            if ($mapped_fields) {
                $html .= "<div class='ncf_row'>
                          <div class='ncf_caption'>" . sprintf(NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING, $delivery_service_name) . ":</div>
                          <div class='ncf_value'>
                          <table>";

                foreach ($mapped_fields as $field => $name) {
                    $html .= "<tr><td>{$name}:&nbsp;</td><td><select name='delivery_service_{$delivery_service_id}_field_{$field}' class='nc--wide'>";
                    $html .= "<option value=''>—</option>";

                    $html .= "<optgroup label='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING_ORDER, ENT_QUOTES) . "'>";
                    foreach ($order_fields as $order_field) {
                        $html .= "<option value='order_{$order_field['name']}'>" . htmlspecialchars($order_field['description'] ?: $order_field['name']) . "</option>";
                    }
                    $html .= "</optgroup>";

                    $html .= "<optgroup label='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING_SHOP, ENT_QUOTES) . "'>";
                    foreach ($shop_fields as $field_name => $field_options) {
                        $html .= "<option value='shop_{$field_name}'>" . htmlspecialchars($field_options['caption']) . "</option>";
                    }
                    $html .= "</optgroup>";

                    $html .= "</select></td></tr>";
                }
                $html .= "</table></div></div>";
            }

            // Блок с настройками службы доставки
            $settings_fields = $delivery_service->get_settings_fields();
            if ($settings_fields) {
                $current_settings = nc_array_value($data, 'ShopDeliveryService_Settings');
                if ($current_settings && !is_array($current_settings)) {
                    // а это поле записано через serialize()...
                    $current_settings = unserialize($current_settings);
                }

                $form = new nc_a2f($settings_fields, "delivery_service_settings[{$delivery_service_id}]");
                $form->show_default_values(false)->show_header(false);
                if ($delivery_service_id == $selected_delivery_service_id && $current_settings) {
                    $form->set_values($current_settings);
                }
                $html .= "<div>
                          <div class='ncf_caption'>" .
                          sprintf(NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_SETTINGS, $delivery_service_name) .
                          ":</div>" .
                          "<div class='ncf_value'>" . $form->render() . "</div>" .
                          "</div>";
            }

            $html .= "</div>";
        }

        return $html;
    }

}