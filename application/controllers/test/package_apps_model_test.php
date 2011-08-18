<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('package_apps_model','package_users');
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
	 * Tests add_package_app()
	 * @author Manassarn M.
	 */
	function add_package_app_test(){
		$result = $this->package_users->add_package_app(array(
			'package_id' => 10,
			'app_id' => 100
		));
		$this->unit->run($result, 'is_true', 'add_package_app()');
		
		$result = $this->package_users->add_package_app(array(
		));
		$this->unit->run($result, 'is_false', 'add_package_app()');
	}
	
	/**
	 * Tests remove_package_app_by_app_id()
	 * @author Manassarn M.
	 */
	function remove_package_app_by_app_id_test(){
		$result = $this->package_users->remove_package_app_by_app_id(100);
		$this->unit->run($result, 'is_true', 'remove_package_app_by_app_id(100)');
		
		$result = $this->package_users->remove_package_app_by_app_id(100);
		$this->unit->run($result, 'is_false', 'remove_package_app_by_app_id(100)');
	}
}
/* End of file package_apps_model_test.php */
/* Location: ./application/controllers/test/package_apps_model_test.php */