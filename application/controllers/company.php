<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/**
	 * JSON : List company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_company_page_list($company_id = null){
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages($company_id);
		echo json_encode($pages);
	}
	
	/** 
	 * JSON : List company apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_company_app_list($company_id = null){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_apps($company_id);
		echo json_encode($apps);
	}
	
	/**
	 * JSON : List page apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_page_app_list($page_id = null){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_page_apps($page_id);
		echo json_encode($apps);
	}
	
	/**
	 * JSON : List apps
	 * @author Manassarn M.
	 */
	function json_app_list(){
		$this->load->model('app_model','apps');
		$apps = $this->apps->get_apps();
		echo json_encode($apps);
	}
}


/* End of file company.php */
/* Location: ./application/controllers/company.php */