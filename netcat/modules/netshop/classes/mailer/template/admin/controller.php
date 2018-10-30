<?php


class nc_netshop_mailer_template_admin_controller extends nc_netshop_admin_table_controller {

    /** @var  nc_netshop_mailer_admin_ui */
    protected $ui_config;

    /** @var string  */
    protected $data_type = 'mailer_template';

    protected function init() {
        parent::init();

        $this->bind('master_template_remove', array('id'));
        $this->bind('master_template_edit', array('id'));

        $this->bind('message_template_edit', array('recipient_role', 'order_status'));
        $this->bind('message_template_save', array('data', 'recipient_role', 'order_status'));
    }

    /**
     *
     */
    protected function before_action() {
        $this->ui_config = new nc_netshop_mailer_admin_ui($this->site_id, 'template');
    }

    /**
     *
     */
    protected function action_index() {
        return $this->action_master_template_index();
    }

    /**
     * @return nc_ui_view
     */
    protected function action_master_template_index() {
        $catalogue_id = $this->site_id;

        $templates = nc_db()->get_results(
            "SELECT `m`.`Template_ID`, `m`.`Name`, COUNT(`s`.`Template_ID`) AS 'sub_template_count'
               FROM `Netshop_MailTemplate` AS `m`
                    LEFT JOIN `Netshop_MailTemplate` AS `s`
                    ON (`m`.`Template_ID` = `s`.`Parent_Template_ID`)
              WHERE `m`.`Catalogue_ID` = $catalogue_id
                AND `m`.`Type` = 'master'
              GROUP BY `m`.`Template_ID`",
            ARRAY_A);

        if (count($templates)) {
            $view = $this->view('master_template_list');
            $view->with('templates', $templates);
        }
        else {
            $view = $this->view('empty_list');
            $view->with('message', NETCAT_MODULE_NETSHOP_MAILER_NO_MASTER_TEMPLATES);
        }

        $this->ui_config->locationHash .= "($catalogue_id)";
        $this->ui_config->add_create_button("mailer.template.add($catalogue_id)");

        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_master_template_add() {
        $view = $this->basic_table_edit_action(0, 'master_template_form', 'netshop_' . $this->site_id . '_master');
        $this->ui_config->set_location_hash("mailer.template.add({$this->site_id})");
        return $view;
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_master_template_edit($id) {
        $view = $this->basic_table_edit_action($id, 'master_template_form', 'netshop_' . $this->site_id . '_master');
        $this->ui_config->set_location_hash("mailer.template.edit($id)");
        return $view;
    }

    /**
     * @param $recipient_role
     * @param $order_status
     * @return nc_ui_view
     */
    protected function action_message_template_edit($recipient_role, $order_status) {
        if (!$order_status) { $order_status = 'order'; }

        $template_type = $template_type = $recipient_role . '_' . $order_status;

        $template = $template = nc_netshop_mailer_template::by_type($this->site_id, $template_type);
        if (!$template) { $template = new nc_netshop_mailer_template(); }

        $view = $this->view('message_template_form')
                     ->with('recipient_role', $recipient_role)
                     ->with('order_status', $order_status)
                     ->with('template_type', $template_type)
                     ->with('template', $template);

        $this->ui_config->activeTab = "{$recipient_role}_mail";
        $this->ui_config->set_location_hash("mailer.{$recipient_role}_mail({$this->site_id},{$order_status})");
        $this->ui_config->add_order_message_status_tabs($recipient_role, $order_status);
        $this->ui_config->add_submit_button();

        return $view;
    }

    /**
     * @param array $data
     * @param string $recipient_role
     * @param string $order_status
     * @return nc_ui_view
     */
    protected function action_message_template_save($data, $recipient_role, $order_status) {
        $data = (array)$data;
        $template = new nc_netshop_mailer_template($data);

        $index_params = "catalogue_id={$this->site_id}&recipient_role={$recipient_role}&order_status={$order_status}";

        try {
            $template->save();
            nc_mail_attachment_form_save('netshop_' . $data['catalogue_id'] . '_' . $data['type']);
            $this->redirect_to_index_action('message_template_edit', $index_params);
        }
        catch (nc_record_exception $e) {
            return $this->view('error_message')
                        ->with('message', NETCAT_MODULE_NETSHOP_UNABLE_TO_SAVE_RECORD);
        }
    }
}