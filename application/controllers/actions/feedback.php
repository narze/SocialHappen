	<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feedback extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
		$this->load->library('action_user_data_lib');
	}
	
	function index() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		$user = $this->socialhappen->get_user();

		if($action_data && is_array($action_data) && $user){
			//print_r($user);
			$data = array(
							'action_data' => $action_data,
							'user' => $user
						);
			$this->load->helper('form');
			$this->load->view('actions/feedback/feedback_form', $data);
		}else{
			show_error('Code not found or user not logged in');
		}
		
	}

	function add_user_data_feedback(){
		print_r($this->input->post());
		
		$user_feedback = $this->input->post('user_feedback');
		$user_score = $this->input->post('user_score');
		$action_data_hash = $this->input->post('action_data_hash');

		$_GET['code'] = $action_data_hash;
		$action_data = $this->action_data_lib->get_action_data_from_code();

		$user = $this->socialhappen->get_user();
	
		if($user_feedback && $user_score && $action_data && $user) {
			$user_data = array(
								'user_feedback' => $user_feedback,
								'user_score' => (int) $user_score,
							);

			if($result = $this->action_user_data_lib->add_action_user_data(
																1, //missing
																$action_data['action_id'],
																get_mongo_id($action_data), 
																'dummychallengeid', //mising
																$user['user_id'],
																$user_data
				)){

				//platform action goes here

			}

			$data = array(
							'action_data' => $action_data
						);
			$this->load->view('actions/feedback/feedback_finish', $data);
		}else{
			show_error('Invalid data');
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
				'feedback_welcome_message' => 'Dear, Our Customer',
				'feedback_question_message' => 'What do you think about our store?',
				'feedback_vote_message' => 'Please provide your satisfaction score',
				'feedback_thankyou_message' => 'Thank you, please come again',
			);
		$result = $this->action_data_lib->add_feedback_action_data($form_data);
	}
}