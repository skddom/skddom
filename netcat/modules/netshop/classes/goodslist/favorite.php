<?php

class nc_netshop_goodslist_favorite extends nc_netshop_goodslist_base {

    protected $tablename = 'Netshop_FavoriteGoods';

    //--------------------------------------------------------------------------

    public function __construct(nc_netshop $netshop) {
        parent::__construct($netshop);
        $this->cache_all();
    }

    public function get_toggle_action_url($item_id, $class_id) {
        return nc_module_path('netshop') . "actions/goodslist.php?type=favorite&action=toggle&item_id={$item_id}&class_id={$class_id}";
    }

    public function get_add_action_url($item_id, $class_id, $return_url = null) {
        $url = "actions/goodslist.php?type=favorite&action=add&item_id={$item_id}&class_id={$class_id}";
        if ($return_url) {
            $url .= '&return_url=' . urlencode($return_url);
        }
        return nc_module_path('netshop') . $url;
    }

    public function get_remove_action_url($item_id, $class_id, $return_url = null) {
        $url = "actions/goodslist.php?type=favorite&action=remove&item_id={$item_id}&class_id={$class_id}";
        if ($return_url) {
            $url .= '&return_url=' . urlencode($return_url);
        }
        return nc_module_path('netshop') . $url;
    }
}