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
	
	function index(){
		$this->load->library('socialhappen');
		$user_facebook_id = $this->FB->getUser();
		
		$this->load->model('User_model','User');
		$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		$token = $this->signedRequest['oauth_token'];
		
		$this->load->model('Page_model','Page');
		$page_id = $this->Page->get_page_id_by_facebook_page_id($this->page['id']);
		
		
		if(!$user_id) {
			//Guest popup
			echo 'guest';
		} else {
		
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id($user_id);
			$this->load->model('page_model','pages');
			$page = $this->pages->get_page_profile_by_page_id($page_id);
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_page_id($page_id);
			$this->load->model('user_companies_model','user_companies');
			$is_company_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);

			$data = array(
				'header' => $this->load->view('tab/header', 
					array(
						// 'title' => $company['company_name'],
						'vars' => array( //'company_id'=>$company_id,
										'page_id' => $page_id,
										'user_id' => $user_id,
										'token' => base64_encode($token)
						),
						'script' => array(
							'common/functions',
							'tab/bar',
							'tab/profile',
							'tab/main',
							'tab/account',
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
					'admin' => FALSE,
					'page_id' => $page_id,
					'user_id' => $user_id,
					'token' => $token,
					'is_admin' => $is_company_admin,
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
	}
	
	function dashboard($page_id = NULL,$user_id = NULL,$token = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		if($page){
			if(!$user_id||!$token) {
			//Guest popup
			} else {
				$this->load->model('user_model','users');
				$user = $this->users->get_user_profile_by_user_id($user_id);
				$user_facebook_id = $this->FB->getUser();
				
				$this->load->model('company_model','companies');
				$company = $this->companies->get_company_profile_by_page_id($page_id);
				$this->load->model('user_companies_model','user_companies');
				$is_company_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
				$this->load->model('campaign_model','campaigns');
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				$this->load->model('installed_apps_model','installed_apps');
				$installed_apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				
				$fql = 'SELECT uid FROM page_fan WHERE page_id = '.$page['facebook_page_id'].' and uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$user_facebook_id.')';
				
				$response = $this->FB->api(array(
					'method' => 'fql.query',
					'access_token' => base64_decode($token),
					'query' =>$fql,
					));
					
				
				
				$friends = array();
				
				foreach($response as $friend){
					$user = $this->FB->api('/'.$friend['uid'].'');
					$friends[] = array(
										'uid' => $friend['uid'],
										'name' => $user['name']
									);
				}
				
				$data = array('user'=>$user,
								'page' => $page,
								'campaigns' => $campaigns,
								'installed_apps' => $installed_apps,
								'is_admin' => $is_company_admin,
								'is_liked' => $this->page['liked'],
								'friends' => $friends
				);
				$this->load->view("tab/dashboard",$data);
			}
		}
	}
	
	function badges($page_id = NULL){
	
	}
	
	function leaderboard($page_id = NULL){}
	
	function favorites($user_id = NULL){}
	
	function notifications($user_id = NULL){}
	
	function account($page_id = NULL, $user_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if($page){
			
			if($user_id && $user_id == $this->socialhappen->get_user_id()){
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
	
	function profile($user_id = NULL){
		$data = array('user_id' => $user_id);
		$this->load->view("tab/profile",$data);
	}
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
