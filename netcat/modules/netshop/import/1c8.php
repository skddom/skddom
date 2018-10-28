<?php

/**
 * Интерфейс для автоматического импорта данных из 1C8
 */
// make user's undivine
@ignore_user_abort(true);

// load system
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");


if (is_file($MODULE_FOLDER . "netshop/" . $nc_core->lang->detect_lang(1) . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . $nc_core->lang->detect_lang(1) . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}
require_once($MODULE_FOLDER . "netshop/function.inc.php");

// log status
$log_1c = true;
// log file
$log_file = $GLOBALS['TMP_FOLDER'] . "1c8.log";
// current date
$date = date("Y-m-d H:i:s");
// zip [yes/no]
//$zip = ($MODULE_VARS['netshop']['1C_ZIP'] ? 'yes' : 'no');
$zip = 'no';
// cookie's name
$cookie = "nc-import-cookie";
// import path
$import_path = $GLOBALS['TMP_FOLDER'];
// import filesname
#$import_file = uniqid("import");
// file limit
$file_limit = 1024 * 50; //1024 * 1024 * 1;
// Входим в режим автоимпорта
$_COOKIE['nc-autoimport-mode'] = true;

// Режим перехвата файлов импорта
$intercept = isset($_REQUEST['source_id']) && $_REQUEST['source_id'] === 'intercept';

//try to detect catalogue
$source_id = isset($_REQUEST['source_id']) ? (int)$_REQUEST['source_id'] : 0;

if ($intercept) {
    $zip = 'no';
    $log_1c = false;
    $import_path = nc_netshop_external_1c_interceptor::get_files_path();
}

$catalogue_id = 0;
if ($source_id) {
    $sql = "SELECT `catalogue_id` FROM `Netshop_ImportSources` WHERE `source_id` = {$source_id}";
    $catalogue_id = $db->get_var($sql);
}

if (!$catalogue_id) {
    $source_id = 0;
    $catalogue = $nc_core->catalogue->get_by_host_name($_SERVER['HTTP_HOST']);
    $catalogue_id = (int)$catalogue["Catalogue_ID"];
}

if (!$catalogue_id) {
    echo "failure" . PHP_EOL;
    echo "catalogue not found";
    exit;
}

// shop secret key
$netshop = nc_netshop::get_instance($catalogue_id);
if ($netshop->is_netshop_v1_in_use()) {
    $MODULE_VARS = $nc_core->modules->get_module_vars();
    $secret_name = $MODULE_VARS['netshop']['SECRET_NAME'];
    $secret_key = $MODULE_VARS['netshop']['SECRET_KEY'];
} else {
    $secret_name = $netshop->get_setting('1cSecretName');
    $secret_key = $netshop->get_setting('1cSecretKey');
}

/**
 * Server authorization
 */
if (
    !isset($_SERVER['PHP_AUTH_USER']) ||
    !(
        $_SERVER['PHP_AUTH_USER'] == $secret_name &&
        $_SERVER['PHP_AUTH_PW'] == $secret_key
    )
) {
    // sen auth headers
    header('WWW-Authenticate: Basic realm="Authorization required"');
    header('HTTP/1.0 401 Unauthorized');
    // log message
    if ($log_1c) file_put_contents($log_file, "wrong key" . PHP_EOL, FILE_APPEND);
    // print message
    echo "WRONG KEY";
    // halt
    exit;
}

/**
 * STEP 1: checkauth
 */
if ($_GET['mode'] == 'checkauth') {
    // delimiter
    $prefix = "=======================================================";
    // log message
    if ($log_1c)
        file_put_contents($log_file, $prefix . PHP_EOL . $date . ' - [' . $_GET['type'] . '] checkauth' . PHP_EOL, FILE_APPEND);
    // status message
    echo 'success' . PHP_EOL;
    echo $cookie . PHP_EOL;
    echo uniqid();
    // halt
    exit;
}

/**
 * STEP 2: init
 */
if ($_GET['mode'] == 'init' && $_GET['type'] != 'get_catalog') {
    // log info
    if ($log_1c)
        file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] init transfer' . PHP_EOL, FILE_APPEND);
    // status message
    echo "zip=no" . PHP_EOL;
    if ($_GET['type'] != 'get_catalog') {
        echo "file_limit=1000000" . PHP_EOL;
    }
    exit;
}

