<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_model','users');
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
	 * Tests get page members from page_id
	 * @author Manassarn M.
	 */
	function get_page_members_test(){
		$result = $this->users->get_page_members(1);
		$this->unit->run($result, 'is_array', 'Get page members');
		$this->unit->run($result[0]->user_id,'is_string','user_id');
		$this->unit->run($result[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($result[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($result[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($result[0]->user_apps_register_date,'is_string','user_apps_register_date');
		$this->unit->run($result[0]->user_apps_last_seen,'is_string','user_apps_last_seen');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->app_id,'is_string','app_id');
		$this->unit->run($result[0]->app_install_status,'is_string','app_install_status');
		$this->unit->run($result[0]->app_install_date,'is_string','app_install_date');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_secret_key,'is_string','app_install_secret_key');

		$this->unit->run(count((array)$result[0]) == 13, 'is_true', 'number of column');
		
	}

	/**
	 * Tests get user profile by id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_id_test(){
		$result = $this->users->get_user_profile_by_id(1);
		$this->unit->run($result, 'is_array', 'Get page members');
		$this->unit->run($result[0]->user_id,'is_string','user_id');
		$this->unit->run($result[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($result[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($result[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run(count((array)$result[0]) == 4, 'is_true', 'number of column');
	}

}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */