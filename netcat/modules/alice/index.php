<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31.10.2018
 * Time: 1:46
 */
define( 'LOG_FILE', __DIR__ . '/alice.log' );
require_once 'inc/func.inc.php';

_l( $_POST );
_l( $_GET );
//_l( $_SERVER );
$ua = explode( ' ', $_SERVER['HTTP_USER_AGENT'] );
$ua = explode( '/', $ua[0] );
if ( ! in_array( $ua[0], array( 'YaAlice', 'Wget' ) ) ) {
	header( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500 );
	exit( 500 );
}
require_once 'inc/Alice.php';
require_once '../../../vars.inc.php';
require_once '../../connect_io.php';
require_once '../default/function.inc.php';

$ua = explode( '.', $ua[1] );

$alice = new Alice( "$ua[0].$ua[1]" );
_l( $alice->getResponse() );
$alice->printResponse();
