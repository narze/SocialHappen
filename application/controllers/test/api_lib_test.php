<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Test class for external API
* baware of chaining effects
* @author Wachiraph C.
*/

/*set these customized parameters first*/
define('APP_ID','1');
define('COMPANY_ID','1');
define('APP_SECRET_KEY','ad3d4f609ce1c21261f45d0a09effba4');
define('USER_ID','1');
define('PAGE_ID','1');
define('USER_FACEBOOK_ID','713558190');
define('FACEBOOK_PAGE_ID','116586141725712');
define('CAMPAIGN_ID','1');
	
class Api_lib_test extends CI_Controller {

	private $app_install_id;
	private $app_install_secret_key;
	
	private $public_invite_key;
	private $private_invite_key;
	
	private $prev_facebook_tab_url;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('api_lib');
				
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
	
	function _dummy_login(){
	
		$userdata = array(
					'user_id' => USER_ID,
					'user_facebook_id' => USER_FACEBOOK_ID,
					'logged_in' => TRUE
				);
			
		$this->session->set_userdata($userdata);
	}
	
	function prepare_data_test(){
		/* //mock facebook_tab_url for page_id 1, to ensure testing method
		$this->load->model('page_model');
		$page_profile = $this->page_model->get_page_profile_by_page_id(PAGE_ID);
		$this->prev_facebook_tab_url = $page_profile['facebook_tab_url'];
		
		$result = $this->page_model->update_page_profile_by_page_id(PAGE_ID, array('facebook_tab_url' => 'http://mock-url.com'));
		
		$ this->unit->run($result, 'is_true', 'prepare_data_test - facebook_tab_url', print_r($result, TRUE));
		*/
	}
	
