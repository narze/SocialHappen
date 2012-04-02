<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feedback extends CI_Controller {
	
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
		
		if(isset($action_data) && is_array($action_data)){
			echo $this->socialhappen->is_logged_in();
			$user_facebook_id = $this->FB->getUser();
			print_r($action_data);
		}else{
			show_error('Code not found');
		
		}
		
	}
	
	/**
	 *	Functional Test
	 *
	 **/
	function yreset_action_data(){
		
		$this->load->model('action_data_model');
		$this->action_data_model->
					delete(
						array(
							'action_id'=> $this->action_data_lib->get_platform_action('feedback')
							)
					);
		$form_data = array(
				'feedback_welcome_message' => 'What do you think about our store?',
				'feedback_thankyou_message' => 'Thank you, please come again',
			);
		$result = $this->action_data_lib->add_feedback_action_data($form_data);
		
	
	}
}