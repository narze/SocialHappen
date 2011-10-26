<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tab extends CI_Controller {
	var $signedRequest;
	var $page;
	var $app_data;
	
	function __construct(){
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		
		$this->load->library('fb_library/fb_library',
							array(
							  'appId'  => $this->config->item('facebook_app_id'),
							  'secret' => $this->config->item('facebook_api_secret'),
							  'cookie' => true,
							),
							'FB');
		$this->signedRequest = $this->FB->getSignedRequest();
		$this->page = $this->signedRequest['page'];
		$this->app_data = isset($this->signedRequest['app_data']) ? json_decode(base64_decode($this->signedRequest['app_data']), TRUE) : NULL ;
	}
	
	function index($page_id = NULL, $token = NULL){
		$user_facebook_id = $this->FB->getUser();
		
		$this->load->model('User_model','User');
		$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		$token = issetor($this->signedRequest['oauth_token']);
		
		$this->load->model('Page_model','Page');
		if(!$page_id){
			if(!$this->Page->get_page_id_by_facebook_page_id($this->page['id'])) 
				exit(); //HARDCODE prevent redirect loop
			//redirect("tab/".$this->Page->get_page_id_by_facebook_page_id($this->page['id']).'/'.$token);	

			//passive assign page_id
			$page_id = $this->Page->get_page_id_by_facebook_page_id($this->page['id']);
			
		}
		
		$this->load->model('user_model','users');
		$user = $this->users->get_user_profile_by_user_id($user_id);
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		$this->load->model('company_model','companies');
		$company = $this->companies->get_company_profile_by_page_id($page_id);
		$this->load->model('user_companies_model','user_companies');
		$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
		
		//is user register to current page
		$this->load->model('page_user_data_model','page_user_data');
		$is_user_register_to_page = $this->page_user_data->get_page_user_by_user_id_and_page_id($user_id, $page_id);
		
		$this->config->load('pagination', TRUE);
		$per_page = $this->config->item('per_page','pagination');

		
		// if($is_admin){
			// $page_update = array();
			// if(!$page['page_installed']){
				// $page_update['page_installed'] = TRUE;
			// } else if($page['page_app_installed_id'] != 0){
				// $page_update['page_app_installed_id'] = 0;
			// }		
			// $this->pages->update_page_profile_by_page_id($page_id, $page_update);
		// }
		$data = array(
			'header' => $this->load->view('tab/header', 
				array(
					'facebook_app_id' => $this->config->item('facebook_app_id'),
					'vars' => array(
									'page_id' => $page_id,
									'user_id' => $user_id,
									'is_guest' => $user ? FALSE : TRUE,
									'token' => urlencode($token),
									'per_page' => $per_page,
									'notifications_per_page' => 5,
									'view' => isset($this->app_data['view']) ? $this->app_data['view'] : '',
									'return_url' => isset($this->app_data['return_url']) ? $this->app_data['return_url'] : ''
					),
					'script' => array(
						'common/functions',
						'tab/bar',
						'tab/profile',
						'tab/main',
						'tab/account',
						'tab/dashboard',
						'common/jquery.pagination',
						'common/jquery.form',
						'common/jquery.countdown.min',
						'common/fancybox/jquery.fancybox-1.3.4.pack'
					),
					'style' => array(
						'common/facebook',
						'common/facebook-main',
						'common/jquery.countdown',
						'common/fancybox/jquery.fancybox-1.3.4',
						'../../css/api_app_bar'
					)
				),
			TRUE),
			'bar' => 
			$this->socialhappen->get_bar(
				array(
					'user_id' => $user_id,
					'user_facebook_id' => $user_facebook_id,
					'page_id' => $page_id
				)
			),
			'main' => $this->load->view('tab/main',array(),
			TRUE),
			'footer' => $this->load->view('tab/footer',array(),
			TRUE)
		);
		$this->parser->parse('tab/tab_view', $data);
	}
	
	function logout($page_id = NULL, $app_install_id = NULL)
	{
		echo "Logged out SocialHappen";
		$this->socialhappen->logout();
		if($app_install_id){
			$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_app($app_install_id, FALSE, TRUE)));
		} else if ($page_id){
			$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_page($page_id, FALSE, TRUE)));
		}
	}
	
	function dashboard($page_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		//is_admin
		$user_facebook_id = $this->FB->getUser();
		$this->load->model('User_model','User');
		$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		$this->load->model('company_model','companies');
		$company = $this->companies->get_company_profile_by_page_id($page_id);
		$this->load->model('user_companies_model','user_companies');
		$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
		
		if($page){
			
			$data = array(
							'page' => $page,
							'is_liked' => $this->page['liked'],
							'is_admin' => $is_admin
			);
			$this->load->view("tab/dashboard",$data);
		
		}
	}
	
	function profile($page_id = NULL, $token = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			$user_facebook_id = $this->FB->getUser();
			$this->load->model('User_model','User');
			$user = $this->User->get_user_profile_by_user_facebook_id($user_facebook_id);
			if(!$user){
				echo 'You are guest';
			} else {
				$user_id = $user['user_id'];
				$fql = 'SELECT uid FROM page_fan WHERE page_id = '.$page['facebook_page_id'].' and uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$user_facebook_id.')';
				
				$response = $this->FB->api(array(
					'method' => 'fql.query',
					'access_token' => urldecode($token),
					'query' =>$fql,
					));
				
				$friends = array();
				foreach($response as $friend){
					$facebook_user = $this->FB->api('/'.$friend['uid'].'');
					$friends[] = array(
										'uid' => $friend['uid'],
										'name' => $facebook_user['name'],
										'image' => 'http://graph.facebook.com/'.$friend['uid'].'/picture'
									);
				}
				
				$app_campaign_filter = $this->input->get('filter');
				
				//user apps
				$this->load->model('user_apps_model','user_apps');
				$user_apps = $this->user_apps->get_user_apps_by_user_id($user_id);
				
				//user campaigns
				$this->load->model('user_campaigns_model','user_campaigns');
				$user_campaigns = $this->user_campaigns->get_user_campaigns_by_user_id($user_id);
				
				$data = array(
					'user' => $user,
					'friends' => $friends,
					'user_apps' => ($app_campaign_filter != 'campaign') ? $user_apps : NULL,
					'user_campaigns' => ($app_campaign_filter != 'app') ? $user_campaigns : NULL
				);
				$this->load->view('tab/profile', $data);
			}
		}
	}
	
	function apps_campaigns($page_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			$user_facebook_id = $this->FB->getUser();
		
			$this->load->model('User_model','User');
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id($user_id);
			
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_page_id($page_id);
			
			$this->load->model('user_companies_model','user_companies');
			$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
			$is_user = $is_guest = FALSE;
			if(!$user_id) {
				$user_id = 0;
				$is_guest = TRUE;
			} else {
				$is_user = TRUE;
			}
			
			if($is_admin) {
				$view_as = $this->input->get('viewas');
				if($view_as == 'guest'){
					$is_guest = TRUE;
					$is_user = FALSE;
					$is_admin = FALSE;
				} else if($view_as == 'user'){
					$is_guest = FALSE;
					$is_user = TRUE;
					$is_admin = FALSE;
				} else {
					$is_guest = FALSE;
					$is_user = FALSE;				
				}
			}
		
			$app_campaign_filter = $this->input->get('filter');
			if(!$app_campaign_filter){
				$full_limit = $limit;
				$limit /= 2;
				$offset /= 2;
			}
			$this->load->model('campaign_model','campaigns');
			$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id,$limit,$offset);
			if(!$app_campaign_filter && count($campaigns)<$limit){
				$limit = $full_limit - count($campaigns);
			}
			$this->load->model('installed_apps_model','installed_apps');
			$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id,$limit,$offset);

			$data = array('user'=>$user,
							'page' => $page,
							'is_admin' => $is_admin,
							'is_user' => $is_user,
							'is_guest' => $is_guest,
							'is_liked' => $this->page['liked'],
							'campaigns' => ($app_campaign_filter != 'app') ? $campaigns : NULL,
							'apps' => ($app_campaign_filter != 'campaign') ? $apps : NULL
			);
			$this->load->view('tab/apps_campaigns', $data);
		}
	}
	
	function user_apps_campaigns($page_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			$user_facebook_id = $this->FB->getUser();
		
			$this->load->model('User_model','User');
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id($user_id);
			
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_page_id($page_id);
			
			$this->load->model('user_companies_model','user_companies');
			$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
			$is_user = $is_guest = FALSE;
			if(!$user_id) {
				$user_id = 0;
				$is_guest = TRUE;
			} else {
				$is_user = TRUE;
			}
			
			if($is_admin) {
				$view_as = $this->input->get('viewas');
				if($view_as == 'guest'){
					$is_guest = TRUE;
					$is_user = FALSE;
					$is_admin = FALSE;
				} else if($view_as == 'user'){
					$is_guest = FALSE;
					$is_user = TRUE;
					$is_admin = FALSE;
				} else {
					$is_guest = FALSE;
					$is_user = FALSE;				
				}
			}
		
			$app_campaign_filter = $this->input->get('filter');
			if(!$app_campaign_filter){
				$full_limit = $limit;
				$limit /= 2;
				$offset /= 2;
			}
			
			//user campaigns
			$this->load->model('user_campaigns_model','user_campaigns');
			$campaigns = $this->user_campaigns->get_user_campaigns_by_user_id($user_id);
			if(!$app_campaign_filter && count($campaigns)<$limit){
				$limit = $full_limit - count($campaigns);
			}
			//user apps
			$this->load->model('user_apps_model','user_apps');
			$apps = $this->user_apps->get_user_apps_by_user_id($user_id);
			
			$data = array('user'=>$user,
							'page' => $page,
							'is_admin' => $is_admin,
							'is_user' => $is_user,
							'is_guest' => $is_guest,
							'is_liked' => $this->page['liked'],
							'campaigns' => ($app_campaign_filter != 'app') ? $campaigns : NULL,
							'apps' => ($app_campaign_filter != 'campaign') ? $apps : NULL
			);
			$this->load->view('tab/apps_campaigns', $data);
		}
	}
	
	function activities($page_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			$activity_filter = $this->input->get('filter'); //(all) app campaign me

			$this->load->library('audit_lib');
			$this->load->model('audit_action_model','audit_action');
			$this->load->model('campaign_model','campaigns');
			$this->load->model('installed_apps_model','installed_apps');
			
			$data = array();
			$data['activities'] = array();
			if($activity_filter == 'app'){
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('app_install_id'=>$app['app_install_id'])));
				}				
			} else if ($activity_filter == 'campaign'){
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('campaign_id'=>$campaign['campaign_id'])));
				}
			} else if ($activity_filter == 'me'){
				$user_facebook_id = $this->FB->getUser();
				$this->load->model('User_model','users');
				$user_id = $this->users->get_user_id_by_user_facebook_id($user_facebook_id);
			
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('user_id'=>$user_id, 'campaign_id'=>$campaign['campaign_id'])));
				}
				
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('user_id'=>$user_id, 'app_install_id'=>$app['app_install_id'])));
				}
			} else if ($activity_filter == 'me_app'){
				$user_facebook_id = $this->FB->getUser();
				$this->load->model('User_model','users');
				$user_id = $this->users->get_user_id_by_user_facebook_id($user_facebook_id);
				
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('user_id'=>$user_id, 'app_install_id'=>$app['app_install_id'])));
				}				
			} else if ($activity_filter == 'me_campaign'){
				$user_facebook_id = $this->FB->getUser();
				$this->load->model('User_model','users');
				$user_id = $this->users->get_user_id_by_user_facebook_id($user_facebook_id);
				
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('user_id'=>$user_id, 'campaign_id'=>$campaign['campaign_id'])));
				}
			} else {
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('campaign_id'=>$campaign['campaign_id'])));
				}
				
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit_lib->list_audit(array('app_install_id'=>$app['app_install_id'])));
				}
			}
			
			$this->load->model('user_model','users');
			foreach($data['activities'] as &$activity)
			{
				if(isset($activity['user_id']))
				{
					$user = $this->users->get_user_profile_by_user_id($activity['user_id']);
				}
				if(!isset($user)) { unset($activity['user_id']); continue; }
				$activity['user_image'] = $user['user_image'];
				$activity['user_name'] = $user['user_first_name'].' '.$user['user_last_name'];
				//$activity['time_ago'] = '1 day ago';
				//$activity['source'] = 'web';
				//$activity['star_point'] = 5;
			}
			unset($activity);
		
			$this->load->view('tab/activities',$data);
		}
	}
	
	function leaderboard($page_id = NULL){}
	
	function favorites($user_id = NULL){}

	function notifications($user_id = NULL) {
		if($this->input->get('return_url')) $return_url = $this->input->get('return_url');
		else $return_url = '';
		$this->load->vars(array('return_url' => $return_url));
		$this->load->view('tab/notifications');
	}
	
	/**
	 * JSON : Get notifications
	 * @param $user_id
	 * @param $limit
	 * @param $offset
	 * @author Weerapat P.
	 */
	function json_get_notifications($user_id = NULL, $limit = NULL, $offset = 0) {
		$this->socialhappen->ajax_check();
		$this->load->library('notification_lib');
		echo json_encode($this->notification_lib->lists($user_id, $limit, $offset));
	}
	
	/**
	 * JSON : Count user notifications
	 * @param $user_id
	 * @author Weerapat P.
	 */
	function json_count_user_notifications($user_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('notification_model','notification');
		echo $this->notification->count(array('user_id'=>(int)$user_id));
	}
	
	function account($page_id = NULL, $user_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			
			if($user_id != $this->socialhappen->get_user_id()){
				log_message('error','user_id mismatch');
				echo 'error : id mismatch'; //DEBUG
			} else {
				$user = $this->socialhappen->get_user();
				$user_facebook = $this->facebook->getUser();
				
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
				$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');	
				$this->form_validation->set_rules('about', 'About', 'trim|xss_clean');
				$this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
					
				$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
				if ($this->form_validation->run() == FALSE) // validation hasn't been passed
				{
					$this->load->view('tab/account', array('page'=>$page,'user'=>$user,'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id'])));
				}
				else // passed validation proceed to post success logic
				{
					if(set_value('use_facebook_picture')){
						$user_image = issetor($this->facebook->get_profile_picture($user['user_facebook_id']));
					} else if (!$user_image = $this->socialhappen->upload_image('user_image')){
						$user_image = $user['user_image'];
					}
				
					// build array for the model
					$user_update_data = array(
									'user_first_name' => set_value('first_name'),
									'user_last_name' => set_value('last_name'),
									'user_about' => set_value('about'),
									'user_image' => $user_image
								);
					$this->load->model('user_model','users');
					if ($this->users->update_user($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
					{
						$this->load->view('tab/account', array('page'=>$page,'user'=>array_merge($user,$user_update_data), 'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id']),'success' => TRUE));
					}
					else
					{
						log_message('error','update user failed');
						echo 'error occured';
					}
				}
			}
		}
	}
	
	function guest(){
		$data = array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_default_scope' => $this->config->item('facebook_default_scope')
		);
		$this->load->view('tab/guest', $data);
	}
	
	function signup($page_id = NULL, $app_install_id = NULL){
		$facebook_access_token = $this->input->get('facebook_access_token');
		// $this->load->library('form_validation');
		$facebook_user = $this->facebook->getUser($facebook_access_token);
		//$this->load->model('user_model','users');
		$this->load->model('user_model','users');
		//if is sh user redirect popup to "regged"
		if($this->users->get_user_profile_by_user_facebook_id($facebook_user['id'])){
			echo "You're already a Socialhappen user";
			$this->socialhappen->login();
			if($app_install_id){
				$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_app($app_install_id, FALSE, TRUE)));
			} else if ($page_id){
				$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_page($page_id, FALSE, TRUE)));
			}
		} else {
			$this->load->helper('form');
			$user_facebook_image = $this->facebook->get_profile_picture($facebook_user['id']);
			// $this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
			// $this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
			// $this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');
				
			// $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			// if ($this->form_validation->run() == FALSE)
			// {
				$this -> load -> view('tab/signup', 
						array(
							'facebook_user'=>$facebook_user,
							'user_profile_picture'=>$user_facebook_image,
							'page_id' => $page_id
						)
				);
			// }
			// else
			// {
				// if (!$user_image = $this->socialhappen->upload_image('user_image')){
					// $user_image = $user_facebook_image;
				// }
				
				// $this->load->model('user_model','users');
				// $post_data = array(
					// 'user_first_name' => set_value('first_name'),
					// 'user_last_name' => set_value('last_name'),
					// 'user_email' => set_value('email'),
					// 'user_image' => $user_image,
					// 'user_facebook_id' => $facebook_user['id']
				// );
				// if($user_id = $this->users->add_user($post_data)){
					// $this->socialhappen->login();
					// if(isset($app_install_id)){
						// redirect('tab/signup_page/'.$page_id.'/'.$app_install_id);
					// }else{
						// redirect('tab/signup_page/'.$page_id);
					// }
				// } else {
					// log_message('error','add user failed : '. print_r($user_add_result, TRUE));
					// log_message('error','$user : '. print_r($user, TRUE));
					// echo 'Error occured';
				// }
			// }
		}
	}
	
	function signup_submit($page_id = NULL, $app_install_id = NULL){
		if(!$facebook_user = $this->facebook->getUser()){
			$data = array(
				'status' => 'error',
				'error' => 'no_fb_user',
			);
		} else {
			$data = array(
				'user_first_name' => $this->input->get('first_name'),
				'user_last_name' => $this->input->get('last_name'),
				'user_email' => $this->input->get('email')
			);
			
			$this->load->library('text_validate');
			$validate_array = array(
				'first_name' => array('label' => 'First name', 'rules' => 'required', 'input' => $data['user_first_name']),
				'last_name' => array('label' => 'Last name', 'rules' => 'required', 'input' => $data['user_last_name']),
				'email' => array('label' => 'Email', 'rules' => 'required|email', 'input' => $data['user_email'], 'verify_message' => 'Please enter a valid email.')
			);
			$validation_result = $this->text_validate->text_validate_array($validate_array);
			
			if(!$validation_result){
				$data['status'] = 'error';
				$data['error'] = 'verify';
				$validate_errors = array();
				foreach($validate_array as $key => $value){
					if(!$value['passed']){
						$validate_errors[$key] = $value['error_message'];
					}
				}
				$data['error_messages'] = $validate_errors;
			} else {
				// if (!$user_image = $this->socialhappen->upload_image('user_image')){
					$user_image = $this->facebook->get_profile_picture($facebook_user['id']);;
				// }
				$this->load->model('user_model','users');
				$post_data = array(
					'user_first_name' => $data['user_first_name'],
					'user_last_name' => $data['user_last_name'],
					'user_email' => $data['user_email'],
					'user_image' => $user_image,
					'user_facebook_id' => $facebook_user['id']
				);
				
				if(!$user_id = $this->users->add_user($post_data)){
					//TODO : erase uploaded image
					log_message('error','add user failed : '. print_r($user_add_result, TRUE));
					log_message('error','$user : '. print_r($user, TRUE));
					echo 'Error occured';
					$data['status'] = 'error';
					$data['error'] = 'add_user';
				} else {
					$this->socialhappen->login();
					$data['status'] = 'ok';
					
					$this->load->library('audit_lib');
					$action_id = $this->socialhappen->get_k('audit_action','User Register SocialHappen');
					$this->audit_lib->add_audit(
						0,
						$user_id,
						$action_id,
						'', 
						'',
						array(
							'app_install_id' => 0,
							'user_id' => $user_id,
							'page_id' => $page_id
						)
					);
					
					$this->load->library('achievement_lib');
					$info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id'=>$page_id);
					$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
				}
				
			}
		}
		echo $this->input->get('callback').'('.json_encode($data).')';
	}
	
	function signup_page($page_id = NULL, $app_install_id = NULL){

		$this->load->library('form_validation');
		$facebook_access_token = $this->input->get('facebook_access_token');
		$facebook_user = $this->facebook->getUser($facebook_access_token);
		$user_facebook_image = $this->facebook->get_profile_picture($facebook_user['id']);
		
		$this->load->model('page_model','pages');
		$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		$data = array();
		// foreach($page_user_fields as $user_fields){
			// $required = ($user_fields['required']) ? "|required" : "";
			// switch($user_fields['type']){
				// case 'radio':
					// $this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);	
				// break;
				// case 'checkbox':
					// $this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'xss_clean'.$required);
				// break;
				// case 'textarea':
					// $this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);
				// break;					
				// case 'text':
				// default:
					// $this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);
			// }
			// $data[$user_fields['name']] = $this->input->post($user_fields['name']);
		// }
		
		// if(!$page_user_fields){
			// $this->form_validation->set_rules('empty', 'Empty', 'required');
		// }
		$this->load->model('user_model','user');
		$this->load->vars(array(
				'user' => ($user = $this->socialhappen->get_user()) ? $user : $this->user->get_user_profile_by_user_facebook_id($facebook_user['id']),
				'page_id' => $page_id,
				'page' => $page,
				'page_user_fields' => $page_user_fields,
				'facebook_user'=>$facebook_user,
				'user_profile_picture'=>$user_facebook_image,
				'app_install_id' => $app_install_id
			)
		);
		
		// if ($this->form_validation->run() == FALSE){
		$this->load->view('tab/signup_page');
		// } else {
			
			// $this->load->model('page_user_data_model','page_users');
			// $data = array(
				// 'user_id' => $this->socialhappen->get_user_id(), //NULL when differect origin
				// 'page_id' => $page_id,
				// 'user_data' => $data
			// );
			
			// if(!isset($app_install_id)){ // mode = page
				// $this->load->model('page_model','page');
				// $page = $this->page->get_page_profile_by_page_id($page_id);
				// $facebook_tab_url = $page['facebook_tab_url'];
			// } else { // mode = app
				// $this->load->model('installed_apps_model','installed_app');
				// $app = $this->installed_app->get_app_profile_by_app_install_id($app_install_id);
				// $facebook_tab_url = $app['facebook_tab_url'];
			// }
      
			// if($this->page_users->add_page_user($data)){
				// $this->load->view('tab/signup_complete', array('facebook_tab_url' => $facebook_tab_url));
			// } else {
				// $this->load->view('tab/signup_page', array('error'=>print_r($data,TRUE)));
			// }
		// }
		$this->socialhappen->login();
	}
	
	function signup_page_submit($page_id = NULL, $app_install_id = NULL){
		if(!$user = $this->socialhappen->get_user()){
			$data = array(
				'status' => 'error',
				'error' => 'no_user',
				'message' => 'Cannot find user, please relogin.'
			);
		} else {
			$data = array();
			$this->load->model('page_model','pages');
			$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
			$page = $this->pages->get_page_profile_by_page_id($page_id);
			
			$post_data = array('user_data'=>array());
			$validate_array = array();
			if(!$page_user_fields){ //Empty field
				$validation_result = TRUE;
			} else {
				foreach($page_user_fields as $user_fields){
					$user_fields_name = $user_fields['name'];
					$user_fields_data = $this->input->get($user_fields_name);
					$post_data['user_data'][$user_fields_name] = $user_fields_data;
					$validate_array[$user_fields_name] = array(
						'label' => $user_fields['label'],
						'rules' => $user_fields['required'] ? 'required' : '',
						'input' => $user_fields_data,
						'verify_message' => $user_fields['verify_message']
					);
				}
				
				// log_message('error', print_r($post_data['user_data'],TRUE));
				// log_message('error', print_r($validate_array,TRUE));
				
				$this->load->library('text_validate');
				$validation_result = $this->text_validate->text_validate_array($validate_array);
				// log_message('error', print_r($validate_array,TRUE));
			
			}
			if(!$validation_result){ //TODO : error checking foreach $data['user_data']
				$data['status'] = 'error';
				$data['error'] = 'verify';
				$validate_errors = array();
				foreach($validate_array as $key => $value){
					if(!$value['passed']){
						$validate_errors[$key] = $value['error_message'];
					}
				}
				$data['error_messages'] = $validate_errors;
			} else {
				$post_data['user_id'] = $user['user_id'];
				$post_data['page_id'] = $page_id;
				
				if(!isset($app_install_id)){ // mode = page
					$this->load->model('page_model','page');
					$page = $this->page->get_page_profile_by_page_id($page_id);
					if(!$facebook_tab_url = $page['facebook_tab_url']){
						$facebook_tab_url = $this->facebook_page($page_id, TRUE, TRUE);
					}
				} else { // mode = app
					$this->load->model('installed_apps_model','installed_app');
					$app = $this->installed_app->get_app_profile_by_app_install_id($app_install_id);
					if(!$facebook_tab_url = $app['facebook_tab_url']){
						$facebook_tab_url = $this->facebook_app($page_id, TRUE, TRUE);
					}
				}
				
				$this->load->model('page_user_data_model','page_users');
				if(!$this->page_users->add_page_user($post_data)){
					log_message('error','add page user failed');
					log_message('error','$post_data : '. print_r($post_data, TRUE));
					$data['status'] = 'error';
					$data['error'] = 'add_page_user';
				} else {
					$data['status'] = 'ok';
					$data['redirect_url'] = $facebook_tab_url;
					
					$action_id = $this->socialhappen->get_k('audit_action','User Register Page');
					$user_id = $user['user_id'];
					$this->load->library('audit_lib');
					$audit_info = array('page_id' => $page_id);
					if(isset($app_install_id)){
						$audit_info['app_install_id'] = $app_install_id;
					}
					$this->audit_lib->add_audit(
						$user_id,
						$action_id,
						'', 
						'',
						$audit_info
					);
					
					$this->load->library('achievement_lib');
					$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'page_id'=>$page_id);
					$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
				}
			}
		}
		echo $this->input->get('callback').'('.json_encode($data).')';
	}
	
	function signup_complete(){
		$redirect_url = $this->input->get('next');
		$this->load->view('tab/signup_complete', array('redirect_url' => $redirect_url));
	}
	
	function page_installed($page_id = NULL){
		$data = array('page_id' => $page_id);
		$this->load->view('tab/page_installed', $data);
	}
	
	function app_installed($app_install_id = NULL){
		$data = array('app_install_id' => $app_install_id);
		$this->load->view('tab/app_installed', $data);
	}
	
	/**
	 * View login button
	 * @author Manassarn M.
	 */
	function login_button($page_id = NULL){
		$this->load->vars(array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_default_scope' => $this->config->item('facebook_default_scope'),
			'page_id' => $page_id
		));
		$this->load->view('tab/login_button');
	}
	
	/**
	 * Go to socialhappen facebook tab in specified page
	 * @param $page_id
	 * @param $force_update If true, facebook_tab_url will be forced to update
	 * @param $return If true, facebook_tab_url will be return instead of browser redirect
	 * @author Manassarn M.
	 */
	function facebook_page($page_id = NULL, $force_update = FALSE, $return = FALSE){
		$this->load->model('page_model','page');
		if(!$page = $this->page->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		$facebook_tab_url = $page['facebook_tab_url'];
		if(!$facebook_tab_url || $force_update){
			$facebook_tab_url = $this->facebook->get_facebook_tab_url($this->config->item('facebook_app_id'), $page['facebook_page_id']);
			
			$this->page->update_facebook_tab_url_by_page_id($page_id, $facebook_tab_url);
		}
		if($return){
			return $facebook_tab_url;
		}
		redirect($facebook_tab_url);
	}
	
	/**
	 * Go to app's facebook tab
	 * @param $app_install_id
	 * @param $force_update If true, facebook_tab_url will be forced to update
	 * @param $return If true, facebook_tab_url will be return instead of browser redirect
	 * @author Manassarn M.
	 */
	function facebook_app($app_install_id = NULL, $force_update = FALSE, $return = FALSE){
		$this->load->model('installed_apps_model','installed_app');
		if(!$app = $this->installed_app->get_app_profile_by_app_install_id($app_install_id)){
			return FALSE;
		}
		$facebook_tab_url = $app['facebook_tab_url'];
		if(!$facebook_tab_url || $force_update){
			$this->load->model('page_model','page');
			$page = $this->page->get_page_profile_by_page_id($app['page_id']);
			$facebook_tab_url = $this->facebook->get_facebook_tab_url($app['app_facebook_api_key'], $page['facebook_page_id']);
			
			$this->installed_app->update_facebook_tab_url_by_app_install_id($app_install_id, $facebook_tab_url);
		}
		if($return){
			return $facebook_tab_url;
		}
		redirect($facebook_tab_url);
	}
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
