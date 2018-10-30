<?php

require_once './no_header.inc.php';
$nc_core = nc_core::get_object();
require_once $nc_core->SYSTEM_FOLDER . '/admin/ui/components/nc_ui_controller.class.php';

$action_name = $nc_core->input->fetch_post_get('action') ?: 'show_settings';
$view_path = dirname(__FILE__) . "/views";

$controller = new nc_captcha_admin_controller($view_path);
echo $controller->execute($action_name);
