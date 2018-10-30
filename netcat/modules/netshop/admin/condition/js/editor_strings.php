<?php

require '../../no_header.inc.php';

/**
 * Exports all constants which names start with "NETCAT_MODULE_NETSHOP_CONDITION_" as JSON
 */

$prefix = "NETCAT_MODULE_NETSHOP_CONDITION_";
$prefix_length = strlen($prefix);
$all_constants = get_defined_constants(true);

$constants_to_export = array();
foreach ($all_constants['user'] as $key => $value) {
    if (substr($key, 0, $prefix_length) != $prefix) { continue; }
    $short_key = substr($key, $prefix_length);
    $constants_to_export[$short_key] = $value;
}

header("Content-Type: application/javascript");

echo "nc_netshop_condition_messages = ", nc_array_json($constants_to_export), ";";
