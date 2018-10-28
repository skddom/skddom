<?php

abstract class nc_netshop_condition {
    /**
     * Creates a nc_netshop_promotion_condition_X instance, where X depends on the
     * 'type' key of the $options array
     * @param array $options
     * @return self
     */
    static public function create(array $options) {
        $class = __CLASS__ . "_" . $options['type'];
        if (!class_exists($class)) {
            $class = 'nc_netshop_condition_dummy';
        }
        return new $class($options);
    }

    /**
     * @param array $parameters
     */
    public function __construct($parameters = array()) {
        foreach ($parameters as $k => $v) {
            if (property_exists($this, $k)) { $this->$k = $v; }
        }
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function get($parameter) {
        if (property_exists($this, $parameter)) { return $this->$parameter; }
        return null;
    }

    /**
     * Проверяет, принадлежит ли условие (или его составные части) к указанному
     * типу $type условий.
     * Например, has_condition_of_type('order') будет TRUE для всех условий классов
     * nc_netshop_condition_order_property и т. п. и составных условий (and, or),
     * содержащих данный тип условий (но не для nc_netshop_condition_orders_item и т. п.)
     *
     * @param string $type    строка в нижнем регистре, например 'item', 'order', 'cart'
     * @return bool
     */
    public function has_condition_of_type($type) {
        $class_name_part_regexp = '/^' . __CLASS__ . '_' . $type . '(?:_|\b)/';
        return (bool)preg_match($class_name_part_regexp, get_class($this));
    }

    /**
     * @param nc_netshop_condition_context $context
     * @param nc_netshop_item $current_item
     * @return boolean
     */
    abstract public function evaluate(nc_netshop_condition_context $context, $current_item = null);


    /**
     * @param $value1
     * @param string $operator
     * @param $value2
     * @param int|null $value_type   one of NC_FIELDTYPE_ values
     * @return bool
     */
    protected function compare($value1, $operator, $value2, $value_type = null) {
        if ($value_type) {
            if ($value_type == NC_FIELDTYPE_DATETIME) {
                $value1 = date("Ymd", $this->get_datetime_value($value1));
                $value2 = date("Ymd", $this->get_datetime_value($value2));
            }
            elseif ($value_type == NC_FIELDTYPE_MULTISELECT) {
                $value1 = ",$value1,";
                $value2 = ",$value2,";
            }
        }

        switch ($operator) {
            case 'eq': return $value1 == $value2;
            case 'ne': return $value1 != $value2;
            case 'gt': return $value1 >  $value2;
            case 'ge': return $value1 >= $value2;
            case 'lt': return $value1 <  $value2;
            case 'le': return $value1 <= $value2;
            // case-insensitive
            case 'contains': return nc_stripos($value1, $value2) !== false;
            case 'notcontains': return nc_stripos($value1, $value2) === false;
            case 'begins': return nc_stripos($value1, $value2) === 0;
            default: trigger_error("Unknown comparison operator '$operator'"); return false;
        }
    }

    /**
     * @param $value
     * @return int
     */
    protected function get_datetime_value($value) {
        // on 32-bit systems date range is limited to years 1901—2038
        return strtotime($value);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function convert_decimal_point($value) {
        if (preg_match('/^\d+,\d+$/', $value)) {
            return str_replace(",", ".", $value);
        }
        return $value;
    }

    /**
     * @param nc_netshop_condition_visitor $visitor
     * @return mixed
     */
    public function visit(nc_netshop_condition_visitor $visitor) {
        return $visitor->accept_condition($this);
    }

    /**
     * Полное описание: название свойства + значение
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_full_description(nc_netshop $netshop) {
        $condition_type = str_replace("nc_netshop_condition_", "", get_class($this));
        return constant("NETCAT_MODULE_NETSHOP_COND_" . strtoupper($condition_type)) . ' ' .
               $this->get_short_description($netshop);
    }

    /**
     * Короткое описание (только значение, для повторяющихся условий)
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_short_description(nc_netshop $netshop) {
        if (isset($this->value)) {
            return $this->add_operator_description($this->value);
        }
        return '?';
    }

    /**
     * @param mixed $value
     * @param string|null $op
     * @return string
     */
    protected function add_operator_description($value, $op = null) {
        if (!$op) {
            if (isset($this->op)) { $op = $this->op; }
            else { $op = "eq"; }
        }
        $string = constant("NETCAT_MODULE_NETSHOP_OP_" . strtoupper($op));
        return sprintf($string, $value);
    }

    /**
     * @param nc_backup_dumper $dumper
     * @return array
     */
    public function get_updated_raw_options_array_on_import(nc_backup_dumper $dumper) {
        return array('type' => substr(get_class($this), strlen(__CLASS__)+1)) +
               $this->get_updated_parameters_on_import($dumper);
    }

    /**
     * @param nc_backup_dumper $dumper
     * @return array
     */
    protected function get_updated_parameters_on_import(nc_backup_dumper $dumper) {
        return get_object_vars($this);
    }
}