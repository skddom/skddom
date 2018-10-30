<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
//==================== Получаем всякие полезные переменные ==================
$apikey = $nc_core->get_settings('apikey', 'sitesecure');
$email = $nc_core->get_settings('email', 'sitesecure');
$request = sitesecure_Request('seal');
$domains = sitesecure_Domains();
$exit = false;

//==================== Обработка исключений =================================
if ($request->status == 'error') {
    //Сюда попадаем, если совсем что-то пошло не так
    nc_print_status($request->message, "error");
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
$isSites = 0;
foreach ($request->websites as $site => $data) {
    if ($data->status != 'safe' && $data->status != 'unsafe') {
        continue;
    }
    $isSites = 1;
}
if (!$isSites) {
    //В системе нет ни одного сайта
    nc_print_status(SKYLAB_MODULE_SITESECURE_NO_ACTIVE_WEBSITES . ". <a style=\"vertical-align: top;\" href=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php?view=main\">" . SKYLAB_MODULE_SITESECURE_NO_ACTIVE_WEBSITES_DASHBOARD . "</a>.", "error");
    $exit = true;
}
//==================== Закончили обработку исключений =======================


//=== Если все сработало хорошо, то отрабатываем основные действия страницы =
if (!$exit) {
    //Выводим select со списком всех сайтов, подходящих по условию
    //Если сайт не задан, то выбираем первый попавшийся и показываем инфу по нему
    echo "<form id=\"website_form\" action=\"" . $HTTP_ROOT_PATH . "modules/sitesecure/admin.php\" method=\"get\">";
    echo "<input type=\"hidden\" name=\"view\" value=\"seal\">";
    echo "<select id=\"website\" name=\"website\">";
    foreach ($request->websites as $site => $data) {
        if ($data->status != 'safe' && $data->status != 'unsafe') {
            continue;
        }
        if (!$website) {
            $website = $site;
        }
        echo "<option " . ($site == $website ? 'selected' : '') . " value=\"" . $site . "\">" . $site . "</option>";
    }
    echo "</select>";
    echo "</form>";

    $info_message = '<div style="margin-left:36px">' . SKYLAB_MODULE_SITESECURE_SEAL_INFO . '</div>';
    nc_print_status($info_message, 'info');

    echo "<script type=\"text/javascript\"> $nc(function() { $nc(\"#website\").change(function() { $nc(\"#website_form\").submit(); } ); });</script>";

    foreach ($request->websites as $site => $data) {
        if ($website != $site) {
            continue;
        }

        if ($request->status == 'error') {
            nc_print_status($request->message, "error");
            break;
        }
        if ($data->status == 'unsafe') {
            nc_print_status($data->message, "error");
            break;
        }
        $active_color = $data->colors[0];

        echo "<h2 class=\"heading\">" . $site . "</h2>";
        echo "<p>" . SKYLAB_MODULE_SITESECURE_SEAL_BEFORE . "</p>";

        echo "<div class=\"hr\"><hr /></div>";
        echo "<div class=\"margin-btm-20\" id=\"seal_color\">";
        echo "<h3>" . SKYLAB_MODULE_SITESECURE_SEAL_DESIGN . "</h3>";
        $height = -40;
        foreach ($data->colors as $i => $color) {
            echo "<div style=\"float: left; padding-right: 40px\">";
            echo "<label for=\"seal_color_green\">";
            echo "<img src=\"" . sitesecure_string_for_color($data->seal, $color) . "\">";
            echo "<input " . ($color == $active_color ? "checked" : "") . " class=\"seal-config\" data-url=\"/seals/" . $site->name . "/seal." . $color . ".png\" name=\"seal[color]\" type=\"radio\" value=\"" . $color . "\" style=\"display: inline-block; margin-left: 6px; position: relative; top: " . $height . "px;\">";
            echo "</label>";
            echo "</div>";
            if ($i == 1) {
                $height = -20;
                echo "<div style=\"clear: both; height: 25px\"><!-- --></div>";
            }
        }
        echo "<div style=\"clear: both; height: 1px\"><!-- --></div>";
        echo "</div>";
        echo "<div class=\"hr\"><hr /></div>";

        echo "
            <div class=\"margin-btm-20\"></div>
            <div class=\"row\">
            <div class=\"control-group select optional seal_position\">
            <h3>" . SKYLAB_MODULE_SITESECURE_SEAL_POSITION . "</h3>
            <p class=\"help-inline\">" . SKYLAB_MODULE_SITESECURE_SEAL_POSITION_HINT . "</p>
            <div style=\"margin-top: -10px\" class=\"controls\"><select class=\"select optional seal-config\" id=\"seal_position\" name=\"seal[position]\"><option value=\"none\">" . SKYLAB_MODULE_SITESECURE_SEAL_POSITION_INLINE . "</option>
            <option value=\"bottom_right\">" . SKYLAB_MODULE_SITESECURE_SEAL_BOTTOM_RIGHT . "</option>
            <option value=\"bottom_left\">" . SKYLAB_MODULE_SITESECURE_SEAL_BOTTOM_LEFT . "</option></select></div></div>
            </div>
            <hr>
            <div id=\"seal_html\" data-html=\"" . htmlspecialchars($data->html) . "\"></div>
            <div class=\"control-group text optional seal_code\"><label class=\"text optional control-label\" for=\"seal_code\">" . SKYLAB_MODULE_SITESECURE_SEAL_HTML . "</label><div class=\"controls\"><textarea class=\"text optional\" cols=\"40\" id=\"seal_code\" rows=\"7\">" . sitesecure_string_for_color($data->html, $active_color) . "</textarea></div></div>
            </div>
            <div style=\"height: 30px\"><!-- --></div>
            <script type=\"text/javascript\">
               function update_seal_code()
               {
                  var color = $nc(\"#seal_color input:checked\").val();
                  var position = $nc(\"#seal_position\").val();
                  var html = $nc(\"#seal_html\").data(html).html;
                  html = html.replace('%seal%', color);
                  if (position == \"bottom_right\") { html = '<div style=\"position:fixed;bottom:0;right:0;\">' + html + '</div>'; }
                  if (position == \"bottom_left\")  { html = '<div style=\"position:fixed;bottom:0;left:0;\">'  + html + '</div>'; }
                  $nc(\"#seal_code\").html(html);
                  $nc(\"#seal_code\").css('display', 'block');
                  $nc(\".CodeMirror\").remove();
                  $nc(\".cm_switcher\").remove();
               }
               $nc(function()
               {
                  $nc(\"#seal_color input\").click(update_seal_code);
                  $nc(\"#seal_position\").change(update_seal_code);
                  $nc(\"#seal_code\").css('display', 'block');
                  $nc(\".CodeMirror\").remove();
                  $nc(\".cm_switcher\").remove();
               });
            </script>
         ";
    }

}

function sitesecure_string_for_color($string, $color) {
    return str_replace('%seal%', $color, $string);
}

