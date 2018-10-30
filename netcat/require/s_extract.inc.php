<?php

/* $Id: s_extract.inc.php 6613 2012-04-05 12:10:55Z russuckoff $ */
if (!class_exists("nc_System")) die("Unable to load file.");
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -3)).( strstr(__FILE__, "/") ? "/" : "\\" );
// connect config file
@include_once ($NETCAT_FOLDER."vars.inc.php");


// parse url before extract! $_GET array remap in this function
$parsed_url = $nc_core->url->parse_url();
// for old versions
$client_url = $nc_core->url->source_url();

// it's faster than using $_REQUEST, even without check whether all variables present in $_REQUEST or not
$_NETCAT_INPUT = $nc_core->input->prepare_extract();

// extract all variables from superglobals arrays, this method must be DEPRECATED!
extract($_NETCAT_INPUT);
// unset temp variable to perform memory usage
unset($_NETCAT_INPUT);

$_cache = array();