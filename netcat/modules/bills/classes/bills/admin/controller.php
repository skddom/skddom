<?php


class nc_bills_bills_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('bills', NETCAT_MODULE_BILLS_BILLS);
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $this->ui_config->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_BILLS_ADD_JURIDICAL,
            "location" => "#module.bills.bills.add(juridical)",
        );

        $this->ui_config->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_BILLS_ADD_PHYSICAL,
            "location" => "#module.bills.bills.add(physical)",
        );

        $query = "SELECT * FROM `%t%` ORDER BY `Bill_ID`";

        try {
            $bills = nc_record_collection::load('nc_bills_bill', $query);
        } catch (nc_record_exception $e) {
            $bills = array();
        }

        return $this->view('index', array(
            'bills' => $bills,
            'status' => nc_core('input')->fetch_get('status'),
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_edit(nc_bills_bill $bill = null) {
        if (!$bill) {
            $bill = new nc_bills_bill();
        }

        $id = (int)nc_core('input')->fetch_get('id');

        $this->ui_config->actionButtons[] = array(
            "id" => "back",
            "align" => "left",
            "caption" => NETCAT_MODULE_BILLS_BACK,
            "action" => "history.go(-1)",
        );

        if ($id) {
            try {
                $bill->load($id);
            } catch (nc_record_exception $e) {

            }
        }

        $bill_type = nc_core('input')->fetch_get('type');
        if ($bill->get('type')) {
            $bill_type = $bill->get('type');
        }

        $type = $id || $bill->get_id() ? 'edit' : 'add';

        $this->ui_config->locationHash .= '.' . $type;
        if ($type == 'edit') {
            $this->ui_config->locationHash .= "({$bill->get_id()})";
        } else {
            $this->ui_config->locationHash .= '(' . $bill_type . ')';
        }

        if ($type == 'add' || $bill->get_id()) {
            $this->ui_config->actionButtons[] = array(
                "id" => "submit_form",
                "caption" => NETCAT_MODULE_BILLS_SAVE,
                "action" => "mainView.submitIframeForm()"
            );
        }

        $query = "SELECT * FROM `%t%` WHERE `Owner` = 0 ORDER BY `Company_ID`";

        try {
            $customers = nc_record_collection::load('nc_bills_company', $query);
        } catch (nc_record_exception $e) {
            $customers = array();
        }

        return $this->view($bill_type == 'physical' ? 'edit_physical' : 'edit_juridical', array(
            'customers' => $customers,
            'type' => $type,
            'bill' => $bill,
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_save() {
        $data = nc_core()->input->fetch_post();

        $bill = new nc_bills_bill();
        $bill->set_values_from_form($data);

        if (!$bill->validate()) {
            return $this->action_edit($bill);
        }

        $id = $data['id'];
        $last_paid_status = null;
        if ($id) {
            $bill_last = new nc_bills_bill();
            try {
                $bill_last->load($id);
                $last_paid_status = $bill_last->get('paid');
            } catch (nc_record_exception $e) {

            }
        }

        $bill->save();

        if ($id && $last_paid_status != $bill->get('paid')) {
            nc_bills::get_instance()->change_payment_status($id, $bill->get('paid'));
        }

        $redirect = nc_module_path('bills') . 'admin/?controller=bills&status=ok';
        header("Location: " . $redirect);

        return null;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_save_status() {
        $this->use_layout = false;

        $id = (int)nc_core()->input->fetch_get('id');
        $status = (int)nc_core()->input->fetch_get('status');

        $bill = new nc_bills_bill();
        try {
            $bill->load($id);
            $last_status = $bill->get('paid');
            $bill->set('paid', $status);
            $bill->save();
            if ($last_status != $status) {
                nc_bills::get_instance()->change_payment_status($id, $status);
            }
        } catch (nc_record_exception $e) {

        }

        return null;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_remove() {
        $id = (int)nc_core()->input->fetch_get('id');

        $bill = new nc_bills_bill();
        try {
            $bill->load($id);
            $bill->delete();
        } catch (nc_record_exception $e) {

        }


        $redirect = nc_module_path('bills') . 'admin/?controller=bills';
        header("Location: " . $redirect);

        return null;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_print() {
        $this->use_layout = false;
        $hash = nc_core('input')->fetch_get('hash');
        $signed = (int)nc_core('input')->fetch_get('signed');

        $bill = new nc_bills_bill();
        try {
            $bill->load_by_hash($hash);
        } catch (nc_record_exception $e) {
            return null;
        }

        $pdf = $this->make_pdf($bill, $signed);

        if ($pdf) {
            $pdf->stream("bill_" . ($signed ? 'signed_' : '') . "{$hash}.pdf");
        }
        exit();
    }

    /**
     * @return nc_ui_view
     */
    public function action_batch() {
        set_time_limit(0);
        $nc_core = nc_core();
        $input = $nc_core->input;

        $bill_ids = $input->fetch_post('bill_id');
        $batch_mode = $input->fetch_post('batch');

        foreach($bill_ids as $index => $value) {
            $bill_ids[$index] = (int)$value;
        }

        if (count($bill_ids) > 0) {
            $sql = "SELECT * FROM %t% WHERE `Bill_ID` IN (" . implode(',', $bill_ids) . ")";
            $bills = nc_record_collection::load('nc_bills_bill', $sql);

            $tmp_folder = $nc_core->TMP_FOLDER . 'bills_batch_' . md5(uniqid('', true));
            mkdir($tmp_folder, 0777);

            foreach($bills as $bill) {
                $pdf = $this->make_pdf($bill, $batch_mode == 2);
                file_put_contents($tmp_folder . '/bill_' . $bill->get_id() . '.pdf', $pdf->output());
            }

            $zip = new ZipArchive();
            $zip->open($tmp_folder . '/bills.zip', ZIPARCHIVE::CREATE);
            if (file_exists($tmp_folder)) {
                $files = (array)scandir($tmp_folder);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $zip->addFile($tmp_folder . '/' . $file, $file);
                    }
                }

            }
            $zip->close();

            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=bills.zip");
            echo file_get_contents($tmp_folder . '/bills.zip');
            nc_delete_dir($tmp_folder);
        }

        exit();
    }

    /**
     * Returns rendered PDF object
     *
     * @param nc_bills_bill $bill
     * @param $signed
     * @return DOMPDF
     */
    protected function make_pdf(nc_bills_bill $bill, $signed) {
        $customer = new nc_bills_company();
        $company = new nc_bills_company();
        try {
            if ($bill->get('type') == 'juridical') {
                $customer->load($bill->get('customer_id'));
            }
            $company->load_where('owner', 1);
        } catch (nc_record_exception $e) {
            return null;
        }

        $view = $this->view($bill->get('type') == 'physical' ? 'print_physical' : 'print_juridical', array(
            'logo' => nc_bills::get_instance()->get_logo(true),
            'director_sign' => $signed ? nc_bills::get_instance()->get_director_sign(true) : '',
            'accountant_sign' => $signed ? nc_bills::get_instance()->get_accountant_sign(true) : '',
            'stamp' => $signed ? nc_bills::get_instance()->get_stamp(true) : '',
            'bill' => $bill,
            'customer' => $customer,
            'company' => $company,
        ));

        $result = $view->make();

        require_once(nc_core()->ROOT_FOLDER . "require/lib/dompdf/dompdf_config.inc.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($result);
        $dompdf->render();

        return $dompdf;
    }

}