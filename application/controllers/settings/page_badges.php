<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_badges extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			exit('You are not admin');
		}
		$this->load->library('settings');
		$config_name = 'badges';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){
		$this->load->library('form_validation');
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			//echo 'ok';
		}
	}
}
/* End of file page_badges.php */
/* Location: ./application/controllers/settings/page_badges.php */