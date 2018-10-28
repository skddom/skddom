<?php

class nc_bills {

    /**
     * @var string
     */
    protected $last_error = '';

    /**
     * @return nc_bills
     */
    public static function get_instance() {
        static $instance = null;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Используйте nc_bills::get_instance()
     */
    protected function __construct() {
    }

    /**
     * Sets VAT value
     *
     * @param $vat
     */
    public function set_vat($vat) {
        $vat = (int)$vat;
        if ($vat < 0 || $vat > 100) {
            $vat = 0;
        }

        nc_core()->set_settings('Vat', $vat, 'bills');
    }

    /**
     * Returns VAT value
     *
     * @return int
     */
    public function get_vat() {
        return nc_core()->get_settings('Vat', 'bills');
    }

    /**
     * Sets key value
     *
     * @param $key
     */
    public function set_key($key) {
        nc_core()->set_settings('LinkKey', $key, 'bills');
    }

    /**
     * Returns key value
     *
     * @return string
     */
    public function get_key() {
        return nc_core()->get_settings('LinkKey', 'bills');
    }

    /**
     * Saves logo file
     *
     * @param $file
     * @param $original_name
     */
    public function set_logo($file, $original_name) {
        $this->save_file($file, $original_name, 'Logo');
    }

    /**
     * Saves director sign file
     *
     * @param $file
     * @param $original_name
     */
    public function set_director_sign($file, $original_name) {
        $this->save_file($file, $original_name, 'DirectorSign');
    }

    /**
     * Saves accountant sign file
     *
     * @param $file
     * @param $original_name
     */
    public function set_accountant_sign($file, $original_name) {
        $this->save_file($file, $original_name, 'AccountantSign');
    }

    /**
     * Saves stamp file
     *
     * @param $file
     * @param $original_name
     */
    public function set_stamp($file, $original_name) {
        $this->save_file($file, $original_name, 'Stamp');
    }

    /**
     * Saves file (main function)
     *
     * @param $file
     * @param $original_name
     * @param $name
     */
    protected function save_file($file, $original_name, $name) {
        $nc_core = nc_core();
        $this->delete_file($name);
        if (!file_exists($nc_core->FILES_FOLDER . 'bills_module')) {
            mkdir($nc_core->FILES_FOLDER . 'bills_module');
        }

        $destination = $original_name;
        $nc_core->set_settings('File_' . $name, $destination, 'bills');

        move_uploaded_file($file, $nc_core->FILES_FOLDER . 'bills_module/' . $destination);
    }

    /**
     * Returns logo path
     *
     * @param bool $system_folder
     * @return string
     */
    public function get_logo($system_folder = false) {
        return $this->get_file('Logo', $system_folder);
    }

    /**
     * Returns director sign path
     *
     * @param bool $system_folder
     * @return string
     */
    public function get_director_sign($system_folder = false) {
        return $this->get_file('DirectorSign', $system_folder);
    }

    /**
     * Returns accountant sign path
     *
     * @param bool $system_folder
     * @return string
     */
    public function get_accountant_sign($system_folder = false) {
        return $this->get_file('AccountantSign', $system_folder);
    }

    /**
     * Returns stamp path
     *
     * @param bool $system_folder
     * @return string
     */
    public function get_stamp($system_folder = false) {
        return $this->get_file('Stamp', $system_folder);
    }

    /**
     * Returns file path (main)
     *
     * @param $name
     * @param $system_folder
     * @return string
     */
    protected function get_file($name, $system_folder) {
        $nc_core = nc_core();
        $destination = $nc_core->get_settings('File_' . $name, 'bills');
        return $destination && file_exists($nc_core->FILES_FOLDER . 'bills_module/' . $destination) ?
            ($system_folder ? $nc_core->FILES_FOLDER : $nc_core->HTTP_FILES_PATH) . 'bills_module/' . $destination : '';
    }

    /**
     * Removes logo
     */
    public function delete_logo() {
        $this->delete_file('Logo');
    }

    /**
     * Removes director sign
     */
    public function delete_director_sign() {
        $this->delete_file('DirectorSign');
    }

    /**
     * Removes accountant sign
     */
    public function delete_accountant_sign() {
        $this->delete_file('AccountantSign');
    }

    /**
     * Removes stamp
     */
    public function delete_stamp() {
        $this->delete_file('Stamp');
    }

    /**
     * Removes file (main)
     *
     * @param $name
     */
    protected function delete_file($name) {
        $nc_core = nc_core();
        $destination = $nc_core->get_settings('File_' . $name, 'bills');
        @unlink($nc_core->FILES_FOLDER . 'bills_module/' . $destination);
    }

    /**
     * Returns total bills count
     *
     * @return int
     */
    public function get_bills_count_total() {
        $sql = "SELECT COUNT(*) FROM `Bills_Bill`";

        return (int)nc_core('db')->get_var($sql);
    }

    /**
     * Returns paid billd count
     *
     * @return int
     */
    public function get_bills_count_paid() {
        $sql = "SELECT COUNT(*) FROM `Bills_Bill` WHERE `Paid` = 1";

        return (int)nc_core('db')->get_var($sql);
    }

    /**
     * Returns not paid bills count
     *
     * @return int
     */
    public function get_bills_count_not_paid() {
        $sql = "SELECT COUNT(*) FROM `Bills_Bill` WHERE `Paid` = 0";

        return (int)nc_core('db')->get_var($sql);
    }

    /**
     * Calls payment handler to
     * change invoice status
     * and trigger events
     *
     * @param $bill_id
     * @param $paid
     */
    public function change_payment_status($bill_id, $paid) {
        if (nc_module_check_by_keyword('payment', false) !== false) {
            nc_core::get_object()->modules->load_env();
            nc_payment::change_invoice_status_by_bill_id($bill_id, $paid);
        }
    }

    /**
     * Creates new customer.
     * If replace argument is true,
     * method tries to find out exists
     * customer by INN.
     *
     * @param $data
     * @param bool $replace
     * @return int
     */
    public function create_juridical_customer($data, $replace = false) {
        $this->last_error = '';
        $customer = new nc_bills_company();
        if ($replace) {
            $inn = $data['inn'];
            try {
                $customer->load_where('inn', $inn);
            } catch (nc_record_exception $e) {

            }
        }

        $customer->set_values($data);
        $customer->set('owner', 0);
        $customer->save();

        return $customer->get_id();
    }

    /**
     * Creates new juridical bill
     *
     * @param $data
     * @return int
     */
    public function create_juridical_bill($data) {
        $data['type'] = 'juridical';
        return $this->create_bill($data);
    }

    /**
     * Creates new physical bill
     *
     * @param $data
     * @return int
     */
    public function create_physical_bill($data) {
        $data['type'] = 'physical';
        return $this->create_bill($data);
    }

    /**
     * Creates new bill
     *
     * @param $data
     * @return int
     */
    public function create_bill($data) {
        $this->last_error = '';
        $bill = new nc_bills_bill();

        if (!isset($data['number']) || !$data['number']) {
            $sql = "SELECT MAX(`Number`) FROM `Bills_Bill`";
            $max_number = (int)nc_core('db')->get_var($sql);
            $data['number'] = $max_number + 1;
        }

        if (!isset($data['date']) || !$data['date']) {
            $data['date'] = date('d.m.Y');
        }

        $id = 0;
        $bill->set_values_from_form($data);
        if ($bill->validate()) {
            $bill->save();
            $id = $bill->get_id();
        }

        $this->last_error = $bill->get_last_error();

        return $id;
    }

    /**
     * Creates payment invoice
     *
     * @param $bill_id
     * @param int $payment_system_id
     * @return nc_payment_invoice|null
     */
    public function create_invoice($bill_id, $payment_system_id = 0) {
        $invoice = null;
        if (nc_module_check_by_keyword('payment', false) !== false) {
            require_once nc_module_folder('payment') . "/function.inc.php";

            $bill = new nc_bills_bill();
            try {
                $bill->load($bill_id);
            } catch (nc_record_exception $e) {
                return null;
            }

            if (!$payment_system_id) {
                $payment_system_id = $bill->get('type') == 'juridical' ?
                    nc_payment::get_juridical_bill_payment_system_id() :
                    nc_payment::get_physical_bill_payment_system_id();
            }

            $invoice = new nc_payment_invoice(array(
                "payment_system_id" => $payment_system_id,
                "amount" => $bill->get_sum(),
                "description" => "",
                "currency" => "RUR",
                "order_source" => 'bills',
                "order_id" => $bill_id,
            ));
            $invoice->save();
        }

        return $invoice;
    }

    /**
     * Returns last error
     *
     * @return string
     */
    public function get_last_error() {
        return $this->last_error;
    }

}