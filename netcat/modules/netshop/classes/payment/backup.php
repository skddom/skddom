<?php

/**
 *
 */
class nc_netshop_payment_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $this->export_classificator('PaymentSystem');

        $payment_methods = nc_db_table::make('Netshop_PaymentMethod')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_PaymentMethod', 'PaymentMethod_ID', $payment_methods);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        $this->dumper->import_data('Netshop_PaymentMethod', null, array('Condition' => array($this, 'update_condition_string')));
    }

    /**
     * @param $row
     * @return array|false
     */
    public function event_before_insert_netshop_paymentmethod($row) {
        if (!$this->can_insert_row_with_condition(nc_array_value($row, 'Condition'))) {
            return false;
        }

        if (nc_array_value($row, 'PaymentSystem_ID', false)) {
            return false;
        }

        return $row;
    }
}