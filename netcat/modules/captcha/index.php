<?php
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
//require ($INCLUDE_FOLDER."index.php");
require ($ROOT_FOLDER."connect_io.php");
require_once $MODULE_FOLDER."captcha/function.inc.php";

if($_GET['nc_get_new_captcha']) {
    $captcha_hash = nc_captcha_generate_hash();
    nc_captcha_generate_code($captcha_hash);

    $MODULE_VARS = $nc_core->modules->get_module_vars();
    if ($MODULE_VARS['captcha']['AUDIOCAPTCHA_ENABLED']) {
        $playlist = "{'playlist':[";
        $code = $nc_core->db->get_row("SELECT `Captcha_Code` FROM `Captchas` WHERE `Captcha_Hash` = '".$captcha_hash."'");
        $code_hash = $nc_core->db->get_results("SELECT * FROM `Captchas_Settings` WHERE `Key` != 'time'");
        foreach (str_split($code->Captcha_Code) as $letter) {
            $letter = strtolower($letter);
            foreach ((array) $code_hash as $hash) {
                if ($hash->Key == $letter.'.mp3') {
                    $h = $hash->Value;
                    break;
                }
            }
            $playlist .= "{'file':'".$nc_core->HTTP_FILES_PATH."captcha/current_voice/".$h."'},";
        }
        $playlist .= "]}";
        $playlist = str_replace('},]}', '}]}', $playlist);
    }
    echo $captcha_hash.'#'.$playlist;
    exit;
}
