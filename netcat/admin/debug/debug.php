<?php

/* $Id: debug.php 7302 2012-06-25 21:12:35Z alive $ */
//This is a script for ajax POST from inside_admin, which returns result of component or template evaluating.
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
@include_once ($NETCAT_FOLDER."vars.inc.php");

include_once($ADMIN_FOLDER."function.inc.php");

# Загрузка файла локализации
$lang = $nc_core->lang->detect_lang();
require_once($ADMIN_FOLDER."lang/".$lang.".php");


header("Content-Encoding: ".$nc_core->NC_CHARSET);

function check_eval($value, $way=1) {
    global $ADMIN_PATH;
    $isDebug = 0;
    if (get_magic_quotes_gpc ()) {
        $value = stripslashes($value);
    }
    $data = "way=$way&value=".urlencode($value);
    $context_options = array(
            'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    ."Content-Length: ".strlen($data)."\r\n"."Cookie: PHP_AUTH_SID=".$_COOKIE['PHP_AUTH_SID'].";\r\n",
                    'content' => $data
            )
    );
    // ."Cookie: " . $_SERVER['HTTP_COOKIE']."\r\n"
    $context = stream_context_create($context_options);
    $url = nc_get_scheme() . '://' . $_SERVER['HTTP_HOST'] . $ADMIN_PATH . "debug/debug_eval.php";
    $buffer = file_get_contents($url, false, $context);
    $buffer = (!$isDebug ? strip_tags($buffer) : $buffer);
    // TODO: Убрать .
    if ($isDebug == 1 )echo $buffer."<br/>";
    if (nc_preg_match("/eval\(\)'d code on line (\d+)/m", $buffer, $match)) {
        return $match[1];
    }
    //if ( preg_match("/eval\(\)'d code\<\/b\> on line \<b\>(\d)\<\/b>/m", $buffer,$match) ) { return $match[1]; }
    else return 0;
}

// Заберем весь буффер потому что проверка eval идет черерз буффер
$buffer = ob_get_clean();
$output = "<table id=\'debuginfo\'><tbody>";

$status = 0; // общий статус проверки;

if ($ClassID > 0) {
// Вызывается из окна редактирования компонента
    switch ($phase) {
        // Форма редактирования компонента
        // FormPrefix, RecordTemplate, FormSuffix, RecordTemplateFull,
        // Settings, CustomSettings, RecordsPerPage, SortBy, TitleTepmlate
        case 5:
            $res = check_eval($FormPrefix, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CLASS_OBJECTSLIST_PREFIX."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($RecordTemplate, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CLASS_OBJECTSLIST_BODY."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($FormSuffix, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CLASS_OBJECTSLIST_SUFFIX."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($RecordTemplateFull, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CLASS_OBJECTVIEW."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($Settings, 0);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_SYSTEM."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($CustomSettingsTemplate, 0);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_CLASS_CUSTOM_SETTINGS_TEMPLATE."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";

            break;
        // Формы редактирования альтерантивных форм:
        case 9:
            switch ($myaction) {
                // Альтернативная форма добавления AddTemplate , Условия добавления AddCond , Дейстивя после добавления AddActionTemplate.
                case 1:
                    $res = check_eval($AddTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_ADDFORM."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($AddCond, 0);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_ADDRULES."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($AddActionTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    break;
                // Альтернативная форма измения EditTemplate,Условия изменения EditCond,Действия после изменения EditActionTemplate, Действия после включения/выключения CheckActionTemplate, Действия после удаления DeleteActionTemplate
                case 2:
                    $res = check_eval($EditTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_EDITFORM."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($EditCond, 0);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_EDITRULES."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($EditActionTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($CheckActionTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_ONONACTION."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";

                    break;
                // Форма поиска перед списком объектов FullSearchTemplate, Форма расширенного поиска на отдельной странице SearchTemplate.
                case 3:
                    $res = check_eval($FullSearchTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_QSEARCH."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($EditSearchTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_SEARCH."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";

                    break;
                // Условия для подписки SubscribeCond, Шаблон письма для подписчиков SubscribeTemplate
                case 4:
                    $res = check_eval($SubscribeCond, 0);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_MAILRULES."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($SubscribeTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_MAILTEXT."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    break;
                case 5:
                    $res = check_eval($DeleteTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_DELETEFORM."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($DeleteCond, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_DELETERULES."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    $res = check_eval($DeleteActionTemplate, 1);
                    $status = $res ? 1 : $status;
                    $output.="<tr> <td>".CONTROL_CLASS_CLASS_FORMS_ONDELACTION."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
                    break;
            }
            break;
    }
} elseif ($TemplateID > 0) {
// Вызывается из окна редактирования макета
    switch ($phase) {
        case 5:
            // Форма редактирования макета
            // Settings, Header, Footer, CustomSettings
            $res = check_eval($Settings, 0);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_TEMPLATE_TEPL_MENU."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($Header, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_TEMPLATE_TEPL_HEADER."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($Footer, 1);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_TEMPLATE_TEPL_FOOTER."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            $res = check_eval($CustomSettings, 0);
            $status = $res ? 1 : $status;
            $output.="<tr> <td>".CONTROL_TEMPLATE_CUSTOM_SETTINGS."</td><td>".($res ? NETCAT_DEBUG_ERROR_INSTRING.$res : "ОК")."</td> </tr>\n";
            break;
    }
}
$output.="</tbody></table>";
//echo stripslashes($Settings);
$output = str_replace(array("\r", "\n"), array("", ""), $output);
$json_answer = " { \"status\": \"".($status ? "err" : "ok")."\" , \"content\" : \"$output\" }; ";
echo $json_answer;


ob_flush();