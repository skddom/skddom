<?php


class nc_netshop_statistics_admin_controller extends nc_netshop_admin_controller {

    protected $rows_per_page = 20;
    protected $table_limits = array(
        20  => 20,
        50  => 50,
        100 => 100,
    );

    protected $chart_defaults = array(
        'width'  => '100%',
        'height' => 300,
        'months_short' => NETCAT_DATEPICKER_CALENDAR_MONTHS_SHORT,
        'months'       => NETCAT_MODULE_NETSHOP_MONTHS_GENITIVE,
    );

    protected $chart_labels = array(
        'total_orders'                 => NECTAT_MODULE_NETSHOP_TOTAL_ORDERS,
        'completed_orders'             => NECTAT_MODULE_NETSHOP_COMPLETED_ORDERS,
        'purchased_goods'              => NECTAT_MODULE_NETSHOP_PURCHASED_GOODS,
        'sales_amount'                 => NECTAT_MODULE_NETSHOP_SALES_AMOUNT,
        'successful_orders_percentage' => NECTAT_MODULE_NETSHOP_SUCCESSFUL_ORDERS_PERCENTAGE,
    );

    //-------------------------------------------------------------------------

    protected function init() {
        parent::init();

        $this->chart_defaults['months'] = trim(str_replace('/', ' ', $this->chart_defaults['months']));

        $this->statistics = $this->netshop->statistics;
        $this->ui         = nc_core('ui');

        $this->bind('orders_by_period', array('period', 'group_by'));
        $this->bind('goods',            array('page', 'limit', 'order'));
        $this->bind('goods_by_period',  array('period', 'group_by'));
        $this->bind('customers',        array('page', 'limit', 'order'));

        $this->chart_defaults['nc_currency'] = $this->get_currency_name();
    }

    //-------------------------------------------------------------------------

    protected function before_action() {
        $this->ui_config = new nc_netshop_statistics_admin_ui($this->site_id, $this->current_action);
        if (!$this->statistics->is_statistics_allowed()) {

            echo BeginHtml() . $this->ui->controls->site_select($this->site_id) . $this->view('error_message')->with('message', NETCAT_MODULE_NETSHOP_ORDER_NO_INFOBLOCK) . EndHtml();
            return false;
        }
    }

    /**************************************************************************
        ACTIONS
    **************************************************************************/

    public function action_index() {
        return $this->action_orders();
    }

    /**************************************************************************
        ORDER STAT
    **************************************************************************/

    public function action_orders($period = false, $group_by = false) {
        // Кол-во заказов по каждому статусу (за весь период)
        $order_status_counts = $this->statistics->get_order_status_counts();

        return $this->view('statistics/orders_index')
            ->with('order_status_counts', $order_status_counts)
        ;
    }

    //-------------------------------------------------------------------------

    public function action_orders_by_period($period = false, $group_by = false) {
        $this->use_layout = false;

        $period_dates  = $this->get_period_dates($period);
        $period_header = $this->get_period_header($period);

        $stat        = $this->statistics->get_order_stat($period_dates, $group_by);
        $totals      = $this->statistics->get_order_totals($period_dates);
        $totals_last = $this->statistics->get_order_totals($period_dates, true);

        return $this->view('statistics/orders_by_period')
            ->with('stat',          $stat)
            ->with('period_header', $period_header)
            ->with('totals',        $totals)
            ->with('totals_last',   $totals_last)
            ->with('totals_diff',   $this->make_stat_diff($totals, $totals_last))
            ->with('chart_stat',    $this->make_chart_stat_json($stat))
        ;
    }

    /**************************************************************************
        GOODS STAT
    **************************************************************************/

