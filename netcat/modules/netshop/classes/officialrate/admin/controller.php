<?php


class nc_netshop_officialrate_admin_controller extends nc_netshop_admin_table_controller {

    /** @var string  */
    protected $data_type = 'officialrate';

    /** @var  nc_netshop_currency_admin_ui */
    protected $ui_config;

    /** @var string|null   Если не задан, определяется на основании $data_type */
    protected $ui_config_class = 'nc_netshop_currency_admin_ui';

    /** @var string  */
    protected $ui_config_base_path = "currency.officialrate";

    protected $rates_per_page = 1000;


    /**
     *
     */
    protected function action_index() {
        $table = new nc_netshop_officialrate_table();
        $rates = $table->for_site($this->site_id)
            ->order_by('Date', 'desc')
            ->limit($this->rates_per_page)
            ->as_object()->get_result();

        if (count($rates)) {
            $view = $this->view('officialrate_list');
            $view->official_rates = $rates;
            $view->fields = $table->get_fields();
            $view->currency_names = $this->netshop->get_setting('Currencies');
        }
        else {
            $view = $this->view('empty_list');
            $view->message = NETCAT_MODULE_NETSHOP_SETTINGS_NO_OFFICIAL_RATES_ON_SITE;
        }

        return $view;
    }

}