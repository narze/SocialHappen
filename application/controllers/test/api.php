<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
		//$this->company_page_list_test();
		$this->company_app_list_test();
		
	}
	
	/*
	 * Tests company_page_list().
	 * @author Manassarn Manoonchai
	 */
	function company_page_list_test(){
		$content = file_get_contents(base_url().'api/company_page_list/1');
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
	
	function company_app_list_test(){
		$content = file_get_contents(base_url().'api/company_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Company page list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->app_install_id,'is_string','page_id');
		$this->unit->run($array[0]->company_id,'is_string','facebook_page_id');
		$this->unit->run($array[0]->app_id,'is_string','company_id');
		$this->unit->run($array[0]->app_install_available,'is_string','page_name');
		$this->unit->run($array[0]->app_install_date,'is_string','page_detail');
		$this->unit->run($array[0]->page_id,'is_string','page_all_member');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','page_new_member');
	}
}

/* End of file api.php */
/* Location: ./application/controllers/test/api.php */