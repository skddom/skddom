<?php


class nc_bills_acts_admin_controller extends nc_bills_admin_controller {

    protected function init() {
        $this->ui_config = new nc_bills_admin_ui('acts', NETCAT_MODULE_BILLS_ACTS);
    }

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $this->ui_config->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_BILLS_ADD,
            "location" => "#module.bills.acts.add",
        );

        $query = "SELECT * FROM `%t%` ORDER BY `Act_ID`";

        try {
            $acts = nc_record_collection::load('nc_bills_act', $query);
        } catch (nc_record_exception $e) {
            $acts = array();
        }

        return $this->view('index', array(
            'acts' => $acts,
            'status' => nc_core('input')->fetch_get('status'),
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_edit(nc_bills_act $act = null) {
        if (!$act) {
            $act = new nc_bills_act();
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
                $act->load($id);
            } catch (nc_record_exception $e) {

            }
        }

        $type = $id || $act->get_id() ? 'edit' : 'add';

        $this->ui_config->locationHash .= '.' . $type;
        if ($type == 'edit') {
            $this->ui_config->locationHash .= "({$act->get_id()})";
        }

        if ($type == 'add' || $act->get_id()) {
            $this->ui_config->actionButtons[] = array(
                "id" => "submit_form",
                "caption" => NETCAT_MODULE_BILLS_SAVE,
                "action" => "mainView.submitIframeForm()"
            );
        }

        $query = "SELECT * FROM `%t%` ORDER BY `Bill_ID`";

        try {
            $bills = nc_record_collection::load('nc_bills_bill', $query);
        } catch (nc_record_exception $e) {
            $bills = array();
        }

        return $this->view('edit', array(
            'bills' => $bills,
            'type' => $type,
            'act' => $act,
        ));
    }

    /**
     * @return nc_ui_view
     */
    protected function action_save() {
        $data = nc_core()->input->fetch_post();

        $act = new nc_bills_act();
        $act->set_values_from_form($data);

        if (!$act->validate()) {
            return $this->action_edit($act);
        }

        $act->save();

        $redirect = nc_module_path('bills') . 'admin/?controller=acts&status=ok';
        header("Location: " . $redirect);

        return null;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_remove() {
        $id = (int)nc_core()->input->fetch_get('id');

        $act = new nc_bills_act();
        try {
            $act->load($id);
            $act->delete();
        } catch (nc_record_exception $e) {

        }


        $redirect = nc_module_path('bills') . 'admin/?controller=acts';
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

        $act = new nc_bills_act();
        try {
            $act->load_by_hash($hash);
        } catch (nc_record_exception $e) {
            return null;
        }

        $pdf = $this->make_pdf($act, $signed);

        if ($pdf) {
            $pdf->stream("act_" . ($signed ? 'signed_' : '') . "{$hash}.pdf");
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

        $act_ids = $input->fetch_post('act_id');
        $batch_mode = $input->fetch_post('batch');

        foreach($act_ids as $index => $value) {
            $act_ids[$index] = (int)$value;
        }

        if (count($act_ids) > 0) {
            $sql = "SELECT * FROM %t% WHERE `Act_ID` IN (" . implode(',', $act_ids) . ")";
            $acts = nc_record_collection::load('nc_bills_act', $sql);

            $tmp_folder = $nc_core->TMP_FOLDER . 'bills_batch_' . md5(uniqid('', true));
            mkdir($tmp_folder, 0777);

            foreach($acts as $act) {
                $pdf = $this->make_pdf($act, $batch_mode == 2);
                file_put_contents($tmp_folder . '/act_' . $act->get_id() . '.pdf', $pdf->output());
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
     * @return nc_ui_view
     */
    public function action_get_bill() {
        $id = (int)nc_core('input')->fetch_get('id');
        $this->use_layout = false;

        $bill = new nc_bills_bill();
        try {
            $bill->load($id);
        } catch (nc_record_exception $e) {

        }

        return $this->view('bill', array(
            'bill' => $bill,
        ));
    }

    /**
     * Returns rendered PDF object
     *
     * @param nc_bills_act $act
     * @param $signed
     * @return DOMPDF
     */
    protected function make_pdf(nc_bills_act $act, $signed) {
        $bill = new nc_bills_bill();
        $customer = new nc_bills_company();
        $company = new nc_bills_company();
        try {
            $bill->load($act->get('bill_id'));
            if ($bill->get('type') == 'juridical') {
                $customer->load($bill->get('customer_id'));
            }
            $company->load_where('owner', 1);
        } catch (nc_record_exception $e) {
            return null;
        }

        $view = $this->view('print', array(
            'logo' => nc_bills::get_instance()->get_logo(true),
            'director_sign' => $signed ? nc_bills::get_instance()->get_director_sign(true) : '',
            'accountant_sign' => $signed ? nc_bills::get_instance()->get_accountant_sign(true) : '',
            'stamp' => $signed ? nc_bills::get_instance()->get_stamp(true) : '',
            'act' => $act,
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