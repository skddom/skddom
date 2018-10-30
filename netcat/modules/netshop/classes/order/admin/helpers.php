<?php

class nc_netshop_order_admin_helpers {

    /*
     * Вывод доступных способов заказа
     * @param nc_netshop_order $order
     * @param bool $include_js
     * @param bool $nc_field_wrap
     * @return string
     */
    static public function get_status_change_select(nc_netshop_order $order, $include_js = true, $nc_field_wrap = false) {
        $nc_core = nc_core::get_object();

        if (!$order->get('Status_id')) {
            $order->set('Status_id', 0);
        }

        $order_status_id = $order->get_status_id();

        $html = '';
        if ($nc_field_wrap) {

            $html .= '<div class="nc-field">';

            $field_data = $nc_core->get_component($order->get_order_component_id())->get_field('Status');
            if ($field_data) {
                $html .= '<span class="nc-field-caption" style="" id="nc_capfld_' . $field_data['id'] . '">' .
                    $field_data['description'] . ':</span>';
            }

        }

        $order_status_id = (int)$order_status_id;

        $netshop = nc_netshop::get_instance($order->get('Catalogue_ID'));
        $available_statuses = $order->get_available_statuses();
        $status_names = $netshop->get_all_order_status_names();

        $status_change_select_name = 'f_Status' . (!$nc_field_wrap ? "[" . $order->get_id() . "]" : null);
        
        $html .= "<select name='" . $status_change_select_name . "' data-nc-original='" . $order_status_id . "'>";
        foreach ($status_names as $status_id => $status_name) {
            if ($status_id == $order_status_id || in_array($status_id, $available_statuses)) {
                $html .= '<option value="' . $status_id . '" ' . ($status_id == $order_status_id ? 'selected' : '') . '>' .
                          htmlspecialchars($status_names[$status_id]) .
                          '</option>';
            }
        }
        $html .= '</select>';
        
        if ($include_js) {
            $html .= "
            <script>
                \$nc('SELECT[name=\"" . $status_change_select_name . "\"').on('change', function (e) {
                    var \$this = \$nc(e.target),
                        status_name = \$this.find(\"option:selected\").text(),
                        confirmation_text = '" . NETCAT_MODULE_NETSHOP_CONFIRM_STATUS_CHANGE_TO . "'.replace('%s', status_name);

                    if (confirm(confirmation_text)) {
                        var value = \$this.val();
                        \$this.attr('data-nc-original', value);
                        var id = /\[(\d+)\]/.exec(\$this.attr('name'));
                        id = id[1];

                        var checked = \$nc('INPUT[name=\"Checked[' + id + ']\"').val();
                        nc.process_start('status_change_' + id);

                        var url = '" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "message.php?inside_admin=1&classID=" . $order->get_order_component_id() . "&posting=1" . ($curPos ? "&curPos=" . $curPos : "") . ($admin_mode ? "&admin_mode=1" : "") . "';
                        url += '&message=' + id + '&f_Status=' + value + '&f_Checked=' + checked + '&do_not_respond=1';

                        \$nc.get(url, function () {
                            nc.process_stop('status_change_' + id);
                            document.location.reload();
                        });
                    } else {
                        \$this.val(\$this.attr('data-nc-original'));
                    }
                    return true;
                });
            </script>
            ";
        }

        if ($nc_field_wrap) {
            $html .= '</div>';
        }
        
        return $html;        
    }

    /**
     * Заготовка под поле поиска клиента для нового заказа
     * @param array $fields_to_set
     * @param array $fields_to_search
     * @return string
     */
    static public function get_customer_search_field(array $fields_to_set = array(), array $fields_to_search = array()) {
        // можно будет передать f_User_ID в форме
        return '';
    }

    /**
     * Заготовка под скрипт инициализации поля поиска клиента
     * @return string
     */
    static public function get_customer_search_field_init_script() {
        return '';
    }

}