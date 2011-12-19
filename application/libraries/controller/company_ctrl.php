<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Company_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }


	function json_get_pages($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('page_model','page');
		$pages = $this->CI->page->get_company_pages_by_company_id($company_id, $limit, $offset);
		return json_encode($pages);
	}

	function json_get_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('company_apps_model','company_apps');
		$apps = $this->CI->company_apps->get_company_apps_by_company_id($company_id, $limit, $offset);
		return json_encode($apps);
	}

	function json_get_installed_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('installed_apps_model','installed_apps');
		$apps = $this->CI->installed_apps->get_installed_apps_by_company_id($company_id, $limit, $offset);
		return json_encode($apps);
	}
	
	function json_get_profile($company_id = NULL){
		$this->CI->load->model('company_model','companies');
		$profile = $this->CI->companies->get_company_profile_by_company_id($company_id);
		return json_encode($profile);
	}

}

/* End of file company_ctrl.php */
/* Location: ./application/libraries/controller/company_ctrl.php */