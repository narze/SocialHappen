<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('company_apps_model','company_apps');
	}

	function index(){
		$this->get_company_apps_test();
		
		
	}
	
	/*
	 * Tests get apps from company_id
	 * @author Manassarn Manoonchai
	 */
	function get_company_apps_test(){
		$result = $this->company_apps->get_company_apps(1);
		$this->unit->run($result, 'is_array', 'Get company apps');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_available,'is_string','app_install_available');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');
		echo $this->unit->report();
	}

}
/* End of file company_apps_model_test.php */
/* Location: ./application/controllers/test/company_apps_model_test.php */