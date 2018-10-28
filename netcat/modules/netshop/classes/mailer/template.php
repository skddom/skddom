<?php

/**
 * Class nc_netshop_mailer_template
 *
 * HTML-шаблон для писем
 *
 * Шаблон ($template->get('body')):
 *  — мастер-шаблон («макет письма») должен содержать текст %BODY% для вставки
 *    дочернего шаблона
 *  — для вставки php-кода — processing instructions,
 *    e.g. <?php foreach ($cart as $item): ?>...<? endforeach; ?>
 *  — для вставки переменных — {item.property} — будет выведено значение $item['property']
 *    или $item->property.
 *    Если элемент item является данными о пользователе, сайте, объекте, то
 *    значение будет зависеть от типа поля:
 *     — для списков — название элемента списка
 *     — для файлов — полный путь к файлу
 *     — дата и время — отформатированы в соответствии с настройками модуля «Интернет-магазин»
 *     — значения HTML-полей выводятся без экранирования, прочих полей — через htmlspecialchars;
 *       исключение — поля, заканчивающиеся на заглавную "F" (с отформатированными ценами) —
 *       выводятся без экранирования)
 *
 * Контекст:
 *  — site — свойства сайта
 *  — shop — настройки магазина (nc_netshop_settings)
 *  — cart — корзина (при оформлении заказа)
 *  — order — свойства заказа
 *  — user — свойства пользователя
 *  — coupon — свойства купона (при генерации купона)
 *
 */

class nc_netshop_mailer_template extends nc_record {

    protected $primary_key = "template_id";
    protected $properties = array(
        'template_id' => null,
        'parent_template_id' => 0,
        'catalogue_id' => 0,
        'name' => '',
        'type' => 'master',
        'subject' => '',
        'body' => '',
        'enabled' => true,
    );

    protected $table_name = "Netshop_MailTemplate";
    protected $mapping = array(
        'template_id' => "Template_ID",
        'parent_template_id' => "Parent_Template_ID",
        'catalogue_id' => "Catalogue_ID",
        'name' => 'Name',
        'type' => 'Type',
        'subject' => 'Subject',
        'body' => 'Body',
        'enabled' => "Enabled",
    );

    protected $prepared_body;
    protected $prepared_subject;
    protected $result_variable_name = '__RESULT__';
    protected $template_variables_array_name = '__DATA__';

