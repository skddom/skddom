<?php

class nc_netshop_currency_admin_ui extends nc_netshop_admin_ui {

    protected $_action = "";

    /**
     * @param $catalogue_id
     * @param string $current_action
     */
    public function __construct($catalogue_id, $current_action = "index") {
        parent::__construct('currency', NETCAT_MODULE_NETSHOP_CURRENCIES);

        $this->catalogue_id = $catalogue_id;
        $this->activeTab = 'currency';
        $this->_action = $current_action;
    }

    /**
     * Сгенерировать табы непосредственно перед выводом (потому что catalogue_id
     * может поменяться в процессе выполнения action)
     *
     * @todo (?) Перенести обратно в __construct после создания универсального интерфейса для посайтового управления модулями.
     *
     * @return string
     */
    public function to_json() {
        $catalogue = $this->catalogue_id ? "($this->catalogue_id)" : "";

        if ($this->_action == "index" && !preg_match('/\(\d+\)$/', $this->locationHash)) {
            $this->locationHash .= $catalogue;
        }

        $this->tabs = array(
            array(
                'id'       => 'currency',
                'caption'  => NETCAT_MODULE_NETSHOP_CURRENCIES,
                'location' => "module.netshop.currency" . $catalogue,
                'group'    => "admin",
            ),
            array(
                'id'       => 'officialrate',
                'caption'  => NETCAT_MODULE_NETSHOP_CB_RATES,
                'location' => "module.netshop.currency.officialrate" . $catalogue,
                'group'    => "admin",
            ),
            array(
                'id'       => 'settings',
                'caption'  => NETCAT_MODULE_NETSHOP_CURRENCY_SETTINGS_TAB,
                'location' => "module.netshop.currency.settings" . $catalogue,
                'group'    => "admin",
            ),
        );

        return parent::to_json();
    }

}