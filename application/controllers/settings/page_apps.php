<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_apps extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
		$this->load->library('form_validation');
	}
	
	function index($page_id = NULL){
		$this->load->library('settings');
		$config_name = 'signup_fields';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}
	
	function app($page_id = NULL, $app_install_id = NULL){
		$this->load->library('settings');
		$config_name = 'app';
		$this->settings->view_page_app_settings($page_id, $config_name, $app_install_id);
	}

	function view($page_id = NULL, $app_install_id = NULL){
		if(!$this->socialhappen->check_admin(array('app_install_id' => $app_install_id),array('role_app_edit','role_all_company_apps_edit'))){
			//no access
		} else {
			// echo 'ok';		
		}
	}
}
/* End of file page_apps.php */
/* Location: ./application/controllers/settings/page_apps.php */