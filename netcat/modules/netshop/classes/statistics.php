<?php


class nc_netshop_statistics {

    // const PERIOD_DAY   = 'DAY';
    // const PERIOD_WEEK  = 'WEEK';
    // const PERIOD_MONTH = 'MONTH';
    // const PERIOD_ALL   = '';


    protected $order_table;
    protected $order_goods_table;
    protected $order_status_table;
    protected $order_status_list;
    protected $order_sum_status_ids;

    protected $order_table_catalogue_subs = array();


    protected $catalogue_id;


    public function __construct(nc_netshop $netshop)
    {
        $this->netshop = $netshop;

        $this->catalogue_id = $netshop->get_catalogue_id();

        // Таблица заказов
        $orders_table_name = $netshop->get_order_table_name();
        $this->order_table = nc_db_table::make($orders_table_name, 'Message_ID');

        // Таблица заказанных товаров
        $this->order_goods_table = nc_db_table::make('Netshop_OrderGoods');

        // Таблица статусов заказа
        $this->order_status_table = nc_db_table::make('Classificator_ShopOrderStatus', 'ShopOrderStatus_ID');

        // Список заказов [Status_ID] => Status_Name
        $this->order_status_list = $this->order_status_table->get_list('ShopOrderStatus_Name');

        // Список идентификаторов статусов заказа по которым считается сумма покупок
        $order_sum_status_ids       = explode(',', $this->netshop->get_setting('PrevOrdersSumStatusID'));
        $this->order_sum_status_ids = array_map('intval', $order_sum_status_ids);

        // ...
        // $order_paid_status_id = $this->netshop->get_setting('PaidOrderStatusID');

        // Соотв-ие разделов (заказы) с id сайтов
        $order_class_id = $netshop->get_setting('OrderComponentID');
        $result = nc_db_table::make('Sub_Class')->where('Class_ID', $order_class_id)->get_list('Subdivision_ID', 'Catalogue_ID');
        foreach ($result as $sub_id => $cat_id) {
            $this->order_table_catalogue_subs[$cat_id][$sub_id] = $sub_id;
        }

        $tz = ini_get('date.timezone');
        if (empty($tz)) {
            date_default_timezone_set("Europe/Moscow");
        }
    }


    /**
     * Доступна ли статистика для текущего сайта
     * @return boolean
     */
    public function is_statistics_allowed() {
        return !empty($this->order_table_catalogue_subs[$this->catalogue_id]);
    }


    /**
     * Кол-во заказов по каждому статусу
     * @return array [status_name=>count]
     */
    public function get_order_status_counts() {
        $order_status_counts = array();

        $this->set_current_catalogue($this->order_table);
        $result = $this->order_table->select('Status, COUNT(*) AS totals')->group_by('Status')->get_list('Status', 'totals');

        foreach ($result as $id => $count) {
            $name = $id ? $this->order_status_list[$id] : NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER;
            $order_status_counts[$id] = array(
                'name'  => $name,
                'count' => $count
            );
        }

        // Проставляем нулевые значения
        // Новый заказ
        if (!isset($order_status_counts[0])) {
            $order_status_counts[0] = array(
                'name'  => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER,
                'count' => 0
            );
        }
        // По каждому статусу
        foreach ($this->order_status_list as $id => $name) {
            if (!isset($order_status_counts[$id])) {
                $order_status_counts[$id] = array(
                    'name'  => $name,
                    'count' => 0
                );
            }
        }

        ksort($order_status_counts, SORT_NUMERIC);

        return $order_status_counts;
    }


