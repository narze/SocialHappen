<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tab extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	
	function index($page_id = NULL){
		$this->load->library('socialhappen');
		$this->socialhappen->check_logged_in('home');
		$user_id = $this->socialhappen->get_user_id();
		if(!$user_id) {
		//Guest popup
		} else {
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id();
			// $this->load->model('company_model','companies');
			// $company = $this->companies->get_company_profile_by_page_id($page_id);
			// $this->load->model('page_model','pages');
			// $page = $this->pages->get_page_profile_by_page_id($page_id);
			// if($page && $company && $user_id){
				// $company_id = $company['company_id'];
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
						'user_id' => $user_id
						),
					TRUE),
					'main' => $this->load->view('tab/main',array(),
					TRUE),
					'footer' => $this->load->view('tab/footer',array(),
					TRUE)
				);
				$this->parser->parse('tab/tab_view', $data);
			// }
		}
	}
	
	function dashboard($page_id = NULL){
		$this->load->library('socialhappen');
		$user_id = $this->socialhappen->get_user_id();
		if(!$user_id) {
		//Guest popup
		} else {
			$this->load->model('user_model','users');
			$user = $this->users->get_user_profile_by_user_id();
			$this->load->model('campaign_model','campaigns');
			$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
			$this->load->model('installed_apps_model','installed_apps');
			$installed_apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
			$data = array('user'=>$user,
							'campaigns' => $campaigns,
							'installed_apps' => $installed_apps);
			$this->load->view("tab/dashboard",$data);
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
