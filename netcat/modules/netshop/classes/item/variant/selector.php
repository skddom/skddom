<?php

/**
 * Класс для формирования элементов интерфейса, упрощающих работу с вариантами
 * товаров (для шаблонов товаров):
 *   — вывод переключателя между вариантами товара по заданному шаблону
 *   — подключение и инициализация скрипта, обеспечивающего загрузку варианта товара
 *     без полной перезагрузки страницы
 */
class nc_netshop_item_variant_selector {

    /** @var nc_netshop_item  Товар, на странице которого отображаются*/
    protected $item;

    /** @var array  */
    protected $option_fields = array();

    /** @var bool  */
    protected $is_script_already_requested = false;

    // -------------------------------------------------------------------------

    /**
     * @param nc_netshop_item $item
     * @param array $option_fields
     */
    public function __construct(nc_netshop_item $item, array $option_fields = array()) {
        $this->item = $item;
        $this->option_fields = $option_fields;
    }


    /**
     *
     * @param $field_name
     * @param string $first_option_text
     * @return string
     */
    public function as_select($field_name, $first_option_text = '') {
        $template = array(
            'prefix' => "<select>\n",
            'first' => "<option>$first_option_text</option>",
            'active' => "<option value='%URL'>%NAME</option>",
            'active_link' => "<option selected>%NAME</option>",
            'unactive' => "<option value='%URL' class='tpl-item-variant-unavailable'>%NAME</option>",
            'suffix' => "</select>\n",
            'divider' => "\n",
        );
        return $this->by_template($field_name, $template);
    }

    /**
     * @param $field_name
     * @param array $template
     * @return string
     */
    public function by_template($field_name, array $template) {
        /** @var nc_netshop_item_collection $all_variants */
        // Все варианты товара:
        $all_variants = $this->item['_Variants'];
        $item_option_value = $this->item[$field_name];

        // Проверяем, включён ли «основной» вариант товара:
        $parent = ($this->item->has_parent()) ? $this->item['_Parent'] : $this->item;
        $current_item_is_disabled_parent = !$parent['Checked'] && !$this->item->has_parent();

        // Если у товара нет вариантов — вернём значение поля текущего товара
        if (!$current_item_is_disabled_parent && count($all_variants) < 2) { return $item_option_value; }

        // Все значения поля $field_name:
        $distinct_option_values = $all_variants->distinct($field_name);
        $distinct_option_values_count = count($distinct_option_values);
        $disabled_parent_is_distinct =
            $current_item_is_disabled_parent &&
            $distinct_option_values_count == 1 &&
            $distinct_option_values[0] != $this->item[$field_name];

        // Если по полю нет вариантов — вернём единственное значение:
        if ($distinct_option_values_count < 2 && !$disabled_parent_is_distinct) {
            return $distinct_option_values[0];
        }

        // Условия для отбора вариантов по тому же сочетанию опций (кроме
        // опции $field_name), что и у текущего товара:
        $filter = array();

        // Подбираем список вариантов с теми же опциями, что и у текущего товара
        $multiple_options = count($this->option_fields) > 1;
        if ($multiple_options) {
            foreach ($this->option_fields as $other_field) {
                if ($other_field == $field_name) { continue; }
                $filter[] = array($other_field, $this->item[$other_field]);
            }
            // Варианты товаров с тем же сочетанием опций (кроме опции $field_name),
            // что и у текущего товара:
            $variants_with_same_options = $all_variants->where_all($filter);
        }
        else {
            $variants_with_same_options = $all_variants;
        }

        // --- Готовим переключатель вариантов товара по указанному полю ---

        // Добавляем маркер для прикрепления javascript
        $marker = "data-role='variant-selector'";
        $result = '';
        if (isset($template['prefix'])) {
            $result .= preg_replace('/(<\w+)/', "$1 $marker", $template['prefix']);
        }

        if (!strpos($result, 'data-role')) { // попытка модификации префикса оказалась неудачной
            $result = "<span $marker>";
            // добавить закрывающий тэг </span> в суффикс
            $template['suffix'] = (isset($template['suffix']) ? $template['suffix'] : '') . "</span>";
        }

        // Если сейчас выбран основной вариант и он отключён — показать пустой элемент
        if ($current_item_is_disabled_parent && isset($template['first'])) {
            $result .= $template['first'];
        }

        $last_row = count($distinct_option_values) - 1;
        $current_row = 0;
        foreach ($distinct_option_values as $row_option_value) {
            if ($row_option_value == $item_option_value) { // значение опции как у текущего товара
                $option_type = 'active_link';
                $row_item = $this->item;
            }
            else {
                $variant_with_these_options = $variants_with_same_options->first($field_name, $row_option_value);

                if ($variant_with_these_options) { // есть товар с таким же сочетанием прочих опций
                    $option_type = 'active';
                    $row_item = $variant_with_these_options;
                }
                else { // нет товара с таким сочетанием прочих опций
                    $option_type = 'unactive';
                    // найти товар, «наиболее похожий» на текущий товар
                    $best_match_filter = $filter;
                    // добавляем в начало условие по текущему полю
                    array_unshift($best_match_filter, array($field_name, $row_option_value));
                    while ($best_match_filter) {
                        array_pop($best_match_filter);
                        $best_match = $all_variants->first_where_all($best_match_filter);
                        if ($best_match) { break; }
                    }
                    // такого не должно быть, это условие добавлено для успокоения анализатора в IDE:
                    if (!isset($best_match)) { $best_match = $all_variants->first(); }

                    $row_item = $best_match;
                }
            }

            $pseudo_variables = array(
                '%NAME' => htmlspecialchars($row_item[$field_name], ENT_QUOTES),
                '%URL' => $this->get_item_link($row_item['Message_ID']), //$row_item['URL'],
            );

            $result .= $this->evaluate_row($template[$option_type], $row_item, $pseudo_variables);

            if (isset($template['divider']) && $current_row != $last_row) {
                $result .= $template['divider'];
            }
            $current_row++;
        }

        $result .= isset($template['suffix']) ? $template['suffix'] : '';

        return $result;
    }

