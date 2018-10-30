<?php

/**
 * Действия с корзиной.
 *
 * Запросы должны выполняться методом POST.
 * Входящие параметры:
 *
 *   — cart_clear: если true, очищает содержимое корзины
 *
 *   — json: если равен 1, будет сформирован ответ в JSON-формате:
 *       {
 *          QuantityNotifications: сообщения о невозможности добавить выбранное
 *              количество товара в корзину. Объект, ключ — "ID_компонента:ID_товара"
 *              Message: текст сообщения
 *              RequestedQty: запрошенное количество
 *          Items: информация о товарах в корзине. Объект, ключ — "ID_компонента:ID_товара"
 *              Class_ID: ID компонента
 *              Message_ID: ID объекта
 *              Name: название товара
 *              VariantName: название варианта товара
 *              Vendor: производитель
 *              FullName: полное наименование (Vendor + Name + VariantName)
 *              Image: путь к картинке, указанной в поле Image
 *              ItemPrice: цена со скидкой, float
 *              ItemPriceF: цена со скидкой, отформатированная
 *              OriginalPrice: цена без скидки, float
 *              OriginalPriceF: цена без скидки, отформатированная
 *              Qty: количество
 *              TotalPrice: стоимость со скидкой, float
 *              TotalPriceF: стоимость со скидкой, отформатированная
 *              ItemDiscount: скидка на 1 шт. товара, float
 *              ItemDiscountF: скидка на 1 шт. товара, отформатированная
 *              DiscountPercent: процент скидки
 *              TotalDiscount: общая скидка на позицию, float
 *              TotalDiscountF: общая скидка на позицию, отформатированная
 *              URL: путь к странице товара
 *          TotalItemPrice: стоимость товаров со скидками, float
 *          TotalItemPriceF: сумма товаров со скидками, отформатированная
 *          TotalItemOriginalPrice: сумма товаров без скидок, float
 *          TotalItemOriginalPriceF: сумма товаров без скидок, отформатированная
 *          TotalCount: количество наименований товаров в корзине
 *          TotalItemCount: количество товаров в корзине
 *          TotalItemDiscountSum: сумма скидок, float
 *          TotalItemDiscountSumF: сумма скидок, отформатированная
 *
 *          // для совместимости с предыдущими версиями:
 *          cart_sum: сумма покупки со скидками
 *          cart_count: количество товаров в корзине
 *          cart_discount_sum: сумма скидок
 *       }
 *
 *   — cart: массив с количеством товаров:
 *        cart[id компонента][id товара] = количество
 *     Если количество равно 0, товар будет удалён из корзины.
 *
 *   — cart_mode: если равно 'add', то указанное количество добавляется
 *     к имеющемуся, иначе количество товара замещается указанным.
 *
 *   — cart_params: массив с дополнительными параметрами. Будет добавлен
 *     ко всем добавляемым и изменяемым товарам в корзине, в дальнейшем доступен
 *     через свойство 'OrderParameters' (например: $item['OrderParameters']).
 *
 *   — item_params: аналогично cart_params, но только для одного товара:
 *        item_params[id компонента][id товара][дополнительный параметр] = значение
 *
 *   — redirect_url: URL, куда следует перенаправить после выполнения действия.
 *
 *
 *   Альтернативный способ добавления товара (для выбора товара из SELECT-списка
 *   или при помощи radio-кнопок):
 *
 *   — items[]: массив, в котором значения — ID компонента и ID объекта, разделённые двоеточием
 *     (например: "520:10" — компонент 520, объект 10)
 *          items[] = "520:10"
 *          items[] = "520:62"
 *
 *   — qty: количество добавляемого товара (одинаковое для всех товаров, перечисленных
 *     в add), по умолчанию равно 1
 */

require realpath(__DIR__ . '/../../../../') . "/vars.inc.php";
require_once $INCLUDE_FOLDER . 'index.php';

$nc_core = nc_core::get_object();
$input = $nc_core->input;

$cart = (array)$input->fetch_post('cart');
$cart_params = $input->fetch_post('cart_params');
$item_params = $input->fetch_post('item_params');

$netshop = nc_netshop::get_instance();

$items = $input->fetch_post('items');

if (is_array($items)) {
    $qty = (int)$input->fetch_post('qty');

    foreach ($items as $item) {
        list($component_id, $item_id) = explode(':', $item);
        $cart[(int)$component_id][(int)$item_id] = $qty;
    }
}

$replace_existing = $input->fetch_post('cart_mode') !== 'add';

foreach ($cart as $component_id => $items) {
    foreach ((array)$items as $item_id => $qty) {
        $additional_params = isset($item_params[$component_id][$item_id])
            ? $item_params[$component_id][$item_id]
            : $cart_params;
        if (!is_array($additional_params)) {
            $additional_params = null;
        }

        $netshop->cart->add_item($component_id, $item_id, $qty, $replace_existing, $additional_params);
    }
}

if ($input->fetch_post('cart_clear')) {
    $netshop->cart->clear();
}

if ($input->fetch_post('json')) {
    $total_price = $netshop->cart->get_totals();
    $total_original_price = $netshop->cart->get_field_sum('OriginalPrice', true);
    $discount_sum = $netshop->cart->get_discount_sum();

    $data = array(
        'TotalItemPrice' => $total_price,
        'TotalItemPriceF' => $netshop->format_price($total_price),
        'TotalItemOriginalPrice' => $total_original_price,
        'TotalItemOriginalPriceF' => $netshop->format_price($total_original_price),
        'TotalCount' => $netshop->cart->get_item_count(),
        'TotalItemCount' => $netshop->cart->get_item_count(true),
        'TotalItemDiscountSum' => $discount_sum,
        'TotalItemDiscountSumF' => $netshop->format_price($discount_sum),
        'Items' => array(),
        'cart_sum' => $total_price,
        'cart_count' => $netshop->cart->get_item_count(),
        'cart_discount_sum' => $discount_sum,
    );

    $item_properties = array('Class_ID', 'Message_ID', 'Name', 'VariantName', 'Vendor',
        'FullName', 'Image', 'ItemPrice', 'ItemPriceF', 'OriginalPrice', 'OriginalPriceF',
        'Qty', 'TotalPrice', 'TotalPriceF', 'ItemDiscount', 'ItemDiscountF',
        'DiscountPercent', 'TotalDiscount', 'TotalDiscountF', 'URL');

    foreach ($netshop->cart->get_items() as $item) {
        $item_data = array();
        foreach ($item_properties as $property) {
            $item_data[$property] = $item[$property];
        }
        $data['Items'][$item['_ItemKey']] = $item_data;
    }

    $quantity_notifications = $netshop->cart->get_quantity_notifications();
    if ($quantity_notifications) {
        $data['QuantityNotifications'] = array();
        foreach ($quantity_notifications->get_all() as $notification) {
            $item = $notification['item'];
            $data['QuantityNotifications'][$item['_ItemKey']] = array(
                'Message' => $notification['message'],
                'RequestedQty' => $notification['requested_qty']
            );
        }
    }

    ob_end_clean();
    echo nc_array_json($data);
    exit;
}

$redirect_url = $input->fetch_post('redirect_url');
if (!$redirect_url) {
    $redirect_url = nc_array_value($_SERVER, 'HTTP_REFERER');
}

if ($redirect_url && $nc_core->security->url_matches_local_site($redirect_url)) {
    ob_end_clean();
    header("Location: $redirect_url");
    exit;
}
