<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

$apikey = $nc_core->get_settings('apikey', 'sitesecure');
$email = $nc_core->get_settings('email', 'sitesecure');

if ($act == "save") {
    $settings = sitesecure_PostRequest('create', array('website' => $_REQUEST['site'], 'api_key' => $apikey, 'email' => $_REQUEST['email'], 'password' => $_REQUEST['password']));
    if ($settings->status == "success") {
        nc_print_status($settings->message, "ok");
    } else {
        nc_print_status($settings->message, "error");
    }

    echo "<form id=\"ToSitesForm\" action=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php\" method=\"get\">";
    echo "<input type=\"hidden\" name=\"view\" value=\"main\">";
    echo "</form>";
    $UI_CONFIG->actionButtons[] = array(
        "id" => "submit",
        "caption" => SKYLAB_MODULE_SITESECURE_CREATE_TOLIST,
        "action" => "mainView.submitIframeForm('ToSitesForm')"
    );
} else if ($apikey && $email) {
    //Если не задан сайт, возвращаем пользователя на страницу со списком всех сайтов
    if (!$website) {
        header("Location: " . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php");
    }

    echo "<form method='post' action='admin.php' id='AddSiteForm' style='padding:0; margin:0;'>";
    echo "<input type='hidden' name='view'  value='create' />";
    echo "<input type='hidden' name='act'   value='save' />";
    echo "<input type='hidden' name='site'  value='" . $website . "' />";
    echo "<input type='hidden' name='email' value='" . $email . "' />";
    echo "<fieldset>";
    echo "<legend>" . SKYLAB_MODULE_SITESECURE_CREATE_HEADER . " " . $website . "</legend>";
    echo "<table id=\"systemSettings\"><tbody>";
    echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_CREATE_EMAIL . ":&nbsp;&nbsp;</td><td><b>" . $email . "</b></td></tr>";
    echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_CREATE_APIKEY . ":&nbsp;&nbsp;</td><td><b>" . $apikey . "</b></td></tr>";
    echo "<tr><td>" . SKYLAB_MODULE_SITESECURE_CREATE_PASSWORD . ":&nbsp;&nbsp;</td><td><input type=\"text\" size=\"50\" name=\"password\"><br>" . SKYLAB_MODULE_SITESECURE_CREATE_DESCRIPTION . "</td></tr>";
    echo "</tbody></table>";
    echo "</fieldset>";
    echo "</form>";

    $UI_CONFIG->actionButtons[] = array(
        "id" => "submit",
        "caption" => SKYLAB_MODULE_SITESECURE_CREATE_SAVE,
        "action" => "mainView.submitIframeForm('AddSiteForm')"
    );
}