    /**
     * @param $object_id
     * @return string
     */
    protected function get_item_link($object_id) {
        $nc_core = nc_core::get_object();
        if ($nc_core->admin_mode) {
            return nc_get_fullLink(
                $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH,
                $GLOBALS['catalogue'],
                $GLOBALS['sub'],
                $GLOBALS['cc'],
                $object_id,
                (int)$nc_core->inside_admin
            );
        }
        else {
            return nc_message_link($object_id, $this->item['Class_ID']);
        }
    }

    /**
     * @param string $template
     * @param nc_netshop_item $item
     * @param array $pseudo_variables
     * @return mixed
     */
    protected function evaluate_row($template, $item, array $pseudo_variables) {
        $result = $template;
        $template = addcslashes($template, '"');
        eval(nc_check_eval('$result = "' . $template . '";'));
        foreach ($pseudo_variables as $var => $value) {
            $result = preg_replace("/$var\\b/", $value, $result);
        }
        return $result;
    }

    /**
     * Подключает JS-скрипт для перехода между вариантами.
     * Если $include_whole_script равен true, то полностью возвращает содержимое
     * скрипта (его размер около 1 Кб)
     *
     * @param bool $include_whole_script
     * @return string
     */
    public function include_script($include_whole_script = true) {
        if (nc_core('input')->fetch_get_post('request_type') === 'get_variant') {
            return '';
        }

        $this->is_script_already_requested = true;
        $script_name = "/js/variant_selector.min.js";
        if ($include_whole_script) {
            return "\n<script>\n" . file_get_contents(nc_module_folder('netshop') . $script_name) . "\n</script>\n";
        }
        else {
            return "<script src='" . nc_module_path('netshop') . $script_name . "'></script>\n";
        }
    }

    /**
     * Инициализирует элементы выбора вариантов. Подключает JS-скрипт, если он
     * не был подключён ранее.
     *
     * @param array $options
     * @return string
     */
    public function init(array $options = null) {
        if (nc_core('input')->fetch_get_post('request_type') === 'get_variant') {
            return '';
        }

        $result = "";
        if (!$this->is_script_already_requested) {
            $result .= $this->include_script(true);
        }

        $result .= "\n<script>\n" .
                   '$(function() { nc_netshop_init_variant_selector(' . nc_array_json($options) . '); });' .
                   "\n</script>\n";

        return $result;
    }

}