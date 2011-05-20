<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_test extends CI_Controller {
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
	 * Tests get app profile by app_install_id
	 * @author Manassarn M.
	 */
	function get_app_profile_test(){
		$content = file_get_contents(base_url().'app/json_get_app_profile_by_id/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Company page list returns json correctly');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($array[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($array[0]->page_id + 0 != 0,'is_true','page_id != 0');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$array[0]) == 7, 'is_true', 'number of column');
	}
}

/* End of file app_test.php */
/* Location: ./application/controllers/test/app_test.php */
