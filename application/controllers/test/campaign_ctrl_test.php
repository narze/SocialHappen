<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('Campaign_id', 1);

class Campaign_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/campaign_ctrl');
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

	function main_fail_test()
	{
		$campaign_id = Campaign_id;
		$result = $this->campaign_ctrl->main($campaign_id);
		$this->unit->run($result['success'], FALSE, 'main fail test', $result['success']);
		$this->unit->run($result['error'], 'User is not admin', 'main fail test', $result['error']);

		$this->unit->mock_login();

		$campaign_id = NULL;
		$result = $this->campaign_ctrl->main($campaign_id);
		$this->unit->run($result['success'], FALSE, 'main fail test', $result['success']);
		$this->unit->run($result['error'], 'Campaign not found', 'main fail test', $result['error']);
	}

	function main_test()
	{
		$campaign_id = Campaign_id;
		$result = $this->campaign_ctrl->main($campaign_id);
		$this->unit->run($result['success'], TRUE, 'main test', $result['success']);
		$data = $result['data'];
		$this->unit->run($data['campaign_id'], $campaign_id, '$data[campaign_id]', $data['campaign_id']);
		$this->unit->run(isset($data['header']), TRUE, '$data[header]', htmlspecialchars($data['header']));
		$this->unit->run(isset($data['company_image_and_name']), TRUE, '$data[company_image_and_name]', htmlspecialchars($data['company_image_and_name']));
		$this->unit->run(isset($data['breadcrumb']), TRUE, '$data[breadcrumb]', htmlspecialchars($data['breadcrumb']));
		$this->unit->run(isset($data['campaign_profile']), TRUE, '$data[campaign_profile]', htmlspecialchars($data['campaign_profile']));
		$this->unit->run(isset($data['campaign_tabs']), TRUE, '$data[campaign_tabs]', htmlspecialchars($data['campaign_tabs']));
		$this->unit->run(isset($data['campaign_stat']), TRUE, '$data[campaign_stat]', htmlspecialchars($data['campaign_stat']));
		$this->unit->run(isset($data['campaign_users']), TRUE, '$data[campaign_users]', htmlspecialchars($data['campaign_users']));
		$this->unit->run(isset($data['footer']), TRUE, '$data[footer]', htmlspecialchars($data['footer']));

	}
	
	/**
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$campaign_id = 1;
		$content = $this->campaign_ctrl->json_get_profile($campaign_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array,'is_array', 'First row');
		$this->unit->run($array['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array['campaign_name'],'is_string','campaign_name');
		$this->unit->run($array['campaign_detail'],'is_string','campaign_detail');
		$this->unit->run($array['campaign_status_id'] == 1,'is_true','campaign_status_id == 1');
		$this->unit->run($array['campaign_status'] == "Inactive",'is_true','campaign_status == "Inactive"');
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
		$campaign_id = 1;
		$limit = NULL;
		$offset = NULL;
		$content = $this->campaign_ctrl->json_get_users($campaign_id, $limit, $offset);
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

	function index_test(){
		
	}

	function get_stat_graph(){
		
	}
}

/* End of file campaign_ctrl_test.php */
/* Location: ./application/controllers/test/campaign_ctrl_test.php */
