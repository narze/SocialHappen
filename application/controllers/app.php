<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($app_install_id = NULL){
		if(!$this->socialhappen->check_admin(array('app_install_id' => $app_install_id),array())){
			//no access
		} else {
			$this -> load -> model('installed_apps_model', 'installed_apps');
			$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
			if($app) {
				$this -> load -> model('company_model', 'companies');
				$company = $this -> companies -> get_company_profile_by_app_install_id($app_install_id);
				$this->load->model('page_model','pages');
				$page = $this->pages->get_page_profile_by_app_install_id($app_install_id);
				$this -> load -> model('campaign_model', 'campaigns');
				$campaigns = $this -> campaigns -> get_campaigns_by_app_install_id($app_install_id);

				$campaign_count = $this->campaigns->count_campaigns_by_app_install_id($app_install_id);
				$user_count = $this->users->count_users_by_app_install_id($app_install_id);
				$this->config->load('pagination', TRUE);
				$per_page = $this->config->item('per_page','pagination');
				
				$this->load->library('audit_lib');
				$new_users = $this->audit_lib->list_stat_app((int)$app_install_id, 102, $this->audit_lib->_date());
				$new_users = count($new_users) == 0 ? 0 : $new_users[0]['count'];
				
				$this -> load -> model('user_model', 'user');
				$all_users = $this->user->count_users_by_app_install_id($app_install_id);
				
				$data = array(
					'app_install_id' => $app_install_id,
					'header' => $this -> socialhappen -> get_header( 
						array(
							'company_id' => $company['company_id'],
							'title' => $app['app_name'],
							'vars' => array(
								'app_install_id'=>$app_install_id,
								'per_page' => $per_page,
								'page_id' => $page['page_id']
							),
							'script' => array(
								'common/functions',
								'common/jquery.form',
								'common/bar',
								'common/jquery.pagination',
								'common/jquery.countdown.min',
								//'app/app_stat',
								'app/app_users',
								'app/app_campaigns',
								'app/app_tabs',
								'app/main',
								'common/fancybox/jquery.fancybox-1.3.4.pack'
							),
							'style' => array(
								'common/main',
								'common/platform',
								'common/fancybox/jquery.fancybox-1.3.4',
								'common/jquery.countdown'
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
								$page['page_name'] => base_url() . "page/{$page['page_id']}",
								$app['app_name'] => base_url() . "app/{$app['app_install_id']}"
								)
							)
						,
					TRUE),
					'app_profile' => $this -> load -> view('app/app_profile', 
						array('app_profile' => $app,
							'new_users' => $new_users,
							'all_users' => $all_users,
							'count_installed_on' => $this->pages->count_pages_by_app_id($app['app_id']),
							'company_id' => $company['company_id']),
					TRUE),
					'app_tabs' => $this -> load -> view('app/app_tabs', 
						array(
							'campaign_count' => $campaign_count,
							'user_count' => $user_count
							),
					TRUE), 
					'app_campaigns' => $this -> load -> view('app/app_campaigns', 
						array(),
					TRUE),
					'app_users' => $this -> load -> view('app/app_users', 
						array(),
					TRUE),
					'footer' => $this -> socialhappen -> get_footer());
				$this -> parser -> parse('app/app_view', $data);
				return $data;
			}
		}
	}
	
	/**
	 * Go to app config page
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function config($app_install_id = NULL){
		if(!$this->socialhappen->check_admin(array('app_install_id'=>$app_install_id),array('role_all_company_apps_edit','role_app_edit'))){
			
		} else {
			$this->load->model('installed_apps_model','installed_apps');
			$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
			if($app && isset($app['app_config_url'])){
				$this->load->library('app_url');
				$this->load->model('user_model','users');
				$config_url = $this->app_url->translate_config_url(
											$app['app_config_url'], 
											$app['app_install_id'], 
											$this->socialhappen->get_user_id(), 
											$app['app_install_secret_key']);
				redirect($config_url);
			}
		}
	}
	
	/**
	 * Go to app
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function go($app_install_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		if($app && isset($app['app_url'])){
			$this->load->library('app_url');
			$app_url = $this->app_url->translate_url(
										$app['app_url'], 
										$app['app_install_id']);
			redirect($app_url);
		}
	}
	
	/**
	 * JSON : Count campaigns
	 * @param $app_install_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_count_campaigns($app_install_id = NULL, $campaign_status_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_campaigns_by_app_install_id_and_campaign_status_id($app_install_id, $campaign_status_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Count users
	 * @param $app_install_id
	 * @param array $labels
	 * @author Manassarn M.
	 */
	function json_count_users($app_install_id = NULL, $labels = array()){
		$this->socialhappen->ajax_check();
		$this->load->model('user_model','users');
		$count = $this->users->count_users_by_app_install_id($app_install_id);
		echo json_encode($count);
	}
	
	/** 
	 * JSON : Gets app profile
	 * @param $app_install_id
	 * @author Prachya P.
	 */
	function json_get_profile($app_install_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_apps');
		$profile = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		echo json_encode($profile);
	}
	
	/**
	 * JSON : Get app campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$campaigns = $this->campaigns->get_app_campaigns_by_app_install_id($app_install_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get campaigns
	 * @param $app_install_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status($app_install_id =NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> campaigns -> get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id, $campaign_status_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get app users
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_users($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_apps_model','user_apps');
		$users = $this->user_apps->get_app_users_by_app_install_id($app_install_id, $limit, $offset);
		echo json_encode($users);
	}
	
	/**
	 * JSON : Get pages
	 * @param : $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_pages($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('page_model','pages');
		$pages = $this->pages->get_app_pages_by_app_install_id($app_install_id, $limit, $offset);
		echo json_encode($pages);
	}
	
	/**
	 * JSON : Get app install status
	 * @param : $status_name
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_app_install_status($status_name = NULL){	
		//$this->socialhappen->ajax_check();
		$this->load->model('app_model','app');
		$app_install_status = array();
		if($app_install_status_id = $this->socialhappen->get_k('app_install_status', $status_name)){
			$app_install_status['app_install_status_id'] = $app_install_status_id;
			$app_install_status['app_install_status'] = $app_install_status['app_install_status_description'] = $this->socialhappen->get_v('app_install_status',$app_install_status['app_install_status_id']);
		}
		echo json_encode($app_install_status);
	}
	
	/**
	 * JSON : Get all app install status
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_all_app_install_status(){
		//$this->socialhappen->ajax_check();
		$app_install_statuses = array();
		foreach($this->socialhappen->get_global('app_install_status') as $key => $value){
			$app_install_statuses[$key]['app_install_status_id'] = $key;
			$app_install_statuses[$key]['app_install_status'] = $app_install_statuses[$key]['app_install_status_description'] = $value;
		}
		echo json_encode(array_values($app_install_statuses));
	}
	
	/**
	 * JSON : application profile by fb_app_api_key
	 * @param $fb_app_api_key
	 * @author Prachya P.
	 */
	function json_get_app_by_api_key($fb_app_api_key){
		$this->socialhappen->ajax_check();
		$this->load->model('app_model','app');
		$app = $this->app->get_app_by_api_key($fb_app_api_key);
		echo json_encode($app);
	}
	
	/**
	 * JSON : update app order in dashboard
	 * @author Prachya P.
	 */
	function json_update_app_order_in_dashboard(){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_app');
		
		$app_orders=$_POST['app_orders'];
		$i=0;
		foreach($app_orders as $app_install_id){
			$this->installed_app->update(array('order_in_dashboard'=>$i),array("app_install_id"=>$app_install_id));
			$i++;	
		}
	}
	
	/**
	 * JSON : curl to app_install_url and get data back
	 * @author Prachya P. 
	 */	
	function curl($url = NULL){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$response = curl_exec($ch);		 
		curl_close($ch);
		$encoded = json_encode($response);
		$stripped = str_replace("\ufeff", "", $encoded); //trim zero-width white spaces
		return json_decode($stripped); //Normal JSON
	}
	
	/**
	 * JSON : Add app to page
	 * @author Manassarn M.
	 */
	function json_add_to_page(){
		$this->socialhappen->ajax_check();
		$url = $this->input->post('install_url');
		$result = json_decode($this->curl($url), TRUE);
		if(strtoupper($result['status']) == 'OK'){
			$app_id = $this->input->post('app_id');
			$facebook_page_id = $this->input->post('facebook_page_id');
			$facebook_app_id = $this->input->post('facebook_app_id');
			$user_id = $this->input->post('user_id');
			$page_id = $this->input->post('page_id');
			$company_id = $this->input->post('company_id');
			
			if($this->facebook->install_facebook_app_to_facebook_page_tab($facebook_app_id, $facebook_page_id)){
				$result['facebook_tab_url'] = $this->facebook->get_facebook_tab_url($facebook_app_id, $facebook_page_id);
				$this->load->model('installed_apps_model','installed_app');
				$this->installed_app->update_facebook_tab_url_by_app_install_id($result['app_install_id'], $result['facebook_tab_url']);
			} else {
				log_message('error','install facebook app to page tab failed');
				$result['status'] = 'ERROR';
				$result['message'] = 'Please manually add Socialhappen facebook app by this <a href="https://www.facebook.com/add.php?api_key='.$facebook_app_id.'&pages=1&page='.$facebook_page_id.'">link</a>';
			}
			
			$this->load->library('audit_lib');
			$action_id = $this->socialhappen->get_k('audit_action','Install App To Page');
			$this->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				$app_id,
				$result['app_install_id'],
				array(
					'page_id'=> $page_id,
					'company_id' => $company_id,
					'app_install_id' => 0,
					'user_id' => $user_id
				)
			);
			
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id' => $page_id);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Add app
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->socialhappen->ajax_check();
		$url = $_POST['url'];
		$result = json_decode($this->curl($url), TRUE);
		
		// $this->load->library('audit_lib');
			// $action_id = $this->socialhappen->get_k('audit_action','Install App');
			// $this->audit_lib->add_audit(
				// 0,
				// $user_id,
				// $action_id,
				// '', 
				// '',
				// array(
					// 'page_id'=> $page_id,
					// 'company_id' => $company_id,
					// 'app_install_id' => 0,
					// 'user_id' => $user_id
				// )
			// );
			
		// $this->load->library('achievement_lib');
		// $info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id' => $page_id);
		// $stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		
		echo $result;
	}
}


/* End of file app.php */
/* Location: ./application/controllers/app.php */