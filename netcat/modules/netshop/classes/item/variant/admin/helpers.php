<?php

/**
 * Класс для вывода таблицы со списком вариантов в режиме администрирования
 */
class nc_netshop_item_variant_admin_helpers {

    static protected $is_script_already_included = false;


    /**
     * Возвращает массив с переменными для использования в поле «Системные настройки»
     * в шаблонах множественного редактирования товаров для использования
     * совместно с таблицей вариантов (кнопка «редактировать все варианты»):
     *    extract(nc_netshop_item_variant_admin_helpers::multi_edit_variables());
     *
     * @return array
     */
    static public function multi_edit_variables() {
        /** @var nc_input $input */
        $input = nc_core('input');
        $item_ids = $input->fetch_post_get('item_ids');
        if (is_array($item_ids)) {
            return array(
                'ignore_limit' => true,
                'ignore_check' => true,
                'ignore_parent' => true,
                'query_where' => "a.`Message_ID` IN (" . join(",", array_map('intval', $item_ids)) . ")",
                'query_order' => "a.`Parent_Message_ID` = 0 DESC, a.`Priority` ASC",
            );
        }
        else {
            return array();
        }
    }
    
    /**
     * Возвращает список вариантов товара в виде таблицы с ссылками
     *
     * @param nc_netshop_item $item
     * @param array $fields_to_show
     * @param bool $show_header
     * @return string
     */
    static public function make_table(nc_netshop_item $item, array $fields_to_show = array('Article', 'VariantName', 'OriginalPriceF'), $show_header = true) {
        if (!nc_core('admin_mode')) { return ''; }

        if ($item['Parent_Message_ID']) {
            // Если товар сам является подвариантом — выводим таблицу с сиблингами
            return self::make_table($item['_Parent'], $fields_to_show, $show_header);
        }

        // Подготовка параметров для ссылок
        $common_params_array = (array)nc_core('input')->fetch_get_post();
        if (!isset($common_params_array['catalogue'])) { $common_params_array['catalogue'] = $item['Catalogue_ID']; }
        if (!isset($common_params_array['sub'])) { $common_params_array['sub'] = $item['Subdivision_ID']; }
        if (!isset($common_params_array['classID'])) { $common_params_array['classID'] = $item['Class_ID']; }
        if (!isset($common_params_array['cc'])) { $common_params_array['cc'] = $item['Sub_Class_ID']; }
        $common_params_array['admin_mode'] = '1';
        unset($common_params_array['isNaked'], $common_params_array['cc_only']);
        if (!empty($common_params_array['action'])) {
            unset($common_params_array['action']);
        }
        $common_params = http_build_query($common_params_array, null, '&amp;');

        $path = nc_core('SUB_FOLDER') . nc_core('HTTP_ROOT_PATH');
        $modal_form_on_click = "onclick='parent.nc_form(this.href); return false;'";
        $modal_update_on_click = "onclick='parent.nc_action_message(this.href); return false;'";

        /** @var nc_netshop_item_collection $all_item_variants */
        $all_item_variants = $item['_AllChildren'];
        $num_variants = count($all_item_variants);

        $multi_edit_template_id = nc_get_AdminCommon_multiedit_button_template_id($item['Class_ID']);

        // Поехали
        $result = "";
        if (!self::$is_script_already_included) {
            $script_url = nc_module_path('netshop') . "admin/item/variant_admin.min.js";
            if ($GLOBALS['isNaked']) {
                $result .=
                    "<script>\n" .
                        "(function() {\n" .
                            "if (window.nc_netshop_init_variant_drag) { setTimeout(nc_netshop_init_variant_drag, 100); }\n" .
                            "else { \$nc.ajax({url:'$script_url', dataType: 'script', cache: true}); }\n" .
                        "})();\n" .
                    "</script>\n";
            }
            else {
                $result .= "<script src='$script_url'></script>\n";
            }
            self::$is_script_already_included = true;
        }

        $parameters = array(
            'component_id' => $item['Class_ID'],
            'parent_item_id' => $item['Message_ID'],
            'edit_template_id' => $multi_edit_template_id,
            'request_parameters' => $common_params_array,
        );

        $result .=
            "<div class='nc-netshop-variant' data-parameters='" .
                htmlspecialchars(nc_array_json($parameters), ENT_QUOTES) . "'>\n" .
            "<ul class='nc-toolbar nc--left'>\n" .
            "<li><span>" . NETCAT_MODULE_NETSHOP_ITEM_VARIANTS . "</span></li>\n" .
            // «Добавить один»
            "<li><a class='nc-netshop-variant-toolbar-text' " .
                "href='{$path}add.php?$common_params&amp;f_Parent_Message_ID=$item[Message_ID]' " .
                "$modal_form_on_click>" .
                    NETCAT_MODULE_NETSHOP_ADD_ITEM_VARIANT .
                "</a></li>\n" .
            // «Добавить несколько»
            "<li>" .
                "<a href='#' class='nc-netshop-variant-toolbar-text nc-netshop-variant-add-multiple'>" .
                    NETCAT_MODULE_NETSHOP_ADD_ITEM_VARIANTS .
                "</a></li>\n";

        if ($num_variants) {

            $result .=
                // «Включить все»
                "<li><a href='#' class='nc-netshop-variant-enable-all" .
                    ($all_item_variants->any('Checked', 0) ? '' : ' nc--disabled' ) .
                    "' title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ENABLE_ALL, ENT_QUOTES) .
                    "'><i class='nc-icon nc--on'></i></a></li>\n" .
                // «Выключить все»
                "<li><a href='#' class='nc-netshop-variant-disable-all" .
                    ($all_item_variants->any('Checked', 1) ? '' : ' nc--disabled' ) .
                    "' title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_DISABLE_ALL, ENT_QUOTES) .
                    "'><i class='nc-icon nc--off'></i></a></li>\n" .
                // «Редактировать все»
                ($multi_edit_template_id
                    ? "<li><a href='#' class='nc-netshop-variant-edit-all" .
                          "' title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_EDIT_ALL, ENT_QUOTES) .
                          "'><i class='nc-icon nc--edit'></i></a></li>\n"
                    : "") .
                // «Удалить все»
                "<li><a href='#' class='nc-netshop-variant-delete-all'" .
                    "' title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_DELETE_ALL, ENT_QUOTES) .
                    "'><i class='nc-icon nc--remove'></i></a></li>\n";
        }

        $result .= "</ul>\n<div class='nc--clearfix'></div>\n";

        if ($num_variants) {
            $result .= "<table class='nc-table nc-netshop-variant-table nc--wide nc--small nc--bordered'>\n";

            if ($show_header) {
                $result .= "<thead>\n<tr>\n";

                $result .= "<th>&nbsp;</th>";
                $result .= "<th>&nbsp;</th>";

                $component = new nc_component($item['Class_ID']);
                foreach ($fields_to_show as $field_name) {
                    $field_description = $component->get_field($field_name, 'description');
                    if (!$field_description) {
                        if ($field_name == "FullName") {
                            $field_description = NETCAT_MODULE_NETSHOP_ITEM_FULL_NAME;
                        }
                        if (strpos($field_name, 'Price')) {
                            $field_description = NETCAT_MODULE_NETSHOP_ITEM_PRICE;
                        }
                    }
                    $result .= "<th class='nc-netshop-item-$field_name'>" . ($field_description ? $field_description : '&nbsp;') . "</th>";
                }
                $result .= str_repeat("<th>&nbsp;</th>", 2);
                $result .= "</tr>\n</thead>\n";
            }

            // <tbody> нужен для tableDnD (чтобы нельзя было перетащить строку выше строки с названиями колонок)
            $tbody_started = false;

            // для наглядности добавим основной вариант в список, если у него указана цена
            if ($item['ItemPrice']) {
                $all_item_variants = clone $all_item_variants;
                $all_item_variants->unshift($item);
            }

            foreach ($all_item_variants as $variant) {
                $common_link = $path . "message.php?" . $common_params . "&amp;message=$variant[Message_ID]";

                $is_parent_item = ($variant['Message_ID'] == $item['Message_ID']);
                $is_checked = $variant['Checked'];

                if (!$is_parent_item && !$tbody_started) {
                    $result .= "<tbody>\n";
                    $tbody_started = true;
                }

                // атрибут id нужен для tableDnD
                $result .= "<tr data-item-id='$variant[Message_ID]'" .
                           ($is_parent_item ? " class='nc-netshop-variant-parent'" : "") .
                           " id='nc_netshop_item_row_$variant[Class_ID]_$variant[Message_ID]'>\n";

                // тащи-бросай
                if ($is_parent_item) {
                    $result .= "<td class='nc-netshop-variant-parent-icon'>" .
                        "<span title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_PARENT, ENT_QUOTES) . "'>" .
                        "<i class='nc-icon nc--minus'></i></span></td>\n";
                }
                else {
                    $result .= "<td class='nc-netshop-variant-button nc-netshop-variant-drag'>" .
                        "<span title='" . htmlspecialchars(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_PRIORITY, ENT_QUOTES) . "'>" .
                        "<i class='nc-icon nc--move'></i></span></td>\n";
                }

                // вкл-выкл
                $toggle_link = $common_link . "&amp;checked=" . ($is_checked ? 1 : 2) .
                    "&amp;posting=1" . self::get_token_for('edit');
                $result .= "<td class='nc-netshop-variant-button'>" .
                    "<a href='$toggle_link' class='nc-text-" . ($is_checked ? 'green' : 'red') .
                    "' $modal_update_on_click>" .
                    ($is_checked ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF) .
                    "</a></td>\n";

                foreach ($fields_to_show as $field_name) {
                    $class = "nc-netshop-item-$field_name";
                    if (strpos($field_name, "_ID")) {
                        $class .= " nc--compact";
                    }
                    if (!$is_checked) {
                        $class .= " nc--disabled";
                    }

                    $value = $variant[$field_name];
                    if (is_array($value)) { // множественный выбор?
                        $value = join(', ', $value);
                    }
                    $result .= "<td class='$class'>$value</td>\n";
                }

                // Кнопки:
                // редактировать
                $result .= "<td class='nc-netshop-variant-button'>" .
                    "<a href='" . $common_link . self::get_token_for('edit') . "' " .
                    "$modal_form_on_click title='" . htmlspecialchars(NETCAT_MODERATION_CHANGE, ENT_QUOTES) . "'>" .
                    "<i class='nc-icon nc--edit'></i></a></td>\n";

                // удалить
                $result .= "<td class='nc-netshop-variant-button'>" .
                    "<a href='" . $common_link ."&amp;delete=1" . self::get_token_for('drop') . "' " .
                    "title='" . htmlspecialchars(NETCAT_MODERATION_DELETE, ENT_QUOTES) . "' " .
                    (nc_core('inside_admin') ? $modal_update_on_click : "") . ">" .
                    "<i class='nc-icon nc--remove'></i></a></td>\n";

                $result .= "</tr>\n";
            }

            $result .= "</tbody>\n</table>\n";
        }

        $result .= "</div>\n"; // of <div class='nc-netshop-variants'>

        return $result;
    }

    /**
     * Получить строку с token для вставки в url
     * @param $action
     * @return string
     */
    static protected function get_token_for($action) {
        /** @var nc_token $token */
        $token = nc_core('token');
        if ($token->is_use($action)) {
            return "&amp;" . $token->get_url();
        }
        return '';
    }

}