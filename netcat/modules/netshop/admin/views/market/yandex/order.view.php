<?php

if (!class_exists('nc_core')) { die; }
$netshop = nc_netshop::get_instance($catalogue_id);

// Основные настройки
$settings = array(
    'YandexMarketCampaignID' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_CAMPAIGN_NUMBER,
    'YandexMarketAuthToken' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_AUTH_TOKEN,
    'YandexMarketOAuthID' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_OAUTH_APP_ID,
    'YandexMarketOAuthToken' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_OAUTH_APP_TOKEN
);

// Внешние статусы заказов
$external_statuses = array(
    'CANCELLED' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_CANCELLED,
    'DELIVERED' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_DELIVERED,
    'DELIVERY' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_DELIVERY,
    'PICKUP' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_PICKUP,
    'PROCESSING' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_PROCESSING,
    'RESERVED' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_RESERVED,
    'UNPAID' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_UNPAID
);

echo $ui->controls->site_select($catalogue_id);
if ($_GET['fields_saved'] == true) {
    echo $ui->alert->success(NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_SETTINGS_SAVED);
}

// Определяем, остались ли незаполненными какие-то необходимые поля
foreach (array_keys($settings) as $name) {
    $$name = $netshop->get_setting($name);
    if (!$$name) {
        $there_are_empty_fields = true;
    }
}
if ($there_are_empty_fields) {
    echo $ui->alert->info(NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_FILL_SETTINGS);
}

// Строим форму с основными настройками
$form = $ui->form("?controller=$controller_name&place=yandex_order&action=save_order&catalogue_id=$catalogue_id")->vertical();
$form->add()->h2('Основные настройки');
foreach ($settings as $name => $caption) {
    $form->add_row($caption)->vertical()->string('fields[' . $name . ']', $$name);
}

// Получаем список локальных статусов заказов
$local_statuses = array(0 => 'новый заказ');
foreach ($db->get_results("SELECT * FROM `Classificator_ShopOrderStatus`") as $row) {
    $local_statuses[$row->ShopOrderStatus_ID] = $row->ShopOrderStatus_Name;
}

// Получаем список уже установленных соответствий статусов заказов
$order_status_mapping = json_decode($netshop->get_setting('YandexMarketOrderStatusMapping'), true);

// Для каждого внешнего статуса заказа подбираем соответствующий локальный
$form->add()->h2(NETCAT_MODULE_NETSHOP_ORDER_STATUSES);
$form->add_row(NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_COMPARE_STATUSES);
foreach ($external_statuses as $status => $caption) {
    $form->add_row($caption)->horizontal()->select('fields[YandexMarketOrderStatusMapping][' . $status . ']', $local_statuses, $order_status_mapping[$status]);
}

// Получаем список способов оплаты
$payment_methods = array();
foreach ($db->get_results("SELECT `PaymentMethod_ID`, `Name` FROM `Netshop_PaymentMethod`") as $row) {
    $payment_methods[$row->PaymentMethod_ID] = $row->Name;
}

// Включена ли предопалата на Яндекс.Маркете для магазина
$form->add()->h2(NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_PAYMENT_PREPAID);
// Включена ли предоплата на Маркете
$form->add_row()->vertical()->checkbox('fields[YandexMarketOnlinePayment]', $netshop->get_setting('YandexMarketOnlinePayment'), NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_ONLINE_PAYMENT_CHECKED, 1);
// Устанавливаем соответствия способов оплаты
$form->add_row(NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_COMPARE_PAYMENT_METHODS);
foreach (array(
    'YandexMarketPrepaid' => NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_PAYMENT_PREPAID,
) as $setting_name => $caption) {
    $form->add_row($caption)->horizontal()->select('fields[' . $setting_name. ']', $payment_methods, $netshop->get_setting($setting_name));
}

echo $form;

?>
