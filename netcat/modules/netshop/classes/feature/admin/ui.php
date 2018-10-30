<?php

class nc_netshop_feature_admin_ui extends nc_netshop_admin_ui {

    public function __construct($tree_node, $sub_header_text) {
        $this->headerText = NETCAT_MODULE_NETSHOP;
        $this->locationHash = '';
        $this->treeMode = "modules";
    }

}