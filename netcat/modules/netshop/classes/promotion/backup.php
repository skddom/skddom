<?php

/**
 *
 */
class nc_netshop_promotion_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $item_discounts = nc_db_table::make('Netshop_ItemDiscount')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_ItemDiscount', 'Discount_ID', $item_discounts);

        $cart_discounts = nc_db_table::make('Netshop_CartDiscount')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_CartDiscount', 'Discount_ID', $cart_discounts);

        $delivery_discounts = nc_db_table::make('Netshop_DeliveryDiscount')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_DeliveryDiscount', 'Discount_ID', $delivery_discounts);

        $coupons = nc_db_table::make('Netshop_Coupon')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_Coupon', 'Coupon_ID', $coupons);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        $condition_mapping = array('Condition' => array($this, 'update_condition_string'));

        $netshop = nc_netshop::get_instance();

        $this->dumper->import_data('Netshop_CartDiscount', null, $condition_mapping);

        if ($netshop->is_feature_enabled('promotion_discount_item')) {
            $this->dumper->import_data('Netshop_ItemDiscount', null, $condition_mapping);
        }

        if ($netshop->is_feature_enabled('promotion_discount_delivery')) {
            $this->dumper->import_data('Netshop_DeliveryDiscount', null, $condition_mapping);
        }

        $coupons_mapping = array(
            'Deal_ID' => array($this, 'map_deal_id'),
            'Batch_ID' => array($this, 'map_batch_id'),
            'SentTo_User_ID' => 'User_ID',
        );
        $this->dumper->import_data('Netshop_Coupon', null, $coupons_mapping);
    }

    /**
     * @param array $row
     * @param $field
     * @return mixed
     */
    public function map_deal_id($row, $field) {
        $value = nc_array_value($row, $field);

        if ($row['Deal_Type'] == 'discount_item') {
            $value = $this->dumper->get_dict('Netshop_ItemDiscount.Discount_ID', $value);
        }
        elseif ($row['Deal_Type'] == 'discount_cart') {
            $value = $this->dumper->get_dict('Netshop_CartDiscount.Discount_ID', $value);
        }
        elseif ($row['Deal_Type'] == 'discount_delivery') {
            $value = $this->dumper->get_dict('Netshop_DeliveryDiscount.Discount_ID', $value);
        }

        return $value;
    }

    /**
     * @param array $row
     * @param $field
     * @return null
     */
    public function map_batch_id($row, $field) {
        // TODO maybe sometime...
        return null;
    }

    /**
     * @param $row
     * @return array|false
     */
    protected function before_insert_discount_row($row) {
        if (!$this->can_insert_row_with_condition(nc_array_value($row, 'Condition'))) {
            return false;
        }
        return $row;
    }

    /**
     * @param $row
     * @return array|false
     */
    public function event_before_insert_netshop_itemdiscount($row) {
        return $this->before_insert_discount_row($row);
    }

    /**
     * @param $row
     * @return array|false
     */
    public function event_before_insert_netshop_cartdiscount($row) {
        return $this->before_insert_discount_row($row);
    }

    /**
     * @param $row
     * @return array|false
     */
    public function event_before_insert_netshop_deliverydiscount($row) {
        return $this->before_insert_discount_row($row);
    }
}