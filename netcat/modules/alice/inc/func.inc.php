<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31.10.2018
 * Time: 7:33
 */
function _l( $s ) {
	static $fr = FALSE;
	file_put_contents( LOG_FILE, date( 'Y-m-d H:i:s' ) . str_repeat( '=', 40 ) . "\n" . var_export( $s, 1 ) . "\n", $fr ? FILE_APPEND : NULL );
	$fr = TRUE;
}
