<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
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
	 * Tests output data
	 * @author Wachiraph C.
	 */
	function index_test(){
		ob_start();
		require(__DIR__.'/../api.php');
		$app = new App();
		
		$data = $app->index(1);
		ob_end_clean();
		$this->unit->run($data,'is_array','$data');
		$this->unit->run($data['app_install_id'], 'is_int', '$app_install_id');
		$this->unit->run(count($data) == 1, 'is_true', 'number of passed variables');
		
		$data = $app->index();
		ob_end_clean();
		$this->unit->run($data,'is_null','$data');
		$this->unit->run($data['app_install_id'], 'is_null', '$app_install_id');
		$this->unit->run(count($data) == 0, 'is_true', 'number of passed variables');
	}
	
	/**
	 * Test request_install_app()
	 * @author Wachiraph C.
	 */
	function request_install_app_test(){
/*
		$content = file_get_contents(base_url().'api/request_install_app_test');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_app_profile()');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($array[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$array[0]) == 7, 'is_true', 'number of column');
		*/

	}
	
}

/* End of file api_test.php */
/* Location: ./application/controllers/test/api_test.php */
