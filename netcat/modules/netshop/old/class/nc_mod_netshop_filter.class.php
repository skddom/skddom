<?php


class nc_mod_netshop_filter {

    //--------------------------------------------------------------------------

    protected $fields      = array();
    protected $reset_field = false;
    protected $filter_data = array();

    protected $netshop;
    protected $db;

    //--------------------------------------------------------------------------

    protected $options = array(
        // Фильтрует возможные значения полей для текущего установленного фильтра.
        // Т.е. фильтр предлагает выбрать только те параметры по которым можно найти товар
        'filter_values' => true,

        'list_field' => 'select',
        'bool_field' => 'checkbox',

        // Где хранятся параметры фильтра
        // session - в массиве $_SESSION
        // url     - в урле ?filter=...
        // 'driver' => 'session',
    );

    public $driver = 'session';

    //--------------------------------------------------------------------------

    public static function get_instance()
    {
        static $instance;

        return is_null($instance) ? ($instance = new self) : $instance;
    }

    //--------------------------------------------------------------------------

    protected function __construct() {
        $this->netshop = nc_mod_netshop::get_instance();
        $this->db      = nc_core('db');
    }

    //--------------------------------------------------------------------------

    public function options($key = null, $set = null) {

        if ( ! is_null($key)) {

            if (is_array($key)) {
                foreach ($key as $k => $val) {
                    $this->options[$k] = $val;
                }
            }
            else {
                if ( ! is_null($set)) {
                    $this->options[$key] = $set;
                }

                return $this->options[$key];
            }

        }

        return $this->options;
    }

    //--------------------------------------------------------------------------

    /**
     * Устанавка полей для фильтра товаров
     * @param  array  $fields Массив полей. ['fieldA', 'fieldB', ...] ['fieldA', 'fieldB'=>['type'=>'list'], ...]
     * @return array  $fields
     */
    public function init_fields($fields = array()) {
        static $filter_fields;

        if ($fields) {
            $all_fields    = $this->get_all_fields();
            $filter_fields = array();

            foreach ($fields as $name => $field) {
                if (is_numeric($name) && is_string($field)) {
                    $filter_fields[$field] = array();
                }
                else {
                    $filter_fields[$name] = $field;
                }
            }

            foreach ($filter_fields as $name => $field) {
                if (empty($all_fields[$name])) {
                    unset($filter_fields[$name]);
                    continue;
                }

                $f = $all_fields[$name];

                $filter_fields[$name] = array(
                    'id'    => $f['id'],
                    'name'  => $name,
                    'type'  => isset($field['type']) ? $field['type'] : $this->_default_type_by_id($f['type']),
                    'label' => isset($field['label']) ? $field['label'] : $f['description'],
                );
                $filter_fields[$name]['field'] = isset($field['field']) ? $field['field'] : $this->_default_field_by_type($field['type']);
            }

            $this->init_filter_data($filter_fields);
        }


        return $filter_fields;
    }

    //--------------------------------------------------------------------------

    /**
     * Добавляет в SQL запрос фильтрующие условия (WHERE ...)
     * @param  ссылка на переменную $query_where
     * @return null
     */
    public function query_where(&$query_where, $fields = array()) {
        $debug = 0;
        if ( ! $fields) {
            $fields = $this->init_fields();
        }
        // If array as: ['fieldA', 'fieldB']
        elseif (isset($fields[0]) && is_string($fields[0])) {
            $fields     = array_flip($fields);
            $all_fields = $this->init_fields();
            foreach ($fields as $k=>$row) $fields[$k] = $all_fields[$k];
        }
        else {
            $debug = 1;
        }
        // $fff = array ( 0 => 'Price', 1 => 'Vendor', 2 => 'Screen', 3 => 'ScreenType', 4 => 'Camera', );
        foreach ($fields as $name => $field) {
            $f_current = $this->field_value($name);

            if ($f_current) {
                $field = $this->get_field($name, $field);

                // print_r($field);echo '<hr>';
                $query_where .= ($query_where ? ' AND ' : ' ');
                switch ($field['type']) {
                    case 'range':
                        $min = $f_current[0] ? "a.`{$name}`>='".(int)$f_current[0]."'" : '';
                        $max = $f_current[1] ? "a.`{$name}`<='".(int)$f_current[1]."'" : '';
                        if ($min || $max) {
                            $query_where .= "($min".($min&&$max?' AND ':'')."$max)";
                        }
                        break;

                    default:
                        if ( ! is_array($f_current)) {
                            $f_current = array($f_current);
                        }
                        foreach ($f_current as &$row) {
                            $row = "'" . $this->db->escape($row) . "'";
                        }
                        $query_where .= "(a.`{$name}` IN (".implode(', ', $f_current)."))";
                        break;
                }
            }

        }

        // echo $query_where;
    }

