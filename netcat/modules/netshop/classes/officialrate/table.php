<?php



class nc_netshop_officialrate_table extends nc_netshop_table
{
    protected $table       = 'Netshop_OfficialRate';
    protected $primary_key = 'Netshop_OfficialRate_ID';
    protected $fields      = array(
        'Netshop_OfficialRate_ID' => array(
            'field' => 'hidden',
        ),
        'Catalogue_ID' => array(
            'field' => 'hidden',
        ),
        'Priority' => array(),
        'Checked' => array(
            'default' => 1,
            'field' => 'hidden',
        ),
        'Date' => array(
            'title' => 'Дата',
            //'field' => 'datetime',
            'field' => 'string',
        ),
        'Currency' => array(
            'title' => 'Валюта',
            'field' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'ShopCurrency'
        ),
        'Rate' => array(
            'title' => 'Курс по отношению к рублю',
            'field' => 'float',
        )
    );


}