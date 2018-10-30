<?php
/* $Id: template.inc.php 8397 2012-11-12 10:02:18Z vadim $ */

function BeginHtml($title = "", $location = "", $HelpURL = "", $module = '', $developer_mode = false) {
    global $NO_RIGHTS_MESSAGE, $REMIND_SAVE, $LAST_LOCAL_PATCH;
    global $SUB_FOLDER, $ADMIN_TEMPLATE, $ADMIN_PATH, $HTTP_ROOT_PATH;
    global $nc_core;

    // $title - то, что стоит между тэгами <title>
    $NO_RIGHTS_MESSAGE = NETCAT_MODERATION_ERROR_NORIGHTS;

    $lang = $nc_core->lang->detect_lang(1);
    if ($lang == 'ru') $lang = $nc_core->NC_UNICODE ? "ru_utf8" : "ru_cp1251";

    // файл со стилями модуля
    $module_css = '';
    if ($module && file_exists($nc_core->MODULE_FOLDER.$module."/admin.css")) {
        $module_css = $SUB_FOLDER.$nc_core->HTTP_ROOT_PATH."modules/".$module."/admin.css";
        $module_css = "<link type='text/css' rel='Stylesheet' href='".$module_css."'>\n";
    }

    // файл со js модуля
    $module_js = '';
    if ($module && file_exists($nc_core->MODULE_FOLDER.$module."/admin.js")) {
        $module_js = $SUB_FOLDER.$nc_core->HTTP_ROOT_PATH."modules/".$module."/admin.js";
        $module_js = "<script type='text/javascript' src='".$module_js."'></script>\n";
    }
    if (!$developer_mode) {
        ?><!DOCTYPE html>
<!--[if lt IE 7]><html lang='<?= MAIN_LANG ?>' dir='<?= MAIN_DIR ?>' class="nc-ie6 nc-oldie"><![endif]-->
<!--[if IE 7]><html lang='<?= MAIN_LANG ?>' dir='<?= MAIN_DIR ?>' class="nc-ie7 nc-oldie"><![endif]-->
<!--[if IE 8]><html lang='<?= MAIN_LANG ?>' dir='<?= MAIN_DIR ?>' class="nc-ie8 nc-oldie"><![endif]-->
<!--[if gt IE 8]><!--><html lang='<?= MAIN_LANG ?>' dir='<?= MAIN_DIR ?>'><!--<![endif]-->
            <head>
                <title><?= ($title ? $title : "NetCat") ?></title>
                <meta http-equiv='Content-Type' content='text/html; charset=<?= $nc_core->NC_CHARSET ?>'>
                <link type='text/css' rel='Stylesheet' href='<?= $ADMIN_TEMPLATE ?>css/admin.css?<?= $LAST_LOCAL_PATCH ?>'>
                <? echo $module_css; }?>
                <?= nc_js(); ?>
                <script type="text/javascript">nc.register_view('main');</script>
                <script type='text/javascript' src='<?=$ADMIN_PATH?>js/sitemap.js?<?=$LAST_LOCAL_PATCH?>'></script>
                <script type='text/javascript' src='<?=$ADMIN_PATH?>js/remind_save.js?<?=$LAST_LOCAL_PATCH?>'></script>
                <script type='text/javascript' src='<?= $ADMIN_PATH ?>js/chosen.jquery.min.js?<?= $LAST_LOCAL_PATCH ?>'></script>
                <script type="text/javascript">
                    $nc(".chosen-select").chosen();
                    $nc(".chosen-select-deselect").chosen({allow_single_deselect:true});

                    $nc(function() {
                        $nc('input[name=Cache_Access_ID]').click(function(){
                            var cacheValue = $nc('input[name=Cache_Access_ID]:checked').val();
                            var cacheInput = $nc('#Cache_Lifetime');
                            var isDisabled = 1 == cacheValue ? '' : 'disabled';

                            if (isDisabled) {
                                cacheInput.attr('disabled', 'disabled');
                            } else {
                                cacheInput.removeAttr('disabled');
                            }

                        });
                    });
                </script>

                <?=$module_js ?>


            <? echo include_cd_files();
            if (!$developer_mode) {
                ?>
                <!-- для диалога генерации альтернативных форм -->
                <script type='text/javascript'>
                    var SUB_FOLDER = "<?= $SUB_FOLDER ?>";
                    var NETCAT_PATH = "<?= $SUB_FOLDER.$HTTP_ROOT_PATH ?>";
                    var ADMIN_PATH = "<?= $ADMIN_PATH ?>";
                    var ADMIN_LANG = "<?= MAIN_LANG ?>";
                    var NC_CHARSET = "<?= $nc_core->NC_CHARSET ?>";
                    var ICON_PATH = "<?= $ADMIN_TEMPLATE ?>" + "img/";
                    var NETCAT_REMIND_SAVE_TEXT = "<?= NETCAT_REMIND_SAVE_TEXT ?>";
                </script>

                <?= ($GLOBALS["BBCODE"] ? "<script type='text/javascript' src='".$ADMIN_PATH."js/bbcode.js'></script>" : "") ?>
            <? }
            if ($GLOBALS["AJAX_SAVER"]) { ?>
                <script type='text/javascript'>
                    var formAsyncSaveEnabled = true;
                    var NETCAT_HTTP_REQUEST_SAVING = "<?= str_replace('"', "&quot;", NETCAT_HTTP_REQUEST_SAVING) ?>";
                    var NETCAT_HTTP_REQUEST_SAVED  = "<?= str_replace('"', "&quot;", NETCAT_HTTP_REQUEST_SAVED) ?>";
                    var NETCAT_HTTP_REQUEST_ERROR  = "<?= str_replace('"', "&quot;", NETCAT_HTTP_REQUEST_ERROR) ?>";
                </script>
            <? } else { ?>
                <script type='text/javascript'>var formAsyncSaveEnabled = false;</script>
            <? } if (!$developer_mode) { ?>

            </head>
            <body<? } else { ?><div<? } ?> class='admin_form nc-admin' id='MainViewBody'>
                <?
            }

