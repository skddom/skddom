<?php

@set_time_limit(0);

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once($NETCAT_FOLDER."vars.inc.php");
require_once($MODULE_FOLDER."netshop/import/nc_netshop_cml2parser.class.php");
include_once($INCLUDE_FOLDER."index.php");
if (!function_exists('EndHtml')) die("Error");
// clean buffer
while (@ob_get_level() && @ob_end_flush()) {
    continue;
}
flush();

if (!$quite) {
    echo "<script>\r\n";
    echo "function nc_netshop_import_progress(p, prefix) {\r\n";
    echo "  try {\r\n";
    echo "    document.getElementById(prefix + '_line').style.width = ( 4.2 * Math.floor(p) ) + 'px';\r\n";
    echo "    document.getElementById(prefix + '_text').innerHTML = p + '%';\r\n";
    echo "    // change procent text color (optional)\r\n";
    echo "  }\r\n";
    echo "  catch (e) {}\r\n";
    echo "}\r\n";
    echo "</script>\r\n";
}

global $_UTFConverter;
if (!$_UTFConverter) {
    // set variable
    $_UTFConverter = false;
    // allow_call_time_pass_reference need in php.ini for utf8 class, check before construct!
    if (!( extension_loaded("mbstring") || extension_loaded("iconv") )) {
        include_once($INCLUDE_FOLDER."lib/utf8/utf8.class.php");
        // CP1251 - constant from utf8.class.php file
        $_UTFConverter = new utf8(CP1251);
    }
}

// construct parser
$nc_netshop_cml2parser = new nc_netshop_cml2parser($db, $_UTFConverter, $source_id, $catalogue_id, $filename);

// init parser if not cached
if (!$nc_netshop_cml2parser->cache_data_exist()) {
    // get classifier data
    $nc_netshop_cml2parser->get_classifier_data();

    // get catalogue data & check source
    if ($nc_netshop_cml2parser->get_catalogue_data()) {
        // check actual catalog && update source
        if (!$nc_netshop_cml2parser->update_sources()) {
            if (!$quite)
                    nc_print_status(NETCAT_MODULE_NETSHOP_IMPORT_SOURCE_WRONG, 'error');
            exit;
        }
    }

    // get offers data
    $nc_netshop_cml2parser->get_offers_data();

    // get orders data
    $nc_netshop_cml2parser->get_orders_data();
}

//update owner data
$owner_data = $nc_netshop_cml2parser->get_owner_data();
if ($owner_data) {
    $owner = array(
        'id' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_ID]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_ID] : '',
        'name' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_NAME]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_NAME] : '',
        'official_name' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_OFFICIAL_NAME]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_OFFICIAL_NAME] : '',
        'address' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_ADDRESS][NETCAT_MODULE_NETSHOP_1C_ADDRESS_VIEW]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_ADDRESS][NETCAT_MODULE_NETSHOP_1C_ADDRESS_VIEW] : '',
        'inn' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_INN]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_INN] : '',
        'kpp' => isset($owner_data[NETCAT_MODULE_NETSHOP_1C_KPP]) ? $owner_data[NETCAT_MODULE_NETSHOP_1C_KPP] : '',
    );

    $owner = json_encode($owner);
    $owner = $db->prepare($owner);

    $sql = "UPDATE `Netshop_ImportSources` SET `owner` = '{$owner}' WHERE `source_id` = $source_id";
    $db->query($sql);
}

// directory structure
$nc_netshop_cml2parser->import_classifier_data();
// import commodities
$nc_netshop_cml2parser->import_catalogue_data();
// import offers
$nc_netshop_cml2parser->import_offers_data();
// import orders
$nc_netshop_cml2parser->import_orders_data();

// if not mapping elements - show dialog
if (!$quite) {
    if ($nc_netshop_cml2parser->not_mapped_sections)
            $nc_netshop_cml2parser->map_sections_dialog();
    if ($nc_netshop_cml2parser->not_mapped_fields)
            $nc_netshop_cml2parser->map_fields_dialog();
    if ($nc_netshop_cml2parser->not_mapped_stores)
            $nc_netshop_cml2parser->map_stores_dialog();
    if ($nc_netshop_cml2parser->not_mapped_characteristics)
        $nc_netshop_cml2parser->map_characteristics_dialog();
    if ($nc_netshop_cml2parser->not_mapped_packets)
            $nc_netshop_cml2parser->map_packets_dialog();
}

// simple and not reliable check whether it's time to create cache
if ($nc_netshop_cml2parser->everything_clear) {
    // count cached data
    $cache_count = $nc_netshop_cml2parser->cache_data_count();
    // erase cached data
    $cache_clear = $nc_netshop_cml2parser->cache_data_destroy();
    // if cached files not erased
    if (!$quite && ($cache_count - $cache_clear)) {
        nc_print_status(NETCAT_MODULE_NETSHOP_IMPORT_CACHE_CLEARED_PARTIAL, 'info');
    }
    // unlink main importing xml file
    if (file_exists($TMP_FOLDER.$filename)) {
        unlink($TMP_FOLDER.$filename);
    }

    $sql = "UPDATE `Netshop_ImportSources` SET `last_update` = NOW() WHERE `source_id` = $source_id";
    $db->query($sql);

    // complete information
    if (!$quite) {
        echo "<h3>".NETCAT_MODULE_NETSHOP_IMPORT_DONE.".</h3>";
        printf(NETCAT_MODULE_NETSHOP_IMPORT_1C8_LINK,
               nc_get_scheme() . '://' . $HTTP_HOST . $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/netshop/import/{$source_id}/1c8.php");
        EndHtml();
        exit;
    }
}
?>