    //--------------------------------------------------------------------------

    public function init_filter_data($fields) {
        $class_id = $this->_current_class_id();

        if (isset($_REQUEST['nc_filter_reset'])) {
            unset($_SESSION['netshop_filter'][$class_id]);
            $this->reset_field = true;
            $this->filter_data = array();
            return;
        }

        if (isset($_REQUEST['nc_filter_set'])) {
            foreach ($fields as $name => $field) {
                $val    = null;
                $f_name = 'filter_' . $name;

                $reset_checkboxes = $field['field'] == 'checkbox' && !isset($_REQUEST[$f_name]);

                if ($this->reset_field || $reset_checkboxes) {
                    $_REQUEST[$f_name] = '';
                }

                if ( isset($_REQUEST[$f_name]) ) {
                    $val = $_REQUEST[$f_name];
                }

                if (is_array($val)) {
                    if ( ! implode('', $val) ) $val = '';
                }

                if (is_null($val)) {
                    unset($_SESSION['netshop_filter'][$class_id][$name]);
                }
                else {
                    if ($val) {
                        $_SESSION['netshop_filter'][$class_id][$name] = $val;
                    }
                    else {
                        if (isset($_SESSION['netshop_filter'][$class_id][$name]))
                            unset($_SESSION['netshop_filter'][$class_id][$name]);
                    }
                }

            }
        }

        $this->filter_data = $_SESSION['netshop_filter'][$class_id];
    }

    //--------------------------------------------------------------------------

    /**
     * Возвращает фильтрующее значениея поля
     * @param  strung $name Название поля
     * @return strung       Значение поля
     */
    public function field_value($name) {
        return isset($this->filter_data[$name]) ? $this->filter_data[$name] : null;
    }

    //--------------------------------------------------------------------------

    /**
     * Гененрирует html форму фильтра
     * @param  array  $fields Массив полей. ['fieldA', 'fieldB', ...] ['fieldA', 'fieldB'=>['type'=>'list'], ...]
     * @return string         html форма
     */
    public function make_form($fields = array()) {
        $fields = $this->get_fields($fields);
        if ( ! $fields) return '';

        foreach ($fields as $name => $field) {
            $result .= "<div class='nc_netshop_filter_row'>";
            $result .= "<div class='nc_netshop_filter_label'><label>" . $field['label'] . "</label></div>";
            $result .= "<div class='nc_netshop_filter_field'>" . $this->make_field($name) . "</div>";
            $result .= "</div>";
        }
        $result = "<div class='nc_netshop_filter_fieldset'>" . $result . "</div>";
        $result .= "<div class='nc_netshop_filter_actions'>\n"
                . "<input type='hidden' name='nc_filter_set' value='1'>\n"
                . "<input class='nc_netshop_filter_submit' type='submit' value='" . NETCAT_MODULE_MINISHOP_FILTER_SHOW . "'>\n"
                . "<input class='nc_netshop_filter_reset' type='submit' name='nc_filter_reset' value='" . NETCAT_MODULE_MINISHOP_FILTER_RESET . "'>\n"
            . "</div>";

        return "<form class='nc_netshop_filter' action='' method='post'>" . $result . "</form>";
    }

    //--------------------------------------------------------------------------

