<?php

/**
 * Class nc_netshop_settings
 *
 * Свойства магазина
 *
 *   URL — составляется с учетом основной настройки сайта "Использовать HTTPS"
 *
 * Настройки модуля
 *
 *   FeatureSet
 *   SecretKey              (ранее в MODULE_VARS: SECRET_KEY)
 *
 * Настройки магазина:
 *   ShopName
 *   CompanyName
 *   Address
 *   City       (идентификатор, список Region)
 *   Phone
 *   MailFrom
 *   ManagerEmail
 *   INN
 *   BankName
 *   BankAccount
 *   CorrespondentAccount
 *   KPP
 *   BIK
 *   VAT
 *
 * Настройки, связанные с полем StockUnits товара
 *   IgnoreStockUnitsValue — не учитывать StockUnits при оформлении заказа
 *   SubtractFromStockStatusID — статусы заказа, при переходе в которые уменьшается StockUnits у товаров (резервирование, отгрузка...)
 *   ReturnToStockStatusID — статусы заказа, при переходе в которые товары приплюсовываются обратно к StockUnits товаров (отмена заказа, возврат...)
 *
 * Настройки компонентов
 *   OrderComponentID        (ранее в MODULE_VARS: ORDER_TABLE)
 *   ItemFullNameDefaultTemplate — шаблон для формирования $item['FullName'] «по умолчанию» (если не задан у компонента).
 *       Задаётся в виде: "{Vendor} {Name} {VariantName}".
 *
 * Статусы заказов
 *   PrevOrdersSumStatusID   (ранее в MODULE_VARS: PREV_ORDERS_SUM_STATUS_ID)
 *   1cExportOrdersStatusID  (ранее в MODULE_VARS: 1C_EXPORT_ORDERS_STATUS)
 *   PaidOrderStatusID       статус, в который переводится заказ при успешной оплате
 *
 * Источник заказа, когда создан оператором:
 *   OperatorOrderSourceID
 *
 * Импорт/экспорт 1С
 *   1cSecretName            (ранее в MODULE_VARS: SECRET_NAME)
 *   1cSecretKey             (ранее в MODULE_VARS: SECRET_KEY)
 *
 * Настройки валют
 *   DefaultCurrencyID
 *   ExternalCurrencyID         (старое название: ExternalCurrency)
 *   CurrencyConversionPercent
 *   DaysToKeepCurrencyRates    (ранее в MODULE_VARS: RATES_DAYS_TO_KEEP)
 *
 *   Currencies       — массив: [id валюты в списке ShopCurrency] => ISO-код валюты
 *   Rates            — массив с курсами валют ([ID] => курс к default_currency)
 *   CurrencyDetails  — массив с настройками валют (NB: ключ — ID, а не код валюты, как в старых версиях)
 *      Currency_ID
 *      Rate
 *      NameShort
 *      NameCases
 *      DecimalName
 *      Format
 *      Decimals
 *      DecPoint
 *      ThousandSep
 *
 * Единицы измерения
 *   Units — массив: id единицы измерения в списке ShopUnit => название единицы измерения
 *
 * Индекс товаров
 *   ItemIndexFields — список полей товаров для индекса товаров
 *
 *
 * Использовалось в компонентах:
 *   OrderFilterForm (1|0) — показ или скрытие формы фильтра заказов в компоненте «Заказ» в режиме администрирования
 *
 */

class nc_netshop_settings implements ArrayAccess {

    /** @var array  Кэш со значениями настроек */
    protected $data = array();

    /** @var array  Значения настроек по умолчанию */
    protected $defaults = array();

    /** @var int */
    protected $catalogue_id;

    /**
     *
     */
    public function __construct(nc_netshop $netshop) {
        $this->catalogue_id = (int)$netshop->get_catalogue_id();
        $this->migrate();
    }

    //-------------------------------------------------------------------------

    /**
     * Обеспечивает «ленивое» получение настроек модуля.
     *
     * @param mixed $key,    Если передано больше одного параметра — возвращает элементы массива $key
     * @return mixed|null
     */
    public function get($key) {
        $result = null;

        if (!is_scalar($key)) { return null; }

        // (1) if the settings is in $this->data, return it
        if (array_key_exists($key, $this->data)) {
            $result = $this->data[$key];
        }
        // (2) if there is a get_<KEY>() method, get value from it (and cache it for further usage)
        else if (method_exists($this, 'get_' . $key)) {
            $getter = 'get_' . $key;
            $this->data[$key] = $result = $this->$getter();
        }
        // (3) try to get the setting from the $nc_core->get_settings()
        else {
            /** @var nc_core $nc_core */
            $nc_core = nc_core();
            $result = $nc_core->get_settings($key, 'netshop', false, $this->catalogue_id);

            // (4) if there is a default value, return it
            if ($result === null && isset($this->defaults[$key])) {
                $result = $this->defaults[$key];
            }

            $this->data[$key] = $result;
        }
        // (5) no setting was found: a silent fail (NULL will be returned)

        // Return an array element?
        $num_args = func_num_args();
        if ($num_args > 1) {
            if (is_array($result)) {
                for ($i = 1; $i < $num_args; $i++) {
                    $key = func_get_arg($i);
                    if (isset($result[$key])) { $result = $result[$key]; }
                                         else { $result = null; break; }
                }
            }
            else { // not an array!
                $result = null;
            }
        }

        return $result;
    }

