<?

/* $Id: s_auth.inc.php 6211 2012-02-10 10:34:15Z denis $ */

#   1 - read
#   2 - add
#   4 - subscribe
#   8 - change
#  16 - moderate

function s_auth($cc_env, $action, $posting) {
    global $nc_core;
    global $admin_mode, $AUTHORIZATION_TYPE, $user_table_mode;
    global $AUTH_USER_ID;

    // редактирование пользователя через лицевую часть
    if ($action == "change" && $user_table_mode && ($AUTH_USER_ID || Authorize()))
            return true;

    if ($action == "index" || $action == "full" || $action == "search")
            $action = "read";

    $cc = $cc_env["Sub_Class_ID"];

    $MODULE_VARS = $nc_core->modules->get_module_vars();

    // для модуля подписки версии 2 своя проверка прав
    if ($action == 'subscribe' && $MODULE_VARS['subscriber']['VERSION'] > 1) {
        try {
            $nc_s = nc_subscriber::get_object();
            $mailer_id = $nc_s->get_mailer_by_cc($cc, 'Mailer_ID');
            return $nc_s = $nc_s->check_rights($mailer_id);
        } catch (Exception $e) {
            ;
        }
    }
    // параметры текущего раздела
    $sub_env = $nc_core->subdivision->get_current();
    // Если нет сс, то права на доступ нужно взять из раздела
    $instance = ( is_array($cc_env) && $cc ) ? "cc_env" : "sub_env";



    switch ($action) {
        case "add": $f_access = ${$instance}["Write_Access_ID"];
            break;
        case "change": $f_access = ${$instance}["Edit_Access_ID"];
            break;
        case "subscribe": $f_access = ${$instance}["Subscribe_Access_ID"];
            break;
        case "comment": $f_access = ${$instance}["Comment_Access_ID"];
            break;
        case "moderate": $f_access = 3;
            break; //модерирование, надо провреить, не забанен ли, а потом проверить на наличие соответ. права
        default: $f_access = ${$instance}["Read_Access_ID"];
            break;
    }

    // действия с объектами (изменение, удаление) не доступно неавторизованным
    if ($f_access == 1 && $action == "change") $f_access = 2;

    switch ($f_access) {

        case 1: { // все
                if ($admin_mode) {
                    if (!Authorize()) return false;
                    if (!CheckUserRights($cc, $action, 1)) return false;
                }
            } break;

        case 2: {  // только зарегистрированные
                if (!Authorize()) return false;
                global $perm;
                if ($perm->isBanned($cc_env, $action)) return false;
            } break;

        case 3: {   // только уполномочнные
                if (!Authorize()) return false;
                global $perm;
                if ($perm->isBanned($cc_env, $action)) return false;
                if (!CheckUserRights($cc, $action, $posting)) return false;
            } break;



        default: break;
    }

    return true;
}
?>