    /**
     * Гененрирует html-код фильтрующего поля
     * @param  string $name Название поля
     * @return string       html
     */
    public function make_field($name) {
        static $script_append = false;

        $field = $this->get_field($name);
        $f_current = $this->field_value($name);
        $f_name    = "filter_{$name}";
        $result    = '';

        switch ($field['field']) {
            // case 'list':
            case 'select':
                $result .= "<select name='{$f_name}'>";
                foreach ($field['options'] as $id=>$name) {
                    $selected = $f_current == $id || $f_current == $name  ? " selected='selected'" : '';
                    $result .= "<option value='{$id}'{$selected}>{$name}</option>";
                }
                $result .= "</select>";
                break;

            case 'multiple':
                $result .= "<select name='{$f_name}[]' multiple='multiple'>";
                foreach ($field['options'] as $id=>$name) {
                    $selected = in_array($id, $f_current) || current($f_current) == $name  ? " selected='selected'" : '';
                    $result .= "<option value='{$id}'{$selected}>{$name}</option>";
                }
                $result .= "</select>";
                break;

            case 'checkbox':
                if ($field['type'] == 'list') {
                    // array_unshift($field['options'], array(''=>'фыв'));
                    foreach ($field['options'] as $id=>$name) {
                        if ( ! $id) continue;
                        $selected = in_array($id, $f_current) || current($f_current) == $name || $f_current==$id  ? " checked='checked'" : '';
                        $result .= "<div><label><input name='{$f_name}[]' type='checkbox' value='{$id}'{$selected} /> {$name}</label></div>";
                    }

                }
                break;

            case 'link':
                if ( ! $script_append) {
                    $script_append = true;
                    $result .= "<script>function nc_netshop_filter_link(ob){
                        jQuery(ob.parentNode).find('input').attr('checked','checked').parents('form').submit();
                        return false;
                    }</script>";
                }

                foreach ($field['options'] as $id=>$name) {
                    if ( ! $id) continue;
                    $active  = in_array($id, $f_current) || current($f_current) == $name  ? " class='active'" : '';
                    $checked = $active  ? " checked='checked'" : '';
                    $result .= "<div{$selected}>
                            <input name='{$f_name}[]' type='checkbox' value='{$id}'{$checked} />
                            <a href='#' onclick='return nc_netshop_filter_link(this)'>{$name}</a>
                    </div>";
                }

                break;

            case 'range':
                $min = $f_current[0] ? (float)$f_current[0] : '';
                $max = $f_current[1] ? (float)$f_current[1] : '';

                $result .= ' ';
                $result .= NETCAT_MODULE_MINISHOP_FILTER_FROM;
                $result .= " <input type='text' name='{$f_name}[]' value='{$min}'> ";
                $result .= NETCAT_MODULE_MINISHOP_FILTER_TO;
                $result .= " <input type='text' name='{$f_name}[]' value='{$max}'> ";
                break;
        }

