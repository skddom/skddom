<?php
$seocrc=crc32($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['REQUEST_URI']);
$seofile = $_SERVER['DOCUMENT_ROOT'].'/anchors.txt';
if (file_exists($seofile)){
$seolist = file($seofile);
echo rtrim($seolist[abs($seocrc%count($seolist))]);
}
?>