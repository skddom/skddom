<?php
error_reporting(E_ALL | E_STRICT) ;
ini_set('display_errors', 'On');

define('DS_FORM_LOAD', true);
define('DS_FORM_ROOT', dirname(__FILE__));

function __autoload($className) {
    include_once 'classes/'.$className .'.php';
}

DSMain::routing();

?>