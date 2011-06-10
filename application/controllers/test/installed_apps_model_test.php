<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Installed_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('installed_apps_model','installed_apps');
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
	 * Tests get_installed_apps_by_company_id()
	 * @author Manassarn M.
	 */
	function get_installed_apps_by_company_id_test(){
		$result = $this->installed_apps->get_installed_apps_by_company_id(1);
		$this->unit->run($result,'is_array', 'get_installed_apps_by_company_id()');
		$this->unit->run(count($result[0]) == 19,'is_true', 'number of column');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($result[0]['app_name'],'is_string','app_name');
		$this->unit->run($result[0]['app_type_id'],'is_string','app_type_id');
		$this->unit->run($result[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($result[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($result[0]['app_description'],'is_string','app_description');
		$this->unit->run($result[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($result[0]['app_url'],'is_string','app_url');
		$this->unit->run($result[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($result[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($result[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($result[0]['app_image'],'is_string','app_image');
		$this->unit->run($result[0]['facebook_app_api_key'],'is_string','facebook_app_api_key');
	}
	
	/**
	 * Tests get_app_profile_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_app_profile_by_app_install_id_test(){
		$result = $this->installed_apps->get_app_profile_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_app_profile_by_app_install_id()');
		$this->unit->run(count($result) == 19,'is_true', 'number of column');
		$this->unit->run($result['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['app_id'],'is_string','app_id');
		$this->unit->run($result['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($result['app_name'],'is_string','app_name');
		$this->unit->run($result['app_type_id'],'is_string','app_type_id');
		$this->unit->run($result['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($result['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($result['app_description'],'is_string','app_description');
		$this->unit->run($result['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($result['app_url'],'is_string','app_url');
		$this->unit->run($result['app_install_url'],'is_string','app_install_url');
		$this->unit->run($result['app_config_url'],'is_string','app_config_url');
		$this->unit->run($result['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($result['app_image'],'is_string','app_image');
		$this->unit->run($result['facebook_app_api_key'],'is_string','facebook_app_api_key');
	}
	
	/**
	 * Test add_installed_app() and remove_installed_app()
	 * @author Manassarn M.
	 */
	function add_installed_app_and_remove_installed_app_test(){
		$installed_app = array(
							'company_id' => '1',
							'app_id' => '1',
							'app_install_status' => '1',
							'app_install_date' => '1',
							'page_id' => '1',
							'app_install_secret_key' => 'test'
						);
		$installed_app_id = $this->installed_apps->add_installed_app($installed_app);
		$this->unit->run($installed_app_id,'is_int','add_installed_app()');
		
		$removed = $this->installed_apps->remove_installed_app($installed_app_id);
		$this->unit->run($removed == 1,'is_true','remove_installed_app()');
		
		$removed_again = $this->installed_apps->remove_installed_app($installed_app_id);
		$this->unit->run($removed_again == 0,'is_true','remove_installed_app()');
	}
	
	/**
	 * Test count_installed_apps_by_page_id()
	 * @author Manassarn M.
	 */
	function count_installed_apps_by_page_id_test(){
		$result = $this->installed_apps->count_installed_apps_by_page_id(1);
		$this->unit->run($result,'is_string', 'count_installed_apps_by_page_id()');
	}
		
}
/* End of file installed_apps_model_test.php */
/* Location: ./application/controllers/test/installed_apps_model_test.php */