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
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->apps))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/** 
	 * Tests get_all_apps() : joined with app_type
	 * @author Manassarn M.
	 */
	function get_all_apps_test(){
		$result = $this->apps->get_all_apps();
		$this->unit->run($result, 'is_array', 'get_all_apps()');
		$this->unit->run($result[0], 'is_array', 'First row');
		$this->unit->run(count($result[0]) == 14, 'is_true', 'Count items');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_name'],'is_string','app_name');
		$this->unit->run($result[0]['app_type_id'],'is_string','app_type_id');
		$this->unit->run($result[0]['app_type_name'],'is_string','app_type_name');
		$this->unit->run($result[0]['app_type_description'],'is_string','app_type_description');
		$this->unit->run($result[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($result[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($result[0]['app_description'],'is_string','app_description');
		$this->unit->run($result[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($result[0]['app_url'],'is_string','app_url');
		$this->unit->run($result[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($result[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($result[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($result[0]['app_image'],'is_string','app_image');
	}

	/**
	 * Test add_app() and remove_app()
	 * @author Manassarn M.
	 */
	function add_app_and_remove_app_test(){
		$app = array(
							'app_name' => 'test',
							'app_type_id' => '1',
							'app_maintainance' => '0',
							'app_show_in_list' => '1',
							'app_description' => 'test',
							'app_secret_key' => 'test',
							'app_url' => 'test',
							'app_install_url' => 'test',
							'app_config_url' => 'test',
							'app_support_page_tab' => '1',
							'app_image' => 'test.jpg'
						);
		$app_id = $this->apps->add_app($app);
		$this->unit->run($app_id, 'is_int','add_app()');
		
		$removed = $this->apps->remove_app($app_id);
		$this->unit->run($removed == 1, 'is_true','remove_app()');
		
		$removed_again = $this->apps->remove_app($app_id);
		$this->unit->run($removed_again == 0, 'is_true','remove_app()');
	}
}
/* End of file app_model_test.php */
/* Location: ./application/controllers/test/app_model_test.php */