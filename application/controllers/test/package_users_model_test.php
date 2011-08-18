<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package_users_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('package_users_model','package_users');
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
	 * Tests add_package_user()
	 * @author Manassarn M.
	 */
	function add_package_user_test(){
		$result = $this->package_users->add_package_user(array(
			'package_id' => 1,
			'user_id' => 100
		));
		$this->unit->run($result, 'is_true', 'add_package_user()');
		
		$result = $this->package_users->add_package_user(array(
		));
		$this->unit->run($result, 'is_false', 'add_package_user()');
	}
	
	/** 
	 * Tests get_package_by_user_id()
	 * @author Manassarn M.
	 */
	function get_package_by_user_id_test(){
		$result = $this->package_users->get_package_by_user_id(1);
		$this->unit->run($result, 'is_array', 'get_package_user()');
		$this->unit->run($result['package_name'], 'is_string', 'package name'); 
	}
	
	/**
	 * Tests check_user_package_can_add_company()
	 * @author Manassarn M.
	 */
	function check_user_package_can_add_company_test(){
		$result = $this->package_users->check_user_package_can_add_company(100);
		$this->unit->run($result, 'is_true', 'check_user_package_can_add_company(100)');
		
		$result = $this->package_users->check_user_package_can_add_company(200);
		$this->unit->run($result, 'is_false', 'check_user_package_can_add_company(200)');
	}
	
	/**
	 * Tests check_user_package_can_add_page()
	 * @author Manassarn M.
	 */
	function check_user_package_can_add_page_test(){
		$result = $this->package_users->check_user_package_can_add_page(100);
		$this->unit->run($result, 'is_true', 'check_user_package_can_add_page(100)');
		
		$result = $this->package_users->check_user_package_can_add_page(200);
		$this->unit->run($result, 'is_false', 'check_user_package_can_add_page(200)');
	}
	
	/**
	 * Tests check_user_package_can_add_user()
	 * @author Manassarn M.
	 */
	function check_user_package_can_add_user_test(){
		$result = $this->package_users->check_user_package_can_add_user(100);
		$this->unit->run($result, 'is_true', 'check_user_package_can_add_user(100)');
		
		$result = $this->package_users->check_user_package_can_add_user(200);
		$this->unit->run($result, 'is_false', 'check_user_package_can_add_user(200)');
	}
	
	/**
	 * Tests update_package_user_by_user_id()
	 * @author Manassarn M.
	 */
	function update_package_user_by_user_id_test(){
		$result = $this->package_users->update_package_user_by_user_id(100, array('package_id' => 201));
		$this->unit->run($result, 'is_true', 'update_package_user_by_user_id()');
		
		$result = $this->package_users->update_package_user_by_user_id(100, array());
		$this->unit->run($result, 'is_false', 'update_package_user_by_user_id() no edit');
	}
	
	/**
	 * Tests remove_package_user_by_user_id()
	 * @author Manassarn M.
	 */
	function remove_package_user_by_user_id_test(){
		$result = $this->package_users->remove_package_user_by_user_id(100);
		$this->unit->run($result, 'is_true', 'remove_package_user_by_user_id(100)');
		
		$result = $this->package_users->remove_package_user_by_user_id(100);
		$this->unit->run($result, 'is_false', 'remove_package_user_by_user_id(100)');
	}
}
/* End of file package_users_model_test.php */
/* Location: ./application/controllers/test/package_users_model_test.php */