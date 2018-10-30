<?php
/*$Id */

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );

include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ROOT_FOLDER."connect_io.php");

if (!isset($_GET['form']) || !isset($_GET['control'])) {
  die("Incorrect params");
}

$lang = $nc_core->lang->detect_lang();
include($ADMIN_FOLDER."lang/".$lang.".php");
$lang = $nc_core->lang->acronym_from_full($lang);
if ( $lang == 'ru' ) $lang = $nc_core->NC_UNICODE ? "ru_utf8" : "ru_cp1251";

$skin = $nc_core->get_settings('CKEditorSkin');
if ( !$skin ) $skin = 'kama';

?>

<html>
  <head>
    <title>NetCat</title>
    <meta http-equiv='Content-Type' content='text/html; charset=<?=$nc_core->NC_CHARSET?>'>
    <style type='text/css'>
      body { margin: 2px; background-color: #EEEEEE; }
    </style>
    <script type='text/javascript' src='<?=$SUB_FOLDER.$HTTP_TEMPLATE_PATH?>jquery/jquery.min.js'></script>
    <script type='text/javascript' src='<?=$SUB_FOLDER.$HTTP_ROOT_PATH?>editors/ckeditor/ckeditor.js'></script>
  </head>
  <body>

  <form name='EditorBackForm' style='margin:0px;'>
    <textarea id='nc_editor'></textarea>
    <input type='button' value='<?=NETCAT_SETTINGS_EDITOR_SEND?>' onclick="OnCloseWindow();">
  </form>

  <script type='text/javascript'>
  <!--
  var user_text = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'].value;
  var el = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'];
  if (typeof(window.opener.$nc(el).codemirror == 'function')
      && window.opener.$nc(el).data('codemirror')) {
     user_text =  window.opener.$nc(el).data('codemirror').getValue();
  }
  document.getElementById('nc_editor').value = user_text;

  CKEDITOR.replace('nc_editor', {
  					filebrowserBrowseUrl: '<?=$SUB_FOLDER.$HTTP_ROOT_PATH?>editors/ckeditor/filemanager/index.php',
                    skin : '<?=$skin?>',
                    width: '100%', height: 330,
                    language : '<?=$lang?>',
                    smiley_path : '<?=$nc_core->SUB_FOLDER?>/images/smiles/'
                    });


  function OnCloseWindow()  {
    var return_value = CKEDITOR.instances.nc_editor.getData(); ;
    var el = opener.document.forms['<?=htmlspecialchars($_GET['form'])?>'].elements['<?=htmlspecialchars($_GET['control'])?>'];
    el.value = return_value;
    if (typeof window.opener.$nc(el).codemirror == 'function') {
        window.opener.$nc(el).codemirror('setValue');
    }
    window.close();
  }



  </script>

</body>
</html>
