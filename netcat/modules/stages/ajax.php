<?php
if ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
	include_once '../../../vars.inc.php';
	include_once '../../connect_io.php';
	include_once '../default/function.inc.php';

	if ( isset( $_GET['f_StageSection'] ) ) {
		$id = intval( $_GET['f_StageSection'] );
		$id = ( $id == 3 ? '' : '_' . $id );
		if ( $query = $GLOBALS['db']->get_results( "SELECT c.StageReports$id" . "_ID id,c.StageReports$id" . "_Name name FROM Classificator_StageReports$id c WHERE c.Checked=1 ORDER BY c.StageReports$id" . "_Priority", ARRAY_A ) ) {
			header( "Content-type: application/json; charset=utf-8" );
			die( json_encode( $query ) );
		}
	}
	if ( isset( $_GET['vote-skdmans'] ) ) {
		$skd_mans = new CSKDMans( $_GET['vote-skdmans'] );
		$skd_mans->set();
		header( "Content-type: application/json; charset=utf-8" );
		die( json_encode( $skd_mans->ajax_response() ) );
	}
}
header( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', TRUE, 500 );
exit( 500 );