    /**
     * Общая статистика по заказам за периоды
     * @param  string $period DAY | WEEK | MONTH | YEAR
     * @return array
     */
    public function get_order_totals($period = 30, $previous_period = false) {
        $period_dates = $this->init_period_query($this->order_table, $period, $previous_period);

        // Всего заказов
        $this->order_table->load_query('period');
        $this->set_current_catalogue($this->order_table);
        $total_orders = $this->order_table->count_all();

        // Завершенные заказы
        $this->set_current_catalogue($this->order_table->load_query('period'));
        $order = $this->order_table
            ->select('COUNT(*) completed_orders, SUM(TotalPrice) sales_amount, SUM(TotalGoods) purchased_goods')
            ->where_in('Status', $this->order_sum_status_ids)
            ->get_row();

        return array(
            // Всего заказов
            'total_orders' => $total_orders,
            // Заказов выполнено (оплачено или завершено)
            'completed_orders' => $order['completed_orders'],
            // Процент успешных заказов
            'successful_orders_percentage' => ($total_orders ? floor(($order['completed_orders'] / $total_orders) * 100) : 0),
            // Куплено товаров
            'purchased_goods' => (int) $order['purchased_goods'],
            // Проданно на сумму (учитывая скидки)
            'sales_amount' => (float) $order['sales_amount'],
            // Средняя стоимость заказа
            'avg_order_amount' => $order['completed_orders'] ? $order['sales_amount'] / $order['completed_orders'] : 0,
            // Средние ежедневные продажи
            'avg_order_amount_by_day' => $period_dates['days'] ? $order['sales_amount'] / $period_dates['days'] : 0,
            // Дней в периоде
            'days_in_period' => $period_dates['days'],
        );
    }


    public function get_order_avg_totals($period = 'DAY', $avg_period = 'WEEK', $avg_offset = 0) {

        $this->init_period_query($this->order_table, $avg_period, $avg_offset);

        // Всего заказов
        $this->set_current_catalogue($this->order_table->load_query('period'));
        $result = $this->order_table->select("{$period}(`Created`) period, COUNT(*)")->group_by("period")->get_list('period', 'COUNT(*)');

        $avg_orders = $result ? number_format(array_sum($result)/count($result), 1) : 0;

        // Завершенные заказы
        $this->set_current_catalogue($this->order_table->load_query('period'));
        $orders = $this->order_table
            ->select("{$period}(`Created`) period, COUNT(*) completed_orders, SUM(TotalPrice) sales_amount, SUM(TotalGoods) purchased_goods")
            ->where_in('Status', $this->order_sum_status_ids)
            ->group_by('period')
            ->get_result();

        $avg = array();
        foreach ($orders as $row) {
            if (!$avg) {
                $avg = $row;
            } else {
                foreach ($row as $k => $val) {
                    $avg[$k] = $avg[$k] + $val;
                }
            }
        }

        $period_dividers = array('WEEK' => 7, 'MONTH' => 30, 'YEAR' => 356);
        $divider = isset($period_dividers[$avg_period]) ? $period_dividers[$avg_period] : count($orders);
        foreach ($avg as &$val) {
            $val = number_format($val / $divider, 1, '.', '');
        }

        return array(
            // Всего заказов
            'total_orders' => $avg_orders,
            // Заказов выполнено (оплачено или завершено)
            'completed_orders' => $avg['completed_orders'],
            // Процент успешных заказов
            'successful_orders_percentage' => ($avg_orders ? floor(($avg['completed_orders'] / $avg_orders) * 100) : '0') . '%',
            // Куплено товаров
            'purchased_goods' => (int) $avg['purchased_goods'],
            // Проданно на сумму (учитывая скидки)
            'sales_amount' => (int) $avg['sales_amount'],
        );
    }


    public function get_order_stat($period = 30, $group_by = 'day', $previous_period = false) {
        $grouping_settings = $this->get_grouping_settings($group_by);
        if (!$grouping_settings) {
            return array();
        }

        $sql_period = $grouping_settings['sql_date_format'] . " AS period";

        $period_dates = $this->init_period_query($this->order_table, $period, $previous_period);
        $dt_from  = $period_dates['from'];
        $dt_to    = $period_dates['to'];

        // Всего заказов
        $this->order_table->load_query('period');
        $total_orders = $this->set_current_catalogue($this->order_table)
            ->select("{$sql_period}, COUNT(*) total_orders")
            ->group_by('period')
            ->index_by('period')
            ->order_by('{table}.Created')
            ->get_result();

        // Завершенные заказы
        $this->order_table->load_query('period');
        $orders = $this->set_current_catalogue($this->order_table)
            ->select("{$sql_period}, COUNT(*) completed_orders, SUM(TotalPrice) sales_amount, SUM(TotalGoods) purchased_goods")
            ->where_in('Status', $this->order_sum_status_ids)
            ->group_by('period')
            ->index_by('period')
            ->order_by('{table}.Created')
            ->get_result();


        $empty_row = array(
            'period'                       => '',
            'completed_orders'             => '0',
            'sales_amount'                 => '0',
            'purchased_goods'              => '0',
            'total_orders'                 => '0',
            'successful_orders_percentage' => '0%',
        );

        $result = array();

        // Заполняем каждый день (период) пустыми значениями
        while ($dt_to > $dt_from) {
            $key = $dt_from->format($grouping_settings['date_format']);
            $dt_from->modify('+1 ' . $group_by);
            $result[$key] = $empty_row;
            $result[$key]['period'] = $key;
        }

        foreach ($orders as $period => $order) {
            if (empty($result[$period])) {
                $result[$period] = $empty_row;
                $result[$period]['period'] = $key;
            }
            $result[$period] = array_merge($result[$period], $order);
        }

        foreach ($total_orders as $period => $order) {
            $result[$period]['total_orders']                 = $order['total_orders'];
            $result[$period]['successful_orders_percentage'] = ($order['total_orders'] ? ($result[$period]['completed_orders'] / $order['total_orders'] * 100) : 0) . '%';
        }

        return $this->prepare_period_result($result, $group_by);
    }


