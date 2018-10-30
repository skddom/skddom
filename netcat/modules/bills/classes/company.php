<?php

/**
 * Company class
 *
 * Class nc_bills_company
 */
class nc_bills_company extends nc_record {
    /**
     * @var string
     */
    protected $primary_key = "id";

    /**
     * @var array
     */
    protected $properties = array(
        "id" => null,
        "owner" => 0,
        "opf" => '',
        "name" => '',
        "address" => '',
        "phone" => '',
        "inn" => '',
        "kpp" => '',
        "bank_name" => '',
        "bank_account" => '',
        "bank_corr_account" => '',
        "bank_inn" => '',
        "bank_bik" => '',
    );

    /**
     * @var string
     */
    protected $table_name = "Bills_Company";

    /**
     * @var array
     */
    protected $mapping = array(
        "id" => "Company_ID",
        "owner" => "Owner",
        "opf" => "OPF",
        "name" => "Name",
        "address" => "Address",
        "phone" => "Phone",
        "inn" => "INN",
        "kpp" => "KPP",
        "bank_name" => "Bank_Name",
        "bank_account" => "Bank_Account",
        "bank_corr_account" => "Bank_Corr_Account",
        "bank_inn" => "Bank_INN",
        "bank_bik" => "Bank_BIK",
    );

}