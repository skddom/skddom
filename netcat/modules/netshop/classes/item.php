<?php

/**
 * Class nc_netshop_item
 *
 * Класс-обёртка для создания единообразной структуры данных с информацией о товаре
 * независимо от источника информации; в частности добавляет "Class_ID", "ItemPrice"
 * при их отсутствии.
 *
 * Кроме того, позволяет получать свойства по мере из необходимости, а не вычислять
 * заранее.
 *
 * Логика получения свойств:
 *  — если есть значение в $this->additional_data, вернуть его;
 *  — если есть значение в $this->item_data, вернуть его;
 *  — если запрашиваемое свойство является свойством объекта компонента (колонкой
 *    в таблице MessageXYZ), попробовать загрузить запись из БД и вернуть
 *    соответствующее значение;
 *  — если значение определено в self::$calculated_data, вызвать метод с
 *    соответствующим именем и вернуть результат (результат также будет помещён в
 *    $this->additional_data);
 *  — если ничего не найдено — вернёт null.
 *
 */
class nc_netshop_item implements ArrayAccess {
    /** @var array  --- please keep the $options array intact to prevent copying original the data in memory! --- */
    protected $item_data;

    /** @var array  all 'extra' data are put there */
    protected $additional_data;

    /** @var array  "Special" component fields which must be pre-processed or calculated */
    protected $uninitialized_special_fields = array();

    /** @var bool  Flag that indicates that there was an attempt to load data from the MessageXYZ table */
    protected $is_loaded = false;

    /** @var bool  Flag that indicates that there is no record in the MessageXYZ table */
    protected $loading_failed = false;

    /** @var array  data that can be calculated from item_data / additional_data */
    static protected $calculated_data = array(
        'Catalogue_ID' => 'get_catalogue_id',

        'RowID' => 'get_row_id',  // equals to "[componentID][itemID]"
        '_ItemKey' => 'get_item_key', // equals to "componentID:itemID"

        '_PriceColumn' => 'get_price_column',
        '_CurrencyColumn' => 'get_currency_column',
        '_MinimumPrice' => 'get_minimum_price', // в отличие от PriceMinimum всегда указана в валюте магазина

        'FullName' => 'get_full_name', // Производитель + Название + Название варианта

        '_Parent' => 'get_parent', // товар «верхнего уровня» (c Parent_Message_ID = 0)
        '_AllChildren' => 'get_all_children', // все дочерние товары (включённые и выключенные)
        '_Children' => 'get_enabled_children', // дочерние товары (только включённые)
        '_Variants' => 'get_variants', // все включённые варианты товара (включая основной)

        'Qty' => 'get_quantity',  // метод всегда возвращает 1
        'URL' => 'get_url',  // для совместимости со старыми версиями и удобства работы с шаблонами писем

        // all prices are in the SHOP CURRENCY  (netshop setting: DefaultCurrencyID)

        // price WITHOUT discounts
        'OriginalPrice' => 'get_original_price',
        'OriginalPriceF' => 'get_formatted_original_price',
        'OriginalPriceMin' => 'get_minimum_original_price',
        'OriginalPriceMax' => 'get_maximum_original_price',
        'OriginalPriceRange' => 'get_original_price_range',

        // price WITH discounts
        'ItemPrice' => 'get_item_price',
        'ItemPriceF' => 'get_formatted_item_price',
        'ItemPriceMin' => 'get_minimum_item_price',
        'ItemPriceMax' => 'get_maximum_item_price',
        'ItemPriceRange' => 'get_item_price_range',

        // ItemPrice * Qty  (i.e., WITH discount)
        'TotalPrice' => 'get_total_price',
        'TotalPriceF' => 'get_formatted_total_price',

        // discount per one item
        'ItemDiscount' => 'get_item_discount',
        'ItemDiscountF' => 'get_formatted_item_discount',
        'DiscountPercent' => 'get_discount_percent',

        // discount for all items (item discount * quantity)
        'TotalDiscount' => 'get_total_discount',
        'TotalDiscountF' => 'get_formatted_total_discount',

        /**
         * $item['Discounts']:
         * array('id' => 1, 'name' => '', 'description' => '', 'sum' => 123.00, 'price_minimum' => 0)
         */
        'Discounts' => 'get_discount_info',
    );

