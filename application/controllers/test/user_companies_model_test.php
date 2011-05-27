<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_companies_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_companies_model','user_companies');
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
	 * TO-DO : do this
	 * @author Manassarn M.
	 */
	function get_page_profile_by_id_test(){
		$this->unit->run(TRUE,'is_false');
	}
	
	/**
	 * Test add_user_company() and remove_user_company()
	 * @author Manassarn M.
	 */
	function add_user_company_and_remove_user_company_test(){
		$user_id = $company_id = 50;
		$user_company = array(
							'user_id' => $user_id,
							'company_id' => $company_id,
							'user_role' => '0'
						);
		$add_result = $this->user_companies->add_user_company($user_company);
		$this->unit->run($add_result,'is_true','add_user_company()');
		
		$removed = $this->user_companies->remove_user_company($user_id, $company_id);
		$this->unit->run($removed == 1, 'is_true','remove_user_company()');
		
		$removed_again = $this->user_companies->remove_user_company($user_id, $company_id);
		$this->unit->run($removed_again == 0, 'is_true','remove_user_company()');
	}
}
/* End of file user_company_model_test.php */
/* Location: ./application/controllers/test/user_company_model_test.php */