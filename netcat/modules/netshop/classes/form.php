<?php

/***************************************************************************
    nc_netshop_form
***************************************************************************/

class nc_netshop_form {

    //--------------------------------------------------------------------------

    public $name;
    public $keyword;
    // public $edit_mode;

    //--------------------------------------------------------------------------

    protected $netshop;

    //--------------------------------------------------------------------------

    public function __construct(nc_netshop $netshop) {
        $this->netshop = $netshop;
    }

    //--------------------------------------------------------------------------

    public function name() {
        return $this->name;
    }

    //--------------------------------------------------------------------------

    public function keyword() {
        return $this->keyword;
    }

    //--------------------------------------------------------------------------

    public function fields() {
        return array();
    }

    //--------------------------------------------------------------------------

    public function get_settings() {
        $nc_core = nc_core::get_object();
        $settings = array();
        // $settings = $this->fields();

        // if ( ! $this->netshop->forms->edit_mode) {
        //     foreach ($settings as $k => $val) {
        //         $settings[$k] = null;
        //     }
        // }

        $db_settings = $nc_core->get_settings('form_' . $this->keyword, 'netshop', true);
        if ($db_settings) {
            $db_settings = unserialize($db_settings);
            foreach ($db_settings as $key => $value) {
                if ($value) {
                    $settings[$key] = $value;
                }
            }
            // $settings = array_merge($settings, (array)unserialize($db_settings));
        }

        return $settings;
    }

    //--------------------------------------------------------------------------

    public function set_settings($_data) {
        $data = array();
        foreach ($_data as $key => $value) {
        	$keys = $this->fields();
            if (isset($keys[$key])) {
                $data[$key] = $value;
            }
        }
        if ($data) {
            $nc_core = nc_core::get_object();
            return $nc_core->set_settings('form_' . $this->keyword, serialize($data), 'netshop');
        }

        return false;
    }

    //--------------------------------------------------------------------------

    public function get_data($order_id = 0) {
        $data = array();
        $data['netshop'] = $data['shop'] = $this->netshop;
        if ($order_id) {
            $order = $this->netshop->load_order($order_id);
            $data['order_items'] = $order->get_items();
        }
        else {
            $order = array('id' => rand(0, 1000), 'count' => 0, 'sum' => 0);
            $items = $data['order_items'] = new nc_netshop_item_collection();
            for ($i=0; $i<5; $i++) {
                $price = rand(1, 30) * 5 * 10;
                $count = rand(1, 3);
                $items->add(new nc_netshop_item(array(
                    'Name'        => 'Product name #' . rand(10000, 99999),
                    'Class_ID'    => PHP_INT_MAX,
                    'ItemPriceF'  => $price,
                    'ItemPrice'   => $price,
                    'Qty'         => $count,
                    'TotalPriceF' => $price * $count,
                    'TotalPrice'  => $price * $count,
                )));
            }
        }

        $data['form']  = new nc_netshop_form_data($this->get_settings(), true);
        $data['form']->set_titles($this->fields());
        $data['order'] = new nc_netshop_form_data($order);

        $month_names = explode("/", NETCAT_MODULE_NETSHOP_MONTHS_GENITIVE);
        $data['current_date'] = strftime("%d")
            . " " . $month_names[(int)strftime("%m")]
            . " " . strftime("%Y");

        return $data;
    }

    //--------------------------------------------------------------------------

    public function get_template($order_id = 0) {
        ob_start();
        $this->template( $this->get_data($order_id) );
        return ob_get_clean();
    }

    //--------------------------------------------------------------------------

    public function template($data) {
        $nc_core     = nc_core::get_object();
        $charset     = $nc_core->NC_UNICODE ? 'utf8' : 'cp1251';
        $view_file   = $this->netshop->forms->FORMS_TEMPLATE_FOLDER . $this->keyword;

        $view_file .= file_exists($view_file .'.' . $charset . '.php') ? '.'.$charset : '';

        echo $nc_core->ui->view($view_file, $data)->make();
    }

    //--------------------------------------------------------------------------
}