	function request_install_app_test(){
		
		$this->load->model('Installed_apps_model');
	
		$result = $this->api_lib->request_install_app(APP_ID,APP_SECRET_KEY,COMPANY_ID,USER_ID,USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_install_app()', print_r($result, TRUE));
		$this->app_install_id = $result['app_install_id'];
		$this->app_install_secret_key = $result['app_install_secret_key'];
		
		$this->unit->run($this->app_install_id, 'is_numeric', 'app_install_id', $this->app_install_id);
		$this->unit->run($this->app_install_secret_key, 'is_string', 'app_install_secret_key', $this->app_install_secret_key);
		
		$result = $this->Installed_apps_model->get_app_profile_by_app_install_id($this->app_install_id);
		$this->unit->run($result, 'is_array', 'new installed app', print_r($result, TRUE));
		
	}
	
	function request_install_page_test(){
		
		$this->load->model('Installed_apps_model');
		$init_installed_page_count = $this->Installed_apps_model->count_installed_apps_by_page_id(PAGE_ID);
		$result = $this->api_lib->request_install_page(APP_ID,APP_SECRET_KEY,$this->app_install_id,
														$this->app_install_secret_key,PAGE_ID,FACEBOOK_PAGE_ID,
														USER_ID, USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_install_page()', print_r($result, TRUE));
		$install_status = $result['status'];
		$this->unit->run($install_status, 'OK', 'install_page_status', $install_status);
		
		$finish_installed_page_count = $this->Installed_apps_model->count_installed_apps_by_page_id(PAGE_ID);
		$different_installed_page = $finish_installed_page_count - $init_installed_page_count;
		$this->unit->run($different_installed_page, 1, 'new installed page', $different_installed_page);
		
	}
		
	function request_user_id_test(){
				
		$result = $this->api_lib->request_user_id(USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_user_id()', print_r($result, TRUE));
		$user_id = $result['user_id'];
		$this->unit->run($user_id, USER_ID, 'user_id', $user_id);
		
	}
	
	function request_user_facebook_id_test(){
		$result = $this->api_lib->request_user_facebook_id(USER_ID);
		
		$this->unit->run($result, 'is_array', 'request_user_facebook_id()', print_r($result, TRUE));
		$user_facebook_id = $result['user_facebook_id'];
		$this->unit->run($user_facebook_id, USER_FACEBOOK_ID, 'user_facebook_id', $user_facebook_id);
	}
		
	function request_page_id_test(){
		$result = $this->api_lib->request_page_id(FACEBOOK_PAGE_ID);
		
		$this->unit->run($result, 'is_array', 'request_page_id()', print_r($result, TRUE));
		$page_id = $result['page_id'];
		$this->unit->run($page_id, PAGE_ID, 'page_id', $page_id);
	}
	
	function request_facebook_page_id_test(){
		$result = $this->api_lib->request_facebook_page_id(PAGE_ID);
		
		$this->unit->run($result, 'is_array', 'request_facebook_page_id()', print_r($result, TRUE));
		$facebook_page_id = $result['facebook_page_id'];
		$this->unit->run($facebook_page_id, FACEBOOK_PAGE_ID, 'facebook_page_id', $facebook_page_id);
	}
		
	function request_log_user_test(){
		$init_timestamp = time();
	
		$result = $this->api_lib->request_log_user(APP_ID, APP_SECRET_KEY, $this->app_install_id, 
													$this->app_install_secret_key, USER_ID, USER_FACEBOOK_ID,
													CAMPAIGN_ID, 1);
		
		$this->unit->run($result, 'is_array', 'request_log_user()', print_r($result, TRUE));
		
		$this->load->model('User_apps_model', 'User_apps');
		$user_app_exist = $this->User_apps->check_exist(USER_ID, $this->app_install_id);
		$this->unit->run($user_app_exist, 'is_true', 'user_app_exist', $user_app_exist);
		
		$this->load->model('audit_model','audit');
		$audit = $this->audit->list_audit(array(
												'app_id' => APP_ID,
												'action_id' => 1,
												'app_install_id' => $this->app_install_id,
												'user_id' => USER_ID,
												),
												1
										);
		if(count($audit>0))
			$audit = $audit[0];
		else
			$audit['timestamp'] = 0;
			
		$created_audit = $audit['timestamp'] - $init_timestamp;
		$is_created_audit = $created_audit >= 0;
		$this->unit->run($is_created_audit, 'is_true', 'is_create_audit', $created_audit);
		
		$this->load->model('achievement_stat_model','achievement_stat');
		$achievement_stat = $this->achievement_stat->get(APP_ID, USER_ID);
		$archivement_list = $achievement_stat['action'][1]['app_install'];
		
		$this->unit->run(array_key_exists($this->app_install_id, $archivement_list), 'is_true', 
			'achievement_stat', array_key_exists($this->app_install_id, $archivement_list));
		
	}
			
	function request_authenticate_test(){
		
		$result = $this->api_lib->request_authenticate(APP_ID, APP_SECRET_KEY, $this->app_install_id, 
													$this->app_install_secret_key, USER_ID, USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_authenticate()', print_r($result, TRUE));
		
		$status = $result['status'];
		$this->unit->run($status, 'OK', 'authentication_status', $status);
	}
		
	function request_user_session_test(){
		$result = $this->api_lib->request_user_session(USER_ID, USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_user_session()', print_r($result, TRUE));
		
		$status = $result['status'];
		$session_id = $result['session_id'];
		$this->unit->run($status, 'OK', 'user_session_status', $status);
		$this->unit->run($session_id, 'is_string', 'user_session_id', $session_id);
	}
	
	function request_user_test(){
		$result = $this->api_lib->request_user(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key,USER_ID, USER_FACEBOOK_ID);
		
		$this->unit->run($result, 'is_array', 'request_user()', print_r($result, TRUE));
		
		$status = $result['status'];
		$user_id = $result['user_id'];
		$user_first_name = $result['user_first_name'];
		$user_last_name = $result['user_last_name'];
		$user_email = $result['user_email'];
		
		$this->unit->run($status, 'OK', 'user status', $status);
		$this->unit->run($user_id, USER_ID, 'user_id', $user_id);
		$this->unit->run($user_first_name, 'is_string', 'user_first_name', $user_first_name);
		$this->unit->run($user_last_name, 'is_string', 'user_last_name', $user_last_name);
		$this->unit->run($user_email, 'is_string', 'user_email', $user_email);
		
	}
	
	function request_page_users_test(){
		$result = $this->api_lib->request_page_users(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, PAGE_ID, FACEBOOK_PAGE_ID);
		
		
		$this->unit->run($result, 'is_array', 'request_page_users()', print_r($result, TRUE));
		
		$status = $result['status'];
		$page_users = $result['page_users'];
		$this->unit->run($status, 'OK', 'status', $status);
		$this->unit->run($page_users, 'is_array', 'page_users', print_r($page_users, TRUE));
		foreach($page_users as $page_user){
			if($page_user['user_id'] == USER_ID){
				$this->unit->run($page_user['user_id'], USER_ID, 'page_user_id', $page_user['user_id']);
				$this->unit->run($page_user['user_facebook_id'], USER_FACEBOOK_ID, 'page_user_facebook_id', $page_user['user_facebook_id']);
				break;
			}
		}
	}
	
	function request_app_users_test(){
		$result = $this->api_lib->request_app_users(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key);
		
		
		$this->unit->run($result, 'is_array', 'request_app_users()', print_r($result, TRUE));
		
		$status = $result['status'];
		$app_users = $result['app_users'];
		$this->unit->run($status, 'OK', 'status', $status);
		$this->unit->run($app_users, 'is_array', 'app_users', print_r($app_users, TRUE));
		foreach($app_users as $app_user){
			if($app_user['user_id'] == USER_ID){
				$this->unit->run($app_user['user_id'], USER_ID, 'app_user_id', $app_user['user_id']);
				$this->unit->run($app_user['user_facebook_id'], USER_FACEBOOK_ID, 'app_user_facebook_id', $app_user['user_facebook_id']);
				$this->unit->run($app_user['app_install_id'], $this->app_install_id, 'app_user_page_install_id', $app_user['app_install_id']);
				break;
			}
		}
		
	}

	function request_add_limit_service_test(){
		$this->load->library('audit_stat_limit_lib');
		
		$limit_count = $this->audit_stat_limit_lib->count(USER_ID, 
										1,
										$this->app_install_id,
										CAMPAIGN_ID);
	
		$result = $this->api_lib->request_add_limit_service(USER_ID, 1, $this->app_install_id,
												CAMPAIGN_ID);
		
		$this->unit->run($result, 'is_array', 'request_add_limit_service()', print_r($result, TRUE));
		
		$status = $result['status'];
		$this->unit->run($status, 'OK', 'status', $status);
		
		$new_limit_count = $this->audit_stat_limit_lib->count(USER_ID, 
										1,
										$this->app_install_id,
										CAMPAIGN_ID);
		
		$limit_diff = $new_limit_count - $limit_count;
		$valid_limit_diff = $limit_diff > 0;
		
		$this->unit->run($valid_limit_diff, 'is_true', 'limit_diff', $limit_diff);
		
	}

	function request_count_limit_service_test(){
		$this->load->library('audit_stat_limit_lib');
		
		$limit_count = $this->audit_stat_limit_lib->count(USER_ID, 
										1,
										$this->app_install_id,
										CAMPAIGN_ID);
	
		$result = $this->api_lib->request_count_limit_service(USER_ID, 1, $this->app_install_id,
												CAMPAIGN_ID);
		
		$this->unit->run($result, 'is_array', 'request_count_limit_service()', print_r($result, TRUE));
		
		$status = $result['status'];
		$count = $result['count'];
		
		$this->unit->run($status, 'OK', 'status', $status);
		
		$valid_limit_diff = $limit_count == $count;
		
		$this->unit->run($valid_limit_diff, 'is_true', 'limit_diff', $limit_count);
		
	}

	function bar_test(){
		
		$result = $this->api_lib->bar(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												USER_ID);
		
		
		$status = $result['status'];
		$html = strip_tags($result['html']);
		$css = $result['css'];
		
		$this->unit->run($result, 'is_array', 'bar()', 'Array');
		$this->unit->run($status, 'OK', 'status', $status);
		$this->unit->run($css, 'is_string', 'css', $css);
		$this->unit->run($html, 'is_string', 'html', $html);
	
	}
	
	function get_started_test(){
		
		$result = $this->api_lib->get_started(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												USER_ID);
		
		
		$status = $result['status'];
		$html = strip_tags($result['html']);
		
		$this->unit->run($result, 'is_array', 'get_started()', 'Array');
		$this->unit->run($status, 'OK', 'status', $status);
		$this->unit->run($html, 'is_string', 'html', $html);
	
	}

	function setting_template_test(){
		
		$result = $this->api_lib->setting_template(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												USER_ID);
		
		
		$status = $result['status'];
		$html = strip_tags($result['html']);
		
		$this->unit->run($result, 'is_array', 'setting_template()', 'Array');
		$this->unit->run($status, 'OK', 'status', $status);
		$this->unit->run($html, 'is_string', 'html', $html);
	
	}

	function show_notification_test(){
		
		$result = $this->api_lib->show_notification(USER_ID, 10);
		
		
		//recheck
		$this->unit->run($result, 'is_array', 'show_notification()', print_r($result, TRUE));
		
	}
	
	function read_notification_test(){
		
		$result = $this->api_lib->read_notification(USER_ID);
		
		
		//recheck
		$this->unit->run($result, 'is_array', 'read_notification()', print_r($result, TRUE));
		$this->unit->run($result['result'], 'OK', 'status', $result['result']);
		
	}

	function request_add_achievement_infos_test(){
		$this->load->model('achievement_info_model');
		$achievement_info = $this->achievement_info_model->list_info();
		$achievement_info_count = count($achievement_info);
		
		$example_achievement_infos = base64_encode(
								json_encode(
									array(
										array(
											'info' => array(
																'name' => 'Example Achievement Info',
																'description' => 'Example Achievement Info',
																'hidden' => false,
																'enable' => true,
																'criteria_string' => array('Share = 10'),
															),
											'criteria' =>
												array('a' => 5, 'b' => 2)
										)
									)
								)
							);
	
		$result = $this->api_lib->request_add_achievement_infos(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, $example_achievement_infos);
		
		
		$this->unit->run($result, 'is_array', 'request_add_achievement_infos()', print_r($result, TRUE));
		
		$status = $result['status'];
		
		$this->unit->run($status, 'OK', 'status', $status);
		
		$achievement_info = $this->achievement_info_model->list_info();
		$new_achievement_info_count = count($achievement_info);
		
		$archievement_diff = $new_achievement_info_count - $achievement_info_count;
		$valid_archievement_diff = (boolean)((int)$archievement_diff > 0);
		
		$this->unit->run($valid_archievement_diff, 'is_true', 'valid_archievement_diff', $new_achievement_info_count. ' '.$achievement_info_count);
		
	}
	
	function request_login_test(){
		
		$result = $this->api_lib->request_login(USER_FACEBOOK_ID); // no facebook_access_token yet
		
		$this->unit->run($result, 'is_array', 'request_login()', print_r($result, TRUE));
		$status = $result['status'];
		
		$this->unit->run($status, 'ERROR', 'status', $status);
		$this->unit->run($result['message'], 'Not connected with facebook', "\$result['message']", $result['message']);

		//add sample facebook_access_token
		$this->load->model('user_model');
		$this->user_model->update_user(USER_ID, array('user_facebook_access_token' => 'SampleAccessToken'));

		$result = $this->api_lib->request_login(USER_FACEBOOK_ID); // no facebook_access_token yet
		
		$this->unit->run($result, 'is_array', 'request_login()', print_r($result, TRUE));
		$status = $result['status'];
		
		$this->unit->run($status, 'ERROR', 'status', $result);
		$this->unit->run($result['message'], 'User not found', "\$result['message']", $result['message']);		

		//TODO test 'OK' status with mocking facebook server
	}

	function request_facebook_tab_url_test(){
	
		$this->load->model('page_model');
		$page_profile = $this->page_model->get_page_profile_by_page_id(PAGE_ID);
		$tab_url = $page_profile['facebook_tab_url'];
			
		$result1 = $this->api_lib->request_facebook_tab_url(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, FACEBOOK_PAGE_ID);
		$result2 = $this->api_lib->request_facebook_tab_url(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, NULL, PAGE_ID);
		
		$this->unit->run($result1, 'is_array', 'request_facebook_tab_url(facebook_page_id)', print_r($result1, TRUE));
		if($result1['status']=='OK')
			$this->unit->run($result1['status'], 'OK', 'status', $result1['status']);
		else
			$this->unit->run($result1['status'], 'ERROR', 'status', $result1['status']);
		
		if(issetor($result1['facebook_tab_url']))
			$this->unit->run($result1['facebook_tab_url'], $tab_url, 'facebook_tab_url', $result1['facebook_tab_url']);
		
		$this->unit->run($result2, 'is_array', 'request_facebook_tab_url(page_id)', print_r($result2, TRUE));
		if($result2['status']=='OK')
			$this->unit->run($result2['status'], 'OK', 'status', $result2['status']);
		else
			$this->unit->run($result2['status'], 'ERROR', 'status', $result2['status']);
		
		if(issetor($result2['facebook_tab_url']))
			$this->unit->run($result2['facebook_tab_url'], $tab_url, 'facebook_tab_url', $result2['facebook_tab_url']);
		
		
		
	}

	function request_create_invite_test(){
		$this->load->library('invite_component_lib');
		
		$result_public = $this->api_lib->request_create_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												NULL, CAMPAIGN_ID, 2, FACEBOOK_PAGE_ID);
		
		$this->unit->run($result_public, 'is_array', 'request_create_invite() - public', print_r($result_public, TRUE));
		$this->unit->run($result_public['status'], 'OK', 'status', $result_public['status']);
		$this->unit->run($result_public['invite_key'], 'is_string', 'invite_key', $result_public['invite_key']);
		
		$public_invite = $this->invite_component_lib->get_invite_by_invite_key($result_public['invite_key']);
		$this->unit->run($public_invite, 'is_array', 'public_invite', print_r($public_invite, TRUE));
		$this->public_invite_key = $result_public['invite_key'];
		
		$result_private = $this->api_lib->request_create_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												'123456,45678,56798', CAMPAIGN_ID, 1, FACEBOOK_PAGE_ID);
		
		
		$this->unit->run($result_private, 'is_array', 'request_create_invite() - private', print_r($result_private, TRUE));
		$this->unit->run($result_private['status'], 'OK', 'status', $result_private['status']);
		$this->unit->run($result_private['invite_key'], 'is_string', 'invite_key', $result_private['invite_key']);
		
		$private_invite = $this->invite_component_lib->get_invite_by_invite_key($result_private['invite_key']);
		$this->unit->run($private_invite, 'is_array', 'private_invite', print_r($private_invite, TRUE));
		$this->private_invite_key = $result_private['invite_key'];
		
		
		
	}

	function request_accept_invite_test(){
		$this->load->library('invite_component_lib');
		
		$random_invited_user_facebook_id1 = rand(100, getrandmax());
		$random_invited_user_facebook_id2 = rand(100, getrandmax());
		$random_invited_user_facebook_id3 = rand(100, getrandmax());
				
		
		$accept_public = $this->api_lib->request_accept_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, $this->public_invite_key, $random_invited_user_facebook_id1);
		
				
		$this->unit->run($accept_public, 'is_array', 'request_accept_invite() - public', print_r($accept_public, TRUE));
		$this->unit->run($accept_public['status'], 'OK', 'status', $accept_public['status']);
		
		$result_private = $this->api_lib->request_create_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, USER_FACEBOOK_ID,
												'123456,45678,56798,'.$random_invited_user_facebook_id3, CAMPAIGN_ID, 1, FACEBOOK_PAGE_ID);
		
		$accept_private1 = $this->api_lib->request_accept_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, $this->private_invite_key, $random_invited_user_facebook_id2);
		
				
		$this->unit->run($accept_private1, 'is_array', 'request_accept_invite() - private1', print_r($accept_private1, TRUE));
		$this->unit->run($accept_private1['status'], 'ERROR', 'status', $accept_private1['status']);
		
		$accept_private2 = $this->api_lib->request_accept_invite(APP_ID,APP_SECRET_KEY,$this->app_install_id,
												$this->app_install_secret_key, $this->private_invite_key, $random_invited_user_facebook_id3);
		
				
		$this->unit->run($accept_private2, 'is_array', 'request_accept_invite() - private2', print_r($accept_private2, TRUE));
		$this->unit->run($accept_private2['status'], 'OK', 'status', $accept_private2['status']);
	}

