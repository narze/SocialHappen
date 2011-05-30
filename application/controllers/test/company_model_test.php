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

	/**
	 * Test add_company() and remove_company()
	 * @author Manassarn M.
	 */
	function add_company_and_remove_company_test(){
		$company = array(
							'creator_user_id' => '1',
							'company_name' => 'test',
							'company_address' => 'test',
							'company_email' => 'test@test.com',
							'company_telephone' => '021234567',
							'company_register_date' => '2011-05-09 17:52:17',
							'company_username' => 'test',
							'company_password' => 'test',
							'company_image' => 'test.jpg'
						);
		$company_id = $this->companies->add_company($company);
		$this->unit->run($company_id, 'is_int','add_company()');
		
		$removed = $this->companies->remove_company($company_id);
		$this->unit->run($removed == 1, 'is_true','remove_company()');
		
		$removed_again = $this->companies->remove_company($company_id);
		$this->unit->run($removed_again == 0, 'is_true','remove_company()');
	}
	
	/**
	 * Tests get_companies_by_user_id()
	 * @author Manassarn M.
	 */
	function get_companies_by_user_id_test(){
		$result = $this->companies->get_companies_by_user_id(1);
		$this->unit->run($result, 'is_array', 'get_companies_by_user_id()');
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