<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');

class Page extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('pagination');
	}
	
	function index($page_id =NULL) {
		$this -> socialhappen -> check_logged_in('home');
		if($page_id) {
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_page_id($page_id);
			$this -> load -> model('page_model', 'pages');
			$page = $this -> pages -> get_page_profile_by_page_id($page_id);
			
			$this -> load ->model('installed_apps_model','installed_apps');
			$this->pagination->initialize(
				array(
					'base_url' => base_url()."page/{$page_id}/apps",
					'total_rows' => $app_count = $this->installed_apps->count_installed_apps_by_page_id($page_id)
				)
			);
			$pagination['app'] = $this->pagination->create_links();
			$this -> load ->model('campaign_model','campaigns');
			$this->pagination->initialize(
				array(
					'base_url' => base_url()."page/{$page_id}/campaigns",
					'total_rows' => $campaign_count = $this->campaigns->count_campaigns_by_page_id($page_id)
				)
			);
			$pagination['campaign'] = $this->pagination->create_links();
			$this -> load ->model('user_model','users');
			$this->pagination->initialize(
				array(
					'base_url' => base_url()."page/{$page_id}/users",
					'total_rows' => $user_count = $this->users->count_users_by_page_id($page_id)
				)
			);
			$pagination['user'] = $this->pagination->create_links();
			
			$data = array(
				'page_id' => $page_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => $page['page_name'],
						'vars' => array('page_id'=>$page_id),
						'script' => array(
							'common/bar',
							'page/page_apps',
							'page/page_campaigns',
							'page/page_report',
							'page/page_users',
							'page/page_tabs'
						),
						'style' => array(
							'common/main',
							'page/main',
							'page/campaign',
							'page/member'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}"
							)
						)
					,
				TRUE),
				'page_profile' => $this -> load -> view('page/page_profile', 
					array('page_profile' => $page),
				TRUE),
				'page_tabs' => $this -> load -> view('page/page_tabs', 
					array(
						'app_count' => $app_count,
						'campaign_count' => $campaign_count,
						'user_count' => $user_count
						),
				TRUE), 
				'page_apps' => $this -> load -> view('page/page_apps', 
					array('pagination' => $pagination),
				TRUE), 
				'page_campaigns' => $this -> load -> view('page/page_campaigns', 
					array('pagination' => $pagination),
				TRUE),
				'page_users' => $this -> load -> view('page/page_users', 
					array('pagination' => $pagination),
				TRUE),
				'page_report' => $this -> load -> view('page/page_report', 
					array(),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer());
			$this -> parser -> parse('page/page_view', $data);
			return $data;
		}
	}

	/**
	 * JSON : Get page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_profile($page_id =NULL) {
		$this -> load -> model('page_model', 'pages');
		$profile = $this -> pages -> get_page_profile_by_page_id($page_id);
		echo json_encode($profile);
	}

	/**
	 * JSON : Get install apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> load -> model('installed_apps_model', 'installed_apps');
		$apps = $this -> installed_apps -> get_installed_apps_by_page_id($page_id, $limit, $offset);
		echo json_encode($apps);
	}

	/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> campaigns -> get_page_campaigns_by_page_id($page_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status($page_id =NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> campaigns -> get_page_campaigns_by_page_id_and_campaign_status_id($page_id, $campaign_status_id, $limit, $offset);
		echo json_encode($campaigns);
	}

	/**
	 * JSON : Get users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_users($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> load -> model('user_model', 'users');
		$users = $this -> users -> get_page_users_by_page_id($page_id, $limit, $offset);
		echo json_encode($users);
	}

	/**
	 * JSON : Add page
	 * @author Manassarn M.
	 */
	function json_add() {
		$this -> load -> model('page_model', 'pages');
		$post_data = array('facebook_page_id' => $this -> input -> post('facebook_page_id'), 'company_id' => $this -> input -> post('company_id'), 'page_name' => $this -> input -> post('page_name'), 'page_detail' => $this -> input -> post('page_detail'), 'page_all_member' => $this -> input -> post('page_all_member'), 'page_new_member' => $this -> input -> post('page_new_member'), 'page_image' => $this -> input -> post('page_image'));
		if($page_id = $this -> pages -> add_page($post_data)) {
			$result['status'] = 'OK';
			$result['page_id'] = $page_id;
		} else {
			$result['status'] = 'ERROR';
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get facebook pages available to install
	 * @author Prachya P.
	 */
	function json_get_not_installed_facebook_pages($company_id, $limit = NULL, $offset = NULL){
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages_by_company_id($company_id, $limit, $offset);
		$all_installed_fb_page_id=array();
		foreach($pages as $page){
			$all_installed_fb_page_id[]=$page['facebook_page_id'];
		}
		$facebook_pages=$this->facebook->get_user_pages();
		$facebook_pages=$facebook_pages['data'];
		$not_installed_facebook_pages=array();
		foreach($facebook_pages as $facebook_page){
			if(!in_array($facebook_page['id'],$all_installed_fb_page_id)) $not_installed_facebook_pages[]=$facebook_page;
		}
		echo json_encode($not_installed_facebook_pages);
	}
}

/* End of file page.php */
/* Location: ./application/controllers/page.php */
