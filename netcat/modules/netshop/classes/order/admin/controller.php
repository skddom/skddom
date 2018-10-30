<?php


class nc_netshop_order_admin_controller extends nc_netshop_admin_controller {

    /** @var string  Должен быть задан, или должен быть переопределён метод before_action() */
    protected $ui_config_class = 'nc_netshop_order_admin_ui';

    /** @var nc_netshop_order_admin_ui */
    protected $ui_config;

    protected $order_component_id;
    protected $order_template_id;
    protected $order_subdivision_id;
    protected $order_infoblock_id;

    /**
     *
     */
    protected function before_action() {
        parent::before_action();
        $this->determine_order_infoblock_data();
    }

    /**
     *
     */
    protected function determine_order_infoblock_data() {
        $order_infoblock_data = $this->netshop->get_order_infoblock_data();

        $this->order_component_id = $order_infoblock_data['order_component_id'];
        $this->order_template_id = $order_infoblock_data['order_admin_template_id'];
        $this->order_subdivision_id = $order_infoblock_data['order_subdivision_id'];
        $this->order_infoblock_id = $order_infoblock_data['order_infoblock_id'];
    }

    /**
     *
     */
    protected function action_index() {
        if (!$this->order_infoblock_id) {
            return $this->view('error_message')->with('message', NETCAT_MODULE_NETSHOP_ORDER_NO_INFOBLOCK);
        }

        $nc_core = nc_core::get_object();
        // установка параметров для правильного вывода списка
        $nc_core->catalogue->set_current_by_id($this->site_id);

        $nc_core->inside_admin = 1;
        $GLOBALS['admin_url_prefix'] = $nc_core->SUB_FOLDER.$nc_core->HTTP_ROOT_PATH;
        $GLOBALS['UI_CONFIG'] = $this->ui_config;
        $this->ui_config->locationHash = "#module.netshop.order($this->site_id)";

        $list_vars = array_merge(
            $nc_core->input->fetch_get_post(),
            array(
                "nc_ctpl" => $this->order_template_id,
                "isMainContent" => 1,
                "catalogue" => $this->site_id,
                "inside_netshop" => 1,
            )
        );
        $list_vars = http_build_query($list_vars, null, '&');

        // генерирование списка
        $order_list = nc_objects_list($this->order_subdivision_id, $this->order_infoblock_id, $list_vars, true);

        if ($nc_core->input->fetch_get_post('isNaked')) {
            $this->use_layout = false;
            return $order_list;
        }
        else {
            return $this->view('order_list')->with('order_list', $order_list);
        }
    }

    /**
     *
     */
    protected function action_view() {
        if (!$this->order_infoblock_id) {
            return $this->view('error_message')->with('message', NETCAT_MODULE_NETSHOP_ORDER_NO_INFOBLOCK);
        }

        $nc_core = nc_core::get_object();
        $order_id = $nc_core->input->fetch_get_post('order_id');

        if ($nc_core->input->fetch_get_post('isNaked')) {
            $this->use_layout = false;
        }

        $nc_core->subdivision->set_current_by_id($this->order_subdivision_id);
        $nc_core->sub_class->set_current_by_id($this->order_infoblock_id);
        $nc_core->input->set('_GET', 'inside_admin', '1');

        $GLOBALS['catalogue'] = $this->site_id;
        $GLOBALS['sub'] = $this->order_subdivision_id;
        $GLOBALS['nc_ctpl'] = $this->order_template_id;
        $GLOBALS['cc'] = $this->order_infoblock_id;
        $GLOBALS['message'] = $order_id;
        $GLOBALS['isNaked'] = 1;
        $GLOBALS['inside_admin'] = 1;
        $GLOBALS['inside_netshop'] = 1;
        $GLOBALS['admin_modal'] = 0;
        extract($GLOBALS);

        ob_start();
        // div.nc_admin_mode_content нужен для обновления содержимого страницы
        // после сохранения формы в диалоге
        if ($this->use_layout) {
            echo '<div class="nc_admin_mode_content">';
        }

        require_once $nc_core->ROOT_FOLDER . "full.php";

        if ($this->use_layout) {
            echo '</div>';
        }
        $page = ob_get_clean();

        $this->ui_config->subheaderText = NETCAT_MODULE_NETSHOP_ORDERS_NUMBER . $order_id;
        $this->ui_config->locationHash = "#module.netshop.order.view($this->site_id,$order_id)";

        return $page;
    }

    /**
     *
     */
    protected function action_duplicate() {
        $nc_core = nc_core::get_object();
        $order_id = $nc_core->input->fetch_get_post('order_id');
        $user_hash = $nc_core->input->fetch_get_post('hash');

        $order = $this->netshop->load_order($order_id);
        $real_hash = sha1("$order_id:$order[Created]:" . session_id());

        if ($user_hash != $real_hash) {
            die('Wrong input');
        }

        $duplicate_order = $order->duplicate();

        ob_get_clean();
        $params = array(
            'catalogue' => $this->site_id,
            'sub' => $duplicate_order['Subdivision_ID'],
            'cc' => $duplicate_order['Sub_Class_ID'],
            'message' => $duplicate_order->get_id(),
            'inside_admin' => 1,
            'isNaked' => 1,
            'inside_netshop' => $nc_core->input->fetch_get_post('inside_netshop'),
            'is_duplicate' => $order_id,
        );
        header("Location: {$nc_core->SUB_FOLDER}{$nc_core->HTTP_ROOT_PATH}message.php?" . http_build_query($params, null, '&'));
        exit;
    }