    public function action_goods($page, $limit, $order) {
        $valid_orders = array('SalesAmount', 'Qty');
        if (!in_array($order, $valid_orders)) {
            $order = $valid_orders[0];
        }
        $page  = $page < 1 ? 1 : (int)$page;
        $limit = $limit ? (int) $limit : $this->rows_per_page;

        $goods = $this->statistics->get_order_goods_by($order, $limit, $page);

        $table_data = array();
        foreach ($goods as $i => $row) {

            $link = nc_core('SUB_FOLDER') . nc_core('HTTP_ROOT_PATH') . 'full.php?sub=' . $row['Subdivision_ID'] . '&cc=' . $row['Sub_Class_ID'] . '&message=' . $row['Message_ID'];
            $table_data[$i] = array(
                'Name'        => $row['Name'] ? $this->ui->html->a($row['Name'])->href($link) : 'Unknown product',
                'Qty'         => $row['Qty'],
                'SalesAmount' => $this->netshop->format_price($row['SalesAmount']),
            );
        }

        $data = array(
            'limit'         => $limit,
            'page'          => $page,
            'order'         => $order,
            'total'         => $this->statistics->get_order_goods_total(),
            'page_url'      => $this->get_action_url(array('action'=>'goods', 'order'=>$order, 'limit'=>$limit, 'page'=>'')),
            'limit_url'     => $this->get_action_url(array('action'=>'goods', 'limit'=>'')),
            'order_url'     => $this->get_action_url(array('action'=>'goods', 'limit'=>$limit, 'order'=>'')),
            'table_data'    => $table_data,
            'table_title'   => NECTAT_MODULE_NETSHOP_TOP_PURCHASED_GOODS,
            'table_headers' => array(
                'Name'        => NETCAT_MODULE_NETSHOP_BANK_GOODS_TITLE,
                'Qty'         => NECTAT_MODULE_NETSHOP_PURCHASED_GOODS,
                'SalesAmount' => NECTAT_MODULE_NETSHOP_SALES_AMOUNT,
            ),
            'table_ordering_fields' => $valid_orders,
        );
        return $this->view('statistics/goods_index')
            ->with('table', $this->view('statistics/table', $data));
        ;
    }

    //--------------------------------------------------------------------------

    public function action_goods_by_period($period = false, $group_by = false) {
        $this->use_layout = false;

        $period_dates  = $this->get_period_dates($period);
        $period_header = $this->get_period_header($period);

        $stat = $this->statistics->get_order_stat($period_dates, $group_by);

        // $totals      = $this->statistics->get_order_totals($period_dates);
        // $totals_last = $this->statistics->get_order_totals($period_dates, true);

        return $this->view('statistics/goods_by_period')
            ->with('stat',          $stat)
            ->with('period_header', $period_header)
            // ->with('totals',        $totals)
            // ->with('totals_last',   $totals_last)
            // ->with('totals_diff',   $this->make_stat_diff($totals, $totals_last))
            ->with('chart_stat',    $this->make_chart_stat_json($stat))
        ;
    }

    /**************************************************************************
        CUSTOMERS STAT
    **************************************************************************/

    public function action_customers($page, $limit, $order) {
        $valid_orders = array('TotalPrice', 'TotalOrders', 'TotalGoods');
        if (!in_array($order, $valid_orders)) {
            $order = $valid_orders[0];
        }
        $page  = $page < 1 ? 1 : (int)$page;
        $limit = $limit ? (int) $limit : $this->rows_per_page;

        $customers = $this->statistics->get_customers_by($order, $limit, $page);
        $table_data = array();

        $netshop_path = nc_core('SUB_FOLDER') . nc_core('HTTP_ROOT_PATH') . 'modules/netshop/';

        foreach ($customers as $i => $row) {
            $user_link = nc_core('ADMIN_PATH') . 'user/index.php?phase=4&amp;UserID=' . $row['User_ID'];
            $total_orders = (int) $row['TotalOrders'];
            if ($total_orders && $row['Email']) {
                $total_orders = $this->ui->html->a($total_orders)
                    ->href("{$netshop_path}admin/?admin_mode=1&inside_admin=1&catalogue={$this->site_id}&controller=order&text_filter={$row['Email']}&order_status=-1&delivery_method=-1");
            }
            $table_data[$i] = array(
                'ContactName' => $row['User_ID'] ? $this->ui->html->a($row['ContactName'])->href($user_link) : $row['ContactName'],
                'Email'       => $row['Email'],
                'TotalOrders' => $total_orders,
                'TotalGoods'  => $row['TotalGoods'],
                'TotalPrice'  => $this->netshop->format_price($row['TotalPrice']),
            );
        }
        $data = array(
            'limit'         => $limit,
            'page'          => $page,
            'order'         => $order,
            'total'         => $this->statistics->get_customers_total(),
            'page_url'      => $this->get_action_url(array('action'=>'customers', 'order'=>$order, 'limit'=>$limit, 'page'=>'')),
            'limit_url'     => $this->get_action_url(array('action'=>'customers', 'limit'=>'')),
            'order_url'     => $this->get_action_url(array('action'=>'customers', 'limit'=>$limit, 'order'=>'')),
            'table_data'    => $table_data,
            'table_title'   => '',
            'table_headers' => array(
                'ContactName' => NETCAT_MODULE_NETSHOP_CUSTOMERS,
                'Email'       => '',
                'TotalOrders' => NECTAT_MODULE_NETSHOP_TOTAL_ORDERS,
                'TotalGoods'  => NECTAT_MODULE_NETSHOP_PURCHASED_GOODS,
                'TotalPrice'  => NECTAT_MODULE_NETSHOP_SALES_AMOUNT,
            ),
            'table_ordering_fields' => $valid_orders,
        );

        return $this->view('statistics/customers_index')
            ->with('table', $this->view('statistics/table', $data));
    }