	function request_invite_list_test(){
		$this->load->library('invite_component_lib');
		
		$invite_list = $this->invite_component_lib->list_invite(
																	array(
																		'app_install_id' => (int) $this->app_install_id,
																		'campaign_id' => (int) CAMPAIGN_ID,
																		'facebook_page_id' => (string) FACEBOOK_PAGE_ID,
																		'user_facebook_id' => (string) USER_FACEBOOK_ID,
																	)
																);
		
		$result = $this->api_lib->request_invite_list(APP_ID, APP_SECRET_KEY, $this->app_install_id,
												$this->app_install_secret_key, CAMPAIGN_ID, FACEBOOK_PAGE_ID,
												USER_FACEBOOK_ID);
		
		
		$this->unit->run($result, 'is_array', 'request_invite_list()', print_r($result, TRUE));
		$this->unit->run($result['status'], 'OK', 'status', $result['status']);
		
		$this->unit->run($result['invite_list'], 'is_array', 'invite_list', $result['invite_list']);
		
		$invite_list_diff = count($result['invite_list']) - count($invite_list);
		$this->unit->run($invite_list_diff, 0, 'invite_list_diff', count($invite_list));
		
	}

	function request_current_campaign_test(){
			
		$this->load->library('campaign_lib');
		$current_campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($this->app_install_id);
		
		$result = $this->api_lib->request_current_campaign(APP_ID, APP_SECRET_KEY, $this->app_install_id,
												$this->app_install_secret_key);
		
		$identical_campaign = $current_campaign['campaign_id'] == $result['campaign_id'];
		
		$this->unit->run($result, 'is_array', 'request_invite_list()', print_r($result, TRUE));
		$this->unit->run($identical_campaign, 'is_true', 'identical_campaign', $result['campaign_id']);
		
	}

