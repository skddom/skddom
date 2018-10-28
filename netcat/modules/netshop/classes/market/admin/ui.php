<?php

class nc_netshop_market_admin_ui extends nc_netshop_admin_ui {

    /**
     * @param $catalogue_id
     * @param string $current_action
     */
    public function __construct($catalogue_id, $place, $current_action = "index") {
        parent::__construct('market', NETCAT_MODULE_NETSHOP_MARKETS);

        $this->locationHash .= ".".$place;
        $this->place = $place;

        $this->catalogue_id = $catalogue_id;
        $this->activeTab = $place;
        $this->_action = $current_action;

        $this->treeSelectedNode = "netshop-market.$place";
    }


    /**
     * @return string
     */
    public function to_json() {
        $catalogue = $this->catalogue_id ? "($this->catalogue_id)" : "";

        if ($this->locationHash == 'module.netshop.market.'.$this->place) {
            if ($this->_action != "index") {
                $this->locationHash = "module.netshop.market.".$this->place.".".$this->_action;
            }
            $this->locationHash .= $catalogue;
        }
        if (in_array($this->place, array('yandex', 'yandex_order'))) {
               $this->treeSelectedNode = "netshop-market.yandex";
               $this->tabs = array(
                   array(
                       'id'       => 'yandex',
                       'caption'  => NETCAT_MODULE_NETSHOP_MARKET_YANDEX_EXPORT,
                       'location' => "module.netshop.market.yandex" . $catalogue,
                       'group'    => "admin"
                   ),
                   array(
                       'id'       => 'yandex_order',
                       'caption'  => NETCAT_MODULE_NETSHOP_MARKET_YANDEX_ORDERS,
                       'location' => "module.netshop.market.yandex.order" . $catalogue,
                       'group'    => "admin",
                   )
               );
        }

        return parent::to_json();
    }

}