/**
 * STEP 3: save file (import)
 */

if ($_GET['mode'] == 'file') {
    if ($_GET['type'] == 'sale') {
        $zip = 'no';
    }

    // Строим имя файла из переданных параметров
    $_file = $_GET['type'] . '-' . $_COOKIE[$cookie] . ($zip == 'yes' ? '.zip' : '-' . $_GET['filename']);
    
    // Логгируем, что за файл собираемся записать
    if ($log_1c){
        file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] save file "' . $_file . '"' . PHP_EOL, FILE_APPEND);
    }

    if (!file_exists($import_path)) {
        @mkdir($import_path, 0777, true);
    }

    // Создаем папку для сохранения файла (если необходимо)
    if (strpos($_file, '/') !== false) {
        $dir = dirname($_file);
        @mkdir($import_path . $dir, 0777, true);
    }

    // Сохраняем полученный файл
    $fp = fopen($import_path . $_file, 'a');
    fwrite($fp, file_get_contents("php://input"));
    fclose($fp);
    $size = filesize($import_path . $_file);

    // Логгируем, сколько байт записали
    if ($log_1c){
        file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] ' . $size . ' saved' . PHP_EOL, FILE_APPEND);
    }

    if ($_GET['type'] == 'sale' && !$intercept) {
        @set_time_limit(0);

        include_once($INCLUDE_FOLDER . "index.php");
        require_once($MODULE_FOLDER . "netshop/import/nc_netshop_cml2parser.class.php");

        global $_UTFConverter;
        if (!$_UTFConverter) {
            // set variable
            $_UTFConverter = false;
            // allow_call_time_pass_reference need in php.ini for utf8 class, check before construct!
            if (!(extension_loaded("mbstring") || extension_loaded("iconv"))) {
                include_once($INCLUDE_FOLDER . "lib/utf8/utf8.class.php");
                // CP1251 - constant from utf8.class.php file
                $_UTFConverter = new utf8(CP1251);
            }
        }

        $sql = "UPDATE `Netshop_ImportSources` SET `last_update` = NOW() WHERE `catalogue_id` = {$catalogue_id}";
        $db->query($sql);

        // construct parser
        $nc_netshop_cml2parser = new nc_netshop_cml2parser($db, $_UTFConverter, $source_id, $catalogue_id, $_file, true);

        // init parser if not cached
        if (!$nc_netshop_cml2parser->cache_data_exist()) {
            // get orders data
            $nc_netshop_cml2parser->get_orders_data();
        }

        // import orders
        $nc_netshop_cml2parser->import_orders_data();

        if ($nc_netshop_cml2parser->everything_clear) {
            // count cached data
            $cache_count = $nc_netshop_cml2parser->cache_data_count();
            // erase cached data
            $cache_clear = $nc_netshop_cml2parser->cache_data_destroy();
        }

        unlink($import_path . $_file);
    }

    echo "success";
    exit;
}

if ($intercept) {
    echo "success";
    exit;
}

/**
 * STEP 4: import file(s)
 */