    public function get_coupons_stat($period = 30, $group_by = 'day', $previous_period = false) {
        $grouping_settings = $this->get_grouping_settings($group_by);
        if (!$grouping_settings) {
            return array();
        }

        $sql_period = $grouping_settings['sql_date_format'] . " AS period";
        // Netshop_Coupon
        // Netshop_ItemDiscount (Deal_ID)
        // dd('ok');
        // exit;
        $period_dates = $this->init_period_query($this->order_table, $period, $previous_period);
        $dt_from  = $period_dates['from'];
        $dt_to    = $period_dates['to'];

        // Всего заказов
        $this->order_table->load_query('period');
        $total_orders = $this->set_current_catalogue($this->order_table)
            ->select("{$sql_period}, COUNT(*) total_orders")
            ->group_by('period')
            ->index_by('period')
            ->order_by('{table}.Created')
            ->get_result();

        // Завершенные заказы
        $this->order_table->load_query('period');
        $orders = $this->set_current_catalogue($this->order_table)
            ->select("{$sql_period}, COUNT(*) completed_orders, SUM(TotalPrice) sales_amount, SUM(TotalGoods) purchased_goods")
            ->where_in('Status', $this->order_sum_status_ids)
            ->group_by('period')
            ->index_by('period')
            ->order_by('{table}.Created')
            ->get_result();


        $empty_row = array(
            'period'                       => '',
            'completed_orders'             => '0',
            'sales_amount'                 => '0',
            'purchased_goods'              => '0',
            'total_orders'                 => '0',
            'successful_orders_percentage' => '0%',
        );

        $result = array();

        // Заполняем каждый день (период) пустыми значениями
        while ($dt_to > $dt_from) {
            $key = $dt_from->format($grouping_settings['date_format']);
            $dt_from->modify('+1 ' . $group_by);
            $result[$key] = $empty_row;
            $result[$key]['period'] = $key;
        }

        foreach ($orders as $period => $order) {
            if (empty($result[$period])) {
                $result[$period] = $empty_row;
                $result[$period]['period'] = $key;
            }
            $result[$period] = array_merge($result[$period], $order);
        }

        foreach ($total_orders as $period => $order) {
            $result[$period]['total_orders']                 = $order['total_orders'];
            $result[$period]['successful_orders_percentage'] = ($order['total_orders'] ? ($result[$period]['completed_orders'] / $order['total_orders'] * 100) : 0) . '%';
        }

        return $this->prepare_period_result($result, $group_by);
    }


    public function get_customers_total() {
        $sql = $this->set_current_catalogue($this->order_table)->select('1')->group_by('Email')->make_query();
        return nc_db()->get_var("SELECT COUNT(*) FROM ({$sql}) AS a");
    }


    public function get_customers_by($order = 'TotalOrders', $items = 30, $page = 1) {
        $items = (int)$items;
        $page  = $page < 1 ? 1 : (int)$page;

        $this->set_current_catalogue($this->order_table);

        return $this->order_table
            ->select('COUNT(*) AS TotalOrders, SUM(TotalGoods) AS TotalGoods, SUM(TotalPrice) AS TotalPrice, User_ID, Email, ContactName')
            ->group_by('Email')
            ->order_by($order, 'DESC')
            ->limit(($page-1)*$items, $items)
            ->get_result();
    }


