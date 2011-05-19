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
		$this->company_page_list_test();
		$this->company_app_list_test();
		$this->page_app_list_test();
		$this->app_list_test();
		
	}
	
	/**
	 * Tests json_company_page_list().
	 * @author Manassarn M.
	 */
	function company_page_list_test(){
		$content = file_get_contents(base_url().'company/json_company_page_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Company page list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->facebook_page_id,'is_string','facebook_page_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->page_name,'is_string','page_name');
		$this->unit->run($array[0]->page_detail,'is_string','page_detail');
		$this->unit->run($array[0]->page_all_member,'is_string','page_all_member');
		$this->unit->run($array[0]->page_new_member,'is_string','page_new_member');
	}
	
	/**
	 * Tests json_company_app_list().
	 * @author Manassarn M.
	 */
	function company_app_list_test(){
		$content = file_get_contents(base_url().'company/json_company_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Company app list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->app_install_id,'is_string','page_id');
		$this->unit->run($array[0]->company_id,'is_string','facebook_page_id');
		$this->unit->run($array[0]->app_id,'is_string','company_id');
		$this->unit->run($array[0]->app_install_available,'is_string','page_name');
		$this->unit->run($array[0]->app_install_date,'is_string','page_detail');
		$this->unit->run($array[0]->page_id,'is_string','page_all_member');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','page_new_member');
	}

	/**
	 * Tests json_page_app_list().
	 * @author Manassarn M.
	 */
	function page_app_list_test(){
		$content = file_get_contents(base_url().'company/json_page_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Page app list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->app_install_id,'is_string','page_id');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
	}
	
	/**
	 * Tests json_app_list().
	 * @author Manassarn M.
	 */
	function app_list_test(){
		$content = file_get_contents(base_url().'company/json_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'App list returns json correctly');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_name,'is_string','app_name');
		$this->unit->run($array[0]->app_type_id,'is_string','app_type_id');
		$this->unit->run($array[0]->app_type_name,'is_string','app_type_name');
		$this->unit->run($array[0]->app_type_description,'is_string','app_type_description');
		$this->unit->run($array[0]->app_maintainance,'is_string','app_maintainance');
		$this->unit->run($array[0]->app_show_in_list,'is_string','app_show_in_list');
		$this->unit->run($array[0]->app_description,'is_string','app_description');
		$this->unit->run($array[0]->app_secret_key,'is_string','app_secret_key');
		$this->unit->run($array[0]->app_url,'is_string','app_url');
		$this->unit->run($array[0]->app_install_url,'is_string','app_install_url');
		$this->unit->run($array[0]->app_config_url,'is_string','app_config_url');
		$this->unit->run($array[0]->app_support_page_tab,'is_string','app_support_page_tab');
	}
}

/* End of file company_test.php */
/* Location: ./application/controllers/test/company_test.php */
