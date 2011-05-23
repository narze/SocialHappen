<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Installed_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('installed_apps_model','installed_apps');
	}
	
	function __destruct(){
		echo $this->unit->report();
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
	 * Tests get_installed_apps_by_company_id()
	 * @author Manassarn M.
	 */
	function get_installed_apps_by_company_id_test(){
		$result = $this->installed_apps->get_installed_apps_by_company_id(1);
		$this->unit->run($result, 'is_array', 'get_installed_apps_by_company_id()');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$result[0]) == 7, 'is_true', 'number of column');
	}
	
	/**
	 * Tests get_app_profile_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_app_profile_by_app_install_id_test(){
		$result = $this->installed_apps->get_app_profile_by_app_install_id(1);
		$this->unit->run($result, 'is_array', 'get_app_profile_by_app_install_id()');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$result[0]) == 7, 'is_true', 'number of column');
	}
}
/* End of file installed_apps_model_test.php */
/* Location: ./application/controllers/test/installed_apps_model_test.php */