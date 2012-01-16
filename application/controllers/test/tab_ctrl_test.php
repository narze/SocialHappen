<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('User_id',1);
define('User_facebook_id','713558190');
define('User_facebook_id_2','713558191');
define('Page_id',1);
define('Facebook_page_id', '116586141725712');
define('App_install_id', 1);
define('Campaign_id', 1);
class Tab_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/tab_ctrl');
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
		$user_facebook_id = User_facebook_id;
		$page_id = NULL;
		$facebook_page_id = NULL;
		$token = NULL;
		$result = $this->tab_ctrl->main($user_facebook_id, $page_id, $facebook_page_id, $token);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['error'], 'No page_id specified', "result['error']", $result['error']);
	}

	function main_test(){
		$user_facebook_id = User_facebook_id;
		$page_id = Page_id;
		$facebook_page_id = NULL;
		$token = NULL;
		$result = $this->tab_ctrl->main($user_facebook_id, $page_id, $facebook_page_id, $token);
		$this->unit->run($result['success'], TRUE, 'main test with session');
		$data = $result['data'];
		$this->unit->run(issetor($data['header']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['bar']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['main']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['footer']) != FALSE, TRUE, '$data');

		$user_facebook_id = User_facebook_id;
		$page_id = NULL;
		$facebook_page_id = Facebook_page_id;
		$token = NULL;
		$result = $this->tab_ctrl->main($user_facebook_id, $page_id, $facebook_page_id, $token);
		$this->unit->run($result['success'], TRUE, 'main test with session');
		$data = $result['data'];
		$this->unit->run(issetor($data['header']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['bar']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['main']) != FALSE, TRUE, '$data');
		$this->unit->run(issetor($data['footer']) != FALSE, TRUE, '$data');
	}

	function logout_test(){
		
	}

	function dashboard_test(){
		
	}

	function get_started_test(){
		
	}

	function profile_test(){
		
	}

	function apps_campaigns_test(){
		
	}

	function user_apps_campaigns_test(){
		
	}

	function activities_test(){
		
	}

	function leaderboard_test(){
		
	}

	function favorites_test(){
		
	}

	function notifications_test(){
		
	}

	function json_get_notifications_test(){
		
	}

	function json_count_user_notifications_test(){
		
	}

	function account_test(){
		
	}

	function guest_test(){
		
	}

	function signup_test(){
		
	}

	function signup_submit_fail_test(){
		$first_name = '';
		$last_name = 'TestLastName';
		$email = 'TestEmail@email.com';
		$user_facebook_id = User_facebook_id;
		$timezone = 'Asia/Bangkok';
		$page_id = Page_id;
		$app_install_id = App_install_id;
		$facebook_access_token = NULL;
		$result = $this->tab_ctrl->signup_submit($first_name, $last_name, $email, $user_facebook_id, $timezone, $page_id, $app_install_id, $facebook_access_token);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['error']['error'], 'verify', "result['error']['error']", $result['error']['error']);
		$this->unit->run($result['error']['error_messages'], 'is_array', "result['error']['error_messages']", $result['error']['error_messages']);
	}

	function signup_submit_dup_user_fail_test(){
		$first_name = 'TestFirstName';
		$last_name = 'TestLastName';
		$email = 'TestEmail@email.com';
		$user_facebook_id = User_facebook_id;
		$timezone = 'Asia/Bangkok';
		$page_id = Page_id;
		$app_install_id = App_install_id;
		$facebook_access_token = NULL;
		$result = $this->tab_ctrl->signup_submit($first_name, $last_name, $email, $user_facebook_id, $timezone, $page_id, $app_install_id, $facebook_access_token);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['error']['error'], 'add_user', "result['error']['error']", $result['error']['error']);
	}

	function signup_submit_test(){
		$first_name = 'TestFirstName';
		$last_name = 'TestLastName';
		$email = 'TestEmail@email.com';
		$user_facebook_id = User_facebook_id_2;
		$timezone = 'Asia/Bangkok';
		$page_id = Page_id;
		$app_install_id = App_install_id;
		$facebook_access_token = 'sampleaccesstoken';
		$result = $this->tab_ctrl->signup_submit($first_name, $last_name, $email, $user_facebook_id, $timezone, $page_id, $app_install_id, $facebook_access_token);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		$this->load->model('user_model');
		$user = $this->user_model->get_user_profile_by_user_id($result['data']['user_id']);
		$this->unit->run($user['user_first_name'], $first_name, 'first_name', $user['user_first_name']);
		$this->unit->run($user['user_last_name'], $last_name, 'last_name', $user['user_last_name']);
		$this->unit->run($user['user_email'], $email, 'email', $user['user_email']);
		$this->unit->run($user['user_facebook_access_token'], $facebook_access_token, 'facebook_access_token', $user['user_facebook_access_token']);
		$this->unit->run($user['user_facebook_id'], $user_facebook_id, 'user_facebook_id', $user['user_facebook_id']);
	}

	function signup_page_test(){
		
	}

	function _remove_page_user_data_before_test(){
		$this->load->model('page_user_data_model');
		$this->page_user_data_model->remove_page_user_by_user_id_and_page_id(User_id, Page_id);
	}

	function signup_page_submit_test(){
		$user_id = User_id;
		$user_facebook_id = User_facebook_id;
		$app_install_id = App_install_id;
		$page_id = Page_id;
		$user_data = array(
			'data1' => 1,
			'data2' => '2'
		);
		$result = $this->tab_ctrl->signup_page_submit($user_id, $user_facebook_id, $app_install_id, $page_id, $user_data);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$data = $result['data'];
		$this->unit->run($data['status'], 'ok', "\$data['status']", $data['status']);
		$this->unit->run($data['redirect_url'], 'is_string', "\$data['redirect_url']", $data['redirect_url']);
	}

	function signup_page_submit_dup_fail_test(){
		$user_id = User_id;
		$user_facebook_id = User_facebook_id;
		$app_install_id = App_install_id;
		$page_id = Page_id;
		$user_data = array(
			'data1' => 1,
			'data2' => '2'
		);
		$result = $this->tab_ctrl->signup_page_submit($user_id, $user_facebook_id, $app_install_id, $page_id, $user_data);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$error = $result['error'];
		$this->unit->run($error['status'], 'error', "\$error['status']", $error['status']);
		$this->unit->run($error['error'], 'add_page_user', "\$error['error']", $error['error']);
	}

	function signup_complete_test(){
		
	}

	function signup_campaign_test(){
		
	}

	function _remove_campaign_user_before_test(){
		$this->load->model('user_campaigns_model');
		$this->user_campaigns_model->remove_user_campaign(User_id, Campaign_id);
	}

	function signup_campaign_submit_test(){
		$user_id = User_id;
		$user_facebook_id = User_facebook_id;
		$campaign_id = Campaign_id;
		$result = $this->tab_ctrl->signup_campaign_submit($user_id, $user_facebook_id, $campaign_id);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$data = $result['data'];
		$this->unit->run($data['status'], 'ok', "\$data['status']", $data['status']);
	}

	function signup_campaign_submit_dup_fail_test(){
		$user_id = User_id;
		$user_facebook_id = User_facebook_id;
		$campaign_id = Campaign_id;
		$result = $this->tab_ctrl->signup_campaign_submit($user_id, $user_facebook_id, $campaign_id);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$error = $result['error'];
		$this->unit->run($error['status'], 'error', "\$error['status']", $error['status']);
		$this->unit->run($error['error'], 'add_user_campaign error', "\$error['error']", $error['error']);
	}

	function page_installed_test(){
		
	}

	function app_installed_test(){
		
	}

	function login_button_test(){
		
	}

	function facebook_page_test(){
		
	}

	function facebook_app_test(){
		
	}

}

/* End of file tab_ctrl_test.php */
/* Location: ./application/controllers/test/tab_ctrl_test.php */
