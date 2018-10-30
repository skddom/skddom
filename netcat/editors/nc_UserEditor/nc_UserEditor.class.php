<?php
/*$Id$*/

class nc_UserEditor{
  public $Value;

  public function __construct() {
    $this->Value  = '';
  }

  public function Create () {
    echo $this->CreateHtml() ;
  }

  public function CreateHtml( $textarea_id ) {
    $nc_core = nc_Core::get_object();
    $ed_path = $nc_core->SUB_FOLDER.$nc_core->HTTP_ROOT_PATH.'editors/nc_UserEditor/';
    $js_path = $ed_path.'nc_UserEditor.js';
    $smile_path = $nc_core->SUB_FOLDER."/images/smiles/";


    $value = htmlspecialchars( $this->Value );
    $value = nc_bbcode($value);

    $value = str_replace(array("\r\n", "\r", "\n"), "\" +  \"<br />\" + \"", $value);
    $lang = $nc_core->lang->detect_lang(1);
    if ( $lang == 'ru' ) $lang = $nc_core->NC_UNICODE ? "ru_utf8" : "ru_cp1251";

    $Html = "<script type='text/javascript' src='".$ed_path."lang/".$lang.".js'></script>\n
             <script type='text/javascript' src='".$js_path."'></script>\n
             <script type='text/javascript'>bkLib.onDomLoaded(function(){nicEditors.allTextAreas('".$textarea_id."', \"".$value."\", '".$ed_path."', '".$smile_path."')});</script>\n";
    return $Html;
  }

}