    /**
     * Получение стоимости доставки
     */
    protected function action_get_delivery_estimate() {
        $netshop = $this->netshop;
        $nc_core = nc_core::get_object();
        $data = $nc_core->input->fetch_post();

        $result = array(
            'price' => 0,
            'error' => NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_ERROR,
        );

        $order = nc_netshop_order::from_post_data($data, $netshop);
        $delivery_method = $order->get_delivery_method();
        if ($delivery_method) {
            $estimate = $delivery_method->get_estimate($order);
            $result['price'] = $estimate->get('price');
            $result['error'] = $estimate->get('error');
        }

        echo nc_array_json($result);
        die;
    }

    /**
     * Получение наценки за способ оплаты
     */
    protected function action_get_payment_extra_cost() {
        $netshop = $this->netshop;
        $nc_core = nc_core::get_object();
        $data = $nc_core->input->fetch_post();

        $result = array(
            'price' => 0,
            'error' => null,
        );

        $order = nc_netshop_order::from_post_data($data, $netshop);
        $payment_method = $order->get_payment_method();
        if ($payment_method) {
            $result['price'] = $payment_method->get_extra_cost($order);
        }

        echo nc_array_json($result);
        die;
    }

    /**
     *
     */
    protected function action_merge_dialog() {
        $this->use_layout = false;
        $order_ids = array_map('intval', nc_core::get_object()->input->fetch_get_post('order_ids'));

        $query = "SELECT *
                    FROM `{$this->netshop->get_order_table_name()}`
                   WHERE `Message_ID` IN (" . join(', ', $order_ids) . ")
                   ORDER BY Created DESC";
        $orders = nc_record_collection::load('nc_netshop_order', $query);

        $statuses = (array)nc_db()->get_col(
            "SELECT `ShopOrderStatus_ID`, `ShopOrderStatus_Name`
               FROM `Classificator_ShopOrderStatus`
              WHERE `Checked` = 1
              ORDER BY `ShopOrderStatus_Priority`",
            1, 0
        );

        return $this->view('merge_dialog')
                    ->with('orders', $orders)
                    ->with('order_statuses', $statuses)
                    ->with('form_action', $this->get_script_path() . 'merge');
    }

    /**
     *
     */
    protected function action_merge() {
        $nc_core = nc_core::get_object();

        $base_order_id = $nc_core->input->fetch_get_post('base_order_id');
        $order_ids = $nc_core->input->fetch_get_post('order_ids');
        $merged_orders_status = $nc_core->input->fetch_get_post('merged_orders_status');

        $new_order = $this->netshop->merge_orders_into_new_order($order_ids, $base_order_id, $merged_orders_status);
        $new_order_id = $new_order->get_id();

        $order_edit_dialog_url =
            $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . 'message.php' .
            '?catalogue=' . $this->site_id .
            '&sub=' . $this->order_subdivision_id .
            '&cc=' . $this->order_infoblock_id .
            '&message=' . $new_order_id .
            '&is_merged=1' .
            '&inside_admin=1' .
            '&inside_netshop=1';

        $result = array(
            'order_id' => $new_order_id,
            'order_edit_dialog_url' => $order_edit_dialog_url,
        );

        echo nc_array_json($result);
        die;
    }

    /**
     * Экран управления сценариями статусов заказов
     */
    protected function action_statuses() {
        $this->ui_config->actionButtons[] = array(
          "id" => "submit_form",
          "caption" => NETCAT_MODULE_NETSHOP_SAVE,
          "action" => "mainView.submitIframeForm()"
        );
        return $this->view('statuses');
    }
    
    /**
     * Сохранение сценариев статусов заказов
     */
    protected function action_statuses_save() {
        $conditions = (array) $this->input->fetch_post('condition');
        $catalogue_id = $this->input->fetch_get('catalogue_id');
        $conditions = json_encode($conditions);
        $this->nc_core->set_settings('OrderStatusConditions', $conditions, 'netshop', $catalogue_id);
        parent::redirect_to_index_action('&action=statuses');
    }

    /*
     * Список способов доставки (для шаблона 5.8)
     */
    protected function action_get_delivery_method_list() {
        $netshop = $this->netshop;
        $order = nc_netshop_order::from_post_data($this->input->fetch_post(), $netshop);

        $context = nc_netshop_condition_context::for_order($order);
        $delivery_methods = $netshop->delivery->get_enabled_methods()->matching($context);

        $this->use_layout = false;
        return $this->view('delivery_method_list', array(
            'delivery_methods' => $delivery_methods,
            'delivery_variant_id' => $this->input->fetch_post('delivery_variant_id'),
            'delivery_point_id' => $this->input->fetch_post('delivery_point_id'),
            'order' => $order,
        ));
    }

    /**
     * Список способов оплаты (для шаблона 5.8)
     */
    protected function action_get_payment_method_list() {
        $netshop = $this->netshop;
        $order = nc_netshop_order::from_post_data($this->input->fetch_post(), $netshop);

        $context = nc_netshop_condition_context::for_order($order);
        $payment_methods = $netshop->payment->get_enabled_methods()->matching($context);

        $this->use_layout = false;
        return $this->view('payment_method_list', array(
            'payment_methods' => $payment_methods,
            'order' => $order,
        ));
    }
}