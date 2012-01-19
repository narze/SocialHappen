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

		$this->load->model('page_model');

		$result = $this->page_model->update_page_profile_by_page_id(Page_id, array('enable_facebook_page_tab' => 0));
		$this->unit->run($result, TRUE, "disable facebook page tab", $result);

		$this->load->model('user_model');
		$result = $this->user_model->update_user(User_id, array('user_is_developer'=>0)); //Revoke developer permission

		$result = $this->tab_ctrl->main($user_facebook_id, $page_id, $facebook_page_id, $token);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['error'], 'Facebook app tab is not enabled', "result['error']", $result['error']);
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

	function get_page_score_1_test(){
		$user_facebook_id = User_facebook_id;
		$page_id = Page_id;
		$result = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id);
		$this->unit->run($result === 0, TRUE, "\$result", $result);
	}

	function _add_invite_component_before_test(){
		//Add app component (campaign_id = 1)
		$campaign_id = 1;
	    $app_id = 1;
	    $app_install_id = 1;
	    $page_id = 1;
	    $app_component_data = array(
	      'campaign_id' => $campaign_id,
	      'invite' => array(
	        'facebook_invite' => TRUE,
	        'email_invite' => TRUE,
	        'criteria' => array(
	          'score' => 1,
	          'maximum' => 7,
	          'cooldown' => 300,
	          'acceptance_score' => array(
	            'page' => 100,
	            'campaign' => 20
	          )
	        ),
	        'message' => array(
	          'title' => 'You are invited',
	          'text' => 'Welcome to the campaign',
	          'image' => 'https://localhost/assets/images/blank.png'
	        )
	      ),
	      'sharebutton' => array(
	        'facebook_button' => TRUE,
	        'twitter_button' => TRUE,
	        'criteria' => array(
	          'score' => 1,
	          'maximum' => 5,
	          'cooldown' => 300
	        ),
	        'message' => array(
	          'title' => 'Join the campaign by this link',
	          'caption' => 'This is caption',
	          'text' => 'this is long description',
	          'image' => 'https://localhost/assets/images/blank.png',
	        )
	      )
	    );

		$this->load->library('app_component_lib');
	    $result = $this->app_component_lib->add_campaign($app_component_data);
	    $this->unit->run($result, TRUE,'Add app_component with full data', print_r($result, TRUE));
	}
	function _give_page_score_to_all_inviters_test(){
		$user_facebook_id = '713558190';
		$facebook_page_id = Facebook_page_id;
		$campaign_id = 1;
		$this->load->model('audit_model');
		$this->load->model('achievement_stat_page_model');
		$audit_count_before = count($this->audit_model->list_audit());
		$this->load->model('user_model');
		$user_id = $this->user_model->get_user_id_by_user_facebook_id($user_facebook_id);
		$this->load->model('page_model');
		$page_id = $this->page_model->get_page_id_by_facebook_page_id($facebook_page_id);
		// $stat_before = $this->achievement_stat_page_model->list_stat(array(
		// 	'user_id' => (int) $user_id,
		// 	'page_id' => (int) $page_id
		// ));

		// $this->unit->run($stat_before_count = $stat_before[0]['action'][114]['count'], 'is_int','count $stat_before', $stat_before[0]['action'][114]['count']);

		$facebook_page_id = Facebook_page_id;
		$inviters = array(713558190, '637741627', 631885465, 713558190, '713558190'); //713558190 should be given one time only
		$this->load->library('invite_component_lib');
		$result = $this->invite_component_lib->_give_page_score_to_all_inviters($facebook_page_id, $inviters, $campaign_id);
		$this->unit->run($result, TRUE, '_give_page_score_to_all_inviters', $result);

		$stat_after = $this->achievement_stat_page_model->list_stat(array(
			'user_id' => (int) $user_id,
			'page_id' => (int) $page_id
		));
		$this->unit->run($stat_after[0]['action'][114]['count'], 1, 'count $stat_after idempotent test', $stat_after[0]['action'][114]['count']);
	}

	function get_page_score_2_test(){ //after get invite page score : 
		$user_facebook_id = User_facebook_id;
		$page_id = Page_id;
		$result = $this->tab_ctrl->get_page_score($user_facebook_id, $page_id);
		$this->unit->run($result, 100, "\$result", $result);
	}

	function page_leaderboard_test(){
		$page_id = Page_id;
		$result = $this->tab_ctrl->page_leaderboard($page_id);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['count'], 6, "\$result['count']", $result['count']);
		$this->unit->run($result['data'], 'is_array', "\$result['data']", $result['data']);
		$this->unit->run($result['data'][0]['page_score'], 100, "\$result['data'][0]['page_score']", $result['data'][0]['page_score']);
		$this->unit->run($result['data'][1]['page_score'], 100, "\$result['data'][1]['page_score']", $result['data'][1]['page_score']);
		$this->unit->run($result['data'][2]['page_score'], 100, "\$result['data'][2]['page_score']", $result['data'][2]['page_score']);	
		$this->unit->run($result['data'][3]['page_score'], 0, "\$result['data'][3]['page_score']", $result['data'][3]['page_score']);
		$this->unit->run($result['data'][4]['page_score'], 0, "\$result['data'][4]['page_score']", $result['data'][4]['page_score']);
		$this->unit->run($result['data'][5]['page_score'], 0, "\$result['data'][5]['page_score']", $result['data'][5]['page_score']);
	}

	function page_leaderboard_fail_test(){
		$result = $this->tab_ctrl->page_leaderboard(1234);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['error'], 'Page not found', "\$result['error']", $result['error']);
	}

}

/* End of file tab_ctrl_test.php */
/* Location: ./application/controllers/test/tab_ctrl_test.php */
