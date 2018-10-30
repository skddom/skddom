<?php

/**
 *
 */
class nc_netshop_currency_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $currency_settings = nc_db_table::make('Netshop_Currency')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_Currency', 'Netshop_Currency_ID', $currency_settings);

        $currency_rates = nc_db_table::make('Netshop_OfficialRate')->where('Catalogue_ID', $id)->get_result();
        $this->dumper->export_data('Netshop_OfficialRate', 'Netshop_OfficialRate_ID', $currency_rates);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        $this->dumper->import_data('Netshop_Currency', null, array('Currency_ID' => 'ShopCurrency_ID'));
        $this->dumper->import_data('Netshop_OfficialRate', null, array('Currency' => 'ShopCurrency_ID'));
    }

} 