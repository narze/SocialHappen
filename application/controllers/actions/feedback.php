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

			$data = array(
							'action_data' => $action_data,
							'user' => $user
						);
			$this->load->helper('form');

			$template = array(
	      'title' => 'Welcome to SocialHappen',
	      'styles' => array(
	        'common/bootstrap.min',
	        'common/bootstrap-responsive.min'
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

	function show($action_data_id) {
		$user_id = NULL;
		$options = array('limit' => 10, 'offset' => 0);
		$feedbacks = $this->action_user_data_lib->get_action_user_data_by_action_data($action_data_id, $user_id, $options);
		$data = array(
			'feedbacks' => $feedbacks,
			'action_data_id' => $action_data_id
		);

		$template = array(
			'title' => 'Welcome to SocialHappen',
			'styles' => array(
				'common/bootstrap.min',
				'common/bootstrap-responsive.min'
				),
			'body_views' => array(
				'actions/feedback/show' => $data,
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
	}

	function get_form() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		$user = $this->socialhappen->get_user();

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

		$user = $this->socialhappen->get_user();

		if($user_feedback && $user_score && $action_data && $user && $challenge) {
			date_default_timezone_set('UTC');
			$user_data = array(
								'user_feedback' => $user_feedback,
								'user_score' => (int) $user_score,
								'timestamp' => time(),
							);
			$user_id = $user['user_id'];
			if($action_user_data_id = $this->action_user_data_lib->add_action_user_data(
																$challenge['company_id'],
																$action_data['action_id'],
																get_mongo_id($action_data),
																get_mongo_id($challenge),
																$user_id,
																$user_data
				)){

				//platform action goes here
				$this->load->library('audit_lib');
				$audit_data = array(
										'user_id' => $user_id,
										'action_id' => $action_data['action_id'],
										'app_id' => 0,
										'app_install_id' => 0,
										'page_id' => 0,
										'company_id' => $challenge['company_id'],
										'subject' => NULL,
										'object' => $user_score,
										'objecti' => $challenge['hash'],
										'image' => $challenge['detail']['image']
									);
				$audit_id = $this->audit_lib->audit_add($audit_data);

				//Update action user data with audit id
				$update_result = $this->action_user_data_lib->update_action_user_data($action_user_data_id, array('audit_id' => $audit_id));

				$this->load->library('achievement_lib');
				$info = array(
								'action_id'=> $action_data['action_id'],
								'app_install_id'=> 0,
								'page_id' => 0
							);
				$achievement_result = $this->achievement_lib->
					increment_achievement_stat($challenge['company_id'], 0, $user_id, $info, 1);

				//Check challenge after stat increment
				$this->load->library('challenge_lib');
				$check_challenge_result = $this->challenge_lib->check_challenge($challenge['company_id'], $user_id, $info);
			}

			if(!$audit_id || !$achievement_result){
				$action_data['data']['feedback_thankyou_message'] = 'Something\'s broken please try again later' ;
			}

			$data = array(
				'action_data' => $action_data
			);


	    $this->load->view('actions/feedback/feedback_finish', $data);
		} else {
			show_error('Invalid data');
		}
	}

	function show_user_data_feedback(){

		//get action_user_data list of an action_data (belong to a challenge) for selection
		$action_data_hash = $this->input->get('action_data_hash');

		$_GET['code'] = $action_data_hash;
		$action_data = $this->action_data_lib->get_action_data_from_code();
		//print_r($action_data);

		if($action_data){
			$action_data_id = get_mongo_id($action_data);
			$action_user_data_array = $this->action_user_data_lib->get_action_user_data_by_action_data($action_data_id);
			$action_user_data_id_array = array();

			foreach ($action_user_data_array as $action_user_data) {
				$action_user_data_id_array[] = get_mongo_id($action_user_data);
			}

			$this->load->library('challenge_lib');
			$challenge = $this->challenge_lib->get_one(array('criteria.action_data_id' => $action_data_id ));

			$criteria_name = '';
			foreach($challenge['criteria'] as $criteria){
				if($criteria['action_data_id'] == $action_data_id)
					$criteria_name = $criteria['name'];

			}

			$data = array(
							'action_user_data_id_array' => json_encode($action_user_data_id_array),
							'action_data_name' => $criteria_name,
						);

			$template = array(
								'title' => 'Welcome to SocialHappen',
								'styles' => array(
									'common/bootstrap.min',
									'common/bootstrap-responsive.min'
								),
								'body_views' => array(
									'actions/feedback/feedback_show' => $data,
									'common/vars' => array(
										'vars' => array(
											'base_url' => base_url()
										)
									)
								),
								'scripts' => array(
									'common/jquery.min',
									'common/bootstrap.min',
									'challenge/feedback/feedback',
								)
		    				);

		    $this->load->view('common/template', $template);
		}else{
			show_error('Invalid data');
		}

	}

	function read_action_user_data(){
		//ajax call from show_user_data_feedback
		//identify action_id first ?
		header('Content-Type: application/json', TRUE);
		$action_user_data_id_array = $this->input->post('action_user_data_id', TRUE);
		//print_r($action_user_data_id_array);
		if(!$action_user_data_id_array){
			echo json_encode(array('result' => 'error', 'message' => 'no input parameter : action_user_data_id'));
		}else{
			//$action_user_data_id_array = json_decode($action_user_data_id_array);
			$this->load->model('user_model');

			$action_user_data_array = array();

			$action_user_data_criteria = array('$or' => array(
											//array('_id' => new MongoId('4fd258d08a837bdc0e000000')),
								));

			foreach ($action_user_data_id_array as $action_user_data_id) {
				$action_user_data_criteria['$or'][] = array('_id' => new MongoId($action_user_data_id));

			}

			if($action_user_data = $this->action_user_data_lib->get_action_user_data_array($action_user_data_criteria)){;
				foreach($action_user_data as &$user_data){

					$user_data['user'] = $this->user_model->get_user_profile_by_user_id($user_data['user_id']);
					$user_data['user_data']['timestamp'] = date('Y-m-j H:i:s', $user_data['user_data']['timestamp']);
					$action_user_data_array[] = $user_data;
				}


			}

			if(sizeof($action_user_data_array) > 0){
				echo json_encode(array('result' => 'ok', 'data' => $action_user_data_array));
			}else{
				echo json_encode(array('result' => 'error', 'message' => 'action_user_data not found'));
			}

		}

	}

}