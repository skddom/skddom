<?php

/**
 * Act class
 *
 * Class nc_bills_act
 */
class nc_bills_act extends nc_record {
    /**
     * @var string
     */
    protected $primary_key = "id";

    /**
     * @var array
     */
    protected $properties = array(
        "id" => null,
        "number" => '',
        "date" => '',
        "bill_id" => null,
    );

    /**
     * @var string
     */
    protected $table_name = "Bills_Act";

    /**
     * @var array
     */
    protected $mapping = array(
        "id" => "Act_ID",
        "number" => "Number",
        "date" => "Date",
        "bill_id" => "Bill_ID",
    );

    /**
     * @var string
     */
    protected $last_error = '';

    /**
     * Returns formatted date
     *
     * @return string
     */
    public function get_formatted_date() {
        $date = strtotime($this->get('date'));

        return $date ? date('d.m.Y', $date) : '';
    }

    /**
     * Validates input data
     *
     * @return bool
     */
    public function validate() {
        $this->last_error = null;

        if (!$this->get('number')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_ACT_NUMBER;
            return false;
        }

        if (!$this->get('date')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_DATE;
            return false;
        }

        if (!$this->get('bill_id')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_ACCOUNT;
            return false;
        }

        return true;
    }

    /**
     * Sets data from user input
     *
     * @param $data
     */
    public function set_values_from_form($data) {
        $date = null;

        $date_raw = explode('.', $data['date']);
        if (count($date_raw) == 3) {
            $date_raw = "{$date_raw[2]}-{$date_raw[1]}-{$date_raw[0]}";
            $date_raw = strtotime($date_raw);
            if ($date_raw) {
                $date = date('Y-m-d', $date_raw);
            }
        }

        $data['date'] = $date;

        $this->set_values($data);
    }

    /**
     * Returns last error
     *
     * @return string
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * Returns link to PDF file
     *
     * @param bool $signed
     * @return string
     */
    public function get_pdf_link($signed = false) {
        $bills = nc_bills::get_instance();
        $secret_key = $bills->get_key();
        return nc_module_path('bills') .
               'print/act.php?hash=' . md5($this->get_id() . ':' . $secret_key) .
               ($signed ? '&signed=1' : '');
    }

    /**
     * Loads object by secret hash
     *
     * @param $hash
     * @return bool|static
     */
    public function load_by_hash($hash) {
        $db = nc_core('db');
        $bills = nc_bills::get_instance();
        $hash = $db->escape($hash);
        $secret_key = $db->escape($bills->get_key());

        $id_column = $this->mapping[$this->primary_key];

        $query = "SELECT " . $this->get_all_column_names() .
            " FROM `$this->table_name`" .
            " WHERE MD5(CONCAT(`{$id_column}`, ':{$secret_key}')) = '{$hash}'" .
            " LIMIT 1";

        return $this->select_from_database($query);
    }

}