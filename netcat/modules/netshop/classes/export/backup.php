<?php

/**
 *
 */
class nc_netshop_export_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $import_sources = nc_db_table::make('Netshop_ImportSources')->where('catalogue_id', $id)->index_by('source_id')->get_result();
        $this->dumper->export_data('Netshop_ImportSources', 'source_id', $import_sources);
        $import_sources_ids = array_keys($import_sources);

        $import_map = nc_db_table::make('Netshop_ImportMap')->where_in('source_id', $import_sources_ids)->get_result();
        $this->dumper->export_data('Netshop_ImportMap', null, $import_map);

        $stores = nc_db_table::make('Netshop_Stores')->where_in('Import_Source_ID', $import_sources_ids)->get_result();
        $this->dumper->export_data('Netshop_ImportMap', 'Netshop_Store_ID', $stores);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        // Netshop_ImportSources
        $import_source_fields = array(
            'catalogue_id' => 'Catalogue_ID',
            'root_subdivision_id' => 'Subdivision_ID',
        );
        $this->dumper->import_data('Netshop_ImportSources', null, $import_source_fields);

        // Netshop_ImportMap
        $import_maps_fields = array(
            'source_id' => 'Netshop_ImportSources.source_id',
            'value' => array($this, 'map_import_map_value'),
        );
        $this->dumper->import_data('Netshop_ImportMap', null, $import_maps_fields);

        // Netshop_Stores
        $stores_fields = array(
            'Import_Source_ID' => 'Netshop_ImportSources.source_id',
        );
        $this->dumper->import_data('Netshop_Stores', null, $stores_fields);
    }

    /**
     * @param array $row
     * @param string $field
     * @return mixed
     */
    public function map_import_map_value($row, $field) {
        $value = nc_array_value($row, $field);

        if ($row['type'] == 'section') {
            $value = $this->dumper->get_dict('Subdivision_ID', $value);
        }
        elseif ($row['type'] == 'property' || $row['type'] == 'oproperty') {
            $value = $this->dumper->get_dict('Field_ID', $value);
        }

        return $value;
    }

}