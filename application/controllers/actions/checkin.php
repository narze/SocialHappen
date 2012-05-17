<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkin extends CI_Controller {
	private $basic_view = NULL;

	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
		$this->load->library('action_user_data_lib');

		$this->basic_view = $this->input->get('basic_view', TRUE);
	}
	
	function index() {
		$action_data = $this->action_data_lib->get_action_data_from_code();
		
		$user = $this->_get_user_data();

		if($action_data && is_array($action_data) && $user){
			//print_r($user);
			$facebook_data = array(
				'facebook_app_id' => $this->config->item('facebook_app_id'),
				'facebook_app_scope' => $this->config->item('facebook_player_scope'),
				'facebook_channel_url' => $this->facebook->channel_url
			);
			$this->load->vars(array(
		    	'static_fb_root' => $this->load->view('player/static_fb_root', $facebook_data, TRUE)
		  	));
			$data = array(
							'action_data' => $action_data,
							'user' => $user
						);
			$this->load->helper('form');

			if(!$this->basic_view){
				$body_views = array(
					        	'actions/checkin/checkin_form' => $data,
						        'common/vars' => array(
						          	'vars' => array(
						          		'base_url' => base_url()
						          	)
						        )
					        );
			}else{
				$body_views = array(
					        	'actions/checkin/checkin_form_basic' => $data,
						        'common/vars' => array(
						          	'vars' => array(
						          		'base_url' => base_url()
						          	)
						        )
					        );
			}

			$template = array(
		        'title' => 'Welcome to SocialHappen',
		        'styles' => array(
		          'common/bootstrap',
		          'common/bootstrap-responsive',
		          'common/jquery.facebook.multifriend.select',
		          'common/jquery.facebook.multifriend.select-list',
		        ),
		        'body_views' => $body_views,
		        'scripts' => array(
		          'common/jquery.min',
		          'common/jquery.facebook.multifriend.select',
		          'common/jquery-ui-1.8.20.autocomplete.min',
		          'challenge/checkin/checkin_form'
		        )
		    );

     		$this->load->view('common/template', $template);

/*
			if(!$this->basic_view){
				$this->load->view('actions/checkin/checkin_form', $data);
			}else{
				$this->load->view('actions/checkin/checkin_form_basic', $data);
			}
			*/
		} else {
			show_error('Code not found or user not logged in');
		}
		
	}

	function add_user_data_checkin(){
		//print_r($this->input->post());
		
		$facebook_place_id = $this->input->post('facebook_place_id');
		$tagged_user_facebook_ids_comma = $this->input->post('tagged_user_facebook_ids');
		$post_message = $this->input->post('post_message');
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
		
		if($facebook_place_id  && $action_data && $user && $challenge) {

			//min friend_count
			$tagged_user_facebook_ids = explode(",", $tagged_user_facebook_ids_comma);

			foreach($tagged_user_facebook_ids as &$tagged_user_facebook_id){
				$tagged_user_facebook_id = trim($tagged_user_facebook_id);
			}

			if(in_array($facebook_place_id, $action_data['data']['checkin_facebook_place_id'])){

				if(count($tagged_user_facebook_ids) >= $action_data['data']['checkin_min_friend_count']){
					$user_data = array(
										'facebook_place_id' => $facebook_place_id,
										'tagged_user_facebook_ids' => $tagged_user_facebook_ids,
										'timestamp' => time()
									);

					if($result = $this->action_user_data_lib->add_action_user_data(
																		$challenge['company_id'],
																		$action_data['action_id'],
																		get_mongo_id($action_data), 
																		get_mongo_id($challenge),
																		$user['user_id'],
																		$user_data
						)){

						//facebook post process
						$post_data = array(
								'message' => htmlspecialchars($post_message),
								// 'picture' => $settings['share_fb_picture'], 
								//'link' => $link, 
								//'name' => 'name', 
								'place' => $facebook_place_id,
								'tags' => $tagged_user_facebook_ids_comma,
								// 'caption' => 'share_fb_caption', 
								// 'description' => 'share_fb_description'
							);
						$post_result = $this->FB->api('me/feed', 'post', $post_data);

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
												'object' => $action_data['data']['checkin_facebook_place_name'],
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
						$action_data['data']['checkin_thankyou_message'] = 'Something\'s broken please try again later' ;
					}else if(!$post_result){
						$action_data['data']['checkin_thankyou_message'] = 'Connection to Facebook failed, information saved' ;
					}
				}else{
					$action_data['data']['checkin_thankyou_message'] = 'Your tagging is less than expected. Please try again' ;
				}

			}else{
				$action_data['data']['checkin_thankyou_message'] = 'Your check-in location isn\'t correct. Please try again' ;
			}

			$data = array(
				'action_data' => $action_data
			);
			
			$template = array(
        'title' => 'Welcome to SocialHappen',
        'styles' => array(
          'common/bootstrap',
          'common/bootstrap-responsive'
        ),
        'body_views' => array(
          'actions/checkin/checkin_finish' => $data
        ),
        'scripts' => array(
          'common/bootstrap.min',
        )
      );
      $this->load->view('common/template', $template);
		} else {
			show_error('Invalid data');
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
							'action_id'=> $this->action_data_lib->get_platform_action('checkin')
							)
					);
		$form_data = array(
				'checkin_facebook_place_id' => '162135693842364',
				'checkin_welcome_message' => 'Here you are!',
				'checkin_challenge_message' => 'Please check-in here at Figabyte',
				'checkin_thankyou_message' => 'Thank you, for check-in',
			);
		$result = $this->action_data_lib->add_checkin_action_data($form_data);
	}
}