if ($_GET['mode'] == 'import' && $_GET['type'] == 'catalog') {

    @set_time_limit(0);

    include_once($INCLUDE_FOLDER . "index.php");
    require_once($MODULE_FOLDER . "netshop/import/nc_netshop_cml2parser.class.php");

    global $_UTFConverter;
    if (!$_UTFConverter) {
        // set variable
        $_UTFConverter = false;
        // allow_call_time_pass_reference need in php.ini for utf8 class, check before construct!
        if (!(extension_loaded("mbstring") || extension_loaded("iconv"))) {
            include_once($INCLUDE_FOLDER . "lib/utf8/utf8.class.php");
            // CP1251 - constant from utf8.class.php file
            $_UTFConverter = new utf8(CP1251);
        }
    }

    switch (nc_netshop_get_cml_file_type($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename'])) {
        case 'import.xml':
            if (false && $zip) {
                // zipped file
                $zip_file = $_GET['type'] . '-' . $_COOKIE[$cookie] . '.zip';

                // init unzip
                $zip = new ZipArchive;
                // open zip
                $res = $zip->open($import_path . $zip_file);
                // extract zip
                if ($res === TRUE) {
                    $zip->extractTo($import_path);
                    $zip->close();
                    // log message
                    file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] unzip OK' . PHP_EOL, FILE_APPEND);
                    // remove file
                    unlink($import_path . $zip_file);
                } else {
                    // log message
                    file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] unzip FAIL' . PHP_EOL, FILE_APPEND);
                    // halt
                    exit;
                }
            } else {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] process "' . $_GET['filename'] . '"' . PHP_EOL, FILE_APPEND);
            }
            // cml2 class
            require_once($MODULE_FOLDER . "netshop/import/cml2.class.php");
            $cml2 = new cml2();

            // get catalog properties
            $catalog_properties = $cml2->get_catalog_properties($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename']);

            // source data
            $external_id = trim($catalog_properties['ИдКлассификатора'] . " " . $catalog_properties['Ид']);

            // log info
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] classifier "' . $catalog_properties['ИдКлассификатора'] . '"' . PHP_EOL, FILE_APPEND);
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] catalog "' . $catalog_properties['Ид'] . '"' . PHP_EOL, FILE_APPEND);

            // shop data
            if ($source_id) {
                $source_data = $db->get_row("SELECT *
                                FROM `Netshop_ImportSources`
                                WHERE `source_id` = '{$source_id}'", ARRAY_A);

                $sql = "UPDATE `Netshop_ImportSources` SET `external_id` = '' WHERE `source_id` = '{$source_id}'";
                $db->query($sql);
            } else {
                $source_data = $db->get_row("SELECT *
                                FROM `Netshop_ImportSources`
                                WHERE `external_id` = '" . $db->escape($external_id) . "'", ARRAY_A);
            }

            if (!empty($source_data)) {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] source "' . $source_data['name'] . '"' . PHP_EOL, FILE_APPEND);
            } else {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] SOURCE NOT FOUND' . PHP_EOL, FILE_APPEND);
                // status message
                echo "failure" . PHP_EOL;
                echo "SOURCE NOT FOUND";
                // halt
                exit;
            }

            $sql = "UPDATE `Netshop_ImportSources` SET `last_update` = NOW() WHERE `source_id` = {$source_data['source_id']}";
            $db->query($sql);

            // construct parser
            $nc_netshop_cml2parser = new nc_netshop_cml2parser($db, $_UTFConverter, $source_data['source_id'], $source_data['catalogue_id'], 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename'], true);

            // init parser if not cached
            if (!$nc_netshop_cml2parser->cache_data_exist()) {
                // get classifier data
                $nc_netshop_cml2parser->get_classifier_data();

                // get catalogue data & check source
                if ($nc_netshop_cml2parser->get_catalogue_data()) {
                    // check actual catalog && update source
                    if (!$nc_netshop_cml2parser->update_sources()) exit;
                }
            }

            // directory structure
            $nc_netshop_cml2parser->import_classifier_data();
            // import commodities
            $nc_netshop_cml2parser->import_catalogue_data();

            // found unmapped elements
            if ($nc_netshop_cml2parser->not_mapped_sections) {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] found UNMAPPED groups' . PHP_EOL, FILE_APPEND);
            }
            if ($nc_netshop_cml2parser->not_mapped_fields) {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] found UNMAPPED catalog fields' . PHP_EOL, FILE_APPEND);
            }

            if ($nc_netshop_cml2parser->everything_clear) {
                // count cached data
                $cache_count = $nc_netshop_cml2parser->cache_data_count();
                // erase cached data
                $cache_clear = $nc_netshop_cml2parser->cache_data_destroy();
            }

            if ($source_data['delete_tmp_files']) {
                @unlink($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename']);
                $filename = preg_replace('/\.xml$|\d|_/i', '', $_GET['filename']);
                @nc_delete_dir($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $filename . '_files');
            }

            break;
        case 'offers.xml':
            // log info
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] process "' . $_GET['filename'] . '"' . PHP_EOL, FILE_APPEND);

            // cml2 class
            require_once($MODULE_FOLDER . "netshop/import/cml2.class.php");
            $cml2 = new cml2();

            // get offers properties
            $offers_properties = $cml2->get_offers_properties($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename']);

            // source data
            $external_id = trim($offers_properties['ИдКлассификатора'] . " " . $offers_properties['ИдКаталога']);

            // log info
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] classifier "' . $offers_properties['ИдКлассификатора'] . '"' . PHP_EOL, FILE_APPEND);
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] catalog "' . $offers_properties['ИдКаталога'] . '"' . PHP_EOL, FILE_APPEND);

            // shop data
            if ($source_id) {
                $source_data = $db->get_row("SELECT *
                                FROM `Netshop_ImportSources`
                                WHERE `source_id` = '{$source_id}'", ARRAY_A);
            } else {
                $source_data = $db->get_row("SELECT *
                                FROM `Netshop_ImportSources`
                                WHERE `external_id` = '" . $db->escape($external_id) . "'", ARRAY_A);
            }

            if (!empty($source_data)) {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] source "' . $source_data['name'] . '"' . PHP_EOL, FILE_APPEND);
            } else {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] SOURCE NOT FOUND' . PHP_EOL, FILE_APPEND);
                // status message
                echo "failure" . PHP_EOL;
                echo "SOURCE NOT FOUND";
                // halt
                exit;
            }

            $sql = "UPDATE `Netshop_ImportSources` SET `last_update` = NOW() WHERE `source_id` = {$source_data['source_id']}";
            $db->query($sql);

            // construct parser
            $nc_netshop_cml2parser = new nc_netshop_cml2parser($db, $_UTFConverter, $source_data['source_id'], $source_data['catalogue_id'], 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename'], true);

            // init parser if not cached
            if (!$nc_netshop_cml2parser->cache_data_exist()) {
                // get catalogue data & check source
                #if ( $nc_netshop_cml2parser->get_catalogue_data() ) {
                #	// check actual catalog && update source
                #	if ( !$nc_netshop_cml2parser->update_sources() ) exit;
                #}
                // get offers data
                $nc_netshop_cml2parser->get_offers_data();
            }

            // import offers
            $nc_netshop_cml2parser->import_offers_data();

            // if not mapping elements - show dialog
            if ($nc_netshop_cml2parser->not_mapped_packets) {
                // log info
                file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] UNMAPPED offers fields' . PHP_EOL, FILE_APPEND);
            }

            if ($nc_netshop_cml2parser->everything_clear) {
                // count cached data
                $cache_count = $nc_netshop_cml2parser->cache_data_count();
                // erase cached data
                $cache_clear = $nc_netshop_cml2parser->cache_data_destroy();
            }

            // log info
            file_put_contents($log_file, $date . ' - [' . $_GET['type'] . '] ' . $_GET['filename'] . PHP_EOL, FILE_APPEND);

            if ($source_data['delete_tmp_files']) {
                @unlink($import_path . 'catalog-' . $_COOKIE[$cookie] . '-' . $_GET['filename']);
            }
            break;
    }

    // status message
    echo "success";
}

