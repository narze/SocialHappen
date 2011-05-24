<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('company_model','companies');
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
	 * Tests get_company_profile_by_company_id()
	 * @author Manassarn M.
	 */
	function get_company_profile_by_company_id_test(){
		$result = $this->companies->get_company_profile_by_company_id(1);
		$this->unit->run($result, 'is_array', 'get_company_profile_by_company_id()');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->creator_user_id,'is_string','creator_user_id');
		$this->unit->run($result[0]->company_name,'is_string','company_name');
		$this->unit->run($result[0]->company_address,'is_string','company_address');
		$this->unit->run($result[0]->company_email,'is_string','company_email');
		$this->unit->run($result[0]->company_telephone,'is_string','company_telephone');
		$this->unit->run($result[0]->company_register_date,'is_string','company_register_date');
		$this->unit->run($result[0]->company_username,'is_string','company_username');
		$this->unit->run($result[0]->company_password,'is_string','company_password');
		$this->unit->run($result[0]->company_image,'is_string','company_image');
		$this->unit->run(count((array)$result[0]) == 10, 'is_true', 'number of column');
	}
}
/* End of file company_model_test.php */
/* Location: ./application/controllers/test/company_model_test_model_test.php */