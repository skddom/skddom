<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
//==================== Получаем всякие полезные переменные ==================
$apikey = $nc_core->get_settings('apikey', 'sitesecure');
$email = $nc_core->get_settings('email', 'sitesecure');
if ($_REQUEST['alert']) {
    $alerts_request = sitesecure_Request("alerts/" . $_REQUEST['alert']);
} else {
    $alerts_request = sitesecure_Request('alerts');
}
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

//Проверяем, что есть сайты, удовлетворяющие требованиям вывода на этой странице
if (!$_REQUEST['alert']) {
    $isSites = 0;
    foreach ($alerts_request->websites as $site => $data) {
        if ($data->status != 'no-alerts' && $data->status != 'has-alerts') {
            continue;
        }
        $isSites = 1;
    }
    if (!$isSites) {
        //В системе нет ни одного сайта
        nc_print_status(SKYLAB_MODULE_SITESECURE_NO_ACTIVE_WEBSITES . ". <a style=\"vertical-align: top;\" href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php?view=main\">" . SKYLAB_MODULE_SITESECURE_NO_ACTIVE_WEBSITES_DASHBOARD . "</a>.", "error");
        $exit = true;
    }
}
//==================== Закончили обработку исключений =======================


//=== Если все сработало хорошо, то отрабатываем основные действия страницы =
if (!$exit && !$_REQUEST['alert']) {
    //Выводим select со списком всех сайтов, подходящих по условию
    //Если сайт не задан, то выбираем первый попавшийся и показываем инфу по нему
    echo "<form id=\"website_form\" action=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php\" method=\"get\">";
    echo "<input type=\"hidden\" name=\"view\" value=\"alerts\">";
    echo "<select id=\"website\" name=\"website\">";
    foreach ($alerts_request->websites as $site => $data) {
        if ($data->status != 'no-alerts' && $data->status != 'has-alerts') {
            continue;
        }
        if (!$website) {
            $website = $site;
        }
        echo "<option " . ($site == $website ? 'selected' : '') . " value=\"" . $site . "\">" . $site . "</option>";
    }
    echo "</select>";
    echo "</form>";
    echo "<script type=\"text/javascript\"> $nc(function() { $nc(\"#website\").change(function() { $nc(\"#website_form\").submit(); } ); });</script>";


    foreach ($alerts_request->websites as $website_name => $website_data) {
        if ($website_name != $website) {
            continue;
        }

        echo "<h2>" . $website_name . "</h2>";
        if (!empty ($website_data->unsolved_alerts)) //Выводим нерешенные проблемы, если такие есть
        {

            echo "<table class=\"nc-table\" width=\"100%\"><thead><th width=\"150\">" . SKYLAB_MODULE_SITESECURE_ALERTS_DATE_AND_TIME . "</th><th>" . SKYLAB_MODULE_SITESECURE_ALERTS_NAME . "</th><th></th></thead><tbody>";
            foreach ($website_data->unsolved_alerts as $alert) {
                echo "<tr>";
                echo "<td class=\"nc-text-center nc--blue\">" . $alert->time . "</td>";
                echo "<td><a href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php?view=alerts&alert=" . $alert->id . "\">" . $alert->title . "</a></td>";
                echo "<td>" . $alert->short_description . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

        }
        if ($website_data->status == "no-alerts") {
            nc_print_status(SKYLAB_MODULE_SITESECURE_ALERTS_NO_UNRESOLVED_ALERTS . ". <a style=\"vertical-align: top;\" href=\"" . $website_data->alerts_url . "\" target=\"_blank\">" . SKYLAB_MODULE_SITESECURE_ALERTS_VIEW_ALL_ALERTS . "</a>", "ok");
        }
        if ($website_data->status == "has-alerts") {
            nc_print_status(SKYLAB_MODULE_SITESECURE_ALERTS_LIST_OF_UNRESOLVED_ALERTS . ". <a style=\"vertical-align: top;\" href=\"" . $website_data->alerts_url . "\" target=\"_blank\">" . SKYLAB_MODULE_SITESECURE_ALERTS_VIEW_ALL_ALERTS . "</a>", "info");
        }
    }
}

if (!$exit && $_REQUEST['alert']) {
    echo "<div style=\"word-wrap:break-word\">" . $alerts_request->html . "</div><div style=\"height: 30px\"><!-- --></div>";
    echo "<form id=\"ToListForm\" action=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php\" method=\"get\">";
    echo "<input type=\"hidden\" name=\"view\" value=\"alerts\">";
    echo "</form>";
    $UI_CONFIG->actionButtons[] = array(
        "id" => "submit",
        "caption" => SKYLAB_MODULE_SITESECURE_ALERTS_TOLIST,
        "action" => "mainView.submitIframeForm('ToListForm')"
    );
}

