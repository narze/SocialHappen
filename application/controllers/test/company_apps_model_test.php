<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('company_apps_model','company_apps');
		$this->unit->reset_mysql();
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * Tests get_company_apps_by_company_id()
	 * @author Manassarn M.
	 */
	function get_company_apps_by_company_id_test(){
		$result = $this->company_apps->get_company_apps_by_company_id(1);
		$this->unit->run($result,'is_array', 'get_company_apps_by_company_id()');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['available_date'],'is_string','available_date');
		$this->unit->run($result[0]['app_name'],'is_string','app_name');
		$this->unit->run($result[0]['app_type_id'] == 1,'is_true','app_type_id == 1');
		$this->unit->run($result[0]['app_type'] == "Page Only",'is_true','app_type == "Page Only"');
		$this->unit->run($result[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($result[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($result[0]['app_description'],'is_string','app_description');
		$this->unit->run($result[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($result[0]['app_url'],'is_string','app_url');
		$this->unit->run($result[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($result[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($result[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($result[0]['app_image'],'is_string','app_image');
		$this->unit->run($result[0]['app_facebook_api_key'],'is_string','app_facebook_api_key');
	}

	/**
	 * Test add_company_app() and remove_company_app()
	 * @author Manassarn M.
	 */
	function add_company_app_and_remove_company_app_test(){
		$company_id = $app_id = 50;
		$company_app = array(
							'company_id' => $company_id,
							'app_id' => $app_id,
							'available_date' => '0'
						);
		
		$add_result = $this->company_apps->add_company_app($company_app);
		$this->unit->run($add_result,'is_true','add_company_app()');
		
		$removed = $this->company_apps->remove_company_app($company_id, $app_id);
		$this->unit->run($removed == 1,'is_true','remove_company_app()');
		
		$removed_again = $this->company_apps->remove_company_app($company_id, $app_id);
		$this->unit->run($removed_again == 0,'is_true','remove_company_app()');
	}
}
/* End of file company_apps_model_test.php */
/* Location: ./application/controllers/test/company_apps_model_test.php */