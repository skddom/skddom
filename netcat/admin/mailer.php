<?

// Parameters (GET): number (default: 20)

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -3)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ROOT_FOLDER."connect_io.php");
require_once ($INCLUDE_FOLDER."lib/Mail/Queue.php");

$number = $_GET['number'] ? $_GET['number'] : 20;

$db_options = array('type' => 'ezsql', 'mail_table' => 'Mail_Queue');
$mail_options = array('driver' => 'mail');

$mail_queue = new Mail_Queue($db_options, $mail_options);
$mail_queue->sendMailsInQueue($number);
?>