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
		$this->load->library('controller/tab_ctrl');
	}
	
	function index($page_id = NULL, $token = NULL){
		$this->signedRequest = $this->FB->getSignedRequest();
		if(!isset($this->signedRequest['page'])) return false;
		$this->page = $this->signedRequest['page'];
		$this->app_data = isset($this->signedRequest['app_data']) ? json_decode(base64_decode($this->signedRequest['app_data']), TRUE) : NULL ;
		
		$user_facebook_id = $this->FB->getUser();
		
		$token = issetor($this->signedRequest['oauth_token']);
		
		if(!$page_id && (!$facebook_page_id = issetor($this->page['id']))){ 
			exit('Your facebook account is not verified, please contact administrator'); //TODO : Blame that your facebook user is not verified.
		}
		$result = $this->tab_ctrl->main($user_facebook_id, $page_id, $facebook_page_id, $token);
		if($result['success']){
			$data = $result['data'];
			$this->parser->parse('tab/tab_view', $data);
		} else {
			echo $result['error'];
		}
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
		
		if($page_id == NULL) 
		{
			$this->load->view("tab/page_under_construction");
			return;
		}
		
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

		if( $page['page_installed'] == 0 && !$is_admin) 
		{
			$this->load->view("tab/page_under_construction", array('page' => $page));
			return;
		}
		
		$is_logged_in = $this->socialhappen->is_logged_in();

		//Is get-started completed?
		$this->load->model('get_started_model', 'get_started');
		$get_started_completed = $this->get_started->is_completed($page_id, 'page');

		//Install apps
		$this->load->model('installed_apps_model', 'installed_apps');
		$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id, 6);
		
		$this->load->vars( array(
			'page' => $page,
			'is_liked' => $this->page['liked'],
			'is_admin' => $is_admin,
			'is_logged_in' => $is_logged_in,
			'get_started_completed' => $get_started_completed,
			'apps'=>$apps
			)
		);
		
		if($page)
		{
			$this->load->view("tab/dashboard");
		}
	}
	
	function get_started($page_id = NULL){
		
		if($page_id == NULL) 
		{
			$this->load->view("tab/page_under_construction");
			return;
		}
		
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

		if( $page['page_installed'] == 0 && !$is_admin) 
		{
			$this->load->view("tab/page_under_construction", array('page' => $page));
			return;
		}
		
		$is_logged_in = $this->socialhappen->is_logged_in();
		
		//get-started checklist
		$this->load->model('get_started_model', 'get_started');
		$result = $this->get_started->get_todo_list_by_page_id($page_id); 
		$checklist = array();
		if($result) {
			foreach($result as $item) {
				$checklist[$item['group']][] = $item;
			}
		}
		
		$this->load->vars( array(
			'page' => $page,
			'is_liked' => $this->page['liked'],
			'is_admin' => $is_admin,
			'is_logged_in' => $is_logged_in,
			'checklist' => $checklist
			)
		);
		
		if($page)
		{
			$this->load->view("tab/get_started");
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

				/*
				//Facebook friends
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
				*/

				//User point
				$page_score = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id) | 0;
				
				//user apps
				$this->load->model('user_apps_model','user_apps');
				$user_apps = $this->user_apps->get_user_apps_by_user_id($user_id);
				
				//user campaigns
				$this->load->model('user_campaigns_model','user_campaigns');
				$user_campaigns = $this->user_campaigns->get_user_campaigns_by_user_id($user_id);

				//Reward
				$wishlist_items = NULL; //TODO get user wishlist
				
				$data = array(
					'user' => $user,
					//'friends' => $friends,
					'page_score' => $page_score,
					'user_apps' => $user_apps,
					'user_campaigns' => $user_campaigns,
					'wishlist_items' => $wishlist_items
				);
				$this->load->view('tab/profile', $data);
			}
		}
	}
	
	/** DEPRECATED
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
	*/

	function campaigns($page_id = NULL, $limit = NULL, $offset = NULL){
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

			//Campaign
			$this->load->model('campaign_model','campaigns');
			$this->load->model('user_campaigns_model','user_campaigns');
			$filter = $this->input->get('filter');
			switch($filter) {
				case 'me':
					$campaigns = $this->user_campaigns->get_user_campaigns_by_user_id($user_id,$limit,$offset);
					break;
				case 'me-active':
					$campaigns = $this->user_campaigns->get_active_user_campaigns($user_id,$limit,$offset);
					break;
				case 'me-expired':
					$campaigns = $this->user_campaigns->get_expired_user_campaigns($user_id,$limit,$offset);
					break;
				case 'active':
					$campaigns = $this->campaigns->get_active_campaigns_by_page_id($page_id,$limit,$offset);
					break;
				case 'expired':
					$campaigns = $this->campaigns->get_expired_campaigns_by_page_id($page_id,$limit,$offset);
					break;
				default : 
					$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id,$limit,$offset);
					break;
			}

			$data = array('user'=>$user,
							'page' => $page,
							'is_admin' => $is_admin,
							'is_user' => $is_user,
							'is_guest' => $is_guest,
							'is_liked' => $this->page['liked'],
							'campaigns' => $campaigns
			);
			$this->load->view('tab/apps_campaigns', $data);
		}
	}
	
	/** DEPRECATED
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
	*/

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
				$this->form_validation->set_rules('timezones', 'Timezone', 'trim|xss|clean');
				
				$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
				
				$this->load->helper('date');
				$timezones = timezones();
				$user['user_timezone'] = array_search($user['user_timezone_offset'] / 60, $timezones);

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
				
					$minute_offset = $timezones[set_value('timezones')] * 60;

					// build array for the model
					$user_update_data = array(
									'user_first_name' => set_value('first_name'),
									'user_last_name' => set_value('last_name'),
									'user_about' => set_value('about'),
									'user_image' => $user_image,
									'user_timezone_offset' => $minute_offset
								);
					$this->load->model('user_model','users');
					if ($this->users->update_user($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
					{
						$updated_user = array_merge($user,$user_update_data);
						$updated_user['user_timezone'] = array_search($updated_user['user_timezone_offset'] / 60, $timezones);
						$this->load->view('tab/account', array('page'=>$page, 'user'=>$updated_user, 'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id']),'success' => TRUE));
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
		$facebook_user = $this->facebook->getUser();
		//$this->load->model('user_model','users');
		$this->load->model('user_model','users');
		//if is sh user redirect popup to "regged"
		if($this->users->get_user_profile_by_user_facebook_id($facebook_user['id'])){
			echo "You're already a Socialhappen user";
			if($app_install_id){
				$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_app($app_install_id, FALSE, TRUE)));
			} else if ($page_id){
				$this->load->view('common/redirect',array('redirect_parent' => $this->facebook_page($page_id, FALSE, TRUE)));
			}
		} else {
			$this->load->helper('form');
			$user_profile_picture = $facebook_user['id'] ? $this->facebook->get_profile_picture($facebook_user['id']) : base_url().'assets/images/default/user.png';
			$this -> load -> view('tab/signup', 
				array(
					'facebook_user' => $facebook_user,
					'user_profile_picture' => $user_profile_picture,
					'page_id' => $page_id
				)
			);
		}
	}
	
	function signup_submit($page_id = NULL, $app_install_id = NULL){
		if(!$facebook_user = $this->facebook->getUser()){
			$data = array(
				'status' => 'error',
				'error' => 'no_fb_user',
			);
		} else {
			$user_facebook_id = $facebook_user['id'];
			$first_name = $this->input->get('first_name');
			$last_name = $this->input->get('last_name');
			$email = $this->input->get('email');
			$timezone = $this->input->get('timezone');
			$facebook_access_token = $this->FB->getAccessToken();
			$result = $this->tab_ctrl->signup_submit($first_name, $last_name, $email, $user_facebook_id, $timezone, $page_id, $app_install_id, $facebook_access_token);
			if($result['success']){
				$data = array('status' => 'ok');
			} else {
				$data = $result['error'];
			}
		}
		echo $this->input->get('callback').'('.json_encode($data).')';
	}
	
	function signup_page($page_id = NULL, $app_install_id = NULL){
		$facebook_access_token = $this->input->get('facebook_access_token');
		$this->load->library('form_validation');
		if($facebook_user_id = $this->FB->getUser()){
			$user_profile_image = $this->facebook->get_profile_picture($facebook_user_id);
		} else { //Only from signup popup->signup_page popup (in non-sh tabs)
			$user_profile_image = $this->input->get('user_image');
		}
		$user_first_name = $this->input->get('user_first_name');
		
		$this->load->model('page_model','pages');
		$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		$this->load->model('user_model','user');
		$this->load->vars(array(
			'page_id' => $page_id,
			'page' => $page,
			'page_user_fields' => $page_user_fields,
			'user_profile_picture'=>$user_profile_image,
			'user_first_name' => $user_first_name,
			'app_install_id' => $app_install_id
		));
		
		$this->load->view('tab/signup_page');
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
			
			$user_data = array();
			$validate_array = array();
			if(!$page_user_fields){ //Empty field
				$validation_result = TRUE;
			} else {
				foreach($page_user_fields as $user_fields){
					$user_fields_name = $user_fields['name'];
					$user_fields_data = $this->input->get($user_fields_name);
					$user_data[$user_fields_name] = $user_fields_data;
					$validate_array[$user_fields_name] = array(
						'label' => $user_fields['label'],
						'rules' => $user_fields['required'] ? 'required' : '',
						'input' => $user_fields_data,
						'verify_message' => $user_fields['verify_message']
					);
				}
				
				// log_message('error', print_r($user_data,TRUE));
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
				$user_id = $user['user_id'];
				$user_facebook_id = $user['user_facebook_id'];
				$result = $this->tab_ctrl->signup_page_submit($user_id, $user_facebook_id, $app_install_id, $page_id, $user_data);
				
				if($result['success']){
					$data = $result['data'];
				} else {
					$data = $result['error'];
				}
			}
		}
		echo $this->input->get('callback').'('.json_encode($data).')';
	}

	function signup_complete(){
		$redirect_url = $this->input->get('next');
		$this->load->view('tab/signup_complete', array('redirect_url' => $redirect_url));
	}

	function signup_campaign($app_install_id = NULL, $campaign_id = NULL){
		$this->load->helper('form_helper');
		$this->load->model('campaign_model', 'campaign');
		$campaign = $this->campaign->get_campaign_profile_by_campaign_id($campaign_id);
		$this->load->vars(array(
			'app_install_id' => $app_install_id,
			'campaign_id' => $campaign_id,
			'campaign' => $campaign
		));
		
		$this->load->view('tab/signup_campaign');
	}
	
	function signup_campaign_submit($app_install_id = NULL, $campaign_id = NULL){
		
		if(!$user = $this->socialhappen->get_user()){
			$data = array(
				'status' => 'error',
				'error' => 'no_user',
				'message' => 'Cannot find user, please relogin.'
			);
		} else {
			if($this->input->get('join-campaign') != 1){
				$data = array(
					'status' => 'error',
					'error' => 'form_error',
					'message' => 'Please submit'
				);
			} else {
				$user_id = $user['user_id'];
				$user_facebook_id = $user['user_facebook_id'];
				$result = $this->tab_ctrl->signup_campaign_submit($user_id, $user_facebook_id, $campaign_id);

				if($result['success']){
					$data = $result['data'];
				} else {
					$data = $result['error'];
				}
			}
		}
		echo $this->input->get('callback').'('.json_encode($data).')';
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
			'facebook_channel_url' => $this->facebook->channel_url,
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

	/**
	 * Show user's page score
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function my_page_score($page_id = NULL){
		if($user_facebook_id = $this->FB->getUser()){
			$page_score = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id) | 0;
			echo "Your page score is ".$page_score;	
		} else {
			echo 'Please login first';
		}
	}

	function page_leaderboard($page_id = NULL){
		$page_user_scores = $this->tab_ctrl->page_leaderboard($page_id);
		if($page_user_scores['success']){ //TODO sort
			if(count($page_user_scores['data']) > 0){
				foreach($page_user_scores['data'] as $user_score){
					echo "user id ".$user_score['user_id'].' got '.($user_score['page_score'] | 0).' points in page id '.$page_id;
					echo '<br />';
				}
			} else {
				echo 'No page users';
			}
		} else {
			echo $page_user_scores['error'];
		}
	}

	function my_campaign_score($campaign_id = NULL, $page_id = NULL){
		$user_facebook_id = $this->FB->getUser();
		$campaign_score = $this->tab_ctrl->get_campaign_score($user_facebook_id, $page_id, $campaign_id);
		echo "Your campaign score is ". ($campaign_score ? $campaign_score : 0);
	}

	function campaign_leaderboard($campaign_id = NULL, $page_id = NULL){
		$campaign_user_scores = $this->tab_ctrl->campaign_leaderboard($campaign_id, $page_id);
		if($campaign_user_scores['success']){ //TODO sort
			foreach($campaign_user_scores['data'] as $user_score){
				echo "user id ".$user_score['user_id'].' got '.($user_score['campaign_score'] | 0).' points in campaign id '.$campaign_id;
				echo '<br />';
			}
		}
	}

	function my_app_score($app_install_id = NULL, $page_id = NULL){
		$user_facebook_id = $this->FB->getUser();
		$app_score = $this->tab_ctrl->get_app_score($user_facebook_id, $page_id, $app_install_id);
		echo "Your app score is ". ($app_score ? $app_score : 0);
	}

	function app_leaderboard($app_install_id = NULL, $page_id = NULL){
		$app_user_scores = $this->tab_ctrl->app_leaderboard($app_install_id, $page_id);
		if($app_user_scores['success']){ //TODO sort
			foreach($app_user_scores['data'] as $user_score){
				echo "user id ".$user_score['user_id'].' got '.($user_score['app_score'] | 0).' points in app id '.$app_install_id;
				echo '<br />';
			}
		}
	}

	function redeem_list($page_id = NULL){
		$user_facebook_id = $this->FB->getUser();
		$page_score = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id) | 0;
		$status = $this->input->get('filter');
		$sort = $this->input->get('sort');
		$order = $this->input->get('order');
		$redeem_item_list = $this->tab_ctrl->redeem_list($page_id, $user_facebook_id, $status, $sort, $order);
		//echo "Your page score is ".$page_score;
		if($this->input->get('tabhead')) {
			$this->load->view('tab/redeem_reward_sort');
		}
		foreach($redeem_item_list as $redeem_item){
			$this->load->view('tab/redeem_reward_item', array(
				'page_id' => $page_id,
				'page_score' => $page_score,
				'reward_item' => $redeem_item,
				'redeem_button' => ($page_score >= $redeem_item['redeem']['point']) 
					? TRUE : FALSE
			));
		}
	}

	function redeem_reward($page_id = NULL, $reward_item_id = NULL){		
		$user_facebook_id = $this->FB->getUser();
		$page_score = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id) | 0;

		$this->load->model('reward_item_model');
		$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id);

		//Convert time
		$this->load->model('user_model');
		$user = $this->user_model->get_user_profile_by_user_facebook_id($user_facebook_id);
		$this->load->library('timezone_lib');
	    if($user){
	    	$reward_item['start_timestamp_local'] = $this->timezone_lib->convert_time(date('Y-m-d H:i:s',$reward_item['start_timestamp']), $user['user_timezone_offset']);
    		$reward_item['end_timestamp_local'] = $this->timezone_lib->convert_time(date('Y-m-d H:i:s',$reward_item['end_timestamp']), $user['user_timezone_offset']);
	    } else {
	    	$reward_item['start_timestamp_local'] = date('Y-m-d H:i:s', $reward_item['start_timestamp']);
			$reward_item['end_timestamp_local'] = date('Y-m-d H:i:s', $reward_item['end_timestamp']);
	    }

		$this->load->library('app_component_lib');
		$page_component = $this->app_component_lib->get_page($page_id);
		$terms_and_conditions = issetor($page_component['reward']['terms_and_conditions']);

		$this->load->vars(array(
			'page_id' => $page_id,
			'reward_item_id' => $reward_item_id,
			'reward_item' => $reward_item,
			'page_score' => $page_score,
			'reward_item_point' => $reward_item['redeem']['point'],
			'reward_item_point_remain' =>  $page_score - $reward_item['redeem']['point'],
			'terms_and_conditions' => $terms_and_conditions
		));
		$this->load->view('tab/redeem_reward');
	}

	function redeem_reward_confirm($page_id = NULL, $reward_item_id = NULL){
		$user_facebook_id = $this->FB->getUser();
		$page_score = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id) | 0;
		$result = $this->tab_ctrl->redeem_reward_confirm($page_id, $reward_item_id, $user_facebook_id);
		
		//$result = true;//test
		if($result) {
			$this->load->model('reward_item_model');
			$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id);
			$this->load->model('page_model');
			$page = $this->page_model->get_page_profile_by_page_id($page_id);
		}

		$this->load->vars(array(
			'success' => $result,
			'reward_item' => issetor($reward_item),
			'page_name'=>issetor($page['page_name']),
			'facebook_tab_url'=>issetor($page['facebook_tab_url'])
		));
		$this->load->view('tab/redeem_reward_confirm');
	}
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
