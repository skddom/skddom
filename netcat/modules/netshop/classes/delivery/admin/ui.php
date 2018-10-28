<?php

class nc_netshop_delivery_admin_ui extends nc_netshop_admin_ui {

    /**
     * @param $catalogue_id
     * @param string $current_action
     */
    public function __construct($catalogue_id, $current_action = "index") {
        parent::__construct('delivery', NETCAT_MODULE_NETSHOP_DELIVERY);

        $this->catalogue_id = $catalogue_id;
        $this->activeTab = $current_action;
    }

    /**
     * @param string $location  путь без 'module.netshop.delivery.'
     */
    public function set_delivery_location_suffix($location) {
        list($tab) = preg_split('/\W/', $location, 2);
        $this->activeTab = $tab;
        $this->locationHash = 'module.netshop.delivery.' . $location;
    }

    /**
     * Сгенерировать табы непосредственно перед выводом (потому что catalogue_id
     * может поменяться в процессе выполнения action)
     *
     * (Перенести обратно в __construct после создания универсального интерфейса для посайтового управления модулями.)
     *
     * @return string
     */
    public function to_json() {
        $this->tabs = array(
            array(
                'id' => 'method',
                'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_METHODS,
                'location' => "module.netshop.delivery.method($this->catalogue_id)"
            ),
            array(
                'id' => 'point',
                'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_POINTS,
                'location' => "module.netshop.delivery.point($this->catalogue_id)"
            ),
        );

        return parent::to_json();
    }

}