    /**************************************************************************
     SETTINGS GETTERS  (%-)
     **************************************************************************/
    /**
     * Валюты
     * @return array   ID в списке ShopCurrency => ISO-код валюты
     */
    protected function get_currencies() {
        // не зависит от сайта, кэшируем
        static $currencies = array();

        if (!$currencies) {
            $currencies = (array)nc_db()->get_col(
                "SELECT `ShopCurrency_ID`, UPPER(`ShopCurrency_Name`)
                   FROM `Classificator_ShopCurrency`",
                1, 0);
        }

        return $currencies;
    }

    /**
     * Курсы валют
     * @return array   ISO-код => курс
     */
    protected function get_rates() {
        $rates = array();

        $rates_data = (array)nc_db()->get_results(
            "SELECT rates.`Currency`, rates.`Rate`
               FROM (SELECT `Currency`, MAX(`Date`) AS 'Date'
                       FROM `Netshop_OfficialRate`
                      WHERE `Catalogue_ID` = $this->catalogue_id
                      GROUP BY `Currency`) AS latest
               JOIN `Netshop_OfficialRate` AS rates
                    USING (`Currency`, `Date`)
              WHERE `Catalogue_ID` = $this->catalogue_id",
            ARRAY_N);

        $currency_settings = $this->get('CurrencyDetails');
        foreach ($rates_data as $row) {
            list ($id, $rate) = $row;
            $rates[$id] = $rate;
        }
        // Внутренние курсы имеют приоритет над официальными курсами
        foreach ($currency_settings as $id => $data) {
            if ($data['Rate']) $rates[$data['Currency_ID']] = $data['Rate'];
        }

        return $rates;
    }

    /**
     * Настройки валют
     * Method name: sic! do not change it
     */
    protected function get_CurrencyDetails() {
        $currency_table = new nc_netshop_currency_table();
        $currency_details = $currency_table->for_site($this->catalogue_id)->checked()
                            ->index_by('Currency_ID')->as_array()
                            ->get_result();

        return $currency_details;
    }

    /**
     * Валюта по умолчанию
     * Method name: sic! do not change it
     */
    protected function get_DefaultCurrencyID() {
        $value = nc_core::get_object()->get_settings('DefaultCurrencyID', 'netshop', false, $this->catalogue_id);
        if ($value) {
            return $value;
        }
        else {
            // Подстраховка: если валюта по умолчанию по недоразумению не задана
            // в настройках сайта, считать таковой первую имеющуюся в настройках валюту
            $currency_details = (array)$this->get('CurrencyDetails');
            $first_currency = array_shift($currency_details);
            return isset($first_currency['Currency_ID']) ? $first_currency['Currency_ID'] : null;
        }
    }

    /**
     * Единицы измерения (из списка)
     */
    protected function get_units() {
        // не зависит от сайта, кэшируем
        static $units = array();

        if (!$units) {
            $result = (array) nc_db()->get_results(
                "SELECT `ShopUnits_ID`, `ShopUnits_Name`
                   FROM `Classificator_ShopUnits`",
                ARRAY_N);

            foreach ($result as $row) { $units[$row[0]] = $row[1]; }
        }

        return $units;
    }

    /**
     * URL магазина
     * @return string
     */
    protected function get_URL() {
        return nc_Core::get_object()->catalogue->get_url_by_id($this->catalogue_id);
    }

    /**************************************************************************
     ArrayAccess interface methods
     **************************************************************************/

    public function offsetExists($offset) {
        return true;
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        throw new Exception("nc_netshop_settings object is read-only.");
    }

    public function offsetUnset($offset) {
        throw new Exception("nc_netshop_settings object is read-only.");
    }

    /**************************************************************************
     MIGRATE
     **************************************************************************/

    protected function migrate() {
        /** @var nc_core $nc_core */
        $nc_core = nc_core();
        if (!$nc_core->get_settings('migration53_complete', 'netshop')) {
            nc_netshop_settings_converter::migrate53();
            $nc_core->set_settings('migration53_complete', '1', 'netshop'); // all catalogue_ids
        }
    }

}