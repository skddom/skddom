<?php
/* $Id: neditor.php 4001 2010-09-17 13:42:41Z denis $ */

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );

include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ROOT_FOLDER."connect_io.php");

if (!isset($_GET['form']) || !isset($_GET['control'])) exit;

$editor_lang = $nc_core->lang->detect_lang();
include($ADMIN_FOLDER."lang/".$editor_lang.".php");
$editor_lang = $nc_core->lang->acronym_from_full($editor_lang);
//if ( $editor_lang == 'ru' ) $editor_lang_file = $nc_core->NC_UNICODE ? "ru_utf8" : "ru_cp1251";

if (isset($_GET['sid'])) {
  $n_sid = $_GET['sid'];
}
else {
  $n_sid = '';
}

?>
<html>
  <head>
    <title>NetCat</title>
    <meta http-equiv='Content-Type' content='text/html; charset=<?=$nc_core->NC_CHARSET?>'>
    <style type='text/css'>
      body { margin: 2px; background-color: #EEEEEE; }
    </style>
    <script type='text/javascript' src='<?=$SUB_FOLDER.$HTTP_ROOT_PATH?>editors/FCKeditor/fckeditor.js'></script>
  </head>
  <body>
  <script type='text/javascript'>
  <!--
  var user_text = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'].value;
  var el = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'];
  if (typeof(window.opener.$nc(el).codemirror == 'function')
      && window.opener.$nc(el).data('codemirror')) {
      user_text =  window.opener.$nc(el).data('codemirror').getValue();
  }
  function GetContents()
  {
    var oEditor = FCKeditorAPI.GetInstance('FCKeditor1');
    return oEditor.GetXHTML(true);
  }

  function OnCloseWindow()
  {
    var return_value = GetContents();
    var el = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'];
    el.value = return_value;
    if (typeof window.opener.$(el).codemirror == 'function') {
        window.opener.$(el).codemirror('setValue');
    }
    window.close();
  }

  var oFCKeditor = new FCKeditor('FCKeditor1', "100%", "96%", "NetCat1", user_text);
  oFCKeditor.BasePath = "<?=$SUB_FOLDER.$HTTP_ROOT_PATH?>editors/FCKeditor/" ;
  oFCKeditor.Config["SmileyPath"] = "<?=$SUB_FOLDER?>/images/smiles/"  ;
  oFCKeditor.Config["DefaultLanguage"] = "<?= $editor_lang ?>";
  oFCKeditor.Config["sid"] = "<?=$sid?>";
  oFCKeditor.Create();
  -->
  </script>
<form name='EditorBackForm' style='margin:0px;'>
  <input type='button' value='<?=NETCAT_SETTINGS_EDITOR_SEND?>' onclick="OnCloseWindow();">
</form>
</body>
</html>