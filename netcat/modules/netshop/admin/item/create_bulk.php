<?php

/**
 * Создание вариантов товара по сочетанию характеристик
 *
 * Входящие параметры:
 *  - component_id
 *  - parent_item_id
 *  - options: 2-dimensional array
 */

$NETCAT_FOLDER = realpath(dirname(__FILE__) . "/../../../../../") . "/";
require $NETCAT_FOLDER . "vars.inc.php";
require $INCLUDE_FOLDER . "index.php";

// (1) Проверка прав
$nc_core = nc_core::get_object();

$item = nc_netshop_item::by_id(
    $nc_core->input->fetch_get_post('component_id'),
    $nc_core->input->fetch_get_post('parent_item_id'));

$cc_env = $nc_core->sub_class->get_by_id($item['Sub_Class_ID']);

if (!$cc_env || !s_auth($cc_env, 'add', 1) || !$nc_core->token->verify()) {
    die(NETCAT_MODERATION_ERROR_NORIGHT);
}

// (2) Предварительно подготавливаем значения полей, чтобы была одинаковая структура
//     для SELECT-полей и полей других типов:
$component = new nc_component($item['Class_ID']);
$select_fields = array_flip($component->get_fields(NC_FIELDTYPE_SELECT, false));
$netshop = nc_netshop::get_instance($item['Catalogue_ID']);

$option_separator = ";";

$options = $nc_core->input->fetch_get_post('options');
$fields_for_names = array();
foreach ($options as $field => $values) {
    if (isset($select_fields[$field])) { // select field
        // В item значения полей типа «список» находятся в свойстве ПОЛЕ_id
        unset($options[$field]);
        $options[$field . "_id"] = $values;
    }
    else { // string/number fields, comma-separated values
        $values = $options[$field] = array_unique(preg_split("/\\s*$option_separator\\s*/", trim($values[0])));
    }

    // использовать поле для генерирования названий?
    if ($field != 'Name' && !isset($options['VariantName']) && count($values) > 1) {
        $fields_for_names[$field] = $field;
    }
}

// (3) Создаём все возможные комбинации опций
$fields = array_keys($options);
$combinations = array();

while ($field = array_shift($fields)) {
    $new_combinations = array();
    foreach ($options[$field] as $value) {
        if (sizeof($combinations)) {
            foreach ($combinations as $prev_combination) {
                $new_combinations[] = $prev_combination + array($field => $value);
            }
        }
        else {
            $new_combinations[] = array($field => $value);
        }
    }
    $combinations = $new_combinations;
}

// (4) Проверяем, существует ли уже такой вариант товара, и если нет — сохраняем в БД
/** @var nc_netshop_item_collection $existing_variants */
$existing_variants = $item['_AllChildren'];
$existing_variants->add($item);

$common_values = array(
    'User_ID' => $AUTH_USER_ID, // $nc_core->user->get_current('User_ID'),
    'Created' => date("Y-m-d H:i:s"), // can’t use NOW() here at the time of writing
    'Subdivision_ID' => $item['Subdivision_ID'],
    'Sub_Class_ID' => $item['Sub_Class_ID'],
    'Priority' => $existing_variants->max('Priority'),
    'IP' => $_SERVER['REMOTE_ADDR'],
    'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
    'Parent_Message_ID' => $item['Message_ID'],
    'ncKeywords' => $item['ncKeywords'],
    'ncDescription' => $item['ncDescription'],
    'ncSMO_Title' => $item['ncSMO_Title'],
    'ncSMO_Description' => $item['ncSMO_Description'],
    'ncSMO_Image' => $item['ncSMO_Image'],
);

$fill_article_field = $nc_core->input->fetch_get_post('fill_article_field');
$last_article_sequence = 0;

$table = nc_db_table::make('Message' . $item['Class_ID'], 'Message_ID');

foreach ($combinations as $new_variant_options) {
    // проверяем
    $filter = array();
    foreach ($new_variant_options as $item_field => $value) {
        $filter[] = array($item_field, $value);
    }
    if ($existing_variants->first_where_all($filter)) { continue; }

    // готовим данные
    $common_values['Priority']++;
    $values = $common_values;

    $variant_name = array();

    foreach ($new_variant_options as $item_field => $value) {
        // SELECT-поля: в таблице — ИМЯПОЛЯ, в $new_variant_options - ИМЯПОЛЯ_id
        $table_field = strpos($item_field, "_id") && isset($select_fields[substr($item_field, 0, -3)])
            ? substr($item_field, 0, -3)
            : $item_field;

        if (isset($fields_for_names[$table_field])) {
            if (isset($select_fields[$table_field])) {
                $variant_name[] = nc_get_list_item_name($component->get_field($table_field, 'table'), $value);
            }
            else {
                $variant_name[] = $value;
            }

        }

        $values[$table_field] = $value;
    }

    if ($variant_name) {
        $values['VariantName'] = join(", ", $variant_name);
    }

    if ($fill_article_field) {
        $parent_article = $item['Article'];
        do {
            $last_article_sequence++;
        } while ($existing_variants->any('Article', "$parent_article-$last_article_sequence"));
        $values['Article'] = "$parent_article-$last_article_sequence";
    }

    // сохраняем
    $new_item_id = $table->set($values)->insert();

    // добавляем в индекс товаров
    $netshop->itemindex->add_item(nc_netshop_item::by_id($item['Class_ID'], $new_item_id));
}

echo 'OK'; // well, probably