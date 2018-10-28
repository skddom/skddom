<?php

/* $Id: get_data.php 5946 2012-01-17 10:44:36Z denis $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");

if (!($perm->isSupervisor() || $perm->isGuest())) die("Please authorize");

$url = $nc_core->input->fetch_get('url');
$what = $nc_core->input->fetch_get('what');

if (!($url && $what)) die('Incorrect data!');

require_once("function.inc.php");

$site_auditor = new site_auditor($url);
$data = $site_auditor->get($what);

$result = "";
foreach (array("ok", "value", "href", "name") as $k) {
    $result.= $k.": '".str_replace("'", "\\'", $data[$k])."',";
}

if ($nc_core->NC_CHARSET && $nc_core->NC_CHARSET != 'utf-8') {
    $result = $nc_core->utf8->conv('utf-8', $nc_core->NC_CHARSET, $result);
}

print "<script type='text/javascript'>parent.print_audit_data('".$what."', {".$result."1:1});</script>";
?>