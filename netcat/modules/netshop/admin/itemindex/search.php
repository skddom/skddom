<?php

/**
 * Поиск в индексе товаров.
 *
 * Входящие параметры:
 *   terms — символы для поиска
 *   limit — максимальное количество результатов
 *   order_data (опционально) — информация о заказе (основные поля — f_*, корзина — элемент item)
 *   site_id
 */

require '../no_header.inc.php';

$nc_core = nc_core::get_object();

$terms = $nc_core->input->fetch_get_post('terms');
$site_id = $nc_core->input->fetch_get_post('site_id');
$limit = $nc_core->input->fetch_get_post('limit');

$netshop = nc_netshop::get_instance($site_id);

$items = $netshop->itemindex->find($terms, $limit);

$properties = array(
    'Class_ID', 'Message_ID', 'RowID',
    'Article', 'FullName', 'URL',
    'OriginalPrice', 'OriginalPriceF',
    'ItemDiscount', 'Discounts',
    'ItemPrice', 'ItemPriceF',
    'Units',
);

if (!$netshop->get_setting('IgnoreStockUnitsValue')) {
    $properties[] = 'StockUnits';
}

$i = 0;
$result = array();

foreach ($items as $item) {
    foreach ($properties as $k) {
        $result[$i][$k] = $item[$k];
    }
    $i++;
}

if (!$nc_core->NC_UNICODE) {
    $result = $nc_core->utf8->array_win2utf($result);
}

echo json_encode($result, 256 /* JSON_UNESCAPED_UNICODE since PHP 5.4.0 */);
