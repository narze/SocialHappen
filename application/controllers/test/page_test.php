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
		$this->json_get_installed_app_list_test();
	}
	
	/**
	 * Tests json_get_installed_app_list()
	 * @author Manassarn M.
	 */
	function json_get_installed_app_list_test(){
		$content = file_get_contents(base_url().'page/json_get_installed_app_list/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'Installed app page list returns json correctly');
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
	
}

/* End of file api.php */
/* Location: ./application/controllers/test/api.php */