    /** @var array  fields that are present in all components */
    static protected $common_component_fields = array(
        'Sub_Class_ID' => 1,
        'Parent_Message_ID' => 1,
        'User_ID' => 1,
        'Subdivision_ID' => 1,
        'Priority' => 1,
        'Checked' => 1,
        'IP' => 1,
        'UserAgent' => 1,
        'Created' => 1,
        'LastUpdated' => 1,
        'LastUser_ID' => 1,
        'LastIP' => 1,
        'LastUserAgent' => 1,
        'Keyword' => 1,
        'ncTitle' => 1,
        'ncKeywords' => 1,
        'ncDescription' => 1,
        'ncSMO_Title' => 1,
        'ncSMO_Description' => 1,
        'ncSMO_Image' => 1
    );

    /** @var array  component-specific field names cache */
    static protected $component_fields = array();

    /** @var array  "special" fields (such as file fields and multiple select fields) settings */
    static protected $special_component_fields = array();

    static protected $uninheritable_fields = array("StockUnits" => true);

    /**
     * Чаще всего наиболее эффективный способ создания объекта nc_netshop_item —
     * через конструктор (с передачей всех предварительно загруженных данных),
     * однако иногда удобнее создать объект по ID товара.
     *
     * @param $component_id
     * @param $item_id
     * @return nc_netshop_item
     */
    static public function by_id($component_id, $item_id) {
        return new self(array('Class_ID' => $component_id, 'Message_ID' => $item_id));
    }

    /**
     * Конструктор.
     *
     * @param array $item_data        Уже загруженные данные о товаре.
     *   Для создания объекта минимально должны быть заданы Class_ID (или Sub_Class_ID) и
     *   Message_ID, все остальные данные объект загрузит при необходимости из БД.
     * @param array $additional_data  Дополнительные данные (для того, чтобы не
     *   вызывать копирование данных в памяти)
     */
    public function __construct(array $item_data, array $additional_data = array()) {
        $this->item_data = $item_data;
        $this->additional_data = $additional_data;

        // prefetch Class_ID property, because it is required in the offsetGet() / is_component_field()
        if (!$this->is_property_defined('Class_ID') || !(int)$this->get_defined_property('Class_ID')) {
            $component_id = $this->get_component_id();
            if ($component_id) {
                $this->offsetSet('Class_ID', $component_id);
            }
            else {
                // Cannot determine component ID. This item object is incorrect.
                // Mark it as loaded to prevent any senseless database queries
                // and do not try to do anything else with it.
                $this->mark_as_loaded();
                return;
            }
        }

        $this->uninitialized_special_fields = $this->get_special_component_fields();
    }

    /**
     * Пометить товар как полностью загруженный из БД
     */
    public function mark_as_loaded() {
        $this->is_loaded = true;
    }

    /**
     * Произведение значения указанного свойства и свойства Qty
     *
     * @param string $field_name
     * @return int|float
     */
    public function get_field_total($field_name) {
        return $this[$field_name] * $this['Qty'];
    }

    /**
     * Проверяет, является ли товар самостоятельным (FALSE) или дочерним (TRUE)
     *
     * @return bool
     */
    public function has_parent() {
        return $this['Parent_Message_ID'] != 0;
    }

    /**
     * @return nc_netshop
     */
    protected function get_netshop() {
        return nc_netshop::get_instance($this['Catalogue_ID']);
    }

    /**
     * Shortcut for $this->get_netshop()->format_price
     */
    protected function format_price($price, $currency = null, $no_nbsp = false, $no_currency_name = false) {
        return $this->get_netshop()->format_price($price, $currency, $no_nbsp, $no_currency_name);
    }

    // --- HANDLERS FOR 'CALCULATED' PROPERTIES --------------------------------

