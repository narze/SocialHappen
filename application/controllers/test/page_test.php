<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_test extends CI_Controller {
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
	 * Tests json_get_installed_app_list()
	 * @author Manassarn M.
	 */
	function json_get_installed_app_list_test(){
		$content = file_get_contents(base_url().'page/json_get_installed_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Installed app list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($array[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$array[0]) == 7, 'is_true', 'number of column');
	}
	
	/**
	 * Tests json_get_campaign_list()
	 * @author Manassarn M.
	 */
	function json_get_campaign_list_test(){
		$content = file_get_contents(base_url().'page/json_get_campaign_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Campaign list returns json correctly');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->campaign_id,'is_string','campaign_id');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->campaign_name,'is_string','campaign_name');
		$this->unit->run($array[0]->campaign_detail,'is_string','campaign_detail');
		$this->unit->run($array[0]->campaign_status_id,'is_string','campaign_status_id');
		$this->unit->run($array[0]->campaign_status_name,'is_string','campaign_status_name');
		$this->unit->run($array[0]->campaign_active_member,'is_string','campaign_active_member');
		$this->unit->run($array[0]->campaign_all_member,'is_string','campaign_all_member');
		$this->unit->run($array[0]->campaign_start_timestamp,'is_string','campaign_start_timestamp');
		$this->unit->run($array[0]->campaign_end_timestamp,'is_string','campaign_end_timestamp');
		
		$this->unit->run($array[0]->company_id,'is_string','company_id');
		$this->unit->run($array[0]->app_id,'is_string','app_id');
		$this->unit->run($array[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($array[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($array[0]->page_id,'is_string','page_id');
		$this->unit->run($array[0]->app_install_secret_key,'is_string','app_install_secret_key');
		$this->unit->run(count((array)$array[0]) == 16, 'is_true', 'number of column');
	}
	
}

/* End of file api.php */
/* Location: ./application/controllers/test/api.php */