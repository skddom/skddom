<?php

require '../../no_header.inc.php';

/**
 * Exports constants required for the dummy condition editor as JSON
 */

$prefix = "NETCAT_MODULE_NETSHOP_SIMPLE_CONDITION_";
$constants = array(
    'NOTICE',
    'CART_TOTALPRICE_FROM',
    'CART_TOTALPRICE_TO'
);

$constants_to_export = array();
foreach ($constants as $short_key) {
    $constants_to_export[$short_key] = constant($prefix . $short_key);
}

header("Content-Type: application/javascript");

echo "nc_netshop_condition_messages = ", nc_array_json($constants_to_export), ";";
