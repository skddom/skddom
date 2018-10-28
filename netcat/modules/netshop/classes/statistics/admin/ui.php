<?php

class nc_netshop_statistics_admin_ui extends nc_netshop_admin_ui {

    /**
     * @param $catalogue_id
     * @param string $current_action
     */
    public function __construct($catalogue_id, $current_action = "index") {
        parent::__construct('statistics', NETCAT_MODULE_NETSHOP_STATISTICS);

        $this->catalogue_id = $catalogue_id;
        $this->activeTab    = $current_action;
    }


    /**
     * Сгенерировать табы непосредственно перед выводом (потому что catalogue_id
     * может поменяться в процессе выполнения action)
     *
     * @todo Перенести обратно в __construct после создания универсального интерфейса  для посайтового управления модулями.
     *
     * @return string
     */
    public function to_json() {
        $current_action = $this->activeTab;
        $catalogue = $this->catalogue_id ? "($this->catalogue_id)" : "";

        if ($this->locationHash == 'module.netshop.statistics') {
            if ($current_action != 'index') {
                $this->locationHash = "module.netshop.statistics.$current_action";
            }
            $this->locationHash .= $catalogue;
        }

        $this->tabs = array(
            array(
                'id'       => 'index',
                'caption'  => NETCAT_MODULE_NETSHOP_SALES,
                'location' => "module.netshop.statistics" . $catalogue,
                'group'    => "admin",
            ),
            array(
                'id'       => 'goods',
                'caption'  => NETCAT_MODULE_NETSHOP_GOODS,
                'location' => "module.netshop.statistics.goods" . $catalogue,
                'group'    => "admin",
            ),
           // array(
           //     'id'       => 'coupons',
           //     'caption'  => NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_COUPONS,
           //     'location' => "module.netshop.statistics.coupons" . $catalogue,
           //     'group'    => "admin",
           // ),
            array(
                'id'       => 'customers',
                'caption'  => NETCAT_MODULE_NETSHOP_CUSTOMERS,
                'location' => "module.netshop.statistics.customers" . $catalogue,
                'group'    => "admin",
            ),
        );

        return parent::to_json();
    }

}