    /**************************************************************************
        COUPONS STAT
    **************************************************************************/

    public function action_coupons() {

        $coupons = $this->statistics->get_coupons_stat();

        // return 'coupons';
    }

    /**************************************************************************
        PROTECTED
    **************************************************************************/

    protected function view($view, $data = array()) {
        $catalogue_id   = nc_core()->catalogue->id();

        $data['controller_link'] = $this->get_action_url();

        return parent::view($view, $data)
            ->with('currency',       $this->get_currency_name())
            ->with('table_limits',   $this->table_limits)
            ->with('chart_defaults', nc_array_json($this->chart_defaults))
            ->with('stat_init',      parent::view('statistics/stat_init', $data))
            ->with('period_stat',    parent::view('statistics/period_stat', $data))
            ->with('chart_init',     '<script src="'.nc_core('ADMIN_PATH').'js/nc/nc.chart.min.js"></script>');
    }

    //-------------------------------------------------------------------------

    protected function get_period_dates($period) {
        if (!is_numeric($period)) {
            if (preg_match('@(\d+)-(\d+)-(\d+):(\d+)-(\d+)-(\d+)@', $period)) {
                return explode(':', $period);
            } else {
                return false;
            }
        }

        return $period;
    }

    //-------------------------------------------------------------------------

    protected function get_period_header($period) {
        if (!is_numeric($period)) {
            if (preg_match('@(\d+)-(\d+)-(\d+):(\d+)-(\d+)-(\d+)@', $period, $m)) {
                return sprintf(' %02d.%02d.%d - %02d.%02d.%d', $m[3], $m[2], $m[1], $m[6], $m[5], $m[4]);
            } else {
                return '';
            }
        }

        return sprintf(NETCAT_MODULE_NETSHOP_X_DAYS, $period);
    }

    //-------------------------------------------------------------------------

    public function make_chart_stat($stat) {
        $chart_stat = array();

        $i = 0;
        if ($stat) {
            foreach ($stat as $period_key => $period_stat) {
                foreach ($period_stat as $k => $val) {
                    if ($k != 'period') {
                        $chart_stat[$k]['data'][] = array($period_stat['period'], $val * 1);
                    }
                }
            }

            foreach ($chart_stat as $period_key => $data) {
                $chart_stat[$period_key]['label'] = isset($this->chart_labels[$period_key]) ? $this->chart_labels[$period_key] : '';
            }
        }

        return $chart_stat;
    }

    //-------------------------------------------------------------------------

    public function make_chart_stat_json($stat) {
        return json_safe_encode( $this->make_chart_stat($stat) );
    }

    //-------------------------------------------------------------------------

    protected function make_stat_diff($data_a, $data_b) {
        $result = array();
        foreach ($data_a as $key => $value) {
            $result[$key] = $data_a[$key] - (isset($data_b[$key]) ? $data_b[$key] : 0);
        }

        return $result;
    }

    //-------------------------------------------------------------------------

    protected function get_action_url($params = array()) {
        $url = nc_core('SUB_FOLDER') . nc_core('HTTP_ROOT_PATH') . 'modules/netshop/admin/?controller=statistics';

        $params = array_merge(array('catalogue_id' => $this->site_id), $params);

        foreach ($params as $name => $value) {
            $url .= '&' . $name . '=' . $value;
        }

        return $url;
    }

    //-------------------------------------------------------------------------

    protected function get_currency_name() {
        static $currency_name;

        if (is_null($currency)) {
            $currency_id   = $this->netshop->get_currency_id(null);
            $params        = $this->netshop->get_setting('CurrencyDetails', $currency_id);
            $currency_name = strip_tags($params['NameShort']);
        }

        return $currency_name;
    }
}
