<?php
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -5 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );

include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ROOT_FOLDER."connect_io.php");
//$nc_core->modules->load_env();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=$nc_core->NC_CHARSET?>" />
<title>File Manager</title>
<link rel="stylesheet" type="text/css" href="styles/reset.css" />
<link rel="stylesheet" type="text/css"
	href="scripts/jquery.filetree/jqueryFileTree.css" />
<link rel="stylesheet" type="text/css"
	href="scripts/jquery.contextmenu/jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="styles/filemanager.css" />
<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="styles/ie.css" />
		<![endif]-->
</head>
<body>
<div>
<form id="uploader" method="post">
<h1></h1>
<div id="uploadresponse"></div>
<input id="mode" name="mode" type="hidden" value="add" /> <input
	id="currentpath" name="currentpath" type="hidden" /> <input
	id="newfile" name="newfile" type="file" />
<button id="upload" name="upload" type="submit" value="Upload"></button>
<button id="newfolder" name="newfolder" type="button" value="New Folder"></button>
<button id="grid" class="ON" type="button">&nbsp;</button>
<button id="list" type="button">&nbsp;</button>
</form>
<div id="splitter">
<div id="filetree"></div>
<div id="fileinfo">
<h1></h1>
</div>
</div>

<ul id="itemOptions" class="contextMenu">
	<li class="select"><a href="#select"></a></li>
	<li class="download"><a href="#download"></a></li>
	<li class="rename"><a href="#rename"></a></li>
	<li class="delete separator"><a href="#delete"></a></li>
</ul>
<?php
$lang = $nc_core->lang->detect_lang(1);
if ( $lang == 'ru' ) $lang = $nc_core->NC_UNICODE ? "ru_utf8" : "ru_cp1251";?>
<script type="text/javascript" src="scripts/languages/<?=$lang?>.js"></script>
<script type="text/javascript" src="scripts/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="scripts/jquery.form.js"></script>
<script type="text/javascript" src="scripts/jquery.splitter/jquery.splitter.js"></script>
<script type="text/javascript" src="scripts/jquery.filetree/jqueryFileTree.js"></script>  
<script type="text/javascript" src="scripts/jquery.contextmenu/jquery.contextMenu.js"></script>
<script type="text/javascript" src="scripts/jquery.impromptu-1.5.js"></script>
<script type="text/javascript" src="scripts/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="scripts/filemanager.config.js"></script>
<script type="text/javascript" src="scripts/filemanager.js"></script></div>
</body>
</html>
