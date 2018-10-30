<?php

/**
 *
 */
class nc_netshop_order_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $order_component_id = nc_netshop::get_instance($id)->get_setting('OrderComponentID');
        $order_ids = nc_db()->get_col(
            "SELECT `m`.`Message_ID`
               FROM `Message{$order_component_id}` AS `m`
                    LEFT JOIN `Subdivision` AS `s` USING (`Subdivision_ID`)
              WHERE `s`.`Catalogue_ID` = " . (int)$id
        );

        $order_goods = nc_db_table::make('Netshop_OrderGoods')
                            ->where('Order_Component_ID', $order_component_id)
                            ->where_in('Order_ID', $order_ids)
                            ->get_result();
        $this->dumper->export_data('Netshop_OrderGoods', 'Netshop_OrderGoods_ID', $order_goods);

        $order_discounts = nc_db_table::make('Netshop_OrderDiscounts')
                            ->where('Order_Component_ID', $order_component_id)
                            ->where_in('Order_ID', $order_ids)
                            ->get_result();
        $this->dumper->export_data('Netshop_OrderDiscounts', 'Netshop_OrderDiscounts_ID', $order_discounts);

        $order_1c_map = nc_db_table::make('Netshop_OrderIds')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_OrderIds', null, $order_1c_map);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        $new_order_component_id = nc_netshop::get_instance($id)->get_setting('OrderComponentID');
        $order_id_dict_field = "Message{$new_order_component_id}.Message_ID";

        $order_fields = array(
            'Order_Component_ID' => 'Class_ID',
            'Order_ID' => $order_id_dict_field,
            'Item_Type' => 'Class_ID',
            'Item_ID' => array($this, 'map_order_item_id'),
        );

        $this->dumper->import_data('Netshop_OrderGoods', null, $order_fields);

        $discounts_fields = array(
            'Order_Component_ID' => 'Class_ID',
            'Order_ID' => $order_id_dict_field,
            'Item_Type'=> 'Class_ID',
            'Item_ID' => array($this, 'map_order_item_id'),
            'Discount_ID' => array($this, 'map_discount_id'),
        );
        $this->dumper->import_data('Netshop_OrderDiscounts', null, $discounts_fields);
        $this->dumper->import_data('Netshop_OrderIds', null, array('Netshop_Order_ID' => $order_id_dict_field));
    }

    /**
     * @param array $row
     * @param string $field
     * @return string
     */
    public function map_order_item_id($row, $field) {
        $new_item_component_id = $this->dumper->get_dict("Class_ID", $row['Item_Type']);
        $value = $this->dumper->get_dict("Message{$new_item_component_id}.Message_ID", $row['Item_ID']);

        return $value;
    }

    /**
     * @param array $row
     * @param string $field
     * @return string
     */
    public function map_discount_id($row, $field) {
        $value = nc_array_value($row, $field);

        if ($row['IsComponentBased'] == 0) {
            $discount_type = ucfirst($row['Discount_Type']);
            $dict_field = "Netshop_{$discount_type}Discount.Discount_ID";
            $value = $this->dumper->get_dict($dict_field, $value);
        }

        return $value;
    }

}