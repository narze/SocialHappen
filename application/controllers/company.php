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
		if($company_id){
			$data = array(
				'company_id' => $company_id,
	            'dragdrop_script' => $this->load->view('company_dashboard_script', 
	            					array('company_id'=>$company_id), true)
            );
			$this->parser->parse('company_view', $data);
			return $data;
		}
	}
	
	/** 
	 * JSON : get company list by user_id
	 * @param $user_id
	 * @author Prachya P.
	 */
	function json_get_user_companies($user_id = NULL){
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
	
	/**
	 * JSON : Get not installed apps
	 * @param $company_id
	 * @author Prachya P.
	 */
	function json_get_not_installed_apps($company_id = NULL,$page_id = NULL){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_not_installed_apps($company_id,$page_id);
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