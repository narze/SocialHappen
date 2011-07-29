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
		$this -> socialhappen -> check_logged_in();
		if($company_id){
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_company_id($company_id);
			$data = array(
				'company_id' => $company_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => $company['company_name'],
						'vars' => array('company_id'=>$company_id
										,'sh_default_fb_app_api_key'=>$this->config->item('sh_default_fb_app_api_key')
										,'user_id'=>$this->session->userdata('user_id')),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'company/company_dashboard',
							'common/fancybox/jquery.mousewheel-3.0.4.pack',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/smoothness/jquery-ui-1.8.9.custom',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array(
						'breadcrumb' => 
							array( 
								$company['company_name'] => base_url() . "company/{$company['company_id']}"
							),
						'settings_url' => base_url()."settings?s=company&id={$company['company_id']}"
					),
				TRUE),
				'company_profile' => $this -> load -> view('company/company_profile', 
					array('company_profile' => $company),
				TRUE),
				'company_dashboard_tabs' => $this -> load -> view('company/company_dashboard_tabs', 
					array(),
				TRUE),
				'company_dashboard_right_panel' => $this -> load -> view('company/company_dashboard_right_panel', 
					array(),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
			);
			$this->parser->parse('company/company_view', $data);
		}
	}
	
	/** 
	 * JSON : get company list by user_id
	 * @param $user_id
	 * @author Prachya P.
	 */
	function json_get_user_companies($user_id = NULL, $limit = NULL, $offset = NULL){
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
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages_by_company_id($company_id, $limit, $offset);
		echo json_encode($pages);
	}
	
	/**
	 * JSON : Get company pages
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function json_get_pages_count($company_id = NULL){
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
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_apps_by_company_id($company_id, $limit, $offset);
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
	function json_get_installed_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps_by_company_id($company_id, $limit, $offset);
		echo json_encode($apps);
	}
	
	/**
	 * JSON : Get installed apps (not in page)
	 * @param $company_id
	 * @author Prachya P.
	 */
	function json_get_installed_apps_not_in_page($company_id = NULL){
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
		$this->load->model('company_model','companies');
		$profile = $this->companies->get_company_profile_by_company_id($company_id);
		echo json_encode($profile);
	}
	
	/**
	 * JSON : Add company
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('company_model','companies');
		$post_data = array(
							'creator_user_id' => $this->input->post('creator_user_id'),
							'company_name' => $this->input->post('company_name'),
							'company_detail' => $this->input->post('company_detail'),
							'company_address' => $this->input->post('company_address'),
							'company_email' => $this->input->post('company_email'),
							'company_telephone' => $this->input->post('company_telephone'),
							'company_register_date' => $this->input->post('company_register_date'),
							'company_username' => $this->input->post('company_username'),
							'company_password' => $this->input->post('company_password'),
							'company_image' => $this->input->post('company_image')
							);
		$result['status'] = 'ERROR';
		if($company_id = $this->companies->add_company($post_data)){
			$result['status'] = 'OK';
			$result['company_id'] = $company_id;
		} 
		echo json_encode($result);
	}
}


/* End of file company.php */
/* Location: ./application/controllers/company.php */