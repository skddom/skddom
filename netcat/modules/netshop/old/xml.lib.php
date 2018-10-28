<?php

if (!class_exists("nc_System")) die("Unable to load file.");
// ============================================================================
// XML-Related Functions
// ============================================================================

if (version_compare(PHP_VERSION, '5', '>=')) {
    require_once(nc_module_folder('netshop') . 'old/domxml-php4-to-php5.php');
}
require_once("utf8/utf8.php");

// xpath c преобразованием win->utf
function xpath(&$doc, $xpath) {
    $nc_core = nc_Core::get_object();
    $xpath_obj = xpath_new_context($doc);
    return (!$nc_core->NC_UNICODE ? xpath_eval($xpath_obj, $nc_core->utf8->win2utf($xpath)) : xpath_eval($xpath_obj, $xpath) );
}

// получить атрибут узла и сконвертировать его в cp1251
function xml_attr(&$node, $attr_name) {
    $nc_core = nc_Core::get_object();
    if (!is_object($node)) return false;
    //return nc_utf2win($node->get_attribute(nc_win2utf($attr_name)));
    return (!$nc_core->NC_UNICODE ? $nc_core->utf8->utf2win($node->get_attribute($nc_core->utf8->win2utf($attr_name))) : $node->get_attribute($attr_name) );
}

// ============================================================================
?>