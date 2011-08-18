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
	 * Tests get_page_users_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id_test(){
		$result = $this->users->get_page_users_by_page_id(1);
		$this->unit->run($result,'is_array', 'get_page_users_by_page_id()');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result[0]['user_email'],'is_string','user_email');
		$this->unit->run($result[0]['user_image'],'is_string','user_image');
		$this->unit->run($result[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['user_apps_register_date'],'is_string','user_apps_register_date');
		$this->unit->run($result[0]['user_apps_last_seen'],'is_string','user_apps_last_seen');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');

		$this->unit->run(count($result[0]) == 17,'is_true', 'number of column');
		
	}

	/**
	 * Tests get_user_profile_by_user_id()
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_id_test(){
		$result = $this->users->get_user_profile_by_user_id(1);
		$this->unit->run($result,'is_array', 'get_user_profile_by_user_id()');
		$this->unit->run($result['user_id'],'is_string','user_id');
		$this->unit->run($result['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result['user_email'],'is_string','user_email');
		$this->unit->run($result['user_image'],'is_string','user_image');
		$this->unit->run($result['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result) == 8,'is_true', 'number of column');
	}

	/**
	 * Tests if user is company admin
	 * @author Manassarn M.
	 */
	function is_company_admin_test(){
		$result = $this->users->is_company_admin(1,1);
		$this->unit->run($result,'is_true','is_company_admin(1,1)');
		$result = $this->users->is_company_admin(1,10);
		$this->unit->run($result,'is_false','is_company_admin(1,1)');
		$result = $this->users->is_company_admin(713558190,1, TRUE);
		$this->unit->run($result,'is_true','is_company_admin(713558190,1,TRUE) : use user_facebook_id');
		$result = $this->users->is_company_admin(10,1);
		$this->unit->run($result,'is_false','is_company_admin(10,1)');
	}
	
	/**
	 * Test add_user() and remove_user()
	 * @author Manassarn M.
	 */
	function add_user_and_remove_user_test(){
		$user = array(
						'user_facebook_id' => '1',
						'user_register_date' => NULL,
						'user_last_seen' => '0'
					);
		$user_id = $this->users->add_user($user);
		$this->unit->run($user_id,'is_int','add_user()');
		
		$removed = $this->users->remove_user($user_id);
		$this->unit->run($removed,'is_true','remove_user()');
		
		$removed_again = $this->users->remove_user($user_id);
		$this->unit->run($removed_again,'is_false','remove_user()');
	}
	
	/**
	 * Test get_user_profile_by_user_facebook_id()
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_facebook_id_test(){
		$result = $this->users->get_user_profile_by_user_facebook_id(713558190);
		$this->unit->run($result,'is_array', 'get_user_profile_by_user_facebook_id()');
		$this->unit->run($result['user_id'],'is_string','user_id');
		$this->unit->run($result['user_first_name'],'is_string','user_first_name');
		$this->unit->run($result['user_last_name'],'is_string','user_last_name');
		$this->unit->run($result['user_email'],'is_string','user_email');
		$this->unit->run($result['user_image'],'is_string','user_image');
		$this->unit->run($result['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($result['user_register_date'],'is_string','user_register_date');
		$this->unit->run($result['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run(count($result) == 8,'is_true', 'number of column');
	}
	
	/**
	 * Test count_users_by_page_id()
	 * @author Manassarn M.
	 */
	function count_users_by_page_id_test(){
		$result = $this->users->count_users_by_page_id(1);
		$this->unit->run($result,'is_string', 'count_users_by_page_id()');
	}
	
	/**
	 * Test count_users_by_app_install_id()
	 * @author Manassarn M.
	 */
	function count_users_by_app_install_id_test(){
		$result = $this->users->count_users_by_app_install_id(1);
		$this->unit->run($result,'is_string', 'count_users_by_app_install_id()');
	}
	
	/**
	 * Test count_users_by_campaign_id()
	 * @author Manassarn M.
	 */
	function count_users_by_campaign_id_test(){
		$result = $this->users->count_users_by_campaign_id(1);
		$this->unit->run($result,'is_string', 'count_users_by_campaign_id()');
	}
	
	/**
	 * Test update_user_profile_by_user_id()
	 * @author Manassarn M.
	 */
	function update_user_profile_by_user_id_test(){
		$new_first_name = rand(1,10000);
		$data = array(
			'user_first_name' => $new_first_name
		);
		$result = $this->users->update_user_profile_by_user_id(1,$data);
		$this->unit->run($result === TRUE,'is_true', 'Updated user_first_name without error');
		
		$result = $this->users->get_user_profile_by_user_id(1);
		$this->unit->run($result['user_first_name'] == $new_first_name,'is_true',"Updated user_first_name to {$new_first_name}");
		
	}
}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */