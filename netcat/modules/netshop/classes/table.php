<?php

abstract class nc_netshop_table extends nc_db_table {

   //-------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function checked()
    {
        return $this->where('Checked', 1);
    }

    //-------------------------------------------------------------------------

    /**
     * @param $catalogue_id
     * @return $this
     */
    public function for_site($catalogue_id)
    {
        return $this->where('Catalogue_ID', (int)$catalogue_id);
    }

    //-------------------------------------------------------------------------

}