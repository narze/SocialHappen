<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_test extends CI_Controller {
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
		$content = file_get_contents(base_url().'page/json_get_profile/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array,'is_array', 'First row');
		$this->unit->run($array['page_id'],'is_string','page_id');
		$this->unit->run($array['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($array['company_id'],'is_string','company_id');
		$this->unit->run($array['page_name'],'is_string','page_name');
		$this->unit->run($array['page_detail'],'is_string','page_detail');
		$this->unit->run($array['page_all_member'],'is_string','page_all_member');
		$this->unit->run($array['page_new_member'],'is_string','page_new_member');
		$this->unit->run($array['page_image'],'is_string','page_image');
	}
	
	/**
	 * Tests json_get_installed_apps()
	 * @author Manassarn M.
	 */
	function json_get_installed_apps_test(){
		$content = file_get_contents(base_url().'page/json_get_installed_apps/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_installed_apps()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		//$this->unit->run($array[0]['company_id'],'is_string','company_id');
		//$this->unit->run($array[0]['app_id'],'is_string','app_id');
		//$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		//$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		//$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		//$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($array[0]['app_name'],'is_string','app_name');
		//$this->unit->run($array[0]['app_type_id'] == 1,'is_true',"app_type_id == 1");
		//$this->unit->run($array[0]['app_type'] == "Page Only",'is_true',"app_type == 'Page Only");
		//$this->unit->run($array[0]['app_maintainance'],'is_string','app_maintainance');
		//$this->unit->run($array[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($array[0]['app_description'],'is_string','app_description');
		//$this->unit->run($array[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($array[0]['app_url'],'is_string','app_url');
		//$this->unit->run($array[0]['app_install_url'],'is_string','app_install_url');
		//$this->unit->run($array[0]['app_config_url'],'is_string','app_config_url');
		//$this->unit->run($array[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($array[0]['app_image'],'is_string','app_image');
		//$this->unit->run($array[0]['app_facebook_api_key'],'is_string','app_facebook_api_key');
		//$this->unit->run(count($array[0]) == 19,'is_true', 'number of column');
	}
	
	/**
	 * Tests json_get_campaigns()
	 * @author Manassarn M.
	 */
	function json_get_campaigns_test(){
		$content = file_get_contents(base_url().'page/json_get_campaigns/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_campaigns()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($array[0]['campaign_status'],'is_string','campaign_status');
		$this->unit->run($array[0]['campaign_active_member'],'is_string','campaign_active_member');
		$this->unit->run($array[0]['campaign_all_member'],'is_string','campaign_all_member');
		$this->unit->run($array[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($array[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}
	
	/**
	 * Tests json_get_campaigns_using_status()
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status_test(){
		$content = file_get_contents(base_url().'page/json_get_campaigns_using_status/1/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_campaigns_using_status()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($array[0]['campaign_status'],'is_string','campaign_status');
		$this->unit->run($array[0]['campaign_active_member'],'is_string','campaign_active_member');
		$this->unit->run($array[0]['campaign_all_member'],'is_string','campaign_all_member');
		$this->unit->run($array[0]['campaign_start_timestamp'],'is_string','campaign_start_timestamp');
		$this->unit->run($array[0]['campaign_end_timestamp'],'is_string','campaign_end_timestamp');
		
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}
	
	/**
	 * Tests json_get_users()
	 * @author Manassarn M.
	 */
	function json_get_users_test(){
		$content = file_get_contents(base_url().'page/json_get_users/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_users()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['user_id'],'is_string','user_id');
		$this->unit->run($array[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($array[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($array[0]['user_email'],'is_string','user_email');
		$this->unit->run($array[0]['user_image'],'is_string','user_image');
		$this->unit->run($array[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($array[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($array[0]['user_last_seen'],'is_string','user_last_seen');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['user_apps_register_date'],'is_string','user_apps_register_date');
		$this->unit->run($array[0]['user_apps_last_seen'],'is_string','user_apps_last_seen');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
	}

	/**
	 * Tests json_add()
	 * @author Manassarn M.
	 */
	function json_add_test(){
		$page = array(
						'facebook_page_id' => rand(1, 10000000),
						'company_id' => rand(1, 10000000),
						'page_name' => 'page_name',
						'page_detail' => 'page_detail',
						'page_all_member' => rand(1, 10000000),
						'page_new_member' => rand(1, 10000000),
						'page_image' => 'page_image',
						);
		$content = $this->curl->ssl(FALSE)->simple_post(base_url().'page/json_add', $page);
		$content = json_decode($content, TRUE);
		$this->unit->run($content,'is_array', 'json_add()');
		$this->unit->run($content['page_id'],'is_int','page_id');
		$this->unit->run($content['status'] == 'OK','is_true', 'status');
		$this->unit->run(count($content) == 2,'is_true','return count');
	}
}

/* End of file page_test.php */
/* Location: ./application/controllers/test/page_test.php */