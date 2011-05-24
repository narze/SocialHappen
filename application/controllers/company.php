<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Company
 * @category Controller
 */
class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index($company_id = NULL){
		$data['company_id'] = $company_id;
		$this->load->view('company_view',$data);
		return $data;
	}
	
	/** 
	 * JSON : get company list by user_id
	 * @param $user_id
	 * @author Prachya P.
	 * @todo Rename
	 */
	function json_get_user_company($user_id = NULL){
		$this->load->model('company_model','company');
		$companies = $this->company->get_company_list_by_user_id($user_id);
		echo json_encode($companies);
	}
	
	/**
	 * JSON : Get company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_pages($company_id = NULL){
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages_by_company_id($company_id);
		echo json_encode($pages);
	}
	
	/** 
	 * JSON : Get company apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_apps($company_id = NULL){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_apps_by_company_id($company_id);
		echo json_encode($apps);
	}

	/**
	 * JSON : Get all apps
	 * @author Manassarn M.
	 */
	function json_get_all_apps(){
		$this->load->model('app_model','apps');
		$apps = $this->apps->get_all_apps();
		echo json_encode($apps);
	}
	
	/**
	 * JSON : Get installed apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($company_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps_by_company_id($company_id);
		echo json_encode($apps);
	}
}


/* End of file company.php */
/* Location: ./application/controllers/company.php */