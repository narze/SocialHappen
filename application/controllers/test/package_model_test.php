<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('package_model','packages');
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
	 * Tests get_packages()
	 * @author Manassarn M.
	 */
	function get_packages_test(){
		$result = $this->packages->get_packages();
		$this->unit->run($result, 'is_array', 'get_packages()');
		$this->unit->run($result[0], 'is_array', 'first package');
		$this->unit->run($result[0]['package_name'], 'is_string', 'first package name'); 
	}
	
	/**
	 * Tests get_package_by_package_id()
	 * @author Manassarn M.
	 */
	function get_package_by_package_id_test(){
		$result = $this->packages->get_package_by_package_id(1);
		$this->unit->run($result, 'is_array', 'get_package_by_package_id(1)');
		$this->unit->run($result['package_name'], 'is_string', 'package name');
		
		$result = $this->packages->get_package_by_package_id(100);
		$this->unit->run($result, 'is_null', 'get_package_by_package_id(100)');
	}
	
	/**
	 * Tests add_package()
	 * @author Manassarn M.
	 */
	function add_package_test(){
		$result = $this->packages->add_package(array(
			'package_id' => 100,
			'package_name' => 'test', 
			'package_detail' => 'detail',
			'package_image' => 'image',
			'package_max_companies' => 1,
			'package_max_pages' => 1,
			'package_max_users' => 100,
			'package_price' => 300,
			'package_custom_badge' => 1
		));
		$this->unit->run($result, 'is_int', 'add_package()');
		
		$result = $this->packages->add_package(array(
		));
		$this->unit->run($result, 'is_false', 'add_package()');
	}
	
	/**
	 * Tests update_package_by_package_id()
	 * @author Manassarn M.
	 */
	function update_package_by_package_id_test(){
		$result = $this->packages->update_package_by_package_id(100, array('package_name' => 'edit_test'));
		$this->unit->run($result, 'is_true', 'update_package_by_package_id(1)');
		
		$result = $this->packages->update_package_by_package_id(100, array());
		$this->unit->run($result, 'is_false', 'update_package_by_package_id(1) no edit');
		
		// $result = $this->packages->update_package_by_package_id(1, array('foo' => 'bar'));
		// $this->unit->run($result, 'is_false', 'update_package_by_package_id(1) wrong edit');
	}
	
	/**
	 * Tests remove_package()
	 * @author Manassarn M.
	 */
	function remove_package_test(){
		$result = $this->packages->remove_package(100);
		$this->unit->run($result, 'is_true', 'remove_package(100)');
		
		$result = $this->packages->remove_package(100);
		$this->unit->run($result, 'is_false', 'remove_package(100)');
	}
	
	/**
	 * Tests is_upgradable()
	 * @author Weerapat P.
	 */
	function is_upgradable_test(){
		$result = $this->packages->is_upgradable(1);
		$this->unit->run($result, 'is_true', 'is_upgradable(1)');
	}
	
	/**
	 * Tests is_the_most_expensive()
	 * @author Manassarn M.
	 */
	function is_the_most_expensive_test(){
		$result = $this->packages->is_the_most_expensive(1);
		$this->unit->run($result, FALSE, 'is_the_most_expensive(1)');
		$result = $this->packages->is_the_most_expensive(2);
		$this->unit->run($result, FALSE, 'is_the_most_expensive(2)');
		$result = $this->packages->is_the_most_expensive(3);
		$this->unit->run($result, TRUE, 'is_the_most_expensive(3)');
	}
}
/* End of file package_model_test.php */
/* Location: ./application/controllers/test/package_model_test.php */