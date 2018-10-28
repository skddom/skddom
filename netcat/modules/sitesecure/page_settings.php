<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

$apikey = $nc_core->get_settings('apikey', 'sitesecure');
$email = $nc_core->get_settings('email', 'sitesecure');

if ($act == "save") {
    //Проверка валидности электронной почты
    if (!sitesecure_is_email($nc_core->input->fetch_get_post('email'))) {
        nc_print_status(SKYLAB_MODULE_SITESECURE_SETTINGS_NOVALID, "error");
    } else {
        $settings = sitesecure_PostRequest('register', array('email' => $nc_core->input->fetch_get_post('email'), 'license' => $nc_core->input->fetch_get_post('license')));
        if ($settings->status == 'success') {
            nc_print_status(SKYLAB_MODULE_SITESECURE_SETTINGS_YESKEY, "ok");

            //Записываем данные в настройки модуля
            $nc_core->set_settings('email', $nc_core->input->fetch_get_post('email'), 'sitesecure');
            $nc_core->set_settings('apikey', $settings->api_key, 'sitesecure');

            //Это нужно, чтобы сразу показать новые данные.
            $apikey = $settings->api_key;
            $email = $nc_core->input->fetch_get_post('email');
        }
    }
}

if (!$apikey) {
    nc_print_status(SKYLAB_MODULE_SITESECURE_CREATE_PLANS, 'info');
}

echo "<form method='post' action='admin.php' id='MainSettigsForm' style='padding:0; margin:0;'>";
echo "<input type='hidden' name='view'    value='settings' />";
echo "<input type='hidden' name='act'     value='save' />";
echo "<input type='hidden' name='license' value='" . $nc_core->get_settings('ProductNumber') . "' />";
echo "<fieldset>";
echo "<legend>" . SKYLAB_MODULE_SITESECURE_SETTINGS_LEGEND . "</legend>";
echo "<table id=\"systemSettings\"><tbody>";
echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_SETTINGS_EMAIL . ": </td><td><input type=\"text\" size=\"50\" name=\"email\" value=\"" . $email . "\"></td></tr>";
echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_SETTINGS_LICENSE . ": </td><td>" . $nc_core->get_settings('ProductNumber') . "</td></tr>";
echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_SETTINGS_APIKEY . ": </td><td>" . ($apikey ? $apikey : SKYLAB_MODULE_SITESECURE_SETTINGS_NOKEY) . "</td></tr>";
echo "</tbody></table>";
echo "</fieldset>";
echo "</form>";

$UI_CONFIG->actionButtons[] = array(
    "id" => "submit",
    "caption" => SKYLAB_MODULE_SITESECURE_SETTINGS_SAVE,
    "action" => "mainView.submitIframeForm('MainSettingsForm')"
);

function sitesecure_is_email($mail) {
    return preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $mail);
}
