<?php


class nc_netshop_currency_admin_controller extends nc_netshop_admin_table_controller {

    /** @var string  */
    protected $data_type = 'currency';

    /** @var  nc_netshop_currency_admin_ui */
    protected $ui_config;

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $table = new nc_netshop_currency_table();
        $currencies = $table->for_site($this->site_id)->as_object()->get_result();

        if (count($currencies)) {
            $view = $this->view('currency_list')
                         ->with('currencies', $currencies)
                         ->with('fields', $table->get_fields())
                         ->with('currency_names', $this->netshop->get_setting('Currencies'));
        }
        else {
            $view = $this->view('empty_list')
                         ->with('is_error', true)
                         ->with('message', NETCAT_MODULE_NETSHOP_SETTINGS_NO_CURRENCIES_ON_SITE);
        }

        $this->ui_config->add_create_button("currency.add($this->site_id)");

        return $view;
    }

    /**
     *
     */
    protected function basic_table_edit_action($id = 0, $view = 'form', $save_mail_attachment_form = null) {
        // Если в настройках магазина на сайте не указана валюта по умолчанию,
        // сделать сохраняемую валюту валютой по умолчанию
        $submitted_data = $this->input->fetch_post('data');
        if (isset($submitted_data['Currency_ID']) && !$this->netshop->get_setting('DefaultCurrencyID')) {
            nc_core::get_object()->set_settings('DefaultCurrencyID', $submitted_data['Currency_ID'], 'netshop', $this->site_id);
        }

        return parent::basic_table_edit_action($id, $view, $save_mail_attachment_form);
    }


    /**
     *
     * @return nc_ui_view
     */
    protected function action_settings() {
        $this->save_settings();

        $this->ui_config->activeTab = "settings";
        $this->ui_config->set_location_hash("currency.settings($this->site_id)");
        $this->ui_config->add_submit_button();

        return $this->view('currency_settings');
    }

    /**
     * @return bool
     */
    protected function save_settings() {
        /** @var nc_core $nc_core */
        $nc_core = nc_core();
        $settings = $nc_core->input->fetch_post('settings');
        if (is_array($settings)) {
            foreach ($settings as $k=>$v) {
                $nc_core->set_settings($k, $v, 'netshop', $this->site_id);
            }
            return true;
        }
        return false;
    }

}