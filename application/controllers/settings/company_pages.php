<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_pages extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($user_id = NULL){
		$this->load->library('settings');
		$setting_name = 'company_pages';
		$this->settings->view_settings($setting_name, $user_id, NULL);
	}
	
	function view($user_id = NULL){
		if($user_id && $user_id == $this->socialhappen->get_user_id()){
			$user_companies = $this->socialhappen->get_user_companies();
			$this->load->model('page_model','pages');
			$company_pages = array();
			foreach ($user_companies as $user_company){
				$company_pages[$user_company['company_id']] = $this->pages->get_company_pages_by_company_id($user_company['company_id']);
			}
			$this->load->view('settings/companies_and_pages',array('company_pages' => $company_pages, 'user_companies' => $user_companies));
		}
	}
}
/* End of file company_pages.php */
/* Location: ./application/controllers/settings/company_pages.php */