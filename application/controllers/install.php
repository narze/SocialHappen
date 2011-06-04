<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * install new app
 */
class Install extends CI_Controller {

	function __construct(){
		parent::__construct();
		 
		$this->load->library('facebook');
		$this->facebook->authentication($this->uri->uri_string());
	}
	
	function index($app_id){
		// nothing
	}
	
	/**
	 * list of apps that available for company's admin to install
	 */
	function apps($company_id){
		$this->load->model('App_model', 'App');
		$this->load->model('Company_model', 'Company');
		$this->load->model('User_companies_model','User_companies');
		
		$facebook_user = $this->facebook->getUser();
		$user_facebook_id = $facebook_user['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
			
		$app_list = $this->App->get_app_list();
		
		foreach ($app_list as $app) {
			$app->translated_app_install_url = $this->app_url->translate_install_url($app->app_install_url, $company_id, $user_facebook_id);
		}
		$data['app_list'] = $app_list;
		$data['company_id'] = $company_id;
		
		// get company data
		$company = $this->Company->get_company($company_id, 1, 0);
		$data['company'] = $company[0];
		$this->load->view('install_views/apps_view',$data); 
	}
	
	/**
	 * Install new app, initialization
	 */
	function install_new_app($company_id, $app_id){
		$this->load->model('App_model', 'App');
		$this->load->model('Installed_apps_model', 'Installed_apps');
		$this->load->model('User_companies_model','User_companies');
		
		$facebook_user = $this->facebook->getUser();
		$user_facebook_id = $facebook_user['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
		
		$app = $this->App->get_app($app_id, 1, 0);
		$app = $app['0'];
		
		$app->translated_app_install_url = $this->app_url->translate_install_url($app->app_install_url, $company_id, $user_facebook_id);
		
		//$this->Installed_apps->add_new_request($app_id, $company_id, $user_facebook_id);
		
		redirect($app->translated_app_install_url);
	}
	
	function company_profile($app_id){
		echo "create your company profile<br/>";
		
		
		echo "<a href=".site_url('admin/config_installed_apps/'.$app_id).">finish</a>";
	}
	
	function _create_new_app_for_company($company_id, $app_id){
		$this->load->model('User_companies_model','User_companies');
		
		$facebook_user = $this->facebook->getUser();
		$user_facebook_id = $facebook_user['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
	
		$this->load->model('Company_apps_model', 'Company_apps');
		$app_install_id = $this->Company_apps->add(array(
					'company_id' => $company_id,
					'app_id' => $app_id,
					'app_install_available' => TRUE,
					//'app_install_date' => date(),
					//'app_install_fanpage_id' => ''
					));
		return $app_install_id;
	}
	
	function _initialize_config($app_install_id, $app_id){
		$this->load->model('Config_item_template_model','Config_item_template');
		$this->load->model('Config_item_model', 'Config_item');
		
		$config_item_list = $this->Config_item_template->get_config_item_template_list_for_app($app_id);
		foreach ($config_item_list as $config_key => $config_value) {
			$this->Config_item->add_config_item($app_install_id, $config_key, $config_value);
		}
	}
}

/* End of file install.php */
/* Location: ./application/controllers/install.php */