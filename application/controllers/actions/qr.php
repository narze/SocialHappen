<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}
	
	function index() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		// array(
		//	'_id' => ...
		// 	'hash' => ...
		// 	'data' => array(
		// 			//everything you need should be here, see and edit fields at action_data_lib
		// 	 )
		// )
	}
}