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
	 * Test get_app_users_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_app_users_by_app_install_id_test(){
		$result = $this->user_apps->get_app_users_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_app_users_by_app_install_id()');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['user_apps_register_date'],'is_string','user_apps_register_date');
		$this->unit->run($result[0]['user_apps_last_seen'],'is_string','user_apps_last_seen');
		$this->unit->run($result[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result[0]['user_email'],'is_string','user_email');
		$this->unit->run($result[0]['user_image'],'is_string','user_image');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result[0]) == 11,'is_true', 'number of column');
	}
	
	/**
	 * Test get_user_apps_by_user_id()
	 * @author Manassarn M.
	 */
	function get_user_apps_by_user_id_test(){
		$result = $this->user_apps->get_user_apps_by_user_id(1);
		$this->unit->run($result,'is_array', 'get_user_apps_by_user_id()');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['user_apps_register_date'],'is_string','user_apps_register_date');
		$this->unit->run($result[0]['user_apps_last_seen'],'is_string','user_apps_last_seen');
		$this->unit->run($result[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result[0]['user_email'],'is_string','user_email');
		$this->unit->run($result[0]['user_image'],'is_string','user_image');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result[0]) == 11,'is_true', 'number of column');
	}
	
	/**
	 * Test add_user_apps() and remove_user_apps()
	 * @author Manassarn M.
	 */
	function add_user_apps_and_remove_user_apps_test(){
		$user_id = $app_install_id = 50;
		$user_app = array(
							'user_id' => $user_id,
							'app_install_id' => $app_install_id,
							'user_apps_register_date' => NULL,
							'user_apps_last_seen' => NULL
						);
		$add_result = $this->user_apps->add_user_app($user_app);
		$this->unit->run($add_result,'is_true','add_user_apps()');
		
		$removed = $this->user_apps->remove_user_app($user_id, $app_install_id);
		$this->unit->run($removed == 1,'is_true','remove_user_apps()');
		
		$removed_again = $this->user_apps->remove_user_app($user_id, $app_install_id);
		$this->unit->run($removed_again == 0,'is_true','remove_user_apps()');
	}
}
/* End of file user_apps_model_test.php */
/* Location: ./application/controllers/test/user_apps_model_test.php */