	function request_user_classes_test(){
		//Add page first
		$campaign_id = 1;
	    $app_id = 2;
	    $app_install_id = 3;
	    $page_id = PAGE_ID;
	    $info = array(
	      'app_id' => $app_id,
	      'app_install_id' => $app_install_id,
	      'campaign_id' => $campaign_id
	    );
	    
	    $app_component_page_data = array(
	      'page_id' => $page_id,
	      'classes' => array(
	        array('name' => 'Founding',
	              'invite_accepted' => 3),
	        array('name' => 'VIP',
	              'invite_accepted' => 10),
	        array('name' => 'Prime',
	              'invite_accepted' => 50)
	      )
	    );
		
	    $this->app_component_page->drop_collection();
	    $result = $this->app_component_lib->add_page($app_component_page_data);
		
	    $this->unit->run($result, TRUE,'Add app_component_page with full data', print_r($result, TRUE));
	    $this->unit->run($this->app_component_page->count_all(), 1, 'count all app_component_page');
	    
	    //test
	    $app_id = APP_ID;
	    $app_secret_key = APP_SECRET_KEY;
		$app_install_id = $this->app_install_id;
		$app_install_secret_key = $this->app_install_secret_key;
		$page_id = PAGE_ID;
		$facebook_page_id = FACEBOOK_PAGE_ID;

	    $result1 = $this->api_lib->request_user_classes($app_id, $app_secret_key, 
		$app_install_id, $app_install_secret_key, $page_id, NULL);
		$this->unit->run($result1['status'], 'OK', 'request_user_classes test', $result1['status']);
		
		$this->unit->run($result1['data'], 'is_array', 'request_user_classes test', print_r($result1['data'], TRUE));
		$this->unit->run(count($result1['data']) == 3, TRUE, 'request_user_classes test', count($result1['data']));

		//Strip achievement_id
		unset($result1['data'][0]['achievement_id']);
		unset($result1['data'][1]['achievement_id']);
		unset($result1['data'][2]['achievement_id']);
		
		$this->unit->run($result1['data'] == $app_component_page_data['classes'], TRUE, 'request_user_classes test', print_r($result1['data'], TRUE));

		// $result = $this->app_component_lib->add_page($app_component_page_data);
	 //    $this->unit->run($result, TRUE,'Add app_component_page with full data', print_r($result, TRUE));
	 //    $this->unit->run($this->app_component_page->count_all(), 2, 'count all app_component_page');
	    
		
		$result2 = $this->api_lib->request_user_classes($app_id, $app_secret_key, 
			$app_install_id, $app_install_secret_key, NULL, $facebook_page_id);
		$this->unit->run($result2['status'], 'OK', 'request_user_classes test', $result2['status']);
		$this->unit->run($result2['data'], 'is_array', 'request_user_classes test', print_r($result2['data'], TRUE));
		$this->unit->run(count($result2['data']) == 3, TRUE, 'request_user_classes test', count($result2['data']));

		//Strip achievement_id
		unset($result2['data'][0]['achievement_id']);
		unset($result2['data'][1]['achievement_id']);
		unset($result2['data'][2]['achievement_id']);
		$this->unit->run($result2['data'] == $app_component_page_data['classes'], TRUE, 'request_user_classes test', print_r($result2['data'], TRUE));

	}

