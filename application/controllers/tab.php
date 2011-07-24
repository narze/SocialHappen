<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tab extends CI_Controller {
	var $signedRequest;
	var $page;
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
	}
	
	function test(){
		$this->load->model('audit_model','audit');
			$this->load->model('audit_action_model','audit_action');
			$this->load->model('campaign_model','campaigns');
			$this->load->model('installed_apps_model','installed_apps');
			var_dump($this->audit->list_audit(array('app_install_id'=>1)));
	}
	
	function index(){
		$user_facebook_id = $this->FB->getUser();
		
		$this->load->model('User_model','User');
		$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		$token = $this->signedRequest['oauth_token'];
		
		$this->load->model('Page_model','Page');
		$page_id = $this->Page->get_page_id_by_facebook_page_id($this->page['id']);

		$page_installed = $this->input->get('page');
		$app_installed = $this->input->get('app');
		
		$this->load->model('user_model','users');
		$user = $this->users->get_user_profile_by_user_id($user_id);
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		$this->load->model('company_model','companies');
		$company = $this->companies->get_company_profile_by_page_id($page_id);
		$this->load->model('user_companies_model','user_companies');
		$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);

		//page activities
		
		$data = array(
			'header' => $this->load->view('tab/header', 
				array(
					'vars' => array(
									'page_id' => $page_id,
									'user_id' => $user_id,
									'page_installed' => $page_installed,
									'app_installed' => $app_installed,
									'is_guest' => $user ? FALSE : TRUE,
									'token' => base64_encode($token)
					),
					'script' => array(
						'common/functions',
						'tab/bar',
						'tab/profile',
						'tab/main',
						'tab/account',
						'tab/dashboard',
						'common/jquery.form',
						'common/jquery.countdown.min',
						'common/fancybox/jquery.fancybox-1.3.4.pack'
					),
					'style' => array(
						'common/facebook',
						'common/jquery.countdown',
						'common/fancybox/jquery.fancybox-1.3.4'
					)
				),
			TRUE),
			'bar' => $this->load->view('tab/bar',array(
				'page' => $page,
				'user' => $user,
				'page_id' => $page_id,
				'user_id' => $user_id,
				'token' => base64_encode($token),
				'is_admin' => $is_admin,
				'is_liked' => $this->page['liked']
				),
			TRUE),
			'main' => $this->load->view('tab/main',array(),
			TRUE),
			'footer' => $this->load->view('tab/footer',array(),
			TRUE)
		);
		$this->parser->parse('tab/tab_view', $data);
		
		
	}
	
	function dashboard($page_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		if($page){
			$user_facebook_id = $this->FB->getUser();
		
			$this->load->model('User_model','User');
			$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
			$is_admin = $is_user = $is_guest = FALSE;
			if(!$user_id) {
				$user_id = 0;
				$is_guest = TRUE;
			} else {
				$is_user = TRUE;
			}
		
			$app_campaign_filter = $this->input->get('filter');
		
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id($user_id);
			
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_page_id($page_id);
			$this->load->model('user_companies_model','user_companies');
			$is_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
			$this->load->model('campaign_model','campaigns');
			$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
			$this->load->model('installed_apps_model','installed_apps');
			$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
			
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
			
			$data = array('user'=>$user,
							'page' => $page,
							'campaigns' => ($app_campaign_filter != 'app') ? $campaigns : NULL,
							'apps' => ($app_campaign_filter != 'campaign') ? $apps : NULL,
							'is_admin' => $is_admin,
							'is_user' => $is_user,
							'is_guest' => $is_guest,
							'is_liked' => $this->page['liked']
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
										'name' => $facebook_user['name']
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
	
	function activities($page_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			$activity_filter = $this->input->get('filter'); //(all) app campaign me
			$this->load->model('audit_model','audit');
			$this->load->model('audit_action_model','audit_action');
			$this->load->model('campaign_model','campaigns');
			$this->load->model('installed_apps_model','installed_apps');
			
			$data = array();
			$data['activities'] = array();
			if($activity_filter == 'app'){
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('app_install_id'=>$app['app_install_id'])));
				}				
			} else if ($activity_filter == 'campaign'){
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('campaign_id'=>$campaign['campaign_id'])));
				}
			} else if ($activity_filter == 'me'){ //problem with user_id
				$user_facebook_id = $this->FB->getUser();
				$this->load->model('User_model','users');
				$user_id = $this->users->get_user_id_by_user_facebook_id($user_facebook_id);
			
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('user_id'=>$user_id, 'campaign_id'=>$campaign['campaign_id'])));
				}
				
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('user_id'=>$user_id, 'app_install_id'=>$app['app_install_id'])));
				}
			} else {
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				foreach($campaigns as $campaign){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('campaign_id'=>$campaign['campaign_id'])));
				}
				
				$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				foreach($apps as $app){
					$data['activities'] = array_merge($data['activities'],$this->audit->list_audit(array('app_install_id'=>$app['app_install_id'])));
				}
				
				//debug
				foreach($data['activities'] as $key => $value){
					$action = $this->audit_action->get_action($data['activities'][$key]['app_id'],$data['activities'][$key]['action_id']);
					
					$data['activities'][$key]['user_name'] = $data['activities'][$key]['subject'];
					$data['activities'][$key]['user_image'] = '';
					
					$data['activities'][$key]['activity_detail'] = $action[0]['description'];
					$data['activities'][$key]['time_ago'] = '1 day ago';
					$data['activities'][$key]['source'] = 'web';
					$data['activities'][$key]['star_point'] = 5;
				}
			}
			$this->load->view('tab/activities',$data);
		}
	}
	
	function leaderboard($page_id = NULL){}
	
	function favorites($user_id = NULL){}
	
	function notifications($user_id = NULL){}
	
	function account($page_id = NULL, $user_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			
			if(!$user_id = $this->socialhappen->get_user_id()){
				
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
					if ($this->users->update_user_profile_by_user_id($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
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
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
