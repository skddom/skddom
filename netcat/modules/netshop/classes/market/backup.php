<?php

/**
 *
 */
class nc_netshop_market_backup extends nc_netshop_backup {

    protected $tables = array(
        'Netshop_GoogleBundles',
        'Netshop_MailBundles',
        'Netshop_YandexBundles',
    );

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        foreach ($this->tables as $table_name) {
            $bundles = nc_db_table::make($table_name)->where('Catalogue_ID', $id)->index_by('Bundle_ID')->get_result();
            $bundles_ids = array_keys($bundles);
            $this->dumper->export_data($table_name, 'Bundle_ID', $bundles);

            $bundle_maps = nc_db_table::make("{$table_name}Map")->where_in('Bundle_ID', $bundles_ids)->get_result();
            $this->dumper->export_data("{$table_name}Map", null, $bundle_maps);
        }
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        foreach ($this->tables as $table_name) {
            $this->dumper->import_data($table_name);
            $this->dumper->import_data("{$table_name}Map", null, array('Bundle_ID' => "$table_name.Bundle_ID"));
        }
    }

}