	function request_add_campaign_test(){
		$app_id = APP_ID;
		$app_secret_key = APP_SECRET_KEY;
		$app_install_id = $this->app_install_id;
		$app_install_secret_key = $this->app_install_secret_key;
		$page_id = PAGE_ID;
		$facebook_page_id = FACEBOOK_PAGE_ID;
		$campaign_start_timestamp = date('Y-m-d H:i:s', strtotime('+11 years'));
		$campaign_end_timestamp = date('Y-m-d H:i:s', strtotime('+12 years'));
		$result = $this->api_lib->request_add_campaign($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, $page_id, NULL,
			$campaign_start_timestamp, $campaign_end_timestamp);
		$this->unit->run($result['status'], 'OK', "\$result['status']", $result['status']);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['campaign_id'], 'is_int', "\$result['data']['campaign_id']",$result['data']['campaign_id']);
		$campaign_id_1 = $campaign_id = $result['data']['campaign_id'];

		$this->load->model('campaign_model');
		$result = $this->campaign_model->get_campaign_profile_by_campaign_id($campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['campaign_id'], $campaign_id, "\$result['campaign_id']", $result['campaign_id']);

		$this->load->model('app_component_model');
		$result = $this->app_component_model->get_by_campaign_id($campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['campaign_id'], $campaign_id, "\$result['campaign_id']", $result['campaign_id']);

