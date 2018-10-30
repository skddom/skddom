<?php

class nc_netshop_promotion_userfilter implements nc_netshop_condition_visitor {

    /** @var nc_netshop_condition */
    protected $conditions;

    /** @var int */
    protected $catalogue_id;

    // QUERY PARTS
    protected $select_fields = array();
    protected $joins = array();

    // Just for short
    /** @var nc_db */
    protected $db;

    /**
     * @param string|array|nc_netshop_condition $conditions
     * @param $catalogue_id
     */
    public function __construct($conditions, $catalogue_id) {
        if (is_string($conditions) || is_array($conditions)) {
            $conditions = json_decode($conditions, true);
        }
        if (is_array($conditions) && count($conditions)) {
            $conditions = nc_netshop_condition::create($conditions);
        }

        if ($conditions instanceof nc_netshop_condition) {
            $this->conditions = $conditions;
        }

        $this->catalogue_id = (int)$catalogue_id;

        $this->db = nc_core('db');
    }

    /**
     * @param string $fields_to_select
     * @param string $extra_conditions
     * @return array
     */
    public function filter($fields_to_select = "User.*", $extra_conditions = '') {
        /* TODO: изменить условие (User.Catalogue_ID), когда будет добавлена возможность привязки пользователя к нескольким сайтам */
        $sql_where = "User.Checked = 1 AND User.Catalogue_ID IN (0, " .(int)$this->catalogue_id . ")";
        if ($this->conditions) {
            // must be called first:
            $sql_where .= " AND " . $this->conditions->visit($this);
        }

        $query = "SELECT $fields_to_select
                    FROM User " .
                 ($this->joins ? " JOIN " . implode(" JOIN ", $this->joins) : "") .
                 " WHERE $sql_where " . ($extra_conditions ? " AND $extra_conditions" : "") . "
                   GROUP BY User.User_ID";

        $result = $this->db->get_results($query, ARRAY_A);
        return (array)$result;
    }

    /**
     * @param nc_netshop_condition $condition
     * @return string
     */
    public function accept_condition(nc_netshop_condition $condition) {
        if ($condition instanceof nc_netshop_condition_composite) {
            return $this->accept_composite($condition);
        }
        else {
            $condition_type = str_replace("nc_netshop_condition_", "", get_class($condition));
            $method = "accept_{$condition_type}";
            if (method_exists($this, $method)) {
                return $this->$method($condition);
            }
            else {
                trigger_error("User filter cannot process condition of type " . get_class($condition), E_USER_ERROR);
            }
        }
    }

    /**
     * @param nc_netshop_condition_composite $condition
     * @return string
     */
    protected function accept_composite(nc_netshop_condition_composite $condition) {
        $sql_conditions = array();
        foreach ($condition->get_children() as $child) {
            $sql_conditions[] = $child->visit($this);
        }
        if (!sizeof($sql_conditions)) { $sql_conditions[] = "1"; }

        $operator = null;
        if ($condition instanceof nc_netshop_condition_and) {
            $operator = "AND";
        }
        elseif ($condition instanceof nc_netshop_condition_or) {
            $operator = "OR";
        }
        else {
            trigger_error("User filter cannot process condition of type " . get_class($condition), E_USER_ERROR);
        }

        return "(" . join("\n$operator ", $sql_conditions) . ")";
    }