function EndHtml() {
    global $UI_CONFIG, $developer_mode;

	$nc_core = nc_Core::get_object();

    // saved via XMLHttpRequest
    if (!empty($_POST["NC_HTTP_REQUEST"])) {
        ob_end_clean();
        // [выкинуть ответ]
        if ($GLOBALS["_RESPONSE"]) {
            print nc_array_json($GLOBALS["_RESPONSE"]);
        }
        exit;
    }

    if ($GLOBALS["AJAX_SAVER"]) {
        ?>
        <div class='save_hint'><?= sprintf( NETCAT_HTTP_REQUEST_HINT, chr( $nc_core->get_settings('SaveKeycode') ? $nc_core->get_settings('SaveKeycode') : 83 ) ) ?></div><br />
        <?php
    }

    if (is_object($UI_CONFIG) && method_exists($UI_CONFIG, 'to_json')) {
        print $UI_CONFIG->to_json();
    }

    if (is_object($UI_CONFIG) && count($UI_CONFIG->remind) > 0) {
        print "<script type='text/javascript'>";
        foreach($UI_CONFIG->remind as $function) {
            print $function . "();";
        }
        print "</script>";
    }

    if (!$developer_mode) {
        ?></body>
        </html>
        <?
    }
}

function GetTemplateDescription($TemplateID) {
    global $db;
    return $db->get_var("SELECT `Description` FROM Template WHERE `Template_ID` = '".intval($TemplateID)."'");
}

function TemplateChildrenNumber($TemplateID) {
    global $db;

    return $db->get_var("SELECT count(Template_ID) FROM `Template` WHERE `Parent_Template_ID` = '".intval($TemplateID)."'");
}

function include_cd_files() {
    global $ADMIN_PATH, $nc_core;
    $set = $nc_core->get_settings();

    if(!$set['CMEmbeded']) {
        return '';
    }
    ob_start();
?>
<link rel='stylesheet' href='<?=$ADMIN_PATH?>js/codemirror/lib/codemirror.css' />
<link rel='stylesheet' href='<?=$ADMIN_PATH?>js/codemirror/lib/simple-hint.css' />
<link rel='stylesheet' href='<?=$ADMIN_PATH?>js/codemirror/addon/iOS/iOSkeyboard.css' />
<script src='<?=$ADMIN_PATH?>js/codemirror/lib/codemirror.js?4'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/xml.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/mysql.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/javascript.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/css.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/clike.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/mode/php.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/lib/simple-hint.js?4'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/lib/netcat-hint.js?4'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/lib/cm_init.js?4'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/addon/iOS/iOSselection.js'></script>
<script src='<?=$ADMIN_PATH?>js/codemirror/addon/iOS/iOSkeyboard.js'></script>
<script type='text/javascript'>

	var nc_cmConfig = {
		CMAutocomplete:!!'<?=$set['CMAutocomplete']?>',
		CMHelp:!!'<?=$set['CMHelp']?>',
		CMDefault:!!'<?=$set['CMDefault']?>',
		autoCompletionData: $nc.parseJSON("<?=addslashes(json_safe_encode(get_autocompletion_data()))?>"),
		label_enable:'<?=NETCAT_SETTINGS_CODEMIRROR_ENABLE?>',
		label_wrap:'<?=NETCAT_SETTINGS_CODEMIRROR_WRAP?>',
		label_fullscreen:'<?=NETCAT_SETTINGS_CODEMIRROR_FULLSCREEN?>'
	};
	$nc(function() {
	   <?if (+$_REQUEST['isNaked']) {?>
		   setTimeout(function() {$nc('textarea:not(.ckeditor_area)').filter(':visible').codemirror(nc_cmConfig);},300);
	   <?} else {?>
                   var customSettingsDiv = $nc('div#loadClassCustomSettings');
                   $nc('textarea', customSettingsDiv).each(function(){ $nc(this).addClass('no_cm')} );
		   $nc('textarea:not(.ckeditor_area, .no_cm)').codemirror(nc_cmConfig);
	   <?}?>
	});
</script>
	<?
	return  ob_get_clean();
}

function get_autocompletion_data() {
	global $db;
	$data = $db->get_results("SELECT * FROM `Documentation`", ARRAY_A);
	$res = array();
	if ( is_array($data) )
	foreach ($data as $e) {
		$completion = array(
			'name' => $e['Name'],
			'value' => $e['Signature'],
			'help' => $e['ShortHelp']
		);
		if ($e['Parent']) {
			$completion['parent'] = $e['Parent'];
		}
		$result_entry = array(
			'type' => $e['Type'],
			'completion' => $completion
		);
		if ($e['Areas'] != '') {
			$result_entry['areas'] = preg_split("~\s+~", $e['Areas']);
		}
		$res []= $result_entry;
	}
	return $res;
}
?>