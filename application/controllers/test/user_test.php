<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_test extends CI_Controller {
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
		require(__DIR__.'/../user.php');
		$user = new User();
		
		$data = $user->index(1);
		ob_end_clean();
		$this->unit->run($data,'is_array','$data');
		$this->unit->run($data['user_id'], 'is_int', '$user_id');
		$this->unit->run(count($data) == 1, 'is_true', 'number of passed variables');
		
		$data = $user->index();
		ob_end_clean();
		$this->unit->run($data,'is_null','$data');
		$this->unit->run($data['user_id'], 'is_null', '$user_id');
		$this->unit->run(count($data) == 0, 'is_true', 'number of passed variables');
	}
	
	/**
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$content = file_get_contents(base_url().'user/json_get_profile/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_profile()');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->user_id,'is_string','user_id');
		$this->unit->run($array[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($array[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($array[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run(count((array)$array[0]) == 4, 'is_true', 'number of column');
	}

	/**
	 * Tests json_get_apps_test()
	 * @author Manassarn M.
	 */
	function json_get_apps_test(){
		$content = file_get_contents(base_url().'user/json_get_apps/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_apps()');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->user_id,'is_string','user_id');
		$this->unit->run($array[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($array[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($array[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run($array[0]->app_install_id,'is_string','app_install_id');
		$this->unit->run($array[0]->user_apps_register_date,'is_string','user_apps_register_date');
		$this->unit->run($array[0]->user_apps_last_seen,'is_string','user_apps_last_seen');
		$this->unit->run(count((array)$array[0]) == 7, 'is_true', 'number of column');
	}

	/**
	 * Tests json_get_campaigns()
	 * @author Manassarn M.
	 */
	function json_get_campaigns_test(){
		$content = file_get_contents(base_url().'user/json_get_campaigns/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_campaigns()');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->user_id,'is_string','user_id');
		$this->unit->run($array[0]->campaign_id,'is_string','campaign_id');
		$this->unit->run($array[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($array[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($array[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run(count((array)$array[0]) == 5, 'is_true', 'number of column');
	}
}

/* End of file user_test.php */
/* Location: ./application/controllers/test/user_test.php */