    /**
     * "Class_ID" from Sub_Class_ID
     * @return string|bool
     */
    protected function get_component_id() {
        if (!$this->is_property_defined('Sub_Class_ID') || !(int)$this->get_defined_property('Sub_Class_ID')) {
            trigger_error("nc_netshop_item: cannot determine Class_ID (neither Sub_Class_ID nor Class_ID is set)", E_USER_WARNING);
            return false;
        }

        try {
            return nc_core('sub_class')->get_by_id($this->get_defined_property('Sub_Class_ID'), 'Class_ID');
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * "Catalogue_ID"
     * @return string
     */
    protected function get_catalogue_id() {
        try {
            return nc_core('sub_class')->get_by_id($this['Sub_Class_ID'], 'Catalogue_ID');
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * "_PriceColumn"
     * @return mixed
     */
    protected function get_price_column() {
        return $this->get_netshop()->get_price_column($this);
    }

    /**
     * "_CurrencyColumn"
     * @return mixed
     */
    protected function get_currency_column() {
        return $this->get_netshop()->get_currency_column($this['_PriceColumn']);
    }

    protected function get_currency() {
        return $this[$this['_CurrencyColumn']];
    }

    /**
     * "_MinimumPrice"
     * @return int|float
     */
    protected function get_minimum_price() {
        $min_price = $this['PriceMinimum'];
        if (!$min_price) { return 0; }
        $netshop = $this->get_netshop();
        return $netshop->round_price($netshop->convert_currency($min_price, $this['CurrencyMinimum']));
    }

    /**
     * "FullName"
     * @return string
     */
    protected function get_full_name() {
        $template = $this->get_full_name_template();
        $full_name = '';
        foreach ($template as $template_part) {
            if ($template_part[0] == '{') {
                $full_name .= $this[substr($template_part, 1, -1)];
            }
            else {
                $full_name .= $template_part;
            }
        }
        return $full_name;
    }

    /**
     * @return array
     */
    protected function get_full_name_template() {
        static $cache = array();
        $cache_key = "$this[Catalogue_ID]:$this[Class_ID]";
        if (!isset($cache[$cache_key])) {
            $template_string = $this->get_netshop()->get_setting('ItemFullNameDefaultTemplate');
            if ($template_string) {
                $template = preg_split('/(\{\w+\})/', $template_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            }
            else {
                $template = array('{Vendor}', ' ', '{Name}', ' ', '{VariantName}');
            }
            $cache[$cache_key] = $template;
        }
        return $cache[$cache_key];
    }

    /**
     * "_Parent"
     * @return nc_netshop_item|null
     */
    protected function get_parent() {
        if ($this->has_parent()) {
            return new self(array(
                'Class_ID' => $this['Class_ID'],
                'Message_ID' => $this['Parent_Message_ID'],
            ));
        }
        return null;
    }

    /**
     * Общий код для get_all_children() и get_enabled_children()
     *
     * @param bool $only_checked
     * @return nc_netshop_item_collection
     */
    protected function select_children($only_checked) {
        $result = new nc_netshop_item_collection();
        // поддерживается только один уровень вложенности
        if (!$this->has_parent()) {
            $query = $this->get_select_query_fields() .
                     " WHERE a.`Parent_Message_ID` = " . (int)$this['Message_ID'] .
                     ($only_checked ? " AND a.`Checked` = 1" : "") .
                     " ORDER BY a.`Priority` ASC";
            $rows = (array)nc_db()->get_results($query, ARRAY_A);
            $page = nc_core::get_object()->page;
            foreach ($rows as $row) {
                $item = new nc_netshop_item($row);
                $item->mark_as_loaded();
                $item->offsetSet('_Parent', $this);
                $page->update_last_modified_if_newer($item->get_defined_property('Created'), 'content');
                $page->update_last_modified_if_newer($item->get_defined_property('LastUpdated'), 'content');
                $result->add($item);
            }
        }
        return $result;
    }

    /**
     * "_AllChildren"
     * Все дочерние товары (только для основного товара)
     *
     * @return nc_netshop_item_collection
     */
    protected function get_all_children() {
        return $this->select_children(false);
    }

    /**
     * "_Children"
     * Дочерние товары с Checked = 1 (только для основного товара)
     *
     * @return nc_netshop_item_collection
     */
    protected function get_enabled_children() {
        if ($this->is_property_defined('_AllChildren')) {
            /** @var nc_netshop_item_collection $all */
            $all = $this['_AllChildren'];
            return $all->where('Checked', 1);
        }
        else {
            return $this->select_children(true);
        }
    }

    /**
     * "_Variants"
     * Все варианты товара (родительский товар, если включён + включённые дочерние товары).
     * Для всех товаров (в т.ч. которые не являются основными).
     *
     * @return nc_netshop_item_collection
     */
    protected function get_variants() {
        $result = new nc_netshop_item_collection();

        $root = $this->has_parent() ? $this['_Parent'] : $this;
        if ($root['Checked']) { $result->add($root); }
        foreach ($root["_Children"] as $child) { $result->add($child); }

        return $result;
    }


    /**
     * "Qty"
     * Возвращает 1, если количество не указано
     *
     * @return int
     */
    protected function get_quantity() {
        return 1;
    }

    /**
     * "OriginalPrice"
     * Price WITHOUT discounts
     * @return float
     */
    protected function get_original_price() {
        $price = $this[$this['_PriceColumn']];
        // fallback to 'Price' column if there is no value
        if ($price === null) { $price = $this['Price']; }
        return $this->get_netshop()->convert_currency($price, $this->get_currency());
    }

    /**
     * "OriginalPriceF"
     * @return string
     */
    protected function get_formatted_original_price() {
        return $this->format_price($this['OriginalPrice']);
    }

    /**
     * "OriginalPriceMin"
     */
    protected function get_minimum_original_price() {
        return $this->get_variants_value('min', 'OriginalPrice');
    }

    /**
     * "OriginalPriceMax"
     */
    protected function get_maximum_original_price() {
        return $this->get_variants_value('max', 'OriginalPrice');
    }

    /**
     * "OriginalPriceRange"
     */
    protected function get_original_price_range() {
        $min = $this['OriginalPriceMin'];
        $max = $this['OriginalPriceMax'];
        if ($min == $max) { return $this->format_price($min); }  // can be not the same as OriginalPriceF if item is disabled
        return sprintf(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_PRICE_RANGE,
                       $this->format_price($min, null, false, true),
                       $this->format_price($max));
    }

    /**
     * "ItemPriceMin"
     */
    protected function get_minimum_item_price() {
        return $this->get_variants_value('min', 'ItemPrice');
    }

    /**
     * "ItemPriceMax"
     */
    protected function get_maximum_item_price() {
        return $this->get_variants_value('max', 'ItemPrice');
    }

    /**
     * "ItemPriceRange"
     */
    protected function get_item_price_range() {
        $min = $this['ItemPriceMin'];
        $max = $this['ItemPriceMax'];
        if ($min == $max) { return $this->format_price($min); } // can be not the same as ItemPriceF if item is disabled
        return sprintf(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_PRICE_RANGE,
                       $this->format_price($min, null, false, true),
                       $this->format_price($max));
    }

    /**
     * "ItemPrice"
     * Цена со скидками
     * @return float|int
     */
    protected function get_item_price() {
        return $this['OriginalPrice'] - $this['ItemDiscount'];
    }

    /**
     * "ItemPriceF"
     * @return string
     */
    protected function get_formatted_item_price() {
        return $this->format_price($this['ItemPrice']);
    }

    /**
     * "TotalPrice"
     * Стоимость всех экземпляров товара (Qty * ItemPrice) со скидками
     * @return int|float
     */
    protected function get_total_price() {
        return $this['ItemPrice'] * $this['Qty'];
    }

    /**
     * "TotalPriceF"
     * @return string
     */
    protected function get_formatted_total_price() {
        return $this->format_price($this['TotalPrice']);
    }

    /**
     * "ItemDiscount"
     * Сумма скидки на 1 шт. товара
     * @return float|int
     */
    protected function get_item_discount() {
        return $this->get_netshop()->promotion->get_item_discount_sum($this);
    }

    protected function get_formatted_item_discount() {
        return $this->format_price($this['ItemDiscount']);
    }

    /**
     * "ItemDiscountPercent"
     * Процент скидки
     * @return int|null
     */
    protected function get_discount_percent() {
        $discount = $this['ItemDiscount'];
        if ($discount) {
            return max(1, round(100 * $discount / $this['OriginalPrice']));
        }
        else {
            return null;
        }
    }


    /**
     * "TotalDiscount"
     * Сумма скидок для всех экземпляров товара (ItemDiscount * Qty)
     * @return float|int
     */
    protected function get_total_discount() {
        return $this['ItemDiscount'] * $this['Qty'];
    }

    protected function get_formatted_total_discount() {
        return $this->format_price($this['TotalDiscount']);
    }

    /**
     * "Discounts"
     * @return array   Массив массивов с элементами:
     *    id
     *    name
     *    description
     *    sum            — сумма данной скидки (в отличие от $item['ItemDiscount'], где учтена сумма всех скидок)
     *    price_minimum  — достигнута ли минимальная цена (0 или 1)
     */
    protected function get_discount_info() {
        $discounts = $this->get_netshop()->promotion->get_item_discounts_for($this);
        if (!$discounts) { return array(); }

        $context = $this->get_netshop()->get_condition_context();
        $discount_info = array();

        $price = $this['OriginalPrice'];

        /** @var nc_netshop_promotion_discount_item $discount */
        foreach ($discounts as $discount) {
            $discount_sum = $discount->get_discount_sum_for($this, $context);
            if (!$discount_sum) { continue; }
            $price -= $discount_sum;

            $discount_info[] = array(
                'type' => 'item',
                'id' => $discount->get_id(),
                'name' => $discount->get('name'),
                'description' => $discount->get('description'),
                'sum' => $discount_sum,
                'price_minimum' => intval(round($this['PriceMinimum']) == round($price)),
            );
        }

        return $discount_info;
    }

    /**
     * "RowID"
     * @return string
     */
    protected function get_row_id() {
        return "[" . $this['Class_ID'] . "][" . $this['Message_ID']  . "]";
    }

    /**
     * "_ItemKey"
     * @return string
     */
    protected function get_item_key() {
        return $this['Class_ID'] . ":" . $this['Message_ID'];
    }

    /**
     * "URL"
     * @return string
     */
    protected function get_url() {
        try {
            $path = nc_message_link($this['Message_ID'], $this['Class_ID']);
            if ($path == '') { return false; }
            $nc_core = nc_core::get_object();

            // URL без протокола ("//") может приводить к проблемам в Outlook
            return nc_get_scheme() .
                   "://" .
                   $nc_core->catalogue->get_by_id($this['Catalogue_ID'], 'Domain') .
                   $path;
        }
        catch (Exception $e) {
            return false;
        }
    }

    // --- Загрузка записи из MessageXYZ ---------------------------------------
    /**
     * Инициализирует self::$component_fields and self::$special_component_fields
     */
    protected function initialize_component_fields() {
        $component_id = $this['Class_ID'];

        if (!isset(self::$component_fields[$component_id])) {
            self::$component_fields[$component_id] = array();
            self::$special_component_fields[$component_id] = array();

            $fields = array();
            $component = new nc_component($component_id);
            foreach ($component->get_fields() as $field) {
                $name = $field['name'];
                $type = $field['type'];
                $fields[$name] = 1;

                // «Специальные» суб-поля
                $extra_subfields = array();
                $special_subfields = array();
                if ($type == NC_FIELDTYPE_SELECT) {
                    $extra_subfields = array("id", "name", "value");
                }
                elseif ($type == NC_FIELDTYPE_MULTISELECT) {
                    $extra_subfields = $special_subfields = array("id", "value");
                    self::$special_component_fields[$component_id][$name] = $field;
                }
                elseif ($type == NC_FIELDTYPE_DATETIME) {
                    $extra_subfields = array("year", "month", "day", "hours", "minutes", "seconds");
                }
                elseif ($type == NC_FIELDTYPE_FILE || $type == NC_FIELDTYPE_MULTIFILE) {
                    $extra_subfields = $special_subfields = array("name", "type", "size", "download", "url", "preview_url");
                    self::$special_component_fields[$component_id][$name] = $field;
                }
                elseif ($type == NC_FIELDTYPE_MULTIFILE) {
                    self::$special_component_fields[$component_id][$name] = $field;
                }

                foreach ($extra_subfields as $suffix) {
                    $fields[$name . "_" . $suffix] = 1;
                }

                foreach ($special_subfields as $suffix) {
                    self::$special_component_fields[$component_id][$name . "_" . $suffix] = $field;
                }

            }
            self::$component_fields[$component_id] = $fields;
        }
    }

    /**
     * @return array
     */
    protected function get_component_fields() {
        $this->initialize_component_fields();
        return self::$component_fields[$this['Class_ID']];
    }

    /**
     * @return array
     */
    protected function get_special_component_fields() {
        $this->initialize_component_fields();
        return self::$special_component_fields[$this['Class_ID']];
    }

    /**
     * @param $field_name
     * @return bool
     */
    protected function is_component_field($field_name) {
        // Общие поля (есть у любого компонента):
        if (isset(self::$common_component_fields[$field_name])) { return true; }

        // Поля компонента товара:
        $component_fields = $this->get_component_fields();
        if (isset($component_fields[$field_name])) { return true; };

        return false;
    }

    /**
     * @return string
     */
    protected function get_select_query_fields() {
        static $cache;
        $component_id = (int)$this['Class_ID'];
        if (!$component_id) { return ""; }

        if (!$cache[$component_id]) {
            $table_name = "Message" . $component_id;

            $component = new nc_component($component_id);
            $cache[$component_id] =
                "SELECT " . $component->get_fields_query() . "\n" .
                  "FROM `$table_name` AS a\n" . $component->get_joins() . "\n";
        }

        return $cache[$component_id];
    }

    /**
     *
     */
    protected function load() {
        if ($this->is_loaded) { return; }

        $id = (int)$this->get_defined_property('Message_ID');
        if ($id) {
            $query = $this->get_select_query_fields() .
                     " WHERE `Message_ID` = ". (int)$this['Message_ID'];

            $data = nc_db()->get_row($query, ARRAY_A);

            if ($data) {
                // existing data is more important, keep it
                $this->additional_data = array_merge($data, $this->additional_data);
                $page = nc_core::get_object()->page;
                $page->update_last_modified_if_newer($this->get_defined_property('Created'), 'content');
                $page->update_last_modified_if_newer($this->get_defined_property('LastUpdated'), 'content');
            }
            else {
                $this->loading_failed = true;
            }
        }
        else {
            /* Cannot load item data (Message_ID is not set): either error or new item */
            // trigger_error("nc_netshop_item: cannot load item data (Message_ID is not set)", E_USER_WARNING);
        }

        $this->is_loaded = true;
    }

    // --- Getters helpers -----------------------------------------------------
    /**
     * Возвращает истину, если значение определено в массивах item_data или additional_data
     * @param $property
     * @return bool
     */
    protected function is_property_defined($property) {
        return array_key_exists($property, $this->item_data) ||
               array_key_exists($property, $this->additional_data);
    }

    /**
     * Возвращает значение из additional_data или item_data
     * @param $property
     * @return mixed
     */
    protected function get_defined_property($property) {
        // (a) return value from $this->additional_data, if any
        if (array_key_exists($property, $this->additional_data)) {
            return $this->additional_data[$property];
        }

        // (b) return value from the original data, if any
        if (isset($this->item_data[$property])) {
            return $this->item_data[$property];
        }

        return null;
    }

    /**
     * Получение максимального или минимального значения по полю среди включённых
     * вариантов товара.
     *
     * @param string $aggregate_function   'min', 'max'
     * @param string $property_name         поле товара
     * @return mixed
     */
    protected function get_variants_value($aggregate_function, $property_name) {
        $variants = $this['_Variants'];
        $num_variants = count($variants);
        if ($num_variants > 1) {
            return $variants->$aggregate_function($property_name);
        }
        else if ($num_variants == 1) {
            return $variants->first()->get($property_name);
        }
        else {
            return $this[$property_name];
        }
    }

    /**
     * Устанавливает значение «специальных» полей для имитации работы nc_objects_list().
     * «Специальные» поля — дополнительные поля для множественных списков, файлов и
     * множественных файлов.
     *
     * @param $property
     */
    protected function set_special_field_value($property) {
        // field data (as returned by the nc_component::get_field()
        $field = $this->uninitialized_special_fields[$property];
        if (!$field) { return; }

        // Field type-specific value processing
        if ($field['type'] == NC_FIELDTYPE_FILE) {
            $this->set_file_fields_values($field);
        }
        elseif ($field['type'] == NC_FIELDTYPE_MULTIFILE) {
            $this->set_multifile_fields_values($field);
        }
        elseif ($field['type'] == NC_FIELDTYPE_MULTISELECT) {
            $this->set_multiselect_fields_values($field);
        }
    }

    /**
     * @param array $field
     */
    protected function set_file_fields_values($field) {
        // set all fields at once: <field>, <field>_name, <field>_type, <field>_size, <field>_url, <field>_download

        $file_field_name = $field['name'];
        $nc_core = nc_core::get_object();

        $file_info = $nc_core->file_info->get_file_info($this['Class_ID'], $this['Message_ID'], $file_field_name, true, false, true);
        foreach ($file_info as $variable => $value) {
            $this->offsetSet($variable, $value);
            $this->uninitialized_special_fields[$variable] = null;
        }
    }

    /**
     * @param $field
     */
    protected function set_multifile_fields_values($field) {
        $field_name = $field['name'];
        $template = $GLOBALS['f_' . $field_name . '_tpl']; // :-(

        $field_value = nc_get_multifile_field_values($this['Class_ID'], $this['Message_ID'], $field_name);
        if ($field_value instanceof nc_multifield) {
            if (!$field_value->count() && $this->has_parent()) {
                $field_value = $this['_Parent'][$field_name];
            }
            $field_value->set_template($template);
        }

        $this->offsetSet($field_name, $field_value);
        $this->uninitialized_special_fields[$field_name] = null;
    }

    /**
     * @param array $field
     */
    protected function set_multiselect_fields_values($field) {
        $names = $ids = $values = array();

        // set all fields at once: <field>, <field>_id, <field>_value
        $raw_value = $this->get_defined_property($field['name']);
        $raw_value = trim($raw_value, ',');
        if (!$raw_value && $this->has_parent()) {
            $f = $field['name'];
            $parent = $this['_Parent'];
            $this->offsetSet($f, $parent[$f]);
            $this->offsetSet($f . "_id", $parent[$f . "_id"]);
            $this->offsetSet($f . "_value", $parent[$f . "_value"]);
            return;
        }

        if (preg_match('/^[\d,]+$/', $raw_value)) { // looks not harmful
            // get data
            // @todo decide what is the best way to optimize this (see also nc_objects_list())
            $db = nc_db();
            $t = $db->escape($field['table']);
            $query = "SELECT `{$t}_Name`, `{$t}_ID`, `Value`
                        FROM `Classificator_{$t}`
                       WHERE `{$t}_ID` IN ($raw_value)
                       ORDER BY FIND_IN_SET(`{$t}_ID`, '$raw_value')";

            $result = $db->get_results($query, ARRAY_N);
            foreach ((array)$result as $row) {
                $names[] = $row[0];
                $ids[] = $row[1];
                $values[] = $row[2];
            }
        }

        $this->offsetSet($field['name'], $names);
        $this->offsetSet("$field[name]_id", $ids);
        $this->offsetSet("$field[name]_value", $values);

        // mark fields as initialized
        $this->uninitialized_special_fields[$field['name']] = null;
        $this->uninitialized_special_fields["$field[name]_id"] = null;
        $this->uninitialized_special_fields["$field[name]_value"] = null;
    }

    /**
     * Returns rate action url
     *
     * @param int $rate
     * @return string
     */
    public function get_rate_link($rate) {
        $url = nc_module_path('netshop') . "actions/item_rate.php" .
                "?class_id=" . $this['Class_ID'] . "&item_id=" . $this['Message_ID'] .
                "&rate=" . $rate;
        return $url;
    }

    /**
     * Alias for offsetGet
     * NB: Обычный способ обращаться к свойствам товара — через array access: $item['ItemPrice']
     * Этот метод добавлен «на всякий случай».
     */
    public function get($property) {
        return $this->offsetGet($property);
    }

    /**
     * @param $property
     * @param $value
     */
    public function override_calculated_property($property, $value) {
        if (!isset(self::$calculated_data[$property])) { return; }
        $this->reset_calculated_prices();
        $this->item_data[$property] = $value;
        switch ($property) {
            case 'ItemPrice':
                $this->item_data['ItemDiscount'] = $this['OriginalPrice'] - $value;
                $this->item_data['Discounts'] = array();
                break;
            case 'ItemDiscount':
                $this->item_data['ItemPrice'] = $this['OriginalPrice'] - $value;
                $this->item_data['Discounts'] = array();
                break;
        }
    }

    /**
     * @param $property
     */
    public function cancel_calculated_property_override($property) {
        if (!isset(self::$calculated_data[$property])) { return; }
        unset($this->item_data[$property]);
        switch ($property) {
            case 'ItemPrice':
                unset($this->item_data['ItemDiscount'], $this->item_data['Discount']);
                break;
            case 'ItemDiscount':
                unset($this->item_data['ItemPrice'], $this->item_data['Discount']);
                break;
        }
    }

    // --- ArrayAccess interface -----------------------------------------------
    /**
     * @param $property
     * @return mixed
     */
    public function offsetGet($property) {
        // (a) try to load data from the MessageXYZ table if a component field is being accessed
        if (!$this->is_loaded && !$this->is_property_defined($property) && $this['Class_ID'] && $this->is_component_field($property)) {
            $this->load();
            return $this[$property]; // recursive call to offsetGet()
        }

        if (isset($this->uninitialized_special_fields[$property])) {
            $this->set_special_field_value($property);
        }

        // (b) return value from $this->additional_data, if any
        // (c) return value from the original data, if any
        // (d) inherit value from the parent item if there is a parent and no own value
        if ($this->is_property_defined($property)) {
            $value = $this->get_defined_property($property);

            // Goods variants: fetch absent values from the parent object (inherit value)
            $inherit = !$this->loading_failed &&
                       (is_null($value) || (is_scalar($value) && !strlen($value))) &&
                       !isset(self::$uninheritable_fields[$property]) &&
                       $this->has_parent() &&
                       $this->is_component_field($property);

            if ($inherit) {
                $value = $this['_Parent'][$property];
            }
            else {
                // Возможны случаи, когда значение пришло из FLOAT-поля и
                // из-за этого содержит ошибку округления. Если название свойства
                // начинается или заканчивается на Price, или заканчивается
                // на Discount, а значение похоже на дробное число — форматируем
                // значение так, чтобы там было два десятичных разряда.
                // ends or starts with 'Price' or 'Discount'?
                $last5 = substr($property, -5);
                $is_price_property =
                    ($last5 == 'Price' || ($last5 == 'count' && substr($property, -8, 3) == 'Dis')) ||
                    substr($property, 0, 5) == 'Price';
                if ($is_price_property && preg_match('/^\d*[,.]\d+$/', $value)) {
                    $value = str_replace(',', '.', $value);
                    $value = sprintf('%1.2F', $value);
                }
            }

            return $value;
        }

        // (e) calculate, store and return value that can be calculated
        if (isset(self::$calculated_data[$property])) {
            $calculation_method = self::$calculated_data[$property];
            $value = $this->$calculation_method($property);
            $this->offsetSet($property, $value); // save the value so there’ll be no need to re-calculate it
            return $value;
        }

        // (f) return null in case the value cannot be found
        $this->offsetSet($property, null);
        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        $this->additional_data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->item_data) ||
               array_key_exists($offset, $this->additional_data) ||
               array_key_exists($offset, self::$calculated_data);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        $this->additional_data[$offset] = null;
    }

    /**
     * Сбрасывает значения всех вычисляемых значений, содержащих Price, Discount,
     * Currency в названии. (Используется при обновлении состава корзины для
     * пересчёта цен, так как они могут измениться при изменении суммы заказа)
     * (временная версия метода)
     */
    public function reset_calculated_prices() {
        $price_properties = preg_grep("/Price|Discount|Currency/", array_keys($this->additional_data));

        foreach ($price_properties as $p) {
            if (isset(self::$calculated_data[$p])) {
                unset($this->additional_data[$p]);
            }
        }
    }

    /**
     * Проверяет, имеет ли товар цену или скидки, которые зависят от свойств пользователя,
     * его предыдущих действий (состава корзины пользователя, активированных скидок,
     * купонов и т. п.)
     * @return bool
     */
    public function price_depends_on_user_data() {
        $user_dependent_conditions = array('user', 'cart', 'orders', 'valueof', 'extension');

        $discounts = $this->get_netshop()->promotion->get_item_discounts_for($this);
        $price_rule = $this->get_netshop()->get_price_rule($this);

        if ($discounts->any('item_activation_required', true)) {
            return true;
        }

        foreach ($user_dependent_conditions as $condition_type) {
            if ($price_rule && $price_rule->has_condition_of_type($condition_type)) {
                return true;
            }

            if ($discounts->any('has_condition_of_type', $condition_type)) {
                return true;
            }
        }

        return false;
    }

}