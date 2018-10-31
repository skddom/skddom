<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31.10.2018
 * Time: 1:46
 */
include_once '../../../vars.inc.php';
include_once '../../connect_io.php';
include_once '../default/function.inc.php';
function _l( $s ) {
	static $fr = FALSE;
	$fn = __FILE__ . '.log';

	file_put_contents( $fn, var_export( $s, 1 ). "\n" . str_repeat( '=', 40 ) . "\n", $fr ? FILE_APPEND : NULL );
	$fr = TRUE;
}

$ret = array( 'ip' => $_SERVER['REMOTE_ADDR'] );
_l( $_REQUEST );
_l( $_SERVER );

print json_encode( $ret );