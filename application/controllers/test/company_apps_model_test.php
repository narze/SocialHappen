<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('company_apps_model','company_apps');
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
	 * Tests get apps from company_id
	 * @author Manassarn M.
	 */
	function get_company_apps_test(){
		$result = $this->company_apps->get_company_apps(1);
		$this->unit->run($result, 'is_array', 'Get company apps');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->available_date,'is_string','available_date');
		$this->unit->run(count((array)$result[0]) == 3, 'is_true', 'number of column');
		
	}

}
/* End of file company_apps_model_test.php */
/* Location: ./application/controllers/test/company_apps_model_test.php */