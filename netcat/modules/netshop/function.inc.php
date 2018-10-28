<?php

$NETSHOP_FOLDER = dirname(__FILE__);

// новый класс
require_once "$NETSHOP_FOLDER/nc_netshop.class.php";
nc_core()->register_class_autoload_path('nc_netshop_', "$NETSHOP_FOLDER/classes");

// слушатель изменений заказа
nc_netshop_order_listener::register();

// слушатель изменений товаров для обновления индекса
nc_netshop_itemindex_listener::register();

// совместимость со старыми версиями
// @todo make this conditional:
require_once "$NETSHOP_FOLDER/old/deprecated.inc.php";


// функции для краткости/удобства

/**
 * Выводит тулбар для управления вариантами товара и таблицу вариантов товара в
 * режиме администрирования
 *
 * @param nc_netshop_item $item
 * @param array $fields_to_show
 * @param bool $show_header
 * @return string
 */
function nc_netshop_item_variant_admin_table(nc_netshop_item $item, array $fields_to_show = array('Article', 'VariantName', 'OriginalPriceF'), $show_header = true) {
    if (nc_core('admin_mode')) { // do not load the class in non-admin mode
        return nc_netshop_item_variant_admin_helpers::make_table($item, $fields_to_show, $show_header);
    }
    else {
        return '';
    }
}