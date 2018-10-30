<?php

/**
 * Расчёт стоимости доставки
 *
 * Входящие параметры
 *
 *  - delivery_method_id
 *  - form_data[]:  поля заказа  (f_*)
 *    Структура — как выдаёт jQuery .serializeArray():
 *    [ { name: 'f_FieldName', value: 'value' }, ... ]
 *
 * Ответ — объект в формате JSON:
 *
 *     error: сообщение об ошибке, если оно возникло
 *     error_code: код возникшей ошибки (см. константы nc_netshop_delivery_estimate::ERROR_*)
 *
 *     price': цена доставки со скидками (равна full_price, если скидок нет)
 *     formatted_price': цена, отформатированная в соответствии с настройками магазина
 *
 *     full_price: цена доставки без скидок
 *     formatted_full_price: отформатированная цена доставки без скидок
 *
 *     discount: скидка на доставку
 *     formatted_discount: отформатированная скидка на доставку
 *
 *     min_days: минимальное количество дней для доставки
 *     max_days: максимальное количество дней для доставки
 *     min_date: ближайшая возможная дата доставки (гггг-мм-дд)
 *     max_date: ближайшая возможная дата доставки (гггг-мм-дд)
 *     dates_string: отформатированная строка с датами доставки (например: «1 — 5 февраля»
 *
 *  Все денежные суммы — в основной валюте магазина.
 *
 */

require realpath(dirname(__FILE__) . "/../../../../") . "/vars.inc.php";
require $INCLUDE_FOLDER . "index.php";

/** @var nc_input $input */
/** @var nc_netshop $netshop */
$input = nc_core('input');
$netshop = nc_modules('netshop');      // this will load language constants

$delivery_method_id = (int)$input->fetch_post('delivery_method_id');

// prepare order data
$order_data = array();
foreach ((array)$input->fetch_post('form_data') as $field) {
    $order_data[$field['name']] = $field['value'];
}

// make fake order object and estimate delivery cost for it
$order = nc_netshop_order::from_post_data($order_data, $netshop);
$estimate = $netshop->delivery->get_estimate($delivery_method_id, $order);

// prepare $result array
if ($estimate->has_error()) {
    $result = array(
        'delivery_method_id' => $delivery_method_id,
        'error' => $estimate->get('error'),
        'error_code' => $estimate->get('error_code'),
    );
}
else {
    $price = $estimate->get('price');
    $discount = $estimate->get('discount');
    $full_price = $estimate->get('full_price');

    $result = array(
        'delivery_method_id' => $delivery_method_id,

        'price' => $price,
        'formatted_price' => ($price ? $netshop->format_price($price) : NETCAT_MODULE_NETSHOP_DELIVERY_FREE_OF_CHARGE),

        'full_price' => $full_price,
        'formatted_full_price' => $netshop->format_price($full_price),

        'discount' => $discount,
        'formatted_discount' => $netshop->format_price($discount),

        'formatted_price_and_discount' => $estimate->get_formatted_price_and_discount(),

        'min_days' => $estimate->get('min_days'),
        'max_days' => $estimate->get('max_days'),
        'min_date' => $estimate->get_closest_delivery_date(),
        'max_date' => $estimate->get_latest_delivery_date(),
        'dates_string' => $estimate->get_dates_string(),
    );
}

echo nc_array_json($result);
