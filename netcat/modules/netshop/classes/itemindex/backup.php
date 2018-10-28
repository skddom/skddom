<?php
/**
 *
 */
class nc_netshop_itemindex_backup extends nc_netshop_backup {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        nc_netshop::get_instance($id)->itemindex->reindex_site();
    }

}