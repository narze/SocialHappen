<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('User_id', 1);
define('Page_id', 1);
define('App_install_id', 1);
define('Campaign_id', 1);
class User_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/user_ctrl');
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
		$user_id = User_id;
		$input = compact('user_id');
		$result = $this->user_ctrl->main($input);
		$this->unit->run($result['success'], FALSE, 'main fail test : no id specified');
		$this->unit->run($result['error'] == 'No id specified', TRUE, 'main fail test : no id specified');

		$page_id = Page_id;
		$input = compact('page_id');
		$result = $this->user_ctrl->main($input);
		$this->unit->run($result['success'], FALSE, 'main fail test : no user id specified');
		$this->unit->run($result['error'] == 'No user_id specified', TRUE, 'main fail test : no user id specified');
	}

	function main_test(){
		$user_id = User_id;
		$page_id = Page_id;
		$app_install_id = App_install_id;
		$campaign_id = Campaign_id;
		$input1 = compact('user_id', 'page_id');
		$result1 = $this->user_ctrl->main($input1);
		$input2 = compact('user_id', 'app_install_id');
		$result2 = $this->user_ctrl->main($input2);
		$input3 = compact('user_id', 'campaign_id');
		$result3 = $this->user_ctrl->main($input3);

		$this->unit->run($result1['success'], TRUE, 'main test with page_id');
		$this->unit->run($result2['success'], TRUE, 'main test with app_install_id');
		$this->unit->run($result3['success'], TRUE, 'main test with campaign_id');

		$data1 = $result1['data'];
		$data2 = $result2['data'];
		$data3 = $result3['data'];

		$this->unit->run($data1['user_id'], $user_id, 'main test $data');
		$this->unit->run($data1['page_id'], $page_id, 'main test $data');
		$this->unit->run($data1['app_install_id'], FALSE, 'main test $data');
		$this->unit->run($data1['campaign_id'], FALSE, 'main test $data');
		$this->unit->run(issetor($data1['header']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['company_image_and_name']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['breadcrumb']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['user_profile']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['user_tabs']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['user_activities']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['user_info']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data1['footer']) != FALSE, TRUE, 'main test $data');

		$this->unit->run($data2['user_id'], $user_id, 'main test $data');
		$this->unit->run($data2['page_id'], FALSE, 'main test $data');
		$this->unit->run($data2['app_install_id'], $app_install_id, 'main test $data');
		$this->unit->run($data2['campaign_id'], FALSE, 'main test $data');
		$this->unit->run(issetor($data2['header']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['company_image_and_name']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['breadcrumb']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['user_profile']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['user_tabs']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['user_activities']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['user_info']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data2['footer']) != FALSE, TRUE, 'main test $data');

		$this->unit->run($data3['user_id'], $user_id, 'main test $data');
		$this->unit->run($data3['page_id'], FALSE, 'main test $data');
		$this->unit->run($data3['app_install_id'], FALSE, 'main test $data');
		$this->unit->run($data3['campaign_id'], $campaign_id, 'main test $data');
		$this->unit->run(issetor($data3['header']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['company_image_and_name']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['breadcrumb']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['user_profile']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['user_tabs']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['user_activities']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['user_info']) != FALSE, TRUE, 'main test $data');
		$this->unit->run(issetor($data3['footer']) != FALSE, TRUE, 'main test $data');
	}
	
	/**
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$user_id = 1;
		$content = $this->user_ctrl->json_get_profile($user_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array['user_id'],'is_string','user_id');
		$this->unit->run($array['user_first_name'],'is_string','user_first_name');
		$this->unit->run($array['user_last_name'],'is_string','user_last_name');
		$this->unit->run($array['user_email'],'is_string','user_email');
		$this->unit->run($array['user_image'],'is_string','user_image');			
		$this->unit->run($array['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($array['user_register_date'],'is_string','user_register_date');
		$this->unit->run($array['user_last_seen'],'is_string','user_last_seen');
	}

	/**
	 * Tests json_get_apps_test()
	 * @author Manassarn M.
	 */
	function json_get_apps_test(){
		$user_id = 1;
		$content = $this->user_ctrl->json_get_apps($user_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_apps()');
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
	}

	/**
	 * Tests json_get_campaigns()
	 * @author Manassarn M.
	 */
	function json_get_campaigns_test(){
		$user_id = 1;
		$content = $this->user_ctrl->json_get_campaigns($user_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_campaigns()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['user_id'],'is_string','user_id');
		$this->unit->run($array[0]['user_first_name'],'is_string','user_first_name');
		$this->unit->run($array[0]['user_last_name'],'is_string','user_last_name');
		$this->unit->run($array[0]['user_email'],'is_string','user_email');
		$this->unit->run($array[0]['user_image'],'is_string','user_image');	
		$this->unit->run($array[0]['campaign_id'],'is_string','campaign_id');
		$this->unit->run($array[0]['user_facebook_id'],'is_string','user_facebook_id');
		$this->unit->run($array[0]['user_register_date'],'is_string','user_register_date');
		$this->unit->run($array[0]['user_last_seen'],'is_string','user_last_seen');
	}

	/**
	 * Tests json_add()
	 * @author Manassarn M.
	 */
	function json_add_test(){
		$user = array(
						'user_facebook_id' => rand(1, 10000000),
						);
		$content = $this->curl->ssl(FALSE)->simple_post(base_url().'user/json_add', $user);
		$content = json_decode($content, TRUE);
		$this->unit->run($content,'is_array', 'json_add()');
		$this->unit->run($content['user_id'],'is_int','user_id');
		$this->unit->run($content['status'] == 'OK','is_true', 'status');
	}
	
	/**
	 * Tests json_get_companies()
	 * @author Manassarn M.
	 */
	function json_get_companies_test(){
		$user_id = 1;
		$content = $this->user_ctrl->json_get_companies($user_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_companies()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['user_id'],'is_string','user_id');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['user_role'],'is_string','user_role');
		$this->unit->run($array[0]['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($array[0]['company_name'],'is_string','company_name');
		$this->unit->run($array[0]['company_detail'],'is_string','company_detail');
		$this->unit->run($array[0]['company_address'],'is_string','company_address');
		$this->unit->run($array[0]['company_email'],'is_string','company_email');
		$this->unit->run($array[0]['company_telephone'],'is_string','company_telephone');
		$this->unit->run($array[0]['company_register_date'],'is_string','company_register_date');
		$this->unit->run($array[0]['company_username'],'is_string','company_username');
		$this->unit->run($array[0]['company_password'],'is_string','company_password');
		$this->unit->run($array[0]['company_image'],'is_string','company_image');
	}
}

/* End of file user_ctrl_test.php */
/* Location: ./application/controllers/test/user_ctrl_test.php */