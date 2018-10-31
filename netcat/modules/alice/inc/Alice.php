<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31.10.2018
 * Time: 7:36
 */

class Alice {
	/**
	 * @var array
	 */
	private $request;
	/**
	 * @var array
	 */
	private $response;

	/**
	 * Alice constructor.
	 *
	 * @param $version string
	 * @param null $post
	 */
	function __construct( $version, $post = NULL ) {
		$this->response['version'] = $version;
		$this->request             = isset( $post ) ? $post : $_POST;
	}

	/**
	 * @return string
	 */
	public function getResponse() {
		$this->response['response'] = $this->testResponse();
		$this->response['session']  = $this->testSession();
		$result                     = json_encode( $this->response );

		return $result;
	}

	function testSession() {
		return array( 'session_id' => uniqid(), 'message_id' => 1, 'user_id' => 1 );
	}

	function testResponse() {
		return array( 'text' => 'Привет это тестовое сообщение', 'end_session' => FALSE );
	}

	/**
	 * @return null
	 */
	public function printResponse() {
		header( "Content-type: application/json; charset=utf-8" );
		print $this->getResponse();

		return NULL;
	}
}