    /**
     *
     */
    protected function escape($value) {
        if (is_numeric($value)) {
            return $value;
        }
        else {
            return "'" . $this->db->escape($value) . "'";
        }
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return string
     */
    protected function compare($field, $operator, $value) {
        switch ($operator) {
            case 'eq': return "$field = "  . $this->escape($value);
            case 'ne': return "$field != " . $this->escape($value);
            case 'gt': return "$field > "  . $this->escape($value);
            case 'ge': return "$field >= " . $this->escape($value);
            case 'lt': return "$field < "  . $this->escape($value);
            case 'le': return "$field <= " . $this->escape($value);
            // case-insensitive
            case 'contains': return "LOCATE(UPPER(" . $this->escape($value) . "), UPPER($field)) > 0";
            case 'notcontains': return "LOCATE(UPPER(" . $this->escape($value) . "), UPPER($field)) = 0";
            case 'begins': return "LOCATE(UPPER(" . $this->escape($value) . "), UPPER($field)) = 1";
            default: trigger_error("Unknown comparison operator '$operator'", E_USER_ERROR); return false;
        }
    }

    /**
     * @param nc_netshop_condition_user $condition
     * @return string
     */
    protected function accept_user(nc_netshop_condition_user $condition) {
        return $this->compare("User.User_ID",
                              $condition->get('op'),
                              $condition->get('value'));
    }

    /**
     * @param nc_netshop_condition_user_created $condition
     * @return string
     */
    protected function accept_user_created(nc_netshop_condition_user_created $condition) {
        return $this->compare("DATE(User.Created)",
                              $condition->get('op'),
                              date("Y-m-d", strtotime($condition->get('value')))
                             );
    }

    /**
     * @param nc_netshop_condition_user_group $condition
     * @return string
     */
    protected function accept_user_group(nc_netshop_condition_user_group $condition) {
        $this->joins["User_Group"] = "User_Group ON (User.User_ID = User_Group.User_ID)";
        return $this->compare("User_Group.PermissionGroup_ID",
                              $condition->get('op'),
                              $condition->get('value'));
    }

    /**
     * @param nc_netshop_condition_user_property $condition
     * @return string
     */
    protected function accept_user_property(nc_netshop_condition_user_property $condition) {
        $field = $condition->get('field_name');
        if (!preg_match("/^\w+$/", $field)) { return "0"; }
        return $this->compare("User.`$field`",
                              $condition->get('op'),
                              $condition->get('value'));
    }

    /**
     * @return string
     */
    protected function get_order_table_name() {
        static $table_name;
        if (!$table_name) {
            $table_name = nc_netshop::get_instance($this->catalogue_id)->get_order_table_name();
        }
        return $table_name;
    }

    protected function get_order_component_id() {
        static $component_id;
        if (!$component_id) {
            $component_id = nc_netshop::get_instance($this->catalogue_id)->get_setting('OrderComponentID');
        }
        return $component_id;
    }

    /**
     *
     */
    protected function join_orders() {
        if (!isset($this->joins["Orders"])) {
            $this->joins["Orders"] = $this->get_order_table_name() . " AS Orders ON (User.User_ID = Orders.User_ID)";
        }
    }

    /**
     *
     */
    protected function join_order_goods() {
        $this->join_orders();
        if (!isset($this->joins["Netshop_OrderGoods"])) {
            $this->joins["Netshop_OrderGoods"] = "Netshop_OrderGoods ON (" .
                "Netshop_OrderGoods.Order_Component_ID = " . $this->get_order_component_id() .
                "AND Orders.Message_ID = Netshop_OrderGoods.Order_ID" .
                ")";
        }
    }

    /**
     * @param nc_netshop_condition_orders_component $condition
     * @return string
     */
    protected function accept_orders_component(nc_netshop_condition_orders_component $condition) {
        $this->join_order_goods();
        return "Netshop_OrderGoods.Item_Type = " . $this->escape($condition->get('value'));
    }

    /**
     * @param nc_netshop_condition_orders_item $condition
     * @return string
     */
    protected function accept_orders_item(nc_netshop_condition_orders_item $condition) {
        $this->join_order_goods();
        return "(Netshop_OrderGoods.Item_Type = " . $this->escape($condition->get('component_id')) .
               " AND Netshop_OrderGoods.Item_ID = " . $this->escape($condition->get('item_id')) . ")";
    }

    /**
     * @param nc_netshop_condition_orders_count $condition
     * @return string
     */
    protected function accept_orders_count(nc_netshop_condition_orders_count $condition) {
        $this->joins["OrderCount"] = "(SELECT User_ID, COUNT(Message_ID) as Value
                                         FROM " . $this->get_order_table_name() . "
                                        GROUP BY User_ID) AS Order_Count
                                        ON (Order_Count.User_ID = User.User_ID)";
        return $this->compare("Order_Count.Value", $condition->get('op'), $condition->get('value'));
    }

    /**
     * @param $op
     * @param $value
     * @param int|null $from_timestamp
     * @param int|null $to_timestamp
     * @return string
     */
    protected function add_order_sum($op, $value, $from_timestamp = null, $to_timestamp = null) {
        static $counter = 1;

        $subquery_alias = "OrderSum$counter";
        $order_component_id = $this->get_order_component_id();
        $order_table = $this->get_order_table_name();

        $where = "";
        if ($from_timestamp) {
            $where = " WHERE DATE($order_table.Created) >= '" . date("Y-m-d", $from_timestamp) . "' ";
            if ($to_timestamp) {
                $where .= " AND DATE($order_table.Created) <= '" . date("Y-m-d", $to_timestamp) . "' ";
            }
        }

        // @todo DISCOUNTS ARE NOT BEING TAKEN INTO CONSIDERATION HERE
        $this->joins[$subquery_alias] = "(SELECT $order_table.User_ID, SUM(Netshop_OrderGoods.ItemPrice * Netshop_OrderGoods.Qty) AS Value
                                            FROM Netshop_OrderGoods
                                            JOIN $order_table ON (
                                                Netshop_OrderGoods.Order_Component_ID = $order_component_id
                                                AND Netshop_OrderGoods.Order_ID = $order_table.Message_ID
                                            )
                                            $where
                                           GROUP BY $order_table.User_ID)
                                          AS $subquery_alias
                                          ON ($subquery_alias.User_ID = User.User_ID)";
        $counter++;
        return $this->compare("$subquery_alias.Value", $op, $value);
    }

    /**
     * @param nc_netshop_condition_orders_sum $condition
     * @return string
     */
    protected function accept_orders_sum(nc_netshop_condition_orders_sum $condition) {
        return $this->add_order_sum($condition->get('op'), $condition->get('value'), null, null);
    }

    /**
     * @param nc_netshop_condition_orders_sumdates $condition
     * @return string
     */
    protected function accept_orders_sumdates(nc_netshop_condition_orders_sumdates $condition) {
        return $this->add_order_sum($condition->get('op'),
                                    $condition->get('value'),
                                    $condition->get('date_from'),
                                    $condition->get('date_to')
                                   );
    }

    /**
     * @param nc_netshop_condition_orders_sumperiod $condition
     * @return string
     */
    protected function accept_orders_sumperiod(nc_netshop_condition_orders_sumperiod $condition) {
        return $this->add_order_sum($condition->get('op'),
                                    $condition->get('value'),
                                    $condition->get('date_from'),
                                    null
                                   );
    }


}