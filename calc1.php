<?php
include "vars.inc.php";
$passed_thru_404=true;
require ($INCLUDE_FOLDER."index.php");
$br = CBranding::get_object();
include ($DOCUMENT_ROOT.$br->current['Projects']);
echo $DOCUMENT_ROOT.$br->current['Projects'];