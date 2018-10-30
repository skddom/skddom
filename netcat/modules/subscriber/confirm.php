<?php

/* $Id: confirm.php 7302 2012-06-25 21:12:35Z alive $ */
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ROOT_FOLDER."connect_io.php");

if (is_file($MODULE_FOLDER."subscriber/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."subscriber/".MAIN_LANG.".lang.php");
    $modules_lang = "Russian";
} else {
    require_once($MODULE_FOLDER."subscriber/en.lang.php");
    $modules_lang = "English";
}
$MODULE_VARS = $nc_core->modules->load_env($modules_lang);

$nc_subscriber = nc_subscriber::get_object();
$res = $nc_subscriber->tools->get_subscribe_sub();
$catalogue = $res['Catalogue_ID'];
$sub = isset($_GET['sub']) ? $_GET['sub'] : $res['Subdivision_ID'];

unset($res);

require($INCLUDE_FOLDER."index.php");

$hash = $db->escape($nc_core->input->fetch_get_post('hash'));

$subsc = false;
if ($hash) {
    $subsc = $db->get_row("SELECT `ID`, `Status` FROM `Subscriber_Subscription` WHERE `Hash` = '".$hash."'", ARRAY_A);
}


if ($subsc) {
    // подтверждение подписки
    if ($subsc['Status'] == 'wait') {
        $nc_subscriber->subscription_confirm($subsc['ID']);
        $nc_text = $nc_subscriber->tools->get_settings('TextConfirm');
    } else { // удаление подписки
        $nc_subscriber->subscription_delete($subsc['ID']);
        $nc_text = $nc_subscriber->tools->get_settings('TextUnscribe');
    }
} else {
    $nc_text = $nc_subscriber->tools->get_settings('TextError');
}

unset($subsc);
unset($hash);


if ($File_Mode) {
    require_once $INCLUDE_FOLDER.'index_fs.inc.php';

    echo $template_header;
    eval(nc_check_eval("echo \"$nc_text\";"));
    echo $template_footer;
} else {
    eval(nc_check_eval("echo \"".$template_header."\";"));
    eval(nc_check_eval("echo \"$nc_text\";"));
    eval(nc_check_eval("echo \"".$template_footer."\";"));
}