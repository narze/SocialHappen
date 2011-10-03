<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tab extends CI_Controller {
	var $signedRequest;
	var $page;
	var $app_data;
	
	function __construct(){
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
									'token' => base64_encode($token),
									'per_page' => $per_page,
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
	
	function logout($redirect_url = NULL)
	{
		$redirect_url = $this->input->get('url');
		$this->socialhappen->logout();
		$this->load->view('common/redirect', array('refresh'=>TRUE));
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
					'access_token' => base64_decode($token),
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
					$action = $this->audit_action->get_action($activity['app_id'],$activity['action_id']);
					$user = $this->users->get_user_profile_by_user_id($activity['user_id']);
					$activity['user_image'] = $user['user_image'];
					
					//$activity['activity_detail'] = $action[0]['description'];
					//$activity['time_ago'] = '1 day ago';
					//$activity['source'] = 'web';
					//$activity['star_point'] = 5;
				}
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
		
		$this->load->library('notification_lib');
		$this->load->vars(array(
			'notifications' => $this->notification_lib->lists($user_id, $limit = NULL, $offset = 0),
			'return_url' => $return_url
			)
		);
		
		$this->load->view('tab/notifications');
	
	}
	
	function account($page_id = NULL, $user_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			
			if($user_id != $this->socialhappen->get_user_id()){
				echo 'error : id mismatch'; //DEBUG
			} else {
				$user = $this->socialhappen->get_user();
				$user_facebook = $this->facebook->getUser($user['user_facebook_id']);
				
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
				$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
				$this->form_validation->set_rules('gender', 'Gender', 'required|xss_clean');
				$this->form_validation->set_rules('birth_date', 'Birth date', 'trim|xss_clean');
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
									'user_gender' => set_value('gender'),
									'user_birth_date' => set_value('birth_date'),
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
	
	function signup($page_id = NULL){
		$this->load->library('form_validation');
		$facebook_user = $this->facebook->getUser();
		//$this->load->model('user_model','users');
		$this->load->model('user_model','users');
		//if is sh user redirect popup to "regged"
		if($this->users->get_user_profile_by_user_facebook_id($facebook_user['id'])){
			echo "You're already a Socialhappen user";
			exit();
		}
		$user_facebook_image = $this->facebook->get_profile_picture($facebook_user['id']);
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this -> load -> view('tab/signup', 
					array(
						'facebook_user'=>$facebook_user,
						'user_profile_picture'=>$user_facebook_image,
						'page_id' => $page_id
					)
			);
		}
		else
		{
			if (!$user_image = $this->socialhappen->upload_image('user_image')){
				$user_image = $user_facebook_image;
			}
			$user = array(
					       	'user_first_name' => set_value('first_name'),
					       	'user_last_name' => set_value('last_name'),
					       	'user_email' => set_value('email'),
					       	'user_image' => $user_image,
					       	'user_facebook_id' => $facebook_user['id']
						);
					
			$user_add_result = json_decode($this->curl->ssl(FALSE)->simple_post(base_url().'user/json_add', $user), TRUE);
			
			if ($user_add_result['status'] == 'OK')
			{
				$this->socialhappen->login();
				redirect('tab/signup_page/'.$page_id);
			}
			else
			{
				echo 'Error occured';
			}
		}
	}
	
	function signup_page($page_id = NULL){
		$this->load->library('form_validation');
		$facebook_user = $this->facebook->getUser();
		$user_facebook_image = $this->facebook->get_profile_picture($facebook_user['id']);
		
		$this->load->model('page_model','pages');
		$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
		
		$data = array();
		foreach($page_user_fields as $user_fields){
			$required = ($user_fields['required']) ? "|required" : "";
			switch($user_fields['type']){
				case 'radio':
					$this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);	
				break;
				case 'checkbox':
					$this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'xss_clean'.$required);
				break;
				case 'textarea':
					$this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);
				break;					
				case 'text':
				default:
					$this->form_validation->set_rules($user_fields['name'], $user_fields['label'], 'trim|xss_clean'.$required);
			}
			$data[$user_fields['name']] = $this->input->post($user_fields['name']);
		}
		
		if(!$page_user_fields){
			$this->form_validation->set_rules('empty', 'Empty', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('tab/signup_page', array(
				'user' => $this->socialhappen->get_user(),
				'page_id' => $page_id,
				'page_user_fields' => $page_user_fields,
				'facebook_user'=>$facebook_user,
				'user_profile_picture'=>$user_facebook_image
			));
		} else {
			
			$this->load->model('page_user_data_model','page_users');
			$data = array(
				'user_id' => $this->socialhappen->get_user_id(),
				'page_id' => $page_id,
				'user_data' => $data
			);
			
			if($this->page_users->add_page_user($data)){
				echo "finished";
				$this->load->view('common/redirect',array('refresh_parent' => TRUE));
			} else {
				echo "already register/cannot signup page";
			}
			
			$this->load->view('tab/signup_page', array(
				'user' => $this->socialhappen->get_user(),
				'page_id' => $page_id,
				'page_user_fields' => $page_user_fields,
				'facebook_user'=>$facebook_user,
				'user_profile_picture'=>$user_facebook_image
			));
			
		}
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
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
