<?php
/**
 * Input GET parameters:
 *   catalogue - current catalogue id (required)
 *   key - secret key md5 hash (required)
 */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require($INCLUDE_FOLDER . "index.php");
require_once($MODULE_FOLDER . "netshop/function.inc.php");

$input = $nc_core->input;

$catalogue_id = $input->fetch_get('catalogue');
$key = $input->fetch_get('key');

if (!$catalogue_id || !$key) {
    echo sprintf(NETCAT_MODULE_NETSHOP_CURRENCY_VAR_NOT_SET, '');
    exit;
}

$detected_language = $nc_core->lang->detect_lang(1);
$language_file = is_file($MODULE_FOLDER . "netshop/" . $detected_language . ".lang.php") ?
    $MODULE_FOLDER . "netshop/" . $detected_language . ".lang.php" :
    $MODULE_FOLDER . "netshop/en.lang.php";
require_once($language_file);

$db = $nc_core->db;
$netshop = nc_netshop::get_instance($catalogue_id);

$is_netshop_v1_in_use = $netshop->is_netshop_v1_in_use();

$currencies = array();

if ($is_netshop_v1_in_use) {
    $MODULE_VARS = $nc_core->modules->get_module_vars();

    if ($key != md5($MODULE_VARS['netshop']['SECRET_KEY'])) {
        die('Wrong key');
    }

    $currency_rates_table = $MODULE_VARS['netshop']['CURRENCY_RATES_TABLE'];
    $sql = "SELECT c.ShopCurrency_ID, c.ShopCurrency_Name FROM Classificator_ShopCurrency as c " .
        "LEFT JOIN  Message{$currency_rates_table} as m ON (c.ShopCurrency_ID=m.Currency) " .
        "WHERE m.Rate IS NULL";
    $currencies_query = (array)$db->get_results($sql, ARRAY_A);

    foreach ($currencies_query as $currency) {
        $currencies[$currency['ShopCurrency_ID']] = $currency['ShopCurrency_Name'];
    }

    $shop_table = $MODULE_VARS['netshop']['SHOP_TABLE'];
    $official_rates_table = $MODULE_VARS['netshop']['OFFICIAL_RATES_TABLE'];
    $shop_id = GetSubdivisionByType($shop_table, "Subdivision_ID", $catalogue_id);
    $sql = "SELECT c.Sub_Class_ID FROM Sub_Class as c, Subdivision as s " .
        "WHERE c.Class_ID = {$official_rates_table} " .
        "AND c.Subdivision_ID = {$shop_id} " .
        "AND c.Subdivision_ID = s.Subdivision_ID LIMIT 1";
    $rates_template_id = $db->get_var($sql);
} else {
    if ($key != $netshop->get_setting('SecretKey')) { die('Wrong key'); }
    $currencies = $netshop->get_setting('Currencies');
    foreach ($netshop->get_setting('CurrencyDetails') as $currency_id => $rate) {
        if ($currencies[$rate['Currency_ID']] && $rate['Rate']) {
            unset($currencies[$rate['Currency_ID']]);
        }
    }
}


if (!count($currencies)) {
    echo NETCAT_MODULE_NETSHOP_CURRENCY_NOTHING_TO_FETCH;
    exit;
}

$rates_source = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=%d%%2F%m%%2F%Y";
$rates_source = strftime($rates_source);

if (function_exists('curl_version')) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $rates_source);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $file = curl_exec($curl);
    $curl_error = curl_errno($curl);
    curl_close($curl);
} else {
    $curl_error = 0;
    $file = @file_get_contents($rates_source);
}

if (!$file || $curl_error) {
    echo NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_NOTFOUND;
    exit;
}

$xml = simplexml_load_string($file);
if (!$xml || !isset($xml->Valute)) {
    echo NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_PARSING_ERROR;
    exit;
}

$source_date = preg_split('![/.-]!', $xml->attributes()->Date);
$source_date = $source_date[2] . '-' . $source_date[1] . '-' . $source_date[0];

foreach ($xml->Valute as $valute) {
    if (in_array($valute->CharCode, $currencies)) {
        $key = array_keys($currencies, $valute->CharCode);
        $value = (double)str_replace(',', '.', $valute->Value);
        $nominal = (double)str_replace(',', '.', $valute->Nominal);
        $rates[$key[0]] = $value / $nominal;
    }
}

foreach ($rates as $id => $rate) {
    if ($is_netshop_v1_in_use) {
        $rate = str_replace(',', '.', $rate);
        $where = "WHERE Date = '{$source_date}' AND Currency = {$id} AND Sub_Class_ID = {$rates_template_id}";

        $sql = "SELECT Message_ID FROM Message{$official_rates_table} {$where} LIMIT 1";
        if ($db->get_var($sql)) {
            $sql = "UPDATE Message{$official_rates_table} SET Rate = {$rate} {$where}";
            $db->query($sql);
        } else {
            $sql = "INSERT Message{$official_rates_table} SET Date = '{$source_date}', Currency = {$id}, Rate = {$rate}, " .
                "Subdivision_ID = {$shop_id}, Sub_Class_ID = {$rates_template_id}";
            $db->query($sql);
        }
    } else {
        $query = new nc_netshop_officialrate_table();
        $query
            ->select()
            ->where('Catalogue_ID', '=', $catalogue_id)
            ->where('Date', '=', $source_date)
            ->where('Currency', '=', $id);

        if ($query->get_row()) {
            $query = new nc_netshop_officialrate_table();
            $query
                ->set('Rate', str_replace(',', '.', $rate))
                ->where('Catalogue_ID', '=', $catalogue_id)
                ->where('Date', '=', $source_date)
                ->where('Currency', '=', $id)
                ->update();
        } else {
            $priority_query = new nc_netshop_officialrate_table();
            $priority_query->select('MAX(Priority) AS Priority')->where('Catalogue_ID', '=', $catalogue_id)->where('Date', '=', $source_date);
            $priority = $priority_query->get_row();
            $priority = (int)$priority->Priority + 1;

            $query = new nc_netshop_officialrate_table();
            $query->set('Date', $source_date)
                ->set('Currency', $id)
                ->set('Rate', str_replace(',', '.', $rate))
                ->set('Catalogue_ID', $catalogue_id)
                ->set('Checked', 1)
                ->set('Priority', $priority)
                ->insert();
        }

    }
}

if ($is_netshop_v1_in_use) {
    $keep_rates_days = (int)$MODULE_VARS['netshop']['RATES_DAYS_TO_KEEP'];
    if (int($keep_rates_days)) {
        $sql = "DELETE FROM Message{$official_rates_table} WHERE Date <= DATE_SUB(CURDATE(),INTERVAL {$keep_rates_days} DAY)";
        $db->query($sql);
    }
} else {
    $keep_rates_days = (int)$netshop->get_setting('DaysToKeepCurrencyRates');
    if (int($keep_rates_days)) {
        $query = new nc_netshop_officialrate_table();
        $query->where("Date <= DATE_SUB(CURDATE(),INTERVAL {$keep_rates_days} DAY)")->delete();
    }
}


printf(NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_OK, join(", ", array_keys($rates)));