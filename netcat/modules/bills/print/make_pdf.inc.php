<?php

if (!defined('NC_BILLS_CONTROLLER_TYPE')) {
    die;
}

$bills_admin_folder = realpath('../admin/');

require_once "$bills_admin_folder/no_header.inc.php";
require_once nc_core('SYSTEM_FOLDER') . '/admin/ui/components/nc_ui_controller.class.php';

//-------------------------------------------------------------------------

$controller_class = "nc_bills_" . NC_BILLS_CONTROLLER_TYPE . "_admin_controller";
$view_path = $bills_admin_folder . "/views/" . NC_BILLS_CONTROLLER_TYPE;

/** @var nc_ui_controller $controller */
$controller = new $controller_class($view_path);
echo $controller->execute('print');
