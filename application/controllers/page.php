<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');

class Page extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($page_id =NULL) {
		$this -> socialhappen -> check_logged_in('home');
		$this -> load -> model('page_model', 'pages');
		$page = $this -> pages -> get_page_profile_by_page_id($page_id);
		if($page) {
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_page_id($page_id);
			
			$facebook_page_graph = json_decode(file_get_contents("http://graph.facebook.com/{$page['facebook_page_id']}"),TRUE);
			
			$this -> load ->model('installed_apps_model','installed_apps');
			$app_count = $this->installed_apps->count_installed_apps_by_page_id($page_id);
			
			$this -> load ->model('campaign_model','campaigns');
			$campaign_count = $this->campaigns->count_campaigns_by_page_id($page_id);
			$this -> load ->model('user_model','users');
			$user_count = $this->users->count_users_by_page_id($page_id);
			$this->config->load('pagination', TRUE);
			$per_page = $this->config->item('per_page','pagination');
			
			// $key = 'subject';
			// $app_id = ???;
			// $action_id = ???;
			// $criteria = array('page_id' => $page_id);
			// $date = date("Ymd");
			// $new_user_count = $this->audit_lib->count_audit($key, $app_id, $action_id, $criteria, $date));
			
			$data = array(
				'page_id' => $page_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'company_id' => $company['company_id'],
						'title' => $page['page_name'],
						'vars' => array(
							'page_id'=>$page_id,
							'company_id' => $company['company_id'],
							'per_page' => $per_page
						),
						'script' => array(
							'common/bar',
							'common/jquery.pagination',
							'page/page_apps',
							'page/page_campaigns',
							'page/page_report',
							'page/page_users',
							'page/page_tabs',
							//for fancybox in application tab
							'common/fancybox/jquery.mousewheel-3.0.4.pack',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							//'common/pagination',
							//for fancybox in application tab
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array(
						'breadcrumb' => array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}"
							),
						'settings_url' => base_url()."settings/{$company['company_id']}/page/{$page['page_id']}"
					),
				TRUE),
				'page_profile' => $this -> load -> view('page/page_profile', 
					array('page_profile' => $page,
						'app_count' => $app_count,
						'campaign_count' => $campaign_count,
						'user_count' => $user_count,
						///'new_user_count' => $new_user_count,
						'facebook' => array(
							'link' => issetor($facebook_page_graph['link']),
							'likes' =>  issetor($facebook_page_graph['likes'])
						)
					),
				TRUE),
				'page_tabs' => $this -> load -> view('page/page_tabs', 
					array(
						'app_count' => $app_count,
						'campaign_count' => $campaign_count,
						'user_count' => $user_count
						),
				TRUE), 
				'page_apps' => $this -> load -> view('page/page_apps', 
					array(),
				TRUE), 
				'page_campaigns' => $this -> load -> view('page/page_campaigns', 
					array(),
				TRUE),
				'page_users' => $this -> load -> view('page/page_users', 
					array(),
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
	 * JSON : Count apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_count_apps($page_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$count = $this->installed_apps->count_installed_apps_by_page_id($page_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Count campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_count_campaigns($page_id = NULL, $campaign_status_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_campaigns_by_page_id_and_campaign_status_id($page_id, $campaign_status_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Count users
	 * @param $page_id
	 * @param array $labels
	 * @author Manassarn M.
	 */
	function json_count_users($page_id = NULL, $labels = array()){
		$this->load->model('user_model','users');
		$count = $this->users->count_users_by_page_id($page_id);
		echo json_encode($count);
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
	 * @author Prachya P. - add audit when install page
	 */
	function json_add() {
		$this -> load -> model('page_model', 'pages');
		$post_data = array('facebook_page_id' => $this -> input -> post('facebook_page_id'), 'company_id' => $this -> input -> post('company_id'), 'page_name' => $this -> input -> post('page_name'), 'page_detail' => $this -> input -> post('page_detail'), 'page_all_member' => $this -> input -> post('page_all_member'), 'page_new_member' => $this -> input -> post('page_new_member'), 'page_image' => $this -> input -> post('page_image'));
		if($page_id = $this -> pages -> add_page($post_data)) {
			$result['status'] = 'OK';
			$result['page_id'] = $page_id;
			$this->load->library('audit_lib');
			$this->audit_lib->add_audit(
				0,
				(int)($this->session->userdata('user_id')),
				5,
				'', 
				'',
				array(
						'page_id'=> $page_id,
						'company_id' => (int)($this -> input -> post('company_id'))
					)
			);
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
		$not_installed_facebook_pages=array();
		$facebook_pages=$this->facebook->get_user_pages();
		if($facebook_pages!=NULL){
			$facebook_pages=$facebook_pages['data'];
			foreach($facebook_pages as $facebook_page){
				if(!in_array($facebook_page['id'],$all_installed_fb_page_id)){
					$facebook_page['page_info']=$this->facebook->get_page_info($facebook_page['id']);
					$not_installed_facebook_pages[]=$facebook_page;
				}
			}
		}
		echo json_encode($not_installed_facebook_pages);
	}
	
	/**
	 * JSON : update page order in dashboard
	 * @author Prachya P.
	 */
	function json_update_page_order_in_dashboard($company_id){
		$this->load->model('page_model','page');
		$page_orders=$_POST['page_orders'];
		$i=0;
		foreach($page_orders as $page_id){
			$this->page->update_page_profile_by_page_id($page_id,array('order_in_dashboard'=>$i));
			$i++;	
		}		
	}
	
	/**
	 * fancybox for adding app 
	 * @author Prachya P.
	 */
	function addapp_lightbox($page_id){
		$this -> socialhappen -> check_logged_in("home");
		if($page_id){			
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_page_id($page_id);
			$this -> load -> model('page_model', 'pages');
			$page = $this -> pages -> get_page_profile_by_page_id($page_id);
			$data = array(
				'page_id' => $page_id,
				'header' => $this -> socialhappen -> get_header_lightbox( 
					array(
						'vars' => array('company_id'=>$company['company_id'],
							'page_id'=>$page_id,
							'page_name'=>$page['page_name'],
							'user_id'=>$this->session->userdata('user_id')),
						'script' => array(
							'page/addapp_lightbox',
							//for fancybox
							'common/fancybox/jquery.mousewheel-3.0.4.pack',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'company/main',
							'common/smoothness/jquery-ui-1.8.9.custom',
							//for fancybox
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'footer' => $this -> socialhappen -> get_footer_lightbox()
			);
			$this->parser->parse('page/addapp_lightbox_view', $data);
		}
	}
}

/* End of file page.php */
/* Location: ./application/controllers/page.php */
