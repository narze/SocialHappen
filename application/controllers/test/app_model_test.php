<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('app_model','apps');
	}

	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
		$this->get_apps_test();
		
		
	}
	
	/** 
	 * Tests get apps : joined with app_type
	 * @author Manassarn M.
	 */
	function get_apps_test(){
		$result = $this->apps->get_apps();
		$this->unit->run($result, 'is_array', 'Get apps');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_name,'is_string','app_name');
		$this->unit->run($result[0]->app_type_id,'is_string','app_type_id');
		$this->unit->run($result[0]->app_type_name,'is_string','app_type_name');
		$this->unit->run($result[0]->app_type_description,'is_string','app_type_description');
		$this->unit->run($result[0]->app_maintainance,'is_string','app_maintainance');
		$this->unit->run($result[0]->app_show_in_list,'is_string','app_show_in_list');
		$this->unit->run($result[0]->app_description,'is_string','app_description');
		$this->unit->run($result[0]->app_secret_key,'is_string','app_secret_key');
		$this->unit->run($result[0]->app_url,'is_string','app_url');
		$this->unit->run($result[0]->app_install_url,'is_string','app_install_url');
		$this->unit->run($result[0]->app_config_url,'is_string','app_config_url');
		$this->unit->run($result[0]->app_support_page_tab,'is_string','app_support_page_tab');
		$this->unit->run($result[0]->app_image,'is_string','app_image');
		$this->unit->run(count((array)$result[0]) == 14, 'is_true', 'number of column');
	}
}
/* End of file app_model_test.php */
/* Location: ./application/controllers/test/app_model_test.php */