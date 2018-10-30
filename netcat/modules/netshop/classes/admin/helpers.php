<?php

class nc_netshop_admin_helpers {

    /**
     * @param string $parameters
     */
    static public function redirect_to_index_action($parameters = "") {
        ob_end_clean();
        header('Location: ?action=index' . ($parameters ? "&$parameters" : ""));
        die;
    }

    /**
     * Возвращает массив с настройками магазина, доступными в шаблонах писем
     */
    public static function get_shop_fields() {
        static $fields = array(
            'URL' => array(
                'caption' => NETCAT_MODULE_NETSHOP_SHOP_URL,
            ),
            'ShopName' => array(
                'caption' => NETCAT_MODULE_NETSHOP_SHOP_NAME,
            ),
            'CompanyName' => array(
                'caption' => NETCAT_MODULE_NETSHOP_COMPANY_NAME,
            ),
            'Address' => array(
                'caption' => NETCAT_MODULE_NETSHOP_ADDRESS,
            ),
            'City' => array(
                'caption' => NETCAT_MODULE_NETSHOP_CITY,
                'classificator' => 'Region',
            ),
            'Phone' => array(
                'caption' => NETCAT_MODULE_NETSHOP_PHONE,
            ),
            'MailFrom' => array(
                'caption' => NETCAT_MODULE_NETSHOP_MAIL_FROM,
            ),
            'ManagerEmail' => array(
                'caption' => NETCAT_MODULE_NETSHOP_MANAGER_EMAIL,
            ),
            'INN' => array(
                'caption' => NETCAT_MODULE_NETSHOP_INN,
            ),
            'BankName' => array(
                'caption' => NETCAT_MODULE_NETSHOP_BANK_NAME,
            ),
            'BankAccount' => array(
                'caption' => NETCAT_MODULE_NETSHOP_BANK_ACCOUNT,
            ),
            'CorrespondentAccount' => array(
                'caption' => NETCAT_MODULE_NETSHOP_CORRESPONDENT_ACCOUNT,
            ),
            'KPP' => array(
                'caption' => NETCAT_MODULE_NETSHOP_KPP,
            ),
            'BIK' => array(
                'caption' => NETCAT_MODULE_NETSHOP_BIK,
            ),
            'VAT' => array(
                'caption' => NETCAT_MODULE_NETSHOP_VAT,
            ),
            'DefaultCurrencyID' => array(
                'caption' => NETCAT_MODULE_NETSHOP_DEFAULT_CURRENCY_ID,
                'classificator' => 'ShopCurrency',
            ),
        );
        return $fields;
    }

    static protected $item_cache = array();

    /**
     * Возвращает объект nc_netshop_item (кэширует объекты в локальном кэше)
     * @param $component_id
     * @param $object_id
     * @return nc_netshop_item
     */
    static public function get_item($component_id, $object_id) {
        $key = "$component_id:$object_id";
        if (!isset(self::$item_cache[$key])) {
            self::$item_cache[$key] = new nc_netshop_item(array(
                'Class_ID' => $component_id,
                'Message_ID' => $object_id,
            ));
        }
        return self::$item_cache[$key];
    }

}