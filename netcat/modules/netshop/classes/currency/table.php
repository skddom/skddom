<?php



class nc_netshop_currency_table extends nc_netshop_table
{
    protected $table       = 'Netshop_Currency';
    protected $primary_key = 'Netshop_Currency_ID';
    protected $fields      = array(
        'Netshop_Currency_ID' => array(
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
        'Currency_ID' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY,
            'field' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'ShopCurrency',
            'required' => 1
        ),
        'Rate' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_RATE,
            'field' => 'float',
            'required' => 0
        ),
        'NameShort' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_SHORT_NAME,
            'field' => 'string',
            'required' => 1
        ),
        'NameCases' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_FULL_NAME,
            'field' => 'string',
            'required' => 0
        ),
        'DecimalName' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_PART_NAME,
            'field' => 'string',
            'required' => 0
        ),
        'Format' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_FORMAT_RULE,
            'field' => 'string',
            'required' => 0
        ),
        'Decimals' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_POINTS,
            'field' => 'int',
            'required' => 0
        ),
        'DecPoint' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_SEPARATOR,
            'field' => 'string',
            'required' => 0
        ),
        'ThousandSep' => array(
            'title' => NETCAT_MODULE_NETSHOP_CURRENCY_THOUSANDS_SEPARATOR,
            'field' => 'string',
            'required' => 0
        ),
    );

}