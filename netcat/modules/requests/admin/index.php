<?php
$default_controller = 'direct';
$default_action = 'index';
//-------------------------------------------------------------------------

require_once './no_header.inc.php';
require_once nc_core('SYSTEM_FOLDER') . '/admin/ui/components/nc_ui_controller.class.php';

//-------------------------------------------------------------------------
$controller_name = nc_core('input')->fetch_post_get('controller');
$action_name = nc_core('input')->fetch_post_get('action');

// Проверяем значение параметра controller, т.к. дальше будем использовать его
// для доступа к файловой системе
if (!preg_match("/^[\w]+$/", $controller_name)) {
    die('Incorrect controller name');
}

/**
 * Если параметр controller содержит знак подчёркивания, то первая часть до подчеркивания
 * определяет папку, в которой находятся шаблоны (views).
 */
$controller_class = "nc_requests_" . $controller_name . "_admin_controller";
$controller_name_parts = explode("_", $controller_name);
$view_path = dirname(__FILE__) . "/views/" . $controller_name_parts[0];

/** @var nc_ui_controller $controller */
$controller = new $controller_class($view_path);
echo $controller->execute($action_name);
