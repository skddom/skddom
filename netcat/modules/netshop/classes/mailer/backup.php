<?php

/**
 *
 */
class nc_netshop_mailer_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $mail_templates = nc_db_table::make('Netshop_MailTemplate')
                            ->where('Catalogue_ID', $id)
                            ->order_by('Parent_Template_ID')->get_result();
        $this->dumper->export_data('Netshop_MailTemplate', 'Template_ID', $mail_templates);

        $mail_rules = nc_db_table::make('Netshop_MailRule')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_MailRule', 'Rule_ID', $mail_rules);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        $this->dumper->import_data('Netshop_MailTemplate', null, array('Parent_Template_ID' => 'Netshop_MailTemplate.Template_ID'));
        $this->dumper->import_data('Netshop_MailRule', null, array('Condition' => array($this, 'update_condition_string')));
    }


    /**
     * @param $row
     * @return array|false
     */
    public function event_before_insert_netshop_mailrule($row) {
        if (!$this->can_insert_row_with_condition(nc_array_value($row, 'Condition'))) {
            return false;
        }
        return $row;
    }

}