<?

abstract class nc_netshop_backup extends nc_backup_extension {

    /**
     * @param array $row
     * @param string $field
     * @return string
     */
    public function update_condition_string($row, $field) {
        $string = nc_array_value($row, $field);
        if (!$string || $this->dumper->get_import_settings('save_ids')) { return $string; }

        $condition_options = json_decode($string, true);
        if (!$condition_options) { return $string; }

        $condition = nc_netshop_condition::create($condition_options);
        $updated_condition_string = json_encode($condition->get_updated_raw_options_array_on_import($this->dumper));

        return $updated_condition_string;
    }

    /**
     * @param $condition_string
     * @return bool
     */
    protected function can_insert_row_with_condition($condition_string) {
        if (!$condition_string) { return true; }

        $condition_array = json_decode($condition_string, true);
        if (!$condition_array) { return true; }

        $condition = nc_netshop_condition::create($condition_array);
        return !($condition->has_condition_of_type('dummy'));
    }
    
}