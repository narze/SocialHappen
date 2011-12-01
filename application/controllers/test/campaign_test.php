<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	function __destruct(){
		$this->unit->report_with_counter();
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
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$content = file_get_contents(base_url().'campaign/json_get_profile/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array,'is_array', 'First row');
		$this->unit->run($array['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array['campaign_status_id'] == 1,'is_true','campaign_status_id == 1');
		$this->unit->run($array['campaign_status'] == "Inactive",'is_true','campaign_status == "Inactive"');
		$this->unit->run($array['campaign_active_member'],'is_string','campaign_active_member');
		$this->unit->run($array['campaign_all_member'],'is_string','campaign_all_member');
		$this->unit->run($array['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($array['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($array['company_id'],'is_string','company_id');
		$this->unit->run($array['app_id'],'is_string','app_id');
		$this->unit->run($array['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array['page_id'],'is_string','page_id');
		$this->unit->run($array['app_install_secret_key'],'is_string','app_install_secret_key');
	}

	/**
	 * Tests json_get_users()
	 * @author Manassarn M.
	 */
	function json_get_users_test(){
		$content = file_get_contents(base_url().'campaign/json_get_users/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_users()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['user_id'],'is_string','user_id');
		$this->unit->run($array[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($array[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($array[0]['user_email'],'is_string','user_email');
		$this->unit->run($array[0]['user_image'],'is_string','user_image');
		$this->unit->run($array[0]['campaign_id'],'is_string','user_facebook_id');
		$this->unit->run($array[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($array[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run($array[0]['user_register_date'],'is_string','app_install_id');
	}
}

/* End of file campaign_test.php */
/* Location: ./application/controllers/test/campaign_test.php */
