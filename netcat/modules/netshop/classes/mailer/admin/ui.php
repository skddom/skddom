<?php

class nc_netshop_mailer_admin_ui extends nc_netshop_admin_ui {

    /**
     * @param int $catalogue_id
     * @param string $active_tab      template|customer_mail|manager_mail
     */
    public function __construct($catalogue_id, $active_tab) {
        parent::__construct('mailer', NETCAT_MODULE_NETSHOP_MAILER_ROOT);

        $this->catalogue_id = $catalogue_id;

        $this->locationHash = "module.netshop.mailer.$active_tab";

        $this->tabs = array(
            array(
                'id' => 'template',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATES,
                'location' => 'module.netshop.mailer.template',
                'group' => "admin",
            ),
            array(
                'id' => 'customer_mail',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_MAIL,
                'location' => 'module.netshop.mailer.customer_mail',
                'group' => "admin",
            ),
            array(
                'id' => 'manager_mail',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_MANAGER_MAIL,
                'location' => 'module.netshop.mailer.manager_mail',
                'group' => "admin",
            ),
            array(
                'id' => 'rule',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_RULES,
                'location' => 'module.netshop.mailer.rule',
                'group' => "admin",
            ),
        );

        $this->activeTab = $active_tab;
    }

    /**
     * @param string $recipient_role    customer|manager
     * @param string $active_tab        order|status_X
     */
    public function add_order_message_status_tabs($recipient_role, $active_tab) {
        $location_prefix = 'module.netshop.mailer.' . $recipient_role . '_mail';
        $this->toolbar = array(
            array(
                'id' => $recipient_role . '_order',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER,
                'location' => "$location_prefix({$this->catalogue_id},order)",
                'group' => "admin",
            ),
        );

        if ($recipient_role != 'manager') {
            $this->toolbar[] = array(
                'id' => $recipient_role . '_order_register',
                'caption' => NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER_REGISTER,
                'location' => "$location_prefix({$this->catalogue_id},order_register)",
                'group' => "admin",
            );
        }

        $this->toolbar[] = array(
            'id' => $recipient_role . '_change_items',
            'caption' => NETCAT_MODULE_NETSHOP_MAILER_ORDER_CHANGE_ITEMS,
            'location' => "$location_prefix({$this->catalogue_id},change_items)",
            'group' => "admin",
        );

        $db = nc_Core::get_object()->db;

        $sql = "SELECT `ShopOrderStatus_ID`, `ShopOrderStatus_Name` FROM `Classificator_ShopOrderStatus` ORDER BY `ShopOrderStatus_Priority` ASC";
        $result = (array)$db->get_results($sql, ARRAY_A);

        foreach ($result as $i => $row) {
            $this->toolbar[] = array(
                'id' => $recipient_role . '_status_' . $row['ShopOrderStatus_ID'],
                'caption' => sprintf(
                    ($i ? NETCAT_MODULE_NETSHOP_MAILER_ORDER_STATUS_SHORT : NETCAT_MODULE_NETSHOP_MAILER_ORDER_STATUS),
                    nc_ucfirst($row['ShopOrderStatus_Name'])
                ),
                'location' => "$location_prefix({$this->catalogue_id},status_$row[ShopOrderStatus_ID])",
                'group' => "admin",
            );
        }

        $this->activeToolbarButtons[] = $recipient_role . "_" . $active_tab;
    }



}