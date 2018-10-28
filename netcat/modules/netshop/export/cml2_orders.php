<?php

// make user's undivine
@ignore_user_abort(true);

// load system
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");

if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}

@set_time_limit(0);

include_once($INCLUDE_FOLDER . "index.php");

// system superior object
$nc_core = nc_Core::get_object();

if (!function_exists('xmlspecialchars')):

    function xmlspecialchars($text) {
        return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }

endif;

class Netshop_ExportCML2 {

    var $attachment;

    function Netshop_ExportCML2() {
        $this->attachment = true;
    }

    /**
     * Biztalk export
     */
    function ExportCML($order_id, $source_id, $catalogue_id) {
        $order_id = (int)$order_id;
        $source_id = (int)$source_id;
        $catalogue_id = (int)$catalogue_id;

        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;

        $order_table = false;
        $currency_table = false;
        $payment_methods_table = false;

        if ($source_id) {
            $sql = "SELECT `catalogue_id` FROM `Netshop_ImportSources` WHERE `source_id` = '{$source_id}'";
            $catalogue_id = $db->get_var($sql);
        }

        if (!$catalogue_id) {
            $catalogue = $nc_core->catalogue->get_by_host_name($_SERVER['HTTP_HOST']);
            $catalogue_id = $catalogue['Catalogue_ID'];
            if (!$catalogue_id) {
                return false;
            }
        }

        $MODULE_VARS = $nc_core->modules->get_module_vars();
        $netshop = nc_netshop::get_instance($catalogue_id);
        $is_netshop_v1_in_use = $netshop->is_netshop_v1_in_use();
        if ($is_netshop_v1_in_use) {
            $order_table = (int)$MODULE_VARS['netshop']['ORDER_TABLE'];
            $currency_table = (int)$MODULE_VARS['netshop']['CURRENCY_RATES_TABLE'];
            $payment_methods_table = (int)$MODULE_VARS['netshop']['PAYMENT_METHODS_TABLE'];
        } else {
            $order_table = $netshop->get_setting('OrderComponentID');
        }

        if (!$order_table) {
            return false;
        }

        $orders = array();

        if (!$order_id) {
            if ($source_id) {
                $orders = (array)$db->get_col("SELECT DISTINCT og.`Order_ID`
                              FROM `Netshop_OrderGoods` as og, `Message{$order_table}` as m
                              WHERE og.`Order_Component_ID`={$order_table} AND og.`Order_ID`=m.`Message_ID`
                              ORDER BY og.`Order_ID`");
            }
        } else {
            $sql = "SELECT `Message_ID` FROM `Message{$order_table}` WHERE `Message_ID` = {$order_id}";
            $orders = (int)$db->get_var($sql) ? array($order_id) : array();
        }

        // set headers
        if ($this->attachment) {
            $attachment_name = ($order_id ? $order_id . '-' : '') . 'order.xml';
            header("Content-Type: application/xml; charset={$nc_core->NC_CHARSET}");
            header("Content-Disposition: attachment; filename={$attachment_name}");
        }

        ob_start();

        echo '<?xml version="1.0" encoding="' . $nc_core->NC_CHARSET . '"?>' . PHP_EOL;
        echo '<КоммерческаяИнформация ВерсияСхемы="2.07" ДатаФормирования="' . date("Y-m-d") . 'T' . date("H:i:s") . '">' . PHP_EOL;

        foreach ($orders as $order_id) {
            $order = $netshop->load_order($order_id);
            if (!$order) {
                continue;
            }

            $sql = "SELECT `Catalogue_ID` FROM `Message{$order_table}` AS m " .
                "INNER JOIN `Subdivision` AS s ON m.`Subdivision_ID` = s.`Subdivision_ID` " .
                "WHERE m.`Message_ID` = {$order_id}";
            $catalogue_id = (int)$db->get_var($sql);

            if ($source_id) {
                $source_ids = array(
                    'source_id' => $source_id,
                );
            } else {
                $sql = "SELECT `source_id` FROM `Netshop_ImportSources` WHERE `catalogue_id` = {$catalogue_id}";
                $source_ids = (array)$db->get_results($sql, ARRAY_A);
            }

            $map_id_fields = array();
            foreach ($source_ids as $source_id) {
                $source_id = (int)$source_id['source_id'];
                $sql = "SELECT `value` FROM `Netshop_ImportMap` WHERE `source_id` = {$source_id} AND `source_string` = 'Ид' LIMIT 1";
                $field_id = (int)$db->get_var($sql);

                if ($field_id) {
                    $sql = "SELECT `Field_Name`, `Class_ID` FROM `Field` WHERE `Field_ID` = {$field_id}";
                    $field = $db->get_row($sql, ARRAY_A);
                    if ($field) {
                        $map_id_fields[] = $field;
                    }
                }
            }

            $sql = "LOCK TABLES `Netshop_OrderIds` WRITE";
            $db->query($sql);

            $sql = "SELECT `1c_Order_ID` FROM `Netshop_OrderIds` WHERE `Netshop_Order_ID` = {$order_id} AND `Catalogue_ID` = {$catalogue_id}";
            $ext_order_id = (int)$db->get_var($sql);

            if (!$ext_order_id) {
                $sql = "SELECT MAX(`1c_Order_ID`) FROM `Netshop_OrderIds` WHERE `Catalogue_ID` = {$catalogue_id}";
                $ext_order_id = (int)$db->get_var($sql) + 1;

                $sql = "INSERT INTO `Netshop_OrderIds` (`Netshop_Order_ID`, `Catalogue_ID`, `1c_Order_ID`) VALUES " .
                    "({$order_id}, {$catalogue_id}, {$ext_order_id})";
                $db->query($sql);
            }

            $sql = "UNLOCK TABLES";
            $db->query($sql);


            $order_timestamp = timestamp($order['Created']);
            $order_date = strftime("%Y-%m-%d", $order_timestamp);
            $order_time = strftime("%H:%M:%S", $order_timestamp);

            $currency = $order['OrderCurrency'];
            if ($is_netshop_v1_in_use) {
                $sql = "SELECT `NameShort` FROM `Message{$currency_table}` AS m " .
                    "LEFT JOIN `Subdivision` AS s ON s.`Subdivision_ID` = m.`Subdivision_ID` " .
                    "WHERE s.`Catalogue_ID` = {$catalogue_id} AND m.`Message_ID` = '{$currency}' LIMIT 1";
                $currency = $db->get_var($sql);
            } else {
                $currencies = $netshop->get_setting('Currencies');
                $currency = $currencies[$currency];
            }
            if ($currency == "RUR" || $currency == "RUB") $currency = "руб";

            $items = $order->get_items();

            echo '  <Документ>' . PHP_EOL;
            echo '    <Ид>' . xmlspecialchars($ext_order_id) . '</Ид>' . PHP_EOL;
            echo '    <Номер>' . xmlspecialchars($ext_order_id) . '</Номер>' . PHP_EOL;
            echo '    <Дата>' . $order_date . '</Дата>' . PHP_EOL;
            echo '    <ХозОперация>Заказ товара</ХозОперация>' . PHP_EOL;
            echo '    <Роль>Продавец</Роль>' . PHP_EOL;
            echo '    <Валюта>' . $currency . '</Валюта>' . PHP_EOL;
            echo '    <Курс>1</Курс>' . PHP_EOL;
            echo '    <Сумма>' . $items->sum('TotalPrice') . '</Сумма>' . PHP_EOL;

            $contragent = xmlspecialchars($order['ContactName']);

            echo '    <Контрагенты>' . PHP_EOL;
            echo '      <Контрагент>' . PHP_EOL;
            echo '        <Ид>' . $order['User_ID'] . '</Ид>' . PHP_EOL;
            echo '        <Наименование>' . $contragent . '</Наименование>' . PHP_EOL;
            echo '        <Роль>Покупатель</Роль>' . PHP_EOL;
            echo '        <ПолноеНаименование>' . $contragent . '</ПолноеНаименование>' . PHP_EOL;
            echo '        <АдресРегистрации>' . PHP_EOL;
            echo '          <Представление>' . xmlspecialchars($order['Address']) . '</Представление>' . PHP_EOL;
            echo '        </АдресРегистрации>' . PHP_EOL;
            echo '      </Контрагент>' . PHP_EOL;
            echo '    </Контрагенты>' . PHP_EOL;
            echo '    <Время>' . $order_time . '</Время>' . PHP_EOL;

            if ($order['Comments'])
                echo '    <Комментарий>' . xmlspecialchars($order['Comments']) . '</Комментарий>' . PHP_EOL;

            echo '    <Товары>' . PHP_EOL;

            $cart_discounts = $order->get_cart_discounts('cart');

            $i = 0;
            $items_count = count($items);
            foreach ($items as $item) {
                $i++;
                /**
                 * @var $item nc_netshop_item
                 */
                $item_ext_id = $item['ItemID'] ?
                    $item['ItemID'] :
                    'netcat_' . $item['Class_ID'] . '_' . $item['Message_ID'];

                foreach ($map_id_fields as $map_id_field) {
                    if ($item['Class_ID'] == $map_id_field['Class_ID'] && isset($item[$map_id_field['Field_Name']])) {
                        $item_ext_id = $item[$map_id_field['Field_Name']];
                        break;
                    }
                }

                echo '      <Товар>' . PHP_EOL;
                echo '        <Ид>' . xmlspecialchars($item_ext_id) . '</Ид>' . PHP_EOL;
                echo '        <ИдКаталога></ИдКаталога>' . PHP_EOL;
                echo '        <Наименование>' . xmlspecialchars($item["Name"]) . '</Наименование>' . PHP_EOL;
                echo '        <БазоваяЕдиница Код="796" НаименованиеПолное="Штука" МеждународноеСокращение="PCE">шт</БазоваяЕдиница>' . PHP_EOL;
                echo '        <ЦенаЗаЕдиницу>' . $item["OriginalPrice"] . '</ЦенаЗаЕдиницу>' . PHP_EOL;
                echo '        <Количество>' . $item["Qty"] . '</Количество>' . PHP_EOL;
                $discounts = $item->get('Discounts');
                $cart_discount_sum = 0;
                if ((is_array($discounts) && count($discounts)) || (is_array($cart_discounts) && count($cart_discounts))) {
                    echo '        <Скидки>' . PHP_EOL;
                    foreach ($discounts as $discount) {
                        echo '            <Скидка>' . PHP_EOL;
                        echo '                <Наименование>' . xmlspecialchars($discount['name']) . '</Наименование>' . PHP_EOL;
                        echo '                <Комментарий>' . xmlspecialchars($discount['description']) . '</Комментарий>' . PHP_EOL;
                        echo '                <Сумма>' . $discount['sum'] . '</Сумма>' . PHP_EOL;
                        echo '                <УчтеноВСумме>true</УчтеноВСумме>' . PHP_EOL;
                        echo '            </Скидка>' . PHP_EOL;
                    }
                    foreach ($cart_discounts as $discount) {
                        echo '            <Скидка>' . PHP_EOL;
                        echo '                <Наименование>' . xmlspecialchars($discount['name']) . '</Наименование>' . PHP_EOL;
                        echo '                <Комментарий>' . xmlspecialchars($discount['description']) . '</Комментарий>' . PHP_EOL;
                        $discount_sum = $items_count == $i ?
                            ceil($discount['sum'] / $items_count * 100) / 100 :
                            floor($discount['sum'] / $items_count * 100) / 100;
                        $cart_discount_sum += $discount_sum;
                        echo '                <Сумма>' . $discount_sum . '</Сумма>' . PHP_EOL;
                        echo '                <УчтеноВСумме>true</УчтеноВСумме>' . PHP_EOL;
                        echo '            </Скидка>' . PHP_EOL;
                    }
                    echo '        </Скидки>' . PHP_EOL;
                }
                echo '        <Сумма>' . ($item["TotalPrice"] - $cart_discount_sum) . '</Сумма>' . PHP_EOL;
                echo '        <ЗначенияРеквизитов>' . PHP_EOL;
                echo '          <ЗначениеРеквизита>' . PHP_EOL;
                echo '            <Наименование>ВидНоменклатуры</Наименование>' . PHP_EOL;
                echo '            <Значение>Товар</Значение>' . PHP_EOL;
                echo '          </ЗначениеРеквизита>' . PHP_EOL;
                echo '          <ЗначениеРеквизита>' . PHP_EOL;
                echo '            <Наименование>ТипНоменклатуры</Наименование>' . PHP_EOL;
                echo '            <Значение>Товар</Значение>' . PHP_EOL;
                echo '          </ЗначениеРеквизита>' . PHP_EOL;
                echo '        </ЗначенияРеквизитов>' . PHP_EOL;
                echo '      </Товар>' . PHP_EOL;
            }
            $delivery_discounts = $order->get_cart_discounts('delivery');
            // включить стоимость доставки в счет
            if ($order['DeliveryCost']) {
                echo '      <Товар>' . PHP_EOL;
                echo '        <Ид>ORDER_DELIVERY</Ид>' . PHP_EOL;
                $delivery_method = new nc_netshop_delivery_method($order['DeliveryMethod']);
                echo '        <Наименование>' . xmlspecialchars($delivery_method->get('name')) . '</Наименование>' . PHP_EOL;
                echo '        <БазоваяЕдиница Код="796" НаименованиеПолное="Штука" МеждународноеСокращение="PCE">шт</БазоваяЕдиница>' . PHP_EOL;
                echo '        <ЦенаЗаЕдиницу>' . $order['DeliveryCost'] . '</ЦенаЗаЕдиницу>' . PHP_EOL;
                echo '        <Количество>1</Количество>' . PHP_EOL;
                echo '        <Сумма>' . ($order['DeliveryCost'] - $order->get_delivery_discount_sum()) . '</Сумма>' . PHP_EOL;
                if (is_array($delivery_discounts) && count($delivery_discounts)) {
                    echo '        <Скидки>' . PHP_EOL;
                    foreach ($delivery_discounts as $discount) {
                        echo '            <Скидка>' . PHP_EOL;
                        echo '                <Наименование>' . xmlspecialchars($discount['name']) . '</Наименование>' . PHP_EOL;
                        echo '                <Комментарий>' . xmlspecialchars($discount['description']) . '</Комментарий>' . PHP_EOL;
                        echo '                <Сумма>' . $discount['sum'] . '</Сумма>' . PHP_EOL;
                        echo '                <УчтеноВСумме>true</УчтеноВСумме>' . PHP_EOL;
                        echo '            </Скидка>' . PHP_EOL;
                    }
                    echo '        </Скидки>' . PHP_EOL;
                }
                echo '        <ЗначенияРеквизитов>' . PHP_EOL;
                echo '          <ЗначениеРеквизита>' . PHP_EOL;
                echo '            <Наименование>ВидНоменклатуры</Наименование>' . PHP_EOL;
                echo '            <Значение>Услуга</Значение>' . PHP_EOL;
                echo '          </ЗначениеРеквизита>' . PHP_EOL;
                echo '          <ЗначениеРеквизита>' . PHP_EOL;
                echo '            <Наименование>ТипНоменклатуры</Наименование>' . PHP_EOL;
                echo '            <Значение>Услуга</Значение>' . PHP_EOL;
                echo '          </ЗначениеРеквизита>' . PHP_EOL;
                echo '        </ЗначенияРеквизитов>' . PHP_EOL;
                echo '      </Товар>' . PHP_EOL;
            }

            echo '    </Товары>' . PHP_EOL;

            echo '    <ЗначенияРеквизитов>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Метод оплаты</Наименование>' . PHP_EOL;

            $payment_method = (int)$order['PaymentMethod'];
            if ($is_netshop_v1_in_use) {
                $sql = "SELECT `Name` FROM `Message{$payment_methods_table}` AS m " .
                    "LEFT JOIN `Subdivision` AS s ON s.`Subdivision_ID` = m.`Subdivision_ID` " .
                    "WHERE s.`Catalogue_ID` = {$catalogue_id} AND m.`Message_ID` = '{$payment_method}' LIMIT 1";
                $payment_method_string = $db->get_var($sql);
            } else {
                try {
                    $payment_method_obj = new nc_netshop_payment_method($payment_method);
                    $payment_method_string = $payment_method_obj['name'];
                } catch (nc_record_exception $e) {
                    $payment_method_string = '';
                }
            }


            echo '            <Значение>' . xmlspecialchars($payment_method_string) . '</Значение>' . PHP_EOL;

            echo '        </ЗначениеРеквизита>' . PHP_EOL;

            $status = (int)$order['Status'];
            $status_update_time = strftime("%Y-%m-%d %H:%M:%S", timestamp($order["LastUpdated"]));
            switch ($status) {
                case 0:
                default:
                    $payed = 'false';
                    $delivery_accepted = 'false';
                    $canceled = 'false';
                    $final_status = 'false';
                    $status_name = '[N] Новый';
                    break;
                case 1:
                    $payed = 'false';
                    $delivery_accepted = 'false';
                    $canceled = 'false';
                    $final_status = 'false';
                    $status_name = '[A] Принят';
                    break;
                case 2:
                    $payed = 'false';
                    $delivery_accepted = 'false';
                    $canceled = 'true';
                    $final_status = 'true';
                    $status_name = '[O] Отклонен';
                    break;
                case 3:
                    $payed = 'true';
                    $delivery_accepted = 'true';
                    $canceled = 'false';
                    $final_status = 'false';
                    $status_name = '[P] Оплачен';
                    break;
                case 4:
                    $payed = 'true';
                    $delivery_accepted = 'true';
                    $canceled = 'false';
                    $final_status = 'true';
                    $status_name = '[F] Завершен';
                    break;
            }

            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Заказ оплачен</Наименование>' . PHP_EOL;
            echo '            <Значение>' . $payed . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Доставка разрешена</Наименование>' . PHP_EOL;
            echo '            <Значение>' . $delivery_accepted . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Отменен</Наименование>' . PHP_EOL;
            echo '            <Значение>' . $canceled . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Финальный статус</Наименование>' . PHP_EOL;
            echo '            <Значение>' . $final_status . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Статус заказа</Наименование>' . PHP_EOL;
            echo '            <Значение>' . xmlspecialchars($status_name) . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '        <ЗначениеРеквизита>' . PHP_EOL;
            echo '            <Наименование>Дата изменения статуса</Наименование>' . PHP_EOL;
            echo '            <Значение>' . $status_update_time . '</Значение>' . PHP_EOL;
            echo '        </ЗначениеРеквизита>' . PHP_EOL;
            echo '    </ЗначенияРеквизитов>' . PHP_EOL;

            echo '  </Документ>' . PHP_EOL;
        }

        echo '</КоммерческаяИнформация>' . PHP_EOL;

        $buffer = ob_get_clean();

        $charset = strtolower($nc_core->NC_CHARSET);

        if ($charset != 'utf8' && $charset != 'utf-8') {
            $buffer = $nc_core->utf8->utf2win($buffer);
        }

        echo $buffer;
    }

}

if (!($perm->isSupervisor() || $perm->isGuest())) {
    die("NO RIGHTS");
}

$shop = new NetShop_ExportCML2();
$shop->ExportCML($_GET["order_id"], $_GET["source_id"], $_GET["catalogue_id"]);