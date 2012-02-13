<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('Page_id', 1);
define('Facebook_page_id', '116586141725712');
define('Company_id', 1);
class Page_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/page_ctrl');
		$this->unit->reset_dbs();
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

	function main_fail_test(){
		$page_id = Page_id;
		$result = $this->page_ctrl->main($page_id);
		$this->unit->run($result['success'], FALSE, 'main fail test', $result['success']);
		$this->unit->run($result['error'], 'User is not admin', 'main fail test', $result['error']);

		$this->unit->mock_login();

		$page_id = NULL;
		$result = $this->page_ctrl->main($page_id);
		$this->unit->run($result['success'], FALSE, 'main fail test', $result['success']);
		$this->unit->run($result['error'], 'Page not found', 'main fail test', $result['error']);
	}

	function main_test(){
		$page_id = Page_id;
		$facebook_page_id = Facebook_page_id;
		$result = $this->page_ctrl->main($page_id);
		$this->unit->run($result['success'], TRUE, 'main test', $result['success']);
		$data = $result['data'];
		$this->unit->run($data['page_id'], $page_id, '$page_id', $data['page_id']);
		$this->unit->run(isset($data['page_installed']), TRUE, 'page_installed', htmlspecialchars($data['page_installed']));
		$this->unit->run($data['facebook_page_id'], $facebook_page_id, '$facebook_page_id', $data['facebook_page_id']);
		$this->unit->run(issetor($data['app_facebook_api_key']) != FALSE, TRUE , 'app_facebook_api_key', htmlspecialchars($data['app_facebook_api_key']));
		$this->unit->run(isset($data['facebook_tab_url']), TRUE , 'facebook_tab_url', htmlspecialchars($data['facebook_tab_url']));
		$this->unit->run(issetor($data['header']) != FALSE, TRUE , 'header', htmlspecialchars($data['header']));
		$this->unit->run(issetor($data['company_image_and_name']) != FALSE, TRUE , 'company_image_and_name', htmlspecialchars($data['company_image_and_name']));
		$this->unit->run(issetor($data['breadcrumb']) != FALSE, TRUE , 'breadcrumb', htmlspecialchars($data['breadcrumb']));
		$this->unit->run(issetor($data['page_profile']) != FALSE, TRUE , 'page_profile', htmlspecialchars($data['page_profile']));
		$this->unit->run(issetor($data['page_tabs']) != FALSE, TRUE , 'page_tabs', htmlspecialchars($data['page_tabs']));
		$this->unit->run(issetor($data['page_apps']) != FALSE, TRUE , 'page_apps', htmlspecialchars($data['page_apps']));
		$this->unit->run(issetor($data['page_campaigns']) != FALSE, TRUE , 'page_campaigns', htmlspecialchars($data['page_campaigns']));
		$this->unit->run(issetor($data['page_users']) != FALSE, TRUE , 'page_users', htmlspecialchars($data['page_users']));
		$this->unit->run(issetor($data['page_report']) != FALSE, TRUE , 'page_report', htmlspecialchars($data['page_report']));
		$this->unit->run(issetor($data['footer']) != FALSE, TRUE , 'footer', htmlspecialchars($data['footer']));
	}

	function json_count_apps_test(){
		
	}

	function json_count_campaigns_test(){
		
	}

	function json_count_user_apps_test(){
		
	}

	function json_count_user_campaigns_test(){
		
	}

	function json_count_users_test(){
		
	}

	/**
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$page_id = 1;
		$content = $this->page_ctrl->json_get_profile($page_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array,'is_array', 'First row');
		$this->unit->run($array['page_id'],'is_string','page_id');
		$this->unit->run($array['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($array['company_id'],'is_string','company_id');
		$this->unit->run($array['page_name'],'is_string','page_name');
		$this->unit->run($array['page_detail'],'is_string','page_detail');
		$this->unit->run($array['page_image'],'is_string','page_image');
	}
	
	/**
	 * Tests json_get_installed_apps()
	 * @author Manassarn M.
	 */
	function json_get_installed_apps_test(){
		$page_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->page_ctrl->json_get_installed_apps($page_id, $limit, $offset);
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
		$page_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->page_ctrl->json_get_campaigns($page_id, $limit, $offset);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_campaigns()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($array[0]['campaign_status'],'is_string','campaign_status');
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
		$page_id = 1;
		$campaign_status_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->page_ctrl->json_get_campaigns_using_status($page_id, $campaign_status_id, $limit, $offset);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_campaigns_using_status()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array[0]['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array[0]['campaign_status_id'],'is_string','campaign_status_id');
		$this->unit->run($array[0]['campaign_status'],'is_string','campaign_status');
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
		$page_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->page_ctrl->json_get_users($page_id, $limit, $offset);
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
	}

	function remove_page_before_add_test(){
		$this->load->model('page_model');
		$result = $this->page_model->remove_page(Page_id);
		$this->unit->run($result, 1, "result", $result);
	}

	function json_add_test(){
		$facebook_page_id = Facebook_page_id;
		$company_id = Company_id;
		$page_name = 'TestPageName';
		$page_detail = 'TestPageDetail';
		$page_image = 'https://localhost/assets/images/blank2.png';
		$result = $this->page_ctrl->json_add($facebook_page_id,$company_id,$page_name,$page_detail,$page_image);
		$this->unit->run($result['success'], TRUE, "result['success']", $result['success']);
		$this->unit->run($result['data']['page_id'], 'is_int', "result['data']['page_id']", $result['data']['page_id']);
	}

	function json_add_fail_dup_test(){
		$facebook_page_id = Facebook_page_id;
		$company_id = Company_id;
		$page_name = 'TestPageName';
		$page_detail = 'TestPageDetail';
		$page_image = 'https://localhost/assets/images/blank2.png';
		$result = $this->page_ctrl->json_add($facebook_page_id,$company_id,$page_name,$page_detail,$page_image);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['error'], 'This page has already installed Socialhappen', "result['error']", $result['error']);
	}

	function json_get_not_installed_facebook_pages_test(){
		
	}

	function json_update_page_order_in_dashboard_test(){
		
	}

	function addapp_lightbox_test(){
		
	}

	function get_stat_graph_test(){
		
	}

	function json_get_page_user_data_test(){
		
	}

	function config_test(){
		
	}

}

/* End of file page_ctrl_test.php */
/* Location: ./application/controllers/test/page_ctrl_test.php */