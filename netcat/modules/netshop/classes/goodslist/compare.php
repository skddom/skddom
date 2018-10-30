<?php

class nc_netshop_goodslist_compare extends nc_netshop_goodslist_base {

    protected $tablename = 'Netshop_CompareGoods';

    //--------------------------------------------------------------------------

    public function __construct(nc_netshop $netshop) {
        parent::__construct($netshop);
        $this->cache_all();
    }

    public function get_add_action_url($item_id, $class_id, $return_url = null) {
        $url = "actions/goodslist.php?type=compare&action=add&item_id={$item_id}&class_id={$class_id}";
        if ($return_url) {
            $url .= '&return_url=' . urlencode($return_url);
        }
        return nc_module_path('netshop') . $url;
    }

    public function get_remove_action_url($item_id, $class_id, $return_url = null) {
        $url = "actions/goodslist.php?type=compare&action=remove&item_id={$item_id}&class_id={$class_id}";
        if ($return_url) {
            $url .= '&return_url=' . urlencode($return_url);
        }
        return nc_module_path('netshop') . $url;
    }
}