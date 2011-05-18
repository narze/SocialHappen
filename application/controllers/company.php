<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/**
	 * API : List company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function company_page_list($company_id = null){
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages($company_id);
		echo json_encode($pages);
	}
	
	/** 
	 * API : List company apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function company_app_list($company_id = null){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_apps($company_id);
		echo json_encode($apps);
	}
	
	/**
	 * API : List page apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function page_app_list($page_id = null){
		$this->load->model('page_apps_model','page_apps');
		$apps = $this->page_apps->get_page_apps($page_id);
		echo json_encode($apps);
	}
	
	/**
	 * API : List apps
	 * @author Manassarn M.
	 */
	function app_list(){
		$this->load->model('app_model','apps');
		$apps = $this->apps->get_apps();
		echo json_encode($apps);
	}
}


/* End of file company.php */
/* Location: ./application/controllers/company.php */