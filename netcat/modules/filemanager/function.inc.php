<?php

/* $Id: function.inc.php 6207 2012-02-10 10:14:50Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");
// get global value (for admin mode)
global $MODULE_FOLDER;

// include need classes
include_once ($MODULE_FOLDER."filemanager/nc_filemanager.class.php");
?>