    public function get_customers_by_tatal_orders($items = 30, $page = 1) {
        return $this->get_customers_by('TotalOrders', $items, $page);
    }


    public function get_order_goods_total() {
        $this->set_current_catalogue($this->order_goods_table);
        return (int) $this->order_goods_table->select('COUNT(DISTINCT CONCAT(`Item_Type`, `Item_ID`))')->get_value();
    }


    public function get_order_goods_by_qty($items = 20, $page = 1) {
        return $this->get_order_goods_by('Qty', $items, $page);
    }


    public function get_order_goods_by_sales_amount($items = 20, $page = 1) {
        return $this->get_order_goods_by('SalesAmount', $items, $page);
    }


    public function get_order_goods_by($by, $items = 20, $page = 1) {
        $items = (int)$items;
        $page  = $page < 1 ? 1 : (int)$page;

        $order_goods = $this->order_goods_table
            ->select('`Item_Type` AS Class_ID, `Item_ID` AS Message_ID, SUM(Qty) AS Qty, SUM(ItemPrice*Qty) AS SalesAmount')
            ->where('Catalogue_ID', $this->catalogue_id)
            ->group_by('`Item_Type`, `Item_ID`')
            ->order_by($by, 'DESC')
            ->limit(($page-1)*$items, $items)
            ->get_result();

        $result = array();
        foreach ($order_goods as $row) {
            $result[] = new nc_netshop_item($row);
        }

        return $result;
    }

    /**************************************************************************
        Protected methods
    **************************************************************************/

    protected function init_period_query($table, $period = 7, $previous_period = false) {
        $dt_from  = new DateTime;
        $dt_to    = new DateTime;

        if (is_numeric($period)) {
            $dt_from->modify("-{$period} day");
        } elseif (is_array($period)) {
            call_user_func_array(array($dt_from, 'setDate'), explode('-', $period[0]));
            call_user_func_array(array($dt_to,   'setDate'), explode('-', $period[1]));
            $dt_to->modify("+1 day");
        }

        $days_in_period = $dt_from->diff($dt_to)->format('%a');

        if ($previous_period) {
            $dt_from->modify("-{$days_in_period} day");
            $dt_to->modify("-{$days_in_period} day");
        }

        $from_date = $dt_from->format('Y-m-d');
        $to_date   = $dt_to->format('Y-m-d');

        $table->where("{table}.`Created` >= DATE('{$from_date} 00:00:00')");
        $table->where("{table}.`Created` <= DATE('{$to_date} 23:59:59')");

        $table->save_query('period');

        return array(
            'from' => $dt_from,
            'to'   => $dt_to,
            'days' => $days_in_period,
        );
    }


    protected function set_current_catalogue($table) {
        if (!empty($this->order_table_catalogue_subs[$this->catalogue_id])) {
            $table->where_in('Subdivision_ID', $this->order_table_catalogue_subs[$this->catalogue_id]);
        } else {
            $table->where('Subdivision_ID', '-1');
        }

        return $table;
    }


    protected function get_grouping_settings($group_by) {
        static $grouping_settings = array(
            'day' => array(
                'date_format'     => 'd.m.Y',
                'sql_date_format' => "DATE_FORMAT({table}.`Created`, '%d.%m.%Y')",
            ),
            'week' => array(
                'date_format'     => 'oW',
                'sql_date_format' => "YEARWEEK({table}.`Created`, 3)",
            ),
            'month' => array(
                'date_format'     => 'm.Y',
                'sql_date_format' => "DATE_FORMAT({table}.`Created`, '%m.%Y')",
            ),
        );

        if (empty($grouping_settings[$group_by])) {
            return false;
        }

        return $grouping_settings[$group_by];
    }


    protected function prepare_period_result($result, $group_by) {
        switch ($group_by) {
            case 'week':
                foreach ($result as $period => $stat) {
                    $y = substr($period, 0, 4);
                    $w = substr($period, 4, 2);
                    $result[$period]['period'] = $w . ' ' . $y;
                }
                break;
        }

        return $result;
    }
}
