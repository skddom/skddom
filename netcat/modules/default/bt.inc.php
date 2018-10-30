<?php

class CSKDMans {
	private $object;
	private $object_clean;
	private $log;
	private $cookie;
	const MessageID = 2316;
	const CookieName = 'fdsfasd2';

	function __construct( $object ) {
		$this->object_clean = $object;
		$this->object = md5( $object );
	}

	public function get() {
		global $db;

		return $db->get_var( "SELECT Votes FROM Message" . self::MessageID . " WHERE Subject = '" . $db->escape( $this->object ) . "'" ) ?: 0;
	}

	public function get_html() {
		return '<div data-myid="' . $this->object_clean . '" class="vote-skdmans' . ( $this->already_voted() ? ' voted' : '' ) . '">' . $this->get() . '</div>';
	}

	private function already_voted() {

		if ( isset( $_COOKIE[ self::CookieName ] ) ) {
			$this->cookie = unserialize( $_COOKIE[ self::CookieName ] );

			return isset( $this->cookie[ $this->object ] );
		} else {
			$this->cookie = array();
		}

		return FALSE;
	}

	public function ajax_response() {
		return array( 'html' => $this->get_html(), 'votes' => $this->get(), 'log' => $this->log );
	}

	public function set() {
		global $db;
		if ( ! $this->already_voted() ) {
			$db->query( "INSERT INTO Message" . self::MessageID .
			            " SET IP = '" . $_SERVER['REMOTE_ADDR'] . "',UserAgent='" . $_SERVER['HTTP_USER_AGENT'] . "'" .
			            ",Subdivision_ID = 501, Sub_Class_ID = 663" .
			            ",Created = now(), LastUser_ID = 0,`Subject` = '" . $this->object . "'" .
			            ",Votes = 1 ON DUPLICATE KEY UPDATE Votes = " . ( $this->get() + 1 ) );
			$this->log[]                   = $db->last_error;
			$this->cookie[ $this->object ] = $db->rows_affected;
			setcookie( self::CookieName, serialize( $this->cookie ), time() + 31536000, '/', $_SERVER['SERVER_NAME'] );
		} else {
			$this->log[] = 'already voted';
		}

		return $this->get();
	}
}