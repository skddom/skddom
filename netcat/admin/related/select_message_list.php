<?
/* $Id: select_message_list.php 5946 2012-01-17 10:44:36Z denis $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require ($ADMIN_FOLDER."related/format.inc.php");
require_once ($INCLUDE_FOLDER."s_common.inc.php");

$sub = (int) $sub;
$cc = (int) $cc;
$field_id = (int) $field_id;
if (!$sub || !$field_id) {
    trigger_error("Not enough data", E_USER_ERROR);
}

$field_format = field_relation_factory::get_instance_by_field_id($field_id);
?>
<html>
    <head>
        <title>Object list</title>
        <link type='text/css' rel='Stylesheet' href='<?=$ADMIN_TEMPLATE ?>css/admin.css'>
        <link type='text/css' rel='Stylesheet' href='<?=$ADMIN_TEMPLATE ?>css/main.css'>
    </head>

    <body style='margin:0; overflow: hidden'>
        <?
        // 1. Список *подходящих* шаблонов в разделе
        $cc_list = $db->get_results("SELECT Sub_Class_ID, Sub_Class_Name
                                 FROM Sub_Class
                                WHERE Subdivision_ID=$sub
                                  AND Class_ID={$field_format->class_id}
                                ORDER BY Priority",
                        ARRAY_A);

        if (!$cc_list) {
            $class_name = $db->get_var("SELECT Class_Name FROM Class WHERE Class_ID={$field_format->class_id}");
            print "<div class='related_list_container'>";
            nc_print_status(sprintf(NETCAT_MODERATION_RELATED_NO_CONCRETE_CLASS_IN_SUB, $class_name), 'info');
            print "</div>";
        } else {

            print "<table border='0' cellspacing='0' cellpadding='0' width='100%' height='100%'>\n";
            print "<tr><td class='toolbar'>\n";

            if (!$cc) {
                $cc = $cc_list[0]["Sub_Class_ID"];
            }

            foreach ($cc_list as $cc_data) {
                if ($cc_data['Sub_Class_ID'] == $cc) {
                    print "<a class='button button_on' href='#'>".
                            "<span class='button_caption'>$cc_data[Sub_Class_Name]</span>".
                            "</a>\n";
                } else {
                    print "<a class='button' href='?sub=$sub&cc={$cc_data[Sub_Class_ID]}&field_id={$field_id}'>".
                            "<span class='button_caption'>$cc_data[Sub_Class_Name]</span>".
                            "</a>\n";
                }
            }

            print "</td></tr>\n";

            // 2. Список объектов
            print "<tr><td height='99%'>\n";
            print "<iframe id='objframe' src='".$SUB_FOLDER.$HTTP_ROOT_PATH."?sub=$sub&cc=$cc&inside_admin=1&list_mode=select' width='100%' height='100%' frameborder='0'></iframe>\n";

            print "</td></tr>\n";
            print "</table>\n";
        }
        ?>

    </body>
</html>