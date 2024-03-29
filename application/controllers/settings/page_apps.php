<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_apps extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
		$this->load->library('form_validation');
	}
	
	function index($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->library('settings');
			$config_name = 'signup_fields';
			$this->settings->view_page_app_settings($page_id, $config_name);
		}
	}
	
	/**
	 * Load page_apps settings with a page app active
	 */
	function app($page_id = NULL, $app_install_id = NULL){
		//Check permission
		if(!$this->socialhappen->check_admin(array('app_install_id' => $app_install_id),array('role_app_edit','role_all_company_apps_edit'))){
			//no access
		} else {
			$this->load->library('settings');
			$config_name = 'app';
			$this->settings->view_page_app_settings($page_id, $config_name, $app_install_id);
		}
	}

	/**
	 * Load a page app settings
	 */
	function view($page_id = NULL, $app_install_id = NULL){
		//Check permission
		if(!$this->socialhappen->check_admin(array('app_install_id' => $app_install_id),array('role_app_edit','role_all_company_apps_edit'))){
			//no access
		} else {
			$vars = array('app_install_id' => $app_install_id);
			$this->load->vars($vars);
			$this->load->view('settings/page_apps/app_settings');	
		}
	}
}
/* End of file page_apps.php */
/* Location: ./application/controllers/settings/page_apps.php */