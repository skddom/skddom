<?php

/* $Id: unauth.php 7302 2012-06-25 21:12:35Z alive $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -3)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require($ADMIN_FOLDER."function.inc.php");

Unauthorize();
LoginFormHeader();

switch ($AUTHORIZATION_TYPE) {
    case "cookie":
        echo BEGINHTML_LOGOUT_OK."<br><br>[<a href='".$ADMIN_PATH."' class='relogin'>".BEGINHTML_LOGOUT_RELOGIN."</a>]";
        break;
    case "http":
        echo BEGINHTML_LOGOUT_IE;
        break;
    case "session":
        echo BEGINHTML_LOGOUT_OK."<br><br>[<a href='".$ADMIN_PATH."' class='relogin'>".BEGINHTML_LOGOUT_RELOGIN."</a>]";
        unset($_SESSION['User']);
        break;
}

LoginFormFooter();