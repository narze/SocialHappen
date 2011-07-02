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
	
	function index($page_id = NULL){
		$this->load->library('socialhappen');
		$user_facebook_id = $this->FB->getUser();
		
		$this->load->model('User_model','User');
		$user_id = $this->User->get_user_id_by_user_facebook_id($user_facebook_id);
		
		$this->load->model('Page_model','Page');
		$page_id = $this->Page->get_page_id_by_facebook_page_id($this->page['id']);
		
		if(!$user_id) {
			//Guest popup
			echo 'guest';
			
		} else {
		
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id($user_id);
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
										'user_id' => $user_id
						),
						'script' => array(
							'tab/bar',
							'tab/profile',
							'tab/account',
							'tab/main'
						),
						'style' => array(
							'tab/main'
						)
					),
				TRUE),
				'bar' => $this->load->view('tab/bar',array(
					'admin' => FALSE,
					'page_id' => $page_id,
					'user_id' => $user_id,
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
	
	function dashboard($page_id = NULL,$user_id = NULL){
		$this->load->model('page_model','pages');
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		
		if($page){
			if(!$user_id) {
			//Guest popup
			} else {
				$this->load->model('user_model','users');
				$user = $this->users->get_user_profile_by_user_id($user_id);
				$this->load->model('company_model','companies');
				$company = $this->companies->get_company_profile_by_page_id($page_id);
				$this->load->model('user_companies_model','user_companies');
				$is_company_admin = $this->user_companies->is_company_admin($user_id, $company['company_id']);
				$this->load->model('campaign_model','campaigns');
				$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
				$this->load->model('installed_apps_model','installed_apps');
				$installed_apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
				
				$fql = 'SELECT uid FROM page_fan WHERE page_id = '.$page['facebook_page_id'].' and uid IN (SELECT uid2 FROM friend WHERE uid1 = me())';
				$response = $this->FB->api(array(
					'method' => 'fql.query',
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
	
	function profile($user_id = NULL){
		$data = array('user_id' => $user_id);
		$this->load->view("tab/profile",$data);
	}
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
