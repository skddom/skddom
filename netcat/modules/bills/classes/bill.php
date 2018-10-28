<?php

/**
 * Bill class
 *
 * Class nc_bills_bill
 */
class nc_bills_bill extends nc_record {
    /**
     * @var string
     */
    protected $primary_key = "id";

    /**
     * @var array
     */
    protected $properties = array(
        "id" => null,
        "type" => '',
        "number" => '',
        "date" => '',
        "customer_id" => 0,
        "customer_name" => '',
        "customer_address" => '',
        "positions" => '',
        "paid" => 0,
        "status_change_date" => '',
    );

    /**
     * @var string
     */
    protected $table_name = "Bills_Bill";

    /**
     * @var array
     */
    protected $mapping = array(
        "id" => "Bill_ID",
        "type" => "Type",
        "number" => "Number",
        "date" => "Date",
        "customer_id" => "Customer_ID",
        "customer_name" => "Customer_Name",
        "customer_address" => "Customer_Address",
        "positions" => "Positions",
        "paid" => "Paid",
        "status_change_date" => "Status_Change_Date",
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
     * Returns total bill sum
     *
     * @return int
     */
    public function get_sum() {
        $total_sum = 0;
        foreach ($this->get_positions_array() as $position) {
            $total_sum += $position['total'];
        }

        return $total_sum;
    }

    /**
     * Returns formatted total sum
     *
     * @return string
     */
    public function get_formatted_sum() {
        return number_format($this->get_sum(), 2, '.', ' ');
    }

    /**
     * Returns formatted sum
     * without VAT
     *
     * @return string
     */
    public function get_formatted_sum_without_vat() {
        $sum = $this->get_sum();
        $vat = nc_bills::get_instance()->get_vat();

        $sum = $sum / 100 * (100 - $vat);

        return number_format($sum, 2, '.', ' ');
    }

    /**
     * Returns formatted
     * VAT value
     *
     * @return string
     */
    public function get_formatted_vat_sum() {
        $sum = $this->get_sum();
        $vat = nc_bills::get_instance()->get_vat();

        $sum = $sum / 100 * ($vat);

        return number_format($sum, 2, '.', ' ');
    }

    /**
     * Returns word-formatted
     * sum value
     *
     * @return string
     */
    public function get_sum_words() {
        return nc_bills_num2str($this->get_sum());
    }

    /**
     * Returns positions in bill
     *
     * @return array
     */
    public function get_positions_array() {
        $positions = $this->get('positions');
        $positions = (array)@json_decode($positions, true);

        foreach ($positions as $index => $position) {
            $positions[$index]['total'] = (float)$position['sum'] * (float)$position['amount'];
            $positions[$index]['formatted_sum'] = number_format((float)$position['sum'], 2, '.', ' ');
            $positions[$index]['formatted_total'] = number_format($positions[$index]['total'], 2, '.', ' ');
        }

        return $positions;
    }

    /**
     * Returns positions count
     *
     * @return int
     */
    public function get_positions_count() {
        return count($this->get_positions_array());
    }

    /**
     * Validates input data
     *
     * @return bool
     */
    public function validate() {
        $this->last_error = null;
        if (!$this->get('type')) {
            $this->last_error = 'Некорректный тип счета';
            return false;
        }

        if (!$this->get('number')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_BILL_NUMBER;
            return false;
        }

        if (!$this->get('date')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_DATE;
            return false;
        }

        if ($this->get('type') == 'juridical' && !$this->get('customer_id')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_CLIENT;
            return false;
        }

        if ($this->get('type') == 'physical' && !$this->get('customer_name')) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_CLIENT;
            return false;
        }

        $positions = $this->get_positions_array();
        if (!count($positions)) {
            $this->last_error = NETCAT_MODULE_BILLS_VALIDATE_NO_POSITION;
            return false;
        }

        return true;
    }

    /**
     * Sets user input data
     *
     * @param $data
     */
    public function set_values_from_form($data) {
        $positions = $data['positions'];

        foreach ($positions as $index => $position) {
            if (!isset($position['name']) ||
                !isset($position['unit']) ||
                !isset($position['sum']) ||
                !isset($position['amount']) ||
                !$position['name'] ||
                !$position['unit']
            ) {
                unset($positions[$index]);
            }
        }

        $data['positions'] = json_encode($positions);

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
     * Returns PDF link
     *
     * @param bool $signed
     * @return string
     */
    public function get_pdf_link($signed = false) {
        $bills = nc_bills::get_instance();
        $secret_key = $bills->get_key();
         return nc_module_path('bills') .
               'print/bill.php?hash=' . md5($this->get_id() . ':' . $secret_key) .
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

    /**
     * Returns formatted customer name
     * string
     *
     * @return string
     */
    public function get_physical_customer() {
        return implode(', ', array(
            $this->get('customer_name'),
            $this->get('customer_address'),
        ));
    }

}