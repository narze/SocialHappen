<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
 
	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
		$this->facebook->authentication($this->uri->uri_string());
	}
	
	/**
	 * index page for platform user who own company
	 */
	function index(){ 
		
		$facebook_user = $this->facebook->getUser();
		
		$this->load->model('User_companies_model', 'User_companies');
		$this->load->model('Company_model', 'Company');
		
		// get company list of current user
		$company_id_list = $this->User_companies->get_user_companies_list($facebook_user['id']);
		$company_list = array();
		foreach($company_id_list as $company){
			$company_data = $this->Company->get_company($company->company_id);
			array_push($company_list, $company_data[0]);
		}
		
		// data pass to view
		$data['company_list'] = $company_list;
		$this->load->view('admin_views/admin_view',$data);
	}
	
	/**
	 * dashboard page for company
	 */
	function dashboard($company_id){	
		// load model
		$this->load->model('App_model', 'App');
		$this->load->model('Company_model', 'Company');
		$this->load->model('Company_apps_model', 'Company_apps');
		$this->load->model('Company_pages_model', 'Company_pages');
		$this->load->model('User_companies_model','User_companies');
		$this->load->model('User_model', 'Users');
		
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		if(!$this->Users->is_company_admin($user_facebook_id, $company_id, TRUE))
			show_error('No Permission');
		
		// get list of apps in current company
		$app_id_list = $this->Company_apps->get_company_apps_by_company_id($company_id);
		
		// prepare app data
		$app_list = array();
		foreach($app_id_list as $app){
			$app_meta = $this->App->get_app($app->app_id);
			$app_meta = $app_meta[0];
			foreach($app_meta as $var => $key) {
				$app -> {$var} = $key;
			}
			
			$app->translated_app_config_url = 
			$this->app_url->translate_config_url($app->app_config_url, $app->app_install_id, $user_facebook_id, $app->app_install_secret_key);
			$app->translated_app_url = 
			$this->app_url->translate_url($app->app_url, $app->app_install_id);
			
			$app_list[] = $app; 
		}
		$company = $this->Company->get_company($company_id, 1, 0);
		
		$data['company'] = $company[0];
		$data['company_id'] = $company_id;
		$data['app_list'] = $app_list;
		
		// get list of pages in current company
		$pages_id_list = $this->Company_pages->get_page_by_company($company_id);
		
		// prepare data for pages
		$page_list = array();
		foreach($pages_id_list as $page){
			if($page_info = $this->facebook->getGraph($page->facebook_page_id)){
				$page->facebook_page_name = $page_info->name;
				$page->facebook_page_url = $page_info->link;
			} else {
				$page->facebook_page_name = '(Private Page)';
				$page->facebook_page_url = 'http://facebook.com';
			}
			$page_list[] = $page;
		}
		
		$data['page_list'] = $page_list;
		
		$this->load->view('admin_views/admin_dashboard_view', $data);
		
	}
	
	/**
	 * app detail page
	 */
	function app($company_id, $app_install_id, $facebook_page_id = NULL){
		$this->load->model('App_model', 'App');
		$this->load->model('Company_model', 'Company');
		$this->load->model('Company_apps_model', 'Company_apps');
		$this->load->model('User_apps_model', 'User_apps');
		$this->load->model('Company_pages_model', 'Company_pages');
		$this->load->model('User_companies_model','User_companies');
	
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
			
		// get app install detail
		$app_install = $this->Company_apps->get_app_install_by_app_install_id($app_install_id);

		$data = array();
		
		// get app detail
		$app = $this->App->get_app($app_install->app_id);
		$app = $app[0];
				
		$app_meta = $this->App->get_app($app->app_id);
		$app_meta = $app_meta[0];
		foreach($app_meta as $key => $var) {
			$app -> {$key} = $var;
		}
		
		// get config URL for current app
		$app->translated_app_config_url = 
			$this->app_url->translate_config_url($app->app_config_url, $app_install_id, $user_facebook_id, $app_install->app_install_secret_key);
		$data['app'] = $app;
		$data['app_install'] = $app_install;
		$data['company_id'] = $company_id;
		$data['app_install_id'] = $app_install_id;
		$data['facebook_page_id'] = $facebook_page_id;
		
		// prepare users detail for current app
		$user_list_meta = $this->User_apps->get_user_apps_list_by_app_install_id($app_install_id);
		$user_list = array();
		foreach($user_list_meta as $user){
			$user_info = $this->facebook->getGraph($user->user_facebook_id);
			$user->facebook_name = $user_info->name;
			$user_list[] = $user;
		}
		
		// prepare app statistic for current app
		$this->load->model('App_statistic_model', 'App_statistic');
		$active_user_list = $this->App_statistic->get_statistic_by_app_install_id($app_install_id);
		
		$data['active_user_list'] = $active_user_list;
		$data['user_list'] = $user_list;
		$this->load->view('admin_views/admin_app_view', $data);
	}
	
	
	/**
	 * facebook-page detail page
	 */
	function company_page($company_id, $facebook_page_id){
		$this->load->model('Page_apps_model','Page_apps');
		$this->load->model('Company_apps_model','Company_apps');
		$this->load->model('App_model','App');
		$this->load->model('Company_model','Company');
		$this->load->model('User_companies_model','User_companies');
				
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
		
		// get company data of this page
		$company = $this->Company->get_company($company_id, 1, 0);
		$data['company'] = $company[0];
		
		// get page information
		$page_info = $this->facebook->getGraph($facebook_page_id);
		$page->facebook_page_name = $page_info->name;
		$page->facebook_page_url = $page_info->link;
		$data['page'] = $page;
				
		// get app list for this page
		$apps_id_list = $this->Page_apps->get_app_by_page($facebook_page_id);
		$app_list = array();
		foreach($apps_id_list as $app){
			$app = $this->Company_apps->get_app_install_by_app_install_id($app->app_install_id);
			$app_meta = $this->App->get_app($app->app_id);
			$app_meta = $app_meta[0];
			foreach($app_meta as $var => $key) {
				$app -> {$var} = $key;
			}
			$app->translated_app_config_url = $this->app_url->translate_config_url(
										$app->app_config_url, 
										$app->app_install_id, 
										$user_facebook_id, 
										$app->app_install_secret_key);
			$app_list[] = $app; 
		}
		$data['app_list'] = $app_list;
		$data['facebook_page_id'] = $facebook_page_id;
		$this->load->view('admin_views/admin_company_page_view', $data);
	}
	
	/**
	 * no use ?
	 */
	function config_installed_apps($app_id){
		echo "config your app: ". $app_id;
		echo "<br/><a href=".site_url('admin/list_installed_apps/').">see list of your apps</a>";
	}
	
	function _check_permission($user_facebook_id, $company_id){
		
		return false;
	}
	
	/**
	 * no use ?
	 */
	function delete_app($company_id, $app_install_id){
		
		redirect('admin/dashboard/'.$company_id);
	}
	
	/**
	 * no use ?
	 */
	function deactivate_app($company_id, $app_install_id){
		$this->load->model('User_companies_model','User_companies');
				
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
		
		$this->load->model('Company_apps_model', 'Company_apps');
		$this->Company_apps->deactivate_app($app_install_id);
		$this->_refresh();
	}
	
	/**
	 * no use ?
	 */
	function activate_app($company_id, $app_install_id){
		$this->load->model('User_companies_model','User_companies');
				
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
			
		$this->load->model('Company_apps_model', 'Company_apps');
		$this->Company_apps->activate_app($app_install_id);
		$this->_refresh();
	}
	
	/**
	* refresh to the referer page
	*/
	function _refresh(){
		$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
  		$url = empty($url) ? base_url() : $url;

  		redirect($url, 'refresh');
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */