<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
//==================== Получаем всякие полезные переменные ==================
$apikey = $nc_core->get_settings('apikey', 'sitesecure');
$email = $nc_core->get_settings('email', 'sitesecure');
$alerts_request = sitesecure_Request('status');
$domains = sitesecure_Domains();
$exit = false;

//==================== Обработка исключений =================================
if ($alerts_request->status == 'error') {
    //Сюда попадаем, если совсем что-то пошло не так
    nc_print_status($alerts_request->message, "error");
    $exit = true;
}
if (!$apikey || !$email) {
    //Не выдан API-ключ
    nc_print_status(SKYLAB_MODULE_SITESECURE_EXCEPTION_NO_SETTINGS_TEXT . ". <a style=\"vertical-align: top;\" href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php?view=settings\">" . SKYLAB_MODULE_SITESECURE_EXCEPTION_NO_SETTINGS_LINK . "</a>.", "error");
    $exit = true;
}
if (empty ($domains)) {
    //В системе нет ни одного сайта
    nc_print_status(SKYLAB_MODULE_SITESECURE_NO_WEBSITES . ". <a style=\"vertical-align: top;\" href=\"" . $HTTP_ROOT_PATH . "/admin/catalogue/index.php?phase=2&type=1\" onclick=\"urlDispatcher.load('site.add()')\">" . SKYLAB_MODULE_SITESECURE_ADD_WEBSITE . "</a>.", "error");
    $exit = true;
}
//==================== Закончили обработку исключений =======================


//=== Если все сработало хорошо, то отрабатываем основные действия страницы =
if (!$exit) {
    if (isset($_REQUEST['confirm'])) {
        // Обрабатываем запрос на подтверждение прав на сайт
        $website_to_auth = $_REQUEST['confirm'];
        $request = sitesecure_PostRequest('authorize', array('website' => $website_to_auth));
        nc_print_status($request->message, "ok");
    }

    if (empty ($alerts_request->websites)) {
        $alerts_request->websites = array();
    }

    $bg_colors = array('default' => "#1a87c2", 'no-alerts' => 'green', 'has-alerts' => 'red');
    echo "<table class=\"nc-table\" width=\"100%\"><thead><th></th><th>Сайт</th><th>Информация</th><th>Действие</th></thead><tbody>";
    foreach ($alerts_request->websites as $website => $info) {
        echo "<tr>";
        echo "<td class=\"nc-text-center nc--blue\"><img src=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/images/" . $info->status . ".png\" alt=\"" . $info->status . "\" /></td>";
        echo "<td>" . $info->name . "</td>";

        //Выводим статус сайта
        if ($info->status == 'no-alerts' || $info->status == 'has-alerts') {
            echo "<td>" . $info->plan . "</td>"; // — <a href=\"".$info->settings_url."\" target=\"_blank\">".SKYLAB_MODULE_SITESECURE_ADMIN_SETUP."</a>
        } else {
            echo "<td>" . $info->info . "</td>";
        }

        //Выводим действия над сайтом
        if ($info->status == "norights") {
            echo "<td><a class=\"ss_button\" style=\"background-color: " . (isset($bg_colors[$info->status]) ? $bg_colors[$info->status] : $bg_colors['default']) . ";\" href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/" . "admin.php?view=main&confirm=" . $website . "\">" . $info->caption . "</a></td>";
        } else if ($info->status == "notfound") {
            echo "<td><a class=\"ss_button\" style=\"background-color: " . (isset($bg_colors[$info->status]) ? $bg_colors[$info->status] : $bg_colors['default']) . ";\" href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/" . "admin.php?view=create&website=" . $website . "\">" . $info->caption . "</a></td>";
        } else {
            $path = $HTTP_ROOT_PATH . "modules/sitesecure/";
            if ($info->status == 'no-alerts') {
                $path .= "admin.php?view=seal&website=" . $website;
            }
            if ($info->status == 'has-alerts') {
                $path .= "admin.php?view=alerts&website=" . $website;
            }
            echo "<td><a class=\"ss_button\" data-status=\"" . $info->status . "\" style=\"background-color: " . (isset($bg_colors[$info->status]) ? $bg_colors[$info->status] : $bg_colors['default']) . ";\" href=\"" . $path . "\">" . $info->caption . "</a></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
