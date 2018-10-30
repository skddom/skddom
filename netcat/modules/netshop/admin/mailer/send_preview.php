<?php

/**
 * Sends an email message
 *
 * INPUT:
 *   - catalogue_id   required for nc_netshop_mailer to determine sender address
 *   - mail_to        several addresses can be provided (comma-delimited)
 *   - mail_subject
 *   - mail_body
 *
 */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -6)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ADMIN_FOLDER."function.inc.php");

$perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);

if (is_file($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php");
} else {
    require_once($MODULE_FOLDER."netshop/en.lang.php");
}

/** @var nc_input $input */
$input = nc_core('input');

/** @var nc_netshop_mailer $mailer */
$mailer = nc_netshop::get_instance($input->fetch_post('catalogue_id'))->mailer;

$message = new nc_netshop_mailer_message(
    $input->fetch_post('mail_subject'),
    $input->fetch_post('mail_body')
);

$recipients = preg_split("/\s*[,;]\s*/", trim($input->fetch_post('mail_to')));
foreach ((array)$recipients as $recipient) {
    $mailer->send($recipient, $message);
}

echo NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SENT;