    /**
     * @param $catalogue_id
     * @param $type
     * @return nc_netshop_mailer_template|null
     */
    static public function by_type($catalogue_id, $type) {
        if (!preg_match("/^[_a-z0-9]+$/i", $type)) {
            trigger_error("Wrong template type", E_USER_WARNING);
            return null;
        }

        $template = new self;
        $template->select_from_database(
            "SELECT * FROM `%t%`
              WHERE `Catalogue_ID` = " . (int)$catalogue_id .
              " AND `Type` = '$type'
              LIMIT 1");

        return ($template->get_id() ? $template : null);
    }

    /**
     *
     */
    public function compose_message(array $template_variables) {
        return new nc_netshop_mailer_message(
            $this->evaluate($this->prepare_subject(), $template_variables),
            $this->evaluate($this->prepare_body(), $template_variables)
        );
    }

    /**
     *
     */
    protected function prepare_subject() {
        if ($this->prepared_subject === null) {
            $this->prepared_subject = $this->prepare_template($this->get('subject'));
        }

        return $this->prepared_subject;
    }

    /**
     * @return string   string that can be passed to the eval()
     */
    protected function prepare_body() {
        if ($this->prepared_body === null) {
            $raw_body = $this->get_full_body();
            // 'parse' the template
            $this->prepared_body = $this->prepare_template($raw_body);
        }

        return $this->prepared_body;
    }

    /**
     * Returns message template body (including inherited part)
     * @return string
     */
    public function get_full_body() {
        $body = $this->get('body');
        if ($this->get('parent_template_id')) {
            $parent_template = new self($this->get('parent_template_id'));
            preg_match("@<body[^>]*>(.+)</body>@ui", $body, $matches);
            $child_body = $matches ? $matches[1] : $body;
            $body = str_replace("%BODY%", $child_body, $parent_template->get_full_body());
        }
        return $body;
    }

    /**
     *
     */
    protected function prepare_template($template) {
        $result_variable = '$' . $this->result_variable_name;
        $inside_php_tag = false;
        $inside_short_echo_tag = false;
        $template_parts = preg_split("/(<\?(?:php|=)?|\?>)/", $template, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $result = "";

        foreach ($template_parts as $part) {
            if ($part == "<?=") {
                $result .= "$result_variable .= ";
                $inside_php_tag = true;
                $inside_short_echo_tag = true;
            }
            elseif ($part == "<?php" || $part == "<?") {
                $inside_php_tag = true;
            }
            elseif ($part == "?>") {
                $inside_php_tag = false;
                $inside_short_echo_tag = false;
            }
            else if ($inside_php_tag) { // php code
                $result .= $part . ($inside_short_echo_tag ? ";" : "") . "\n";
            }
            else { //
                $part = addcslashes($part, "'");
                // replace variables placeholders: {item.property}
                $part = preg_replace_callback("/\{(\w+(?:\.\w+)*)(\|[^}]+)?\}/", array($this, 'replace_variables_callback'), $part);
                $result .= "$result_variable .= '$part';\n";
            }
        }

        return $result;
    }

    /**
     *
     */
    protected function replace_variables_callback($matches) {
        // only two levels are supported now (var.prop, not var.prop.subprop)
        list($variable, $property) = explode(".", $matches[1]);
        // assuming only correct variable placeholders were extracted (alphanumeric only), so no escaping below

        $insert_variable = "\$this->insert_variable_value(\${$this->template_variables_array_name}, '$variable', '$property')";

        // special case for 'item': it must be used inside the "foreach ($cart as $item)" loop
        if ($variable == 'item') {
            return "'. (\$item instanceof nc_netshop_item
                            ? \$this->insert_item_value(\$item, '$property')
                            : $insert_variable
                       ) . '";
        }
        else {
            return "' . $insert_variable . '";
        }
    }

    /**
     * @param string $code
     * @param array $template_variables
     * @return string
     */
    protected function evaluate($code, array $template_variables) {
        extract($template_variables);
        ${$this->template_variables_array_name} = $template_variables;
        ${$this->result_variable_name} = "";
        eval(nc_check_eval($code));
        return ${$this->result_variable_name};
    }


    protected function insert_item_value(nc_netshop_item $item, $property) {
        $component = new nc_component($item['Class_ID']);
        $value = $item[$property];

        $field_type = $component->get_field($property, 'type');

        if (!$field_type && substr($property, -1) == "F") {
            // special fields with "F" suffix (formatted prices): no html entities encoding
            return $value;
        }

        if ($field_type == NC_FIELDTYPE_TEXT && strpos($component->get_field('format'), 'html:1;') !== false) {
            return $value;
        }

        if ($field_type == NC_FIELDTYPE_FILE) {
            $value = $this->get_file_url($item, $property, null);
        }

        return nl2br(htmlspecialchars($value, ENT_QUOTES, nc_core('NC_CHARSET'), false));
    }

    /**
     * Tries to guess the correct format for the field and modifies value accordingly when needed
     *
     * @param array $template_variables
     * @param string $item_name   required to guess what type of item it is (order? user? site? item?)
     * @param string $item_field
     * @return string
     */
    protected function insert_variable_value(array $template_variables, $item_name, $item_field) {
        $value = null;
        $value_type = 'plaintext';

        $item = $template_variables[$item_name];

        if ($item instanceof nc_netshop_settings) {
            list($value_type, $value) = $this->get_shop_field_type_and_value($item, $item_field);
        }
        else if (is_array($item) || $item instanceof ArrayAccess) {
            $site_id = $template_variables['site']['Catalogue_ID'];
            // (numeric indexes are treated in the previous IF branch)
            // (a) if the item name == 'user', assume it is user properties
            // (b) if the item name == 'site', assume it is catalogue properties
            // (c) nc_netshop_order (item name == 'order')
            if ($item_name == 'user' || $item_name == 'site' || $item instanceof nc_netshop_order) {
                list($value_type, $value) = $this->get_field_type_and_value($item, $item_field, $item_name, $site_id);
            }
            // (d) treat everything else as an array ($value_type = plaintext):
            else {
                $value = $item[$item_field];
            }
        }
        else if (is_object($item)) {
            $value = $item->$item_field;
        }
        else { // not array, not object
            $value = $item;
        }

        switch ($value_type) {
            case 'raw':
                return $value;
            default:
                // 'plaintext'
                return htmlspecialchars($value, ENT_QUOTES, nc_core('NC_CHARSET'), false);
        }

    }

    // ----------------- methods required for insert_variable_value() ------------------

    /**
     * @param integer $class_id
     * @param integer $message_id
     * @return string
     */
    protected function get_item_domain($class_id, $message_id) {
        static $cache = array();
        $cache_key = "$class_id:$message_id";
        if (!array_key_exists($cache_key, $cache)) {
            $site_domain = nc_db()->get_var("SELECT c.Domain
                                               FROM Message" . (int)$class_id . " AS m,
                                                    Subdivision AS s,
                                                    Catalogue AS c
                                              WHERE m.Message_ID = " . (int)$message_id . "
                                                AND m.Subdivision_ID = s.Subdivision_ID
                                                AND s.Catalogue_ID = c.Catalogue_ID
                                              LIMIT 1");

            // fallback for incorrectly configured sites
            if (!$site_domain) { $site_domain = $_SERVER['HTTP_HOST']; }
            $cache[$cache_key] = $site_domain;
        }
        return $cache[$cache_key];
    }

    /**
     * @param array|ArrayAccess $item
     * @param string $item_field
     * @param string $item_type variable name, e.g. 'order', 'user' or 'site'
     * @param int $site_id
     * @return array (value_type, value)
     */
    protected function get_field_type_and_value($item, $item_field, $item_type, $site_id) {
        $value_type = 'plaintext';
        $value = $item[$item_field];

        // (0) determine component ID
        if ($item instanceof nc_netshop_order) {
            $component_id = nc_netshop::get_instance($site_id)->get_setting('OrderComponentID');
        }
        else if ($item_type == 'user' || $item_type == 'site') {
            $component_id = $item_type;
        }
        else {
            $component_id = $item['Class_ID'];
        }

        // (1) fetch all fields for this component
        $component_fields = $this->get_component_fields($component_id);

        // (2) check field type (if there is such field in that component)
        if (isset($component_fields[$item_field])) {
            $field_info = $component_fields[$item_field];
            switch ($field_info['TypeOfData_ID']) {
                case NC_FIELDTYPE_TEXT:
                    if (strpos($field_info['Format'], 'html:1;') !== false) {
                        $value_type = 'raw';
                    }
                    else {
                        $value = nl2br($value);
                    }
                    break;

                case NC_FIELDTYPE_SELECT:
                    list($classifier) = explode(":", $field_info['Format']);
                    $text_value = nc_get_list_item_name($classifier, $value);
                    // in case the $value wasn’t an ID of the classifier entry, $text_value will be ''
                    if (strlen($text_value)) { $value = $text_value; }
                    break;

                case NC_FIELDTYPE_MULTISELECT:
                    list($classifier) = explode(":", $field_info['Format']);
                    $ids = explode(",", trim($value, ","));
                    $values = array();
                    foreach ($ids as $id) {
                        $values[] = nc_get_list_item_name($classifier, $value);
                    }
                    $value = join(", ", $values);
                    break;

                case NC_FIELDTYPE_FILE:
                    $value = $this->get_file_url($item, $item_field, $item_type);
                    $value_type = 'raw';
                    break;

                case NC_FIELDTYPE_DATETIME:
                    $value = date(NETCAT_MODULE_SEARCH_DATETIME_FORMAT, strtotime($value));
                    break;

                case NC_FIELDTYPE_MULTIFILE: // NOT SUPPORTED
                    $value = '';
                    break;
                case NC_FIELDTYPE_RELATION: // NOT SUPPORTED
                    $value = '';
                    break;
            }
        }
        elseif ($item_type == 'order' && substr($item_field, -1) == "F") {
            // special fields with "F" suffix (formatted prices): no html entities encoding
            $value_type = 'raw';
        }

        return array($value_type, $value);

    }

    /**
     * @param nc_netshop_settings $settings
     * @param $site_field
     * @return array
     */
    protected function get_shop_field_type_and_value(nc_netshop_settings $settings, $site_field) {
        $value_type = 'plaintext';
        $value = $settings->get($site_field);

        // check if this is a classifier field
        $site_field_options = nc_netshop_admin_helpers::get_shop_fields();
        if (isset($site_field_options[$site_field]["classificator"])) {
            $value = nc_get_list_item_name($site_field_options[$site_field]["classificator"], $value);
        }

        return array($value_type, $value);
    }

    /**
     * @param array|ArrayAccess $item
     * @param string $item_field
     * @param string $system_table_name   'user', 'site'
     * @return string
     */
    protected function get_file_url($item, $item_field, $system_table_name) {
        if ($system_table_name == 'user') {
            $class_id = 'User';
            $item_id = $item['User_ID'];
        }
        elseif ($system_table_name == 'site') {
            $class_id = 'Catalogue';
            $item_id = $item['Catalogue_ID'];
        }
        else {
            $class_id = (int)$item['Class_ID'];
            $item_id = $item['Message_ID'];
            $file_path = $item[$item_field];
        }

        $file_path = $file_path ? $file_path : nc_file_path($class_id, $item_id, $item_field);
        if ($file_path) {
            if ($system_table_name == 'site') { $domain = $item['Domain']; }
            elseif (isset($item['Message_ID'])) { $domain = $this->get_item_domain($class_id, $item_id); }
            else { $domain = $_SERVER['HTTP_HOST']; } // ?!  dunno... :(
            return nc_get_scheme() . "://$domain$file_path";
        }
        else {
            return '';
        }
    }

    /**
     * @param string $component_type
     * @return array
     */
    protected function get_component_fields($component_type) {
        static $fields = array();
        // @todo consider replacing with nc_component->get_fields()
        if (!array_key_exists($component_type, $fields)) {
            /** @var nc_db $db */
            $db = nc_core('db');
            if ($component_type == 'user') {
                $condition = "System_Table_ID = 3";
            }
            elseif ($component_type == 'site') {
                $condition = "System_Table_ID = 1";
            }
            else {
                $component_type = (int)$component_type;
                $condition = "Class_ID = $component_type";
            }

            $rows = $db->get_results("SELECT Field_Name, TypeOfData_ID, Format
                                        FROM Field
                                       WHERE Checked = 1 AND $condition", ARRAY_A);

            $fields[$component_type] = array();
            foreach ((array)$rows as $row) {
                $fields[$component_type][$row['Field_Name']] = $row;
            }

        }
        return $fields[$component_type];
    }

}