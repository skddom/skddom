<?php
$action = "change";

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -3)) . (strstr(__FILE__, "/") ? "/" : "\\");
@include_once($NETCAT_FOLDER . "vars.inc.php");

require($INCLUDE_FOLDER . "index.php");

$nc_core = nc_Core::get_object();

$fieldName = $nc_core->input->fetch_post('fieldName');
$newValue = $nc_core->input->fetch_post('newValue');

$message = $nc_core->input->fetch_post('messageId');
$subClassId = $nc_core->input->fetch_post('subClassId');

$subClass = $nc_core->sub_class->get_by_id($subClassId);

$classID = $subClass['Class_ID'];
$sub = $subClass['Subdivision_ID'];
$cc = $subClassId;
$current_cc = $subClass;

if ($posting) {
    $variable = 'f_' . $fieldName;
    $$variable = $newValue;
}

ob_start();
require $ROOT_FOLDER . "message.php";

if ($posting) {
    exit();
}
ob_end_clean();


echo "<div class='nc_admin_form_menu' style='padding-top: 20px;'>
    <h2>" . NETCAT_MODERATION_APPLY_CHANGES_TITLE . "</h2>
    <div class='nc_admin_form_menu_hr'></div>
</div>
<div class='nc_admin_form_body nc-admin'>" . NETCAT_MODERATION_APPLY_CHANGES_TEXT;

echo "<form name='adminForm' class='nc-form' id='adminForm' enctype='multipart/form-data' method='post' action='{$nc_core->SUB_FOLDER}{$nc_core->HTTP_ROOT_PATH}message.php'>\r\n";
echo "<input name='admin_mode' type='hidden' value='1' />\r\n";
echo $nc_core->token->get_input() . "\r\n";
echo "<input name='catalogue' type='hidden' value='{$subClass['Catalogue_ID']}' />\r\n";
echo "<input name='cc' type='hidden' value='{$subClassId}' />\r\n";
echo "<input name='sub' type='hidden' value='{$sub}' />\r\n";
echo "<input name='message' type='hidden' value='{$message}' />\r\n";
echo "<input name='posting' type='hidden' value='1' />\r\n";
echo "<input name='curPos' type='hidden' value='{$curPos}' />\r\n";
echo "<input name='f_Parent_Message_ID' type='hidden' value='{$f_Parent_Message_ID}' />\r\n";
echo "<input name='f_Checked' type='hidden' value='{$f_Checked}' />\r\n";
echo "<input name='f_Priority' type='hidden' value='{$f_Priority}' />\r\n";
echo "<input name='f_Keyword' type='hidden' value='{$f_Keyword}' />\r\n";
echo "<input name='f_ncTitle' type='hidden' value='{$f_ncTitle}' />\r\n";
echo "<input name='f_ncKeywords' type='hidden' value='{$f_ncKeywords}' />\r\n";
echo "<input name='f_ncDescription' type='hidden' value='{$f_ncDescription}' />\r\n";
echo "<input name='f_ncSMO_Title' type='hidden' value='{$f_ncSMO_Title}' />\r\n";
echo "<input name='f_ncSMO_Description' type='hidden' value='{$f_ncSMO_Description}' />\r\n";
echo "<input name='f_ncSMO_Image' type='hidden' value='{$f_ncSMO_Image}' />\r\n";

foreach($fld as $index => $field) {
    if ($fldType[$index] == NC_FIELDTYPE_FILE || $fldType[$index] == NC_FIELDTYPE_MULTIFILE) {
        continue;
    }
    if ($field == $fieldName) {
        $value = htmlspecialchars($newValue, ENT_QUOTES);
    } else {
        $value = htmlspecialchars($fldValue[$index], ENT_QUOTES);
        $value = str_replace("'", "&#39;", $value);
    }

    echo "<input name='f_{$field}' type='hidden' value='{$value}' />\r\n";
}

echo "</form></div>
<div class='nc_admin_form_buttons'>
    <button type='button' class='nc_admin_metro_button nc-btn nc--blue' disable onclick='confirmNewValue(\"{$fieldName}_{$message}_{$subClassId}_edit_inline\"); return true;'>" . NETCAT_REMIND_SAVE_SAVE . "</button>
    <button type='button' class='nc_admin_metro_button_cancel nc-btn nc--red nc--bordered nc--right' >" . CONTROL_BUTTON_CANCEL . "</button>
</div>

<style>
    a {color:#1a87c2;}
    a:hover {text-decoration:none;}
    a img {border:none;}
    p {margin:0px; padding:0px 0px 18px 0px;}
    h2 {font-size:20px; font-family:'Segoe UI', SegoeWP, Arial; color:#333333; font-weight:normal; margin:0px; padding:20px 0px 10px 0px; line-height:20px;}
    form {margin:0px; padding:0px;}
    input {outline:none;}
    .clear {margin:0px; padding:0px; font-size:0px; line-height:0px; height:1px; clear:both; float:none;}
    select, input, textarea {border:1px solid #dddddd;}
    :focus {outline:none;}
    .input {outline:none; border:1px solid #dddddd;}
</style>
<script type='text/javascript'>
    function confirmNewValue(id) {
        var \$element = \$nc('#' + id);
        \$element.attr('data-oldvalue', " . json_encode($newValue) . ");
    }
</script>
<script type='text/javascript'>prepare_message_form();</script>";