<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_apps_model','user_apps');
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
	 * Test get members from app_install_id
	 * @author Manassarn M.
	 */
	function get_members_from_app_install_id_test(){
		$result = $this->user_apps->get_members_from_app_install_id(1);
		$this->unit->run($result, 'is_array', 'Get app members');
		$this->unit->run($result[0]->user_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_id,'is_string','facebook_page_id');
		$this->unit->run($result[0]->user_apps_register_date,'is_string','company_id');
		$this->unit->run($result[0]->user_apps_last_seen,'is_string','page_name');
		$this->unit->run($result[0]->user_facebook_id,'is_string','page_detail');
		$this->unit->run($result[0]->user_register_date,'is_string','page_all_member');
		$this->unit->run($result[0]->user_last_seen,'is_string','page_new_member');
		$this->unit->run(count((array)$result[0]) == 7, 'is_true', 'number of column');
	}
	
}
/* End of file user_apps_model_test.php */
/* Location: ./application/controllers/test/user_apps_model_test.php */