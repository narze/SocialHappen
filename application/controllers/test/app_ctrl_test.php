<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('App_install_id','1');
class App_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/app_ctrl');
		$this->unit->reset_dbs();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
		$this->unit->report_with_counter();
	}
	
	function main_fail_test(){
		$app_install_id = App_install_id;
		$data = $this->app_ctrl->main($app_install_id);
		$this->unit->run($data['success'], FALSE, 'main test without session');
		$this->unit->run($data['error'], 'User is not admin', 'main test without session');
	}

	function main_test(){
		$app_install_id = App_install_id;
		$this->unit->mock_login();
		$result = $this->app_ctrl->main($app_install_id);
		$this->unit->run($result['success'], TRUE, 'main test with session');
		$data = $result['data'];
		$this->unit->run(issetor($data['app_install_id']), $app_install_id, '$data');
		$this->unit->run(issetor($data['header']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['company_image_and_name']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['breadcrumb']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['app_profile']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['app_tabs']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['app_campaigns']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['app_users']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['footer']) != FALSE, TRUE, '$data');
	}

	function config_test(){
		
	}

	function go_test(){
		
	}

	function json_count_campaigns_test(){
		
	}

	function json_count_users_test(){
		
	}

	function json_get_app_install_status_test(){
		
	}

	function json_get_all_app_install_status_test(){
		
	}

	// function json_get_app_by_api_key_test(){
		
	// }

	function json_update_app_order_in_dashboard_test(){
		
	}

	function curl_test(){
		
	}

	function json_add_to_page_test(){
		
	}

	/**
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$app_install_id = 1;
		$content = $this->app_ctrl->json_get_profile($app_install_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array, 'is_array', 'json_get_profile()');
		$this->unit->run($array['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array['company_id'],'is_string','company_id');
		$this->unit->run($array['app_id'],'is_string','app_id');
		$this->unit->run($array['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array['page_id'],'is_string','page_id');
		$this->unit->run($array['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($array['app_name'],'is_string','app_name');
		$this->unit->run($array['app_type_id'] == 1,'is_true','app_type_id == 1');
		$this->unit->run($array['app_type'] == "Page Only",'is_true','app_type == "Page Only"');
		$this->unit->run($array['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($array['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($array['app_description'],'is_string','app_description');
		$this->unit->run($array['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($array['app_url'],'is_string','app_url');
		$this->unit->run($array['app_install_url'],'is_string','app_install_url');
		$this->unit->run($array['app_config_url'],'is_string','app_config_url');
		$this->unit->run($array['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($array['app_image'],'is_string','app_image');
		$this->unit->run($array['app_facebook_api_key'],'is_string','app_facebook_api_key');
	}
	
	/**
	 * Tests json_get_campaigns()
	 * @author Manassarn M.
	 */
	function json_get_campaigns_test(){
		$app_install_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->app_ctrl->json_get_campaigns($app_install_id, $limit, $offset);
		$array = json_decode($content, TRUE);
		$this->unit->run($array, 'is_array', 'json_get_campaigns()');
		$this->unit->run($array[0], 'is_array', 'First row');
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
		$app_install_id = 1;
		$campaign_status_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->app_ctrl->json_get_campaigns_using_status($app_install_id, $campaign_status_id, $limit, $offset);
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
	 * Test json_get_users()
	 * @author Manassarn M.
	 */
	function json_get_users_test(){
		$app_install_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->app_ctrl->json_get_users($app_install_id, $limit, $offset);
		$array = json_decode($content, TRUE);
		$this->unit->run($array, 'is_array', 'json_get_users()');
		$this->unit->run($array[0], 'is_array', 'First row');
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
	}
	
	/**
	 * Tests json_get_pages()
	 * @author Manassarn M.
	 */
	function json_get_pages_test(){
		$app_install_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->app_ctrl->json_get_pages($app_install_id, $limit, $offset);
		$array = json_decode($content, TRUE);
		$this->unit->run($array, 'is_array', 'json_get_pages()');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($array[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($array[0]['page_name'],'is_string','page_name');
		$this->unit->run($array[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($array[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($array[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($array[0]['page_image'],'is_string','page_image');
	}
	
	function json_add_test(){
		
	}
}

/* End of file app_test.php */
/* Location: ./application/controllers/test/app_test.php */
