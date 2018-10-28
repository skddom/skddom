<?php

class nc_mod_netshop extends NetShopDeprecated {
    //--------------------------------------------------------------------------

    protected static $instance;

    //--------------------------------------------------------------------------

    // public $Cart;
    public $cart;
    public $filter;
    public $forms;
    public $delivery;
    public $recent;

    /**
     * Обновляемый заказ
     *
     * @var null|array
     */
    private $_updatingOrder = null;

    //--------------------------------------------------------------------------

    // TODO: Конструктор пока публичный для сохранения возможности создания объекта через "new NetShop"
    // После окончательной переделки магазина нужно убрать эту возможность (доступ только через get_instance())
    public function __construct($put_to_cart = false) {
        parent::NetShopDeprecated($put_to_cart);

        self::$instance = $this;

        $NETSHOP_CLASS_FOLDER = realpath(dirname(__FILE__)) . '/class/';

        require_once $NETSHOP_CLASS_FOLDER . 'nc_mod_netshop_cart.class.php';
        require_once $NETSHOP_CLASS_FOLDER . 'nc_mod_netshop_filter.class.php';

        $this->cart = nc_mod_netshop_cart::get_instance();
        $this->filter = nc_mod_netshop_filter::get_instance();
//        $this->forms = nc_netshop_forms::get_instance();
//        $this->delivery = nc_netshop_delivery::get_instance();
//        $this->recent = nc_netshop_recent::get_instance();
    }

    //--------------------------------------------------------------------------

    private function __clone() {
    }

    private function __wakeup() {
    }

    //--------------------------------------------------------------------------

    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    //--------------------------------------------------------------------------


    /**
     * Обработчик события перед обновлением заказа
     *
     * @param $Catalogue_ID
     * @param $Subdivision_ID
     * @param $Sub_Class_ID
     * @param $Class_ID
     * @param $Message_ID
     */
    public function updateOrderPrepHandler($Catalogue_ID, $Subdivision_ID, $Sub_Class_ID, $Class_ID, $Message_ID) {
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;
        $MODULE_VARS = $nc_core->modules->get_module_vars();
        $order_table = (int)$MODULE_VARS['netshop']['ORDER_TABLE'];

        $Message_ID = (int)$Message_ID;

        if ($Class_ID == $order_table) {
            $sql = "SELECT `Status` FROM `Message{$order_table}` WHERE `Message_ID` = {$Message_ID}";
            $current_status = (int)$db->get_var($sql);

            $this->_updatingOrder = array(
                'message_id' => $Message_ID,
                'status' => $current_status,
            );
        }
    }

    /**
     * Обработчик события обновления заказа
     *
     * @param $Catalogue_ID
     * @param $Subdivision_ID
     * @param $Sub_Class_ID
     * @param $Class_ID
     * @param $Message_ID
     */
    public function updateOrderHandler($Catalogue_ID, $Subdivision_ID, $Sub_Class_ID, $Class_ID, $Message_ID) {
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;
        $MODULE_VARS = $nc_core->modules->get_module_vars();
        $order_table = (int)$MODULE_VARS['netshop']['ORDER_TABLE'];

        $Message_ID = (int)$Message_ID;

        if ($Class_ID == $order_table) {
            $sql = "SELECT `Status` FROM `Message{$order_table}` WHERE `Message_ID` = {$Message_ID}";
            $current_status = (int)$db->get_var($sql);

            $updatedOrder = array(
                'message_id' => $Message_ID,
                'status' => $current_status,
            );

            $updatingOrder = $this->_updatingOrder;

            if (
                $updatingOrder && $updatingOrder['message_id'] == $updatedOrder['message_id'] &&
                $updatingOrder['status'] != 4 && $updatedOrder['status'] == 4
            ) {
                $sql = "SELECT `Item_Type`, `Item_ID`, `Qty`
                          FROM `Netshop_OrderGoods`
                         WHERE `Order_Component_ID` = {$order_table}
                           AND `Order_ID` = {$Message_ID}";
                $goods = (array)$db->get_results($sql, ARRAY_A);

                foreach ($goods as $item) {
                    $item_id = (int)$item['Item_ID'];
                    $class_id = (int)$item['Item_Type'];
                    $qty = (float)$item['Qty'];

                    $sql = "UPDATE `Netshop_StoreGoods` SET `Quantity` = `Quantity` - {$qty} WHERE " .
                        "`Quantity` > 0 " .
                        "AND `Netshop_Item_ID` = {$item_id} " .
                        "AND `Class_ID` = {$class_id} " .
                        "ORDER BY `Netshop_Store_ID` ASC LIMIT 1";

                    $db->query($sql);
                }
            }
        }

        $this->_updatingOrder = null;
    }

    /**
     * Returns stores remains
     * for item
     *
     * @param int $class_id
     * @param int $item_id
     * @param mixed $store_id
     * @return mixed
     */
    public function get_store_qty($class_id, $item_id, $store_id = 0) {
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;

        $item_id = (int)$item_id;
        $class_id = (int)$class_id;

        $sql = "SELECT `Import_Store_ID`, `Quantity` FROM `Netshop_StoreGoods` AS `sg` " .
            "LEFT JOIN `Netshop_Stores` AS `s` ON s.`Netshop_Store_ID` = sg.`Netshop_Store_ID` " .
            "LEFT JOIN `Message{$class_id}` AS `m` ON sg.`Netshop_Store_ID` = s.`Import_Source_ID` " .
            "WHERE sg.`Class_ID` = {$class_id} AND m.`ImportSourceID` = {$item_id}";

        if ($store_id) {
            if (is_int($store_id)) {
                $sql .= " AND s.`Netshop_Store_ID` = {$store_id}";
            } else {
                $store_id_escaped = $db->escape($store_id);
                $sql .= " AND s.`Import_Store_ID` = '{$store_id_escaped}'";
            }

            $result = $db->get_var($sql);
            if ($result !== null) {
                $result = (float)$result;
            }
        } else {
            $result = array();

            foreach ((array)$db->get_results($sql) as $row) {
                $result[$row['Import_Store_ID']] = $row['Quantity'];
            }
        }

        return $result;
    }

    /**
     * Returns store data
     * by internal or external id
     *
     * @param mixed $store_id
     * @return null|array
     */
    public function get_store_data($store_id) {
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;

        $sql = "SELECT * FROM `Netshop_Stores`";

        if (is_int($store_id)) {
            $sql .= " WHERE `Netshop_Store_ID` = {$store_id}";
        } else {
            $store_id_escaped = $db->escape($store_id);
            $sql .= " WHERE `Import_Store_ID` = '{$store_id_escaped}'";
        }

        return $db->get_row($sql, ARRAY_A);
    }

}