        return "{$result}";
    }

    //--------------------------------------------------------------------------

    public function get_all_fields() {
        static $all_fields = null;

        // Все поля текущего компонента магазина
        if ( is_null($all_fields) ) {
            $class_id = $this->_current_class_id();
            $component   = new nc_Component($class_id);
            $_all_fields = $component->get_fields(0, 1);
            $all_fields  = array();
            foreach ($_all_fields as $f) {
                $all_fields[$f['name']] = $f;
            }
        }

        return $all_fields;
    }

    //--------------------------------------------------------------------------

    /**
     * Возвращает поле с установленными параметрами
     * @param  string  $name Название полея
     * @return array
     */
    public function get_field($name, $options = array()) {
        // Уже инициализировано
        if ( isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        $init_fields = $this->init_fields();
        $all_fields  = $this->get_all_fields();
        $class_id    = $this->_current_class_id();

        // Поле не существует
        if ( ! isset($all_fields[$name]) ) {
            return false;
        }

        $options = array_merge($init_fields[$name], $options);

        $field = $all_fields[$name];

        $result = array(
            'id'    => $field['id'],
            'name'  => $name,
            'type'  => isset($options['type']) ? $options['type'] : $this->_default_type_by_id($field['type']),
            'label' => isset($options['label']) ? $options['label'] : $field['description'],
        );
        $result['field'] = isset($options['field']) ? $options['field'] : $this->_default_field_by_type($result['type']);

        // Значения для списков (options list)
        if ($result['type'] == 'list') {

            $options = isset($options['options']) ? $options['options'] : '';

            if ( ! $options) {
                // Если поле является списком
                if ($field['format'] && $field['type'] == NC_FIELDTYPE_SELECT || $field['type'] == NC_FIELDTYPE_MULTISELECT) {
                    $format  = explode(':', $field['format']);
                    $options = $this->_get_classificator_items($format[0]);
                }
                else {
                    $options = $this->_get_class_items($class_id, $name);
                }
            }

            $result['options'] = $options;
        }

        // Min/Max для поля "диапазон" (range)
        elseif ($result['type'] == 'range') {
            $result['range'] = isset($options['range'])
                ? $options['range']
                // : $this->_get_range_from_class($class_id, $name);
                : array(0,0);
        }

        $this->fields[$name] = $result;

        return $result;
    }

    //--------------------------------------------------------------------------

    /**
     * Возвращает массив полей с установленными параметрами
     * @param  array  $fields Массив полей. ['fieldA', 'fieldB', ...] ['fieldA', 'fieldB'=>['type'=>'list'], ...]
     * @return array
     */
    public function get_fields($fields = array()) {
        if ( ! is_array($fields)) {
            // $fields = array($fields => array());
        }

        if ( ! $fields) {
            $fields = $this->init_fields();
        }

        $result = array();

        foreach ($fields as $name => $filter_options) {
            $result[$name] = $this->get_field($name, $filter_options);
        }

        return $result;
    }

    /***************************************************************************
        PROTECTED
    ***************************************************************************/

    protected function _get_range_from_class($class_id, $field) {
        static $range = array();

        if ( ! isset($range[$field]) ) {
            $sql = "SELECT MIN(`$field`) AS `min`, MAX(`$field`) AS `max`
                FROM `Message{$class_id}`
                WHERE `Checked` = '1'";
            $result = $this->db->get_row($sql, ARRAY_A);

            $range[$field] = array($result['min'], $result['max']);
        }

        return $range[$field];
    }

    //--------------------------------------------------------------------------

    protected function _current_class_id() {
        static $current_class_id;

        if (is_null($current_class_id)) {
            $current_class_id = nc_Core::get_object()->sub_class->get_current('Class_ID');
        }

        return $current_class_id;
    }

    //--------------------------------------------------------------------------


    protected function _get_class_items($class_id, $field) {
        static $list = array();
        static $allow_filter_values = true;

        if ( ! isset($list[$field]) ) {

            $query_where = "a.`Checked` = '1'";


            if ($allow_filter_values && $this->options('filter_values')) {
                $allow_filter_values = false;
                $fields = $this->init_fields();
                unset($fields[$field]);
                $this->query_where($query_where, $fields);
                $allow_filter_values = true;
            }

            $field = $this->db->escape($field);

            $sql = "SELECT DISTINCT a.`$field` AS `name`
                FROM `Message{$class_id}` a
                WHERE {$query_where}
                ORDER BY a.`$field`";
            $result = $this->db->get_results($sql, ARRAY_A);

            $list[$field] = array(''=>'');
            foreach ($result as $row) {
                $id = htmlspecialchars($row['name'], ENT_QUOTES);
                $list[$field][$id] = $row['name'];
            }
        }
        return $list[$field];
    }

    //--------------------------------------------------------------------------

    protected function _get_classificator_items($clft_name) {
        $clft_name = $this->db->escape($clft_name);

        if ( ! $clft_name) return array();

        $options = $this->db->get_row("SELECT * FROM `Classificator` WHERE Table_Name='" . $clft_name . "'", ARRAY_A);

        if (empty($options)) return array();

        // сортировка по полю...
        switch ($options['Sort_Type']) {
            case  1: $order_by = "`" . $clft_name . "_Name`"; break;
            case  2: $order_by = "`" . $clft_name . "_Priority`"; break;
            default: $order_by = "`" . $clft_name . "_ID`";
        }
        $order_by .= ($options['Sort_Direction'] ? " DESC" : " ASC");

        # выбор данных о списке, цикл ниже
        $result = $this->db->get_results("SELECT `{$clft_name}_ID` AS id, `{$clft_name}_Name` AS name, `{$clft_name}_Priority`
            FROM `Classificator_{$clft_name}`
            WHERE `Checked` = '1'
            ORDER BY " . $order_by, ARRAY_A);

        $list = array(''=>'');
        foreach ($result as $row) {
            $list[$row['id']] = $row['name'];
        }

        return $list;
    }

    //--------------------------------------------------------------------------

    protected function _default_field_by_type($type) {
        switch ($type) {
            case 'list':
                return $this->options('list_field');

            case 'bool':
                return $this->options('bool_field');

            default:
                return $type;
        }
    }

    //--------------------------------------------------------------------------

    protected function _default_type_by_id($f_type_id) {

        switch ($f_type_id) {
            case NC_FIELDTYPE_TEXT:
                return 'text';

            case NC_FIELDTYPE_STRING:
            case NC_FIELDTYPE_SELECT:
            case NC_FIELDTYPE_MULTISELECT:
                return 'list';

            case NC_FIELDTYPE_INT:
            case NC_FIELDTYPE_FLOAT:
                return 'range';

            case NC_FIELDTYPE_BOOLEAN:
                return 'bool';
        }
    }

    //--------------------------------------------------------------------------

    private function __clone() {}
    private function __wakeup() {}

    //--------------------------------------------------------------------------

}