		$campaign_start_timestamp = date('Y-m-d H:i:s', strtotime('+13 years'));
		$campaign_end_timestamp = date('Y-m-d H:i:s', strtotime('+14 years'));
		$result = $this->api_lib->request_add_campaign($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, NULL, $facebook_page_id,
			$campaign_start_timestamp, $campaign_end_timestamp);
		$this->unit->run($result['status'], 'OK', "\$result['status']", $result['status']);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['campaign_id'], 'is_int', "\$result['data']['campaign_id']",$result['data']['campaign_id']);
		$campaign_id_2 = $campaign_id = $result['data']['campaign_id'];

		$this->load->model('campaign_model');
		$result = $this->campaign_model->get_campaign_profile_by_campaign_id($campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['campaign_id'], $campaign_id, "\$result['campaign_id']", $result['campaign_id']);

		$this->load->model('app_component_model');
		$result = $this->app_component_model->get_by_campaign_id($campaign_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['campaign_id'], $campaign_id, "\$result['campaign_id']", $result['campaign_id']);

		$campaign_start_timestamp = date('Y-m-d H:i:s', strtotime('+11 years'));
		$campaign_end_timestamp = date('Y-m-d H:i:s', strtotime('+13 years'));
		$result = $this->api_lib->request_add_campaign($app_id, $app_secret_key, $app_install_id, $app_install_secret_key, NULL, $facebook_page_id,
			$campaign_start_timestamp, $campaign_end_timestamp);
		$this->unit->run($result['status'], 'ERROR', "\$result['status']", $result['status']);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run(isset($result['data']['campaign_id']), FALSE, "isset(\$result['data']['campaign_id'])",isset($result['data']['campaign_id']));
		$this->unit->run($result['error'], 'conflicted_campaigns', "\$result['error']", $result['error']);
		$this->unit->run(count($result['conflicted_campaigns']), 2, "count(\$result['conflicted_campaigns'])", count($result['conflicted_campaigns']));
		$this->unit->run(in_array($result['conflicted_campaigns'][0]['campaign_id'], array($campaign_id_1,$campaign_id_2)), TRUE, 
			"in_array(\$result['conflicted_campaigns'][0]['campaign_id'], array(\$campaign_id_1,\$campaign_id_2)", 
			in_array($result['conflicted_campaigns'][0]['campaign_id'], array($campaign_id_1,$campaign_id_2)));
	}
}

/* End of file api_lib_test.php */
/* Location: ./application/controllers/test/api_lib_test.php */
