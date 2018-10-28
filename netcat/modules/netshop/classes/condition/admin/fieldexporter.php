<?php

/**
 * Класс, формирующий список полей товаров (admin/condition/data/json/component_field_list.php)
 */
class nc_netshop_condition_admin_fieldexporter {
    protected $component_data;
    protected $component_ids;
    protected $number_of_components;

    protected $fields;

    public function __construct() {
        $shop = nc_modules('netshop');
        $this->component_data = $shop->get_goods_components_data("c.`Class_Name`");
        $this->component_ids = join(', ', array_keys($this->component_data));
        $this->number_of_components = count($this->component_data);

        $this->load_field_data();
    }

    protected function load_field_data() {
        /* @var nc_db $db */
        $db = nc_core('db');

        $this->fields = $db->get_results(
            "SELECT `Class_ID`, `Field_ID`, `Field_Name`, `Description`, `TypeOfData_ID`, `Format`
               FROM `Field`
              WHERE `Class_ID` IN ($this->component_ids)
                AND `Checked` = 1
                AND " . nc_netshop_condition_admin_helpers::get_field_types_to_export_for_query() . "
              ORDER BY `Class_ID`, `Priority`",
            ARRAY_A
        );

        if (!$this->fields) {
            trigger_error("Unable to retrieve field data; check if goods components exist.", E_USER_ERROR);
        }
    }

    protected function get_grouped_fields() {
        $result = array();

        // determine which fields are common among several types of goods
        $grouped_fields = array();
        foreach ($this->fields as $field) {
            $key = "$field[TypeOfData_ID]__$field[Field_Name]";
            $grouped_fields[$key][] = $field;
        }

        // COMMON FIELDS
        foreach ($grouped_fields as $key => $fields) {
            if (count($fields) < 2) { continue; }
            $field = $fields[0];
            $field_id = "*:$field[Field_Name]";

            $result[$field_id] = nc_netshop_condition_admin_helpers::export_field($field, "*");

            $description = "";
            if (count($fields) == $this->number_of_components) {
                $description .= " [" . NETCAT_MODULE_NETSHOP_CONDITION_FIELD_BELONGS_TO_ALL_COMPONENTS . "]";
            }
        //    else {
        //        $field_components = array();
        //        foreach ($fields as $f) {
        //            $field_components[] = $this->get_component_name($f["Class_ID"]]);
        //        }
        //        $description .= " (" . join(", ", $field_components) . ")";
        //    }
            $result[$field_id]["description"] .= $description;
        }

        return array_values($result);
    }

    protected function get_component_name($component_id) {
        return $this->component_data[$component_id]["Class_Name"];
    }

    protected function get_component_fields() {
        // FIELDS BY COMPONENT TYPE
        $result = array();
        $prev_component_id = null;
        $group = null;
        foreach ($this->fields as $field) {
            $cid = $field["Class_ID"];
            if ($cid != $prev_component_id) {
                $group = "$cid. " . $this->get_component_name($cid);
            }
            $result[$group][] = nc_netshop_condition_admin_helpers::export_field($field);
        }
        return $result;
    }

    public function export() {
        $result = array(
            NETCAT_MODULE_NETSHOP_CONDITION_COMMON_FIELDS => $this->get_grouped_fields(),
        );

        $result += $this->get_component_fields();

        return $result;
    }

}
