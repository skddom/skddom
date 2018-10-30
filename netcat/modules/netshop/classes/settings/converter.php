<?php


class nc_netshop_settings_converter {

    static protected $shop_settings_map = array(
        'ShopName' => 'ShopName',
        'CompanyName' => 'CompanyName',
        'Address' => 'Address',
        'City' => 'City',
        'Phone' => 'Phone',
        'MailFrom' => 'MailFrom',
        'ManagerEmail' => 'ManagerEmail',
        'INN' => 'INN',
        'BankName' => 'BankName',
        'BankAccount' => 'BankAccount',
        'CorrespondentAccount' => 'CorrespondentAccount',
        'KPP' => 'KPP',
        'BIK' => 'BIK',
        'VAT' => 'VAT',
        'DefaultCurrencyID' => 'DefaultCurrencyID',
        'ExternalCurrency' => 'ExternalCurrencyID',
        'CurrencyConversionPercent' => 'CurrencyConversionPercent',
    );

    static protected $module_vars_map = array(
        'order_table' => 'OrderComponentID',
        'prev_orders_sum_status_id' => 'PrevOrdersSumStatusID',
        '1c_export_orders_status' => '1cExportOrdersStatusID',
        'rates_days_to_keep' => 'DaysToKeepCurrencyRates',
        'secret_name' => '1cSecretName',
        'secret_key' => array('SecretKey', '1cSecretKey'),
    );


    /**
     *
     */
    static public function migrate53() {
        /** @var nc_db $db */
        $db = nc_core('db');
        /** @var nc_core $nc_core */
        $nc_core = nc_core();
        $old_netshop = nc_mod_netshop::get_instance();

        // ensure that module vars are loaded
        nc_modules()->get_module_vars();

        // (1) shop settings: move values to the 'Settings'
        $old_settings_table = "Message" . $old_netshop->shop_table;
        $old_settings = $db->get_results(
            "SELECT m.*, s.`Catalogue_ID`
               FROM `$old_settings_table` AS m
                    JOIN `Subdivision` AS s USING (`Subdivision_ID`)",
            ARRAY_A);

        foreach ($old_settings as $row) {
            foreach (self::$shop_settings_map as $old => $new) {
                $nc_core->set_settings($new, $row[$old], 'netshop', $row['Catalogue_ID']);
            }
        }

        // Some extra settings, which were in the MODULE_VARS
        foreach (self::$module_vars_map as $old => $new) {
            foreach ((array)$new as $new) {
                $nc_core->set_settings($new, $old_netshop->$old, 'netshop', 0);
            }
        }

        // (2) other settings: move values to the new tables

        $message_tables = array(
            // COPY: Message242 TO: Netshop_PriceRule
            'price_rules_table' => 'nc_netshop_pricerule_table',
            // COPY: Message222 TO: Netshop_OfficialRate
            'official_rates_table' => 'nc_netshop_officialrate_table',
            // COPY: Message223 TO: Netshop_Currency
            'currency_rates_table' => 'nc_netshop_currency_table',
        );

        foreach ($message_tables as $source_table_role => $target_table_class) {
            /** @var nc_db_table $target_table */
            $target_table = new $target_table_class;
            $target_table_name = $target_table->get_table();
            $target_primary_key = $target_table->get_primary_key();

            $source_table_name = "Message" . $old_netshop->$source_table_role;
            $source_field_names = str_replace($target_primary_key,
                "Message_ID",
                $target_table->get_field_names());
            $source_field_names = "m.`" . join("`, m.`", $source_field_names) . "`";

            // Catalogue_ID comes from another table:
            $source_field_names = str_replace("m.`Catalogue_ID`", "s.`Catalogue_ID`", $source_field_names);

            // special case :(
            if ($source_table_role == 'currency_rates_table') {
                $source_field_names = str_replace("`Currency_ID`", "`Currency` AS `Currency_ID`", $source_field_names);
            }

            $db->query("CREATE TABLE `{$target_table_name}`
                        SELECT $source_field_names
                          FROM `{$source_table_name}` AS m
                               JOIN `Subdivision` AS s USING(`Subdivision_ID`)");

            $db->query("ALTER TABLE `{$target_table_name}` ADD PRIMARY KEY (`Message_ID`) ");
            $db->query("ALTER TABLE `{$target_table_name}` CHANGE `Message_ID` `{$target_primary_key}` INT(11) NOT NULL AUTO_INCREMENT;");
        }

    }

}