/**
 * STEP X: sales
 */
if ($_GET['mode'] == 'query' && $_GET['type'] == 'sale') {

    // make user's undivine
    @ignore_user_abort(true);
    include_once($MODULE_FOLDER . "/netshop/function.inc.php");

    @set_time_limit(0);

    // system superior object
    $nc_core = nc_Core::get_object();

    if (!function_exists('xmlspecialchars')):

        function xmlspecialchars($text) {
            return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
        }

    endif;

    $order_table = false;
    $currency_table = false;
    $payment_methods_table = false;

    if ($netshop->is_netshop_v1_in_use()) {
        $MODULE_VARS = $nc_core->modules->get_module_vars();
        $order_table = $MODULE_VARS['netshop']['ORDER_TABLE'];
        $export_orders_status = $MODULE_VARS['netshop']['1C_EXPORT_ORDERS_STATUS'];
        $prev_orders_sum_status_id = $MODULE_VARS['netshop']['PREV_ORDERS_SUM_STATUS_ID'];
        $currency_table = (int)$MODULE_VARS['netshop']['CURRENCY_RATES_TABLE'];
        $payment_methods_table = (int)$MODULE_VARS['netshop']['PAYMENT_METHODS_TABLE'];
    } else {
        $order_table = $netshop->get_setting('OrderComponentID');
        $export_orders_status = $netshop->get_setting('1cExportOrdersStatusID');
        $prev_orders_sum_status_id = $netshop->get_setting('PrevOrdersSumStatusID');
    }

    $where_status = ($export_orders_status ? "AND m.`Status` IN (" . $export_orders_status . ")" : "");
    $sql = "SELECT DISTINCT og.`Order_ID` " .
        "FROM `Netshop_OrderGoods` as og, `Message{$order_table}` as m " .
        "WHERE og.`Order_Component_ID`={$order_table} AND og.`Order_ID`=m.`Message_ID` " .
        $where_status .
        " ORDER BY og.`Order_ID`";
    $orders_arr = $db->get_col($sql);

    if (!($orders_arr)) {
        // status message
        // echo "failure" . PHP_EOL;
        // echo "NO ORDERS";
        exit;
    }

    if ($source_id) {
        $sources = array($source_id);
    } else {
        $sql = "SELECT `source_id` FROM `Netshop_ImportSources` WHERE `catalogue_id` = {$catalogue_id}";
        $sources = (array)$db->get_results($sql, ARRAY_A);
    }

    $map_id_fields = array();
    foreach ($sources as $source) {
        $source = (int)$source['source_id'];
        $sql = "SELECT `value` FROM `Netshop_ImportMap` WHERE `source_id` = {$source} AND `source_string` = 'Ид' LIMIT 1";
        $field_id = (int)$db->get_var($sql);

        if ($field_id) {
            $sql = "SELECT `Field_Name`, `Class_ID` FROM `Field` WHERE `Field_ID` = {$field_id}";
            $field = $db->get_row($sql, ARRAY_A);
            if ($field) {
                $map_id_fields[] = $field;
            }
        }
    }

    // set headers
    header("Content-type: text/xml; charset=windows-1251");

    ob_start();

    echo '<?xml version="1.0" encoding="windows-1251"?>' . PHP_EOL;
    echo '<КоммерческаяИнформация ВерсияСхемы="2.07" ДатаФормирования="' . date("Y-m-d") . '">' . PHP_EOL;

    foreach ($orders_arr as $order_id) {
        $order = $netshop->load_order($order_id);
        if (!$order) {
            continue;
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

        $order_timestamp = timestamp($order["Created"]);
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
            $payment_method_obj = new nc_netshop_payment_method();
            try {
                $payment_method_obj->load($payment_method);
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
    $buffer = ob_get_contents();
    ob_end_clean();
    echo $nc_core->utf8->utf2win($buffer);
}

/**
 * STEP X: get_catalog
 */
if ($_GET['mode'] == 'query' && $_GET['type'] == 'get_catalog') {
    $step_file = $GLOBALS['TMP_FOLDER'] . '1c_get_catalog_' . $_COOKIE['nc-import-cookie'];
    if (!file_exists($step_file)) {
        $step = 1;
        file_put_contents($step_file, 1);
    } else {
        $step = (int)file_get_contents($step_file);
    }

    if ($step == 1) {
        @ignore_user_abort(true);
        @set_time_limit(0);

        include_once($MODULE_FOLDER . "/netshop/function.inc.php");

        if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
            require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
        } else {
            require_once($MODULE_FOLDER . "netshop/en.lang.php");
        }

        //get source_id
        if ($source_id) {
            $use_source_id = $source_id;
        } else {
            $sql = "SELECT `source_id` FROM `Netshop_ImportSources` WHERE `catalogue_id` = {$catalogue_id} ORDER BY `source_id` DESC LIMIT 1";
            $use_source_id = (int)$db->get_var($sql);
        }

        if (!$use_source_id) {
            echo "failure" . PHP_EOL;
            echo "wrong source";
            unlink($step_file);
            exit(0);
        }

        require_once($MODULE_FOLDER . "/netshop/export/cml2_catalog.inc.php");
        $CML2_Catalog_Export = new CML2_Catalog_Export($use_source_id, $nc_core);
        $CML2_Catalog_Export->export();

        file_put_contents($step_file, 2);
    } else if ($step == 2) {
        echo 'finished=yes';
        unlink($step_file);
    }
}

/**
 * STEP X: sales - confirmation
 */
if ($_GET['mode'] == 'success' && $_GET['type'] == 'sale') {
    // status message
    echo "success";
}

function nc_netshop_get_cml_file_type($filename) {
    if (!file_exists($filename)) return '';

    $handle = @fopen($filename, "r");

    if (!$handle) return '';

    $type = '';

    do {
        $buffer = trim(fgets($handle, 4096));
        if (strpos($buffer, '<Каталог') === 0) {
            $type = 'import.xml';
            break;
        } else if (strpos($buffer, '<ПакетПредложений') === 0) {
            $type = 'offers.xml';
            break;
        }
    } while (!feof($handle));

    // close file descriptor
    fclose($handle);

    return $type;
}