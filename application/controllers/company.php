<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Company
 * @category Controller
 */
class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
		$this->load->library('controller/company_ctrl');
	}

	function index($company_id = NULL){
		$result = $this->company_ctrl->main($company_id);
		if($result['success']){
			$data = $result['data'];
			$this -> parser -> parse('company/company_view', $data);
		} else {
			echo $result['error'];
		}
	}

	/**
	 * Over package limit popup
	 * @author Weerapat P.
	 */
	function company_package_limited()
	{
		$this->load->view('company/company_package_limited');
	}

	/**
	 * Page installed popup
	 * @author Weerapat P.
	 */
	function page_installed()
	{
		$this->load->view('company/page_installed');
	}

	/**
	 * JSON : get company list by user_id
	 * @param $user_id
	 * @author Prachya P.
	 */
	function json_get_user_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('company_model','company');
		$companies = $this->company->get_company_list_by_user_id($user_id, $limit, $offset);
		echo json_encode($companies);
	}

	/**
	 * JSON : Get company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_pages($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$pages = $this->company_ctrl->json_get_pages($company_id, $limit, $offset);
		echo $pages;
	}

	/**
	 * JSON : Get company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_pages_count($company_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('page_model','page');
		$count = $this->page->count_all(array("company_id" => $company_id));
		$count=array('page_count' => $count);
		echo json_encode($count);
	}

	/**
	 * JSON : Get company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps_count($company_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_app');
		$count = $this->installed_app->count_all_distinct("app_id",array("company_id" => $company_id));
		$count=array('app_count' => $count);
		echo json_encode($count);
	}

	/**
	 * JSON : Get company campaigns
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns_count($company_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_campaigns_by_company_id($company_id);
		$count=array('campaign_count' => $count);
		echo json_encode($count);
	}

	/**
	 * JSON : Get installed app count not in page
	 * @param $company_id
	 * @author Prachya P.
	 */
	function json_get_installed_apps_count_not_in_page($company_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_app');
		$count = $this->installed_app->count_all_distinct("app_id",array("company_id" => $company_id,"page_id" => 0));
		$count=array('app_count' => $count);
		echo json_encode($count);
	}

	/**
	 * JSON : Get company apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$apps = $this->company_ctrl->json_get_apps($company_id, $limit, $offset);
		echo $apps;
	}

	/**
	 * JSON : Get all apps
	 * @author Manassarn M.
	 */
	function json_get_all_apps(){
		$this->socialhappen->ajax_check();
		$this->load->model('app_model','apps');
		$apps = $this->apps->get_all_apps();
		echo json_encode($apps);
	}

	/**
	 * JSON : Get installed apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$apps = $this->company_ctrl->json_get_installed_apps($company_id, $limit, $offset);
		echo $apps;
	}

	/**
	 * JSON : Get installed apps (not in page)
	 * @param $company_id
	 * @author Prachya P.
	 */
	function json_get_installed_apps_not_in_page($company_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps_by_company_id_not_in_page($company_id);
		echo json_encode($apps);
	}

	/**
	 * JSON : Get not installed apps
	 * @param $company_id
	 * @author Prachya P.
	 */
	function json_get_not_installed_apps($company_id = NULL,$page_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_not_installed_apps($company_id,$page_id, $limit, $offset);
		echo json_encode($apps);
	}


	/**
	 * JSON : Get profile
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_profile($company_id = NULL){
		$this->socialhappen->ajax_check();
		$profile = $this->company_ctrl->json_get_profile($company_id);
		echo $profile;
	}

	/**
	 * Create company
	 */
	function create() {
		$this->load->model('user_companies_model');
		$user_id = $this->socialhappen->get_user_id();
		if(($companies = $this->user_companies_model->get_user_companies_by_user_id($user_id)) && (count($companies) > 0)) {
			$company = array_pop($companies);
		  redirect('assets/company/#/company/'.$company['company_id']);
		}

		redirect('assets/company/#/create');
	}

	function redirect($company_id) {
		redirect('assets/company/#/company/'.$company_id);
	}

}


/* End of file company.php */
/* Location: ./application/controllers/company.php */