<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_test extends CI_Controller {
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
	 * @author Manassarn M.
	 */
	function index_test(){
		ob_start();
		require(__DIR__.'/../company.php');
		$company = new Company();
		$data = $company->index(1);
		ob_end_clean();
		$this->unit->run($data,'is_array','$data');
		$this->unit->run($data['company_id'], 'is_int', '$company_id');
		$this->unit->run(count($data) == 1, 'is_true', 'number of passed variables');
	}
	
	/**
	 * Tests json_get_pages().
	 * @author Manassarn M.
	 */
	function json_get_pages_test(){
		$content = file_get_contents(base_url().'company/json_get_pages/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_pages()');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->facebook_page_id,'is_string','facebook_page_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->page_name,'is_string','page_name');
		$this->unit->run($array[0]->page_detail,'is_string','page_detail');
		$this->unit->run($array[0]->page_all_member,'is_string','page_all_member');
		$this->unit->run($array[0]->page_new_member,'is_string','page_new_member');
		$this->unit->run($array[0]->page_image,'is_string','page_image');
		$this->unit->run(count((array)$array[0]) == 8, 'is_true', 'number of column');
	}
	
	/**
	 * Tests json_get_apps().
	 * @author Manassarn M.
	 */
	function json_get_apps_test(){
		$content = file_get_contents(base_url().'company/json_get_apps/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_apps()');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->available_date,'is_string','available_date');
		$this->unit->run(count((array)$array[0]) == 3, 'is_true', 'number of column');
	}

	/**
	 * Tests json_get_installed_apps()
	 * @author Manassarn M.
	 */
	function json_get_installed_apps_test(){
		$content = file_get_contents(base_url().'company/json_get_installed_apps/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_installed_apps()');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($array[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$array[0]) == 7, 'is_true', 'number of column');
	}
}

/* End of file company_test.php */
/* Location: ./application/controllers/test/company_test.php */
