<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");

$MODULE_VARS = $nc_core->modules->load_env();
require_once('commonxml.inc.php');

$rows = $db->get_results("SELECT Catalogue_ID, Domain FROM `Catalogue`", ARRAY_A);
if (count($rows) > 0) {
  foreach ($rows as $result) {
    $catalogue = $result['Catalogue_ID'];
    $netshop = nc_netshop::get_instance($catalogue);
    foreach (array('yandex', 'google', 'mail') as $place) {
      $export = new Common_Export_V1($netshop, $result['Domain']);
      $export->Export($place);
    }
  }
}
echo 'Done';