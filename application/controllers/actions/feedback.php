<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feedback extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
		$this->load->library('action_user_data_lib');
	}
	
	function index() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		
		$user = $this->_get_user_data();

		if($action_data && is_array($action_data) && $user){
			//print_r($user);
			$data = array(
							'action_data' => $action_data,
							'user' => $user
						);
			$this->load->helper('form');

			$template = array(
	      'title' => 'Welcome to SocialHappen',
	      'styles' => array(
	        'common/bootstrap',
	        'common/bootstrap-responsive'
	      ),
	      'body_views' => array(
	        'actions/feedback/feedback_form' => $data,
	        'common/vars' => array(
	        	'vars' => array(
	        		'base_url' => base_url()
	        	)
	        )
	      ),
	      'scripts' => array(
	        'common/bootstrap.min',
	      )
	    );
	    $this->load->view('common/template', $template);
		} else {
			show_error('Code not found or user not logged in');
		}
		
	}

	function get_form() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		$user = $this->_get_user_data();

		$data = array(
			'action_data' => $action_data,
			'user' => $user
		);

		if($action_data && is_array($action_data) && $user){
			$this->load->view('actions/feedback/feedback_form', $data);
		}
	}

	function add_user_data_feedback(){
		//print_r($this->input->post());
		
		$user_feedback = $this->input->post('user_feedback');
		$user_score = $this->input->post('user_score');
		$action_data_hash = $this->input->post('action_data_hash');

		$_GET['code'] = $action_data_hash;
		$action_data = $this->action_data_lib->get_action_data_from_code();

		$this->load->library('challenge_lib');
		$challenge = $this->challenge_lib->get_one(
													array( 
														'criteria.action_data_id' => get_mongo_id($action_data)
													)
												);
		
		$user = $this->_get_user_data();
		
		if($user_feedback && $user_score && $action_data && $user && $challenge) {
			date_default_timezone_set('UTC');
			$user_data = array(
								'user_feedback' => $user_feedback,
								'user_score' => (int) $user_score,
								'timestamp' => time(),
							);

			if($result = $this->action_user_data_lib->add_action_user_data(
																$challenge['company_id'],
																$action_data['action_id'],
																get_mongo_id($action_data), 
																get_mongo_id($challenge),
																$user['user_id'],
																$user_data
				)){

				//platform action goes here
				$this->load->library('audit_lib');
				$audit_data = array(
										'user_id' => $user['user_id'],
										'action_id' => $action_data['action_id'],
										'app_id' => 0,
										'app_install_id' => 0,
										'page_id' => 0,
										'company_id' => $challenge['company_id'],
										'subject' => NULL,
										'object' => $challenge['detail']['name'],
										'objecti' => NULL,
										//'additional_data' => $additional_data
									);
				$audit_result = $this->audit_lib->audit_add($audit_data);

				$this->load->library('achievement_lib');
				$info = array(
								'action_id'=> $action_data['action_id'],
								'app_install_id'=> 0, 
								'page_id' => 0
							);
				$achievement_result = $this->achievement_lib->
										increment_achievement_stat($challenge['company_id'], 0, $user['user_id'], $info, 1);

			}

			if(!$audit_result || !$achievement_result){
				$action_data['data']['feedback_thankyou_message'] = 'Something\'s broken please try again later' ;
			}

			$data = array(
				'action_data' => $action_data
			);
			
			
	    $this->load->view('actions/feedback/feedback_finish', $data);
		} else {
			echo '<pre>';
			 var_dump($user_feedback , $user_score , $action_data , $user , $challenge);
			 echo '</pre>'; show_error('Invalid data');
		}
		
	}

	function _get_user_data(){
		$user = $this->socialhappen->get_user();

		if(!$user){
			if($user_facebook_id = $this->FB->getUser()){
				$this->load->model('user_model');
				$user = $this->user_model->get_user_profile_by_user_facebook_id($user_facebook_id);
			}
		}

		if($user){
			return $user;
		} else {
			return NULL;
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