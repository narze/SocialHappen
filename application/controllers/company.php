<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		echo "hi!";
	}
	
	/* API : Lists company pages
	 * @param int $company_id
	 * @author Manassarn Manoonchai
	 */
	function company_page_list($company_id = null){
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages($company_id);
		echo json_encode($pages);
	}
	
	/* API : Lists company apps
	 * @param int $company_id
	 * @author Manassarn Manoonchai
	 */
	function company_app_list($company_id = null){
		$this->load->model('company_apps_model','company_apps');
		$apps = $this->company_apps->get_company_apps($company_id);
		echo json_encode($apps);
	}
}


/* End of file company.php */
/* Location: ./application/controllers/company.php */