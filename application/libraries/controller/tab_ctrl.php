<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tab_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($user_facebook_id = NULL, $page_id = NULL, $facebook_page_id = NULL, $token = NULL){
    	$result = array('success' => FALSE);

		$this->CI->load->model('user_model','users');
		if ($user_facebook_id){
			$user = $this->CI->users->get_user_profile_by_user_facebook_id($user_facebook_id);
		} else {
			$result['error'] = 'No user_facebook_id specified';
			return $result;
		}
		$user_id = $user['user_id'];

		$this->CI->load->model('page_model','pages');
		if($page_id){
			$page = $this->CI->pages->get_page_profile_by_page_id($page_id);
		} else if($facebook_page_id){
			$page = $this->CI->pages->get_page_profile_by_facebook_page_id($facebook_page_id);
		} else {
			 $result['error'] = 'No page_id specified';
			 return $result;
		}
		$page_id = $page['page_id'];

		$this->CI->load->model('company_model','companies');
		$company = $this->CI->companies->get_company_profile_by_page_id($page_id);
		$this->CI->load->model('user_companies_model','user_companies');
		$is_admin = $this->CI->user_companies->is_company_admin($user_id, $company['company_id']);
		
		$this->CI->config->load('pagination', TRUE);
		$per_page = $this->CI->config->item('per_page','pagination');

		$result['data'] = array(
			'header' => $this->CI->load->view('tab/header', 
				array(
					'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
					'facebook_channel_url' => $this->CI->facebook->channel_url,
					'vars' => array(
									'page_id' => $page_id,
									'user_id' => $user_id,
									'is_guest' => $user ? FALSE : TRUE,
									'token' => urlencode($token),
									'per_page' => $per_page,
									'notifications_per_page' => 5,
									'view' => isset($this->CI->app_data['view']) ? $this->CI->app_data['view'] : '',
									'return_url' => isset($this->CI->app_data['return_url']) ? $this->CI->app_data['return_url'] : ''
					),
					'script' => array(
						'common/functions',
						'common/onload',
						'tab/bar',
						'tab/profile',
						'tab/main',
						'tab/account',
						'tab/dashboard',
						'common/jquery.pagination',
						'common/jquery.form',
						'common/jquery.countdown.min',
						'common/fancybox/jquery.fancybox-1.3.4.pack'
					),
					'style' => array(
						'common/facebook',
						'common/facebook-main',
						'common/jquery.countdown',
						'common/fancybox/jquery.fancybox-1.3.4',
						'../../assets/css/common/api_app_bar'
					)
				),
			TRUE),
			'bar' => 
			$this->CI->socialhappen->get_bar(
				array(
					'user_id' => $user_id,
					'user_facebook_id' => $user_facebook_id,
					'page_id' => $page_id
				)
			),
			'main' => $this->CI->load->view('tab/main',array(),
			TRUE),
			'footer' => $this->CI->load->view('tab/footer',array(),
			TRUE)
		);
		$result['success'] = TRUE;

    	return $result;
    }

    function signup_submit($first_name = NULL, $last_name = NULL, $email = NULL, $user_facebook_id = NULL, $timezone = NULL, $page_id = NULL, $app_install_id = NULL){

		$result = array('success'=>FALSE);
		$error = array();
		$this->CI->load->library('text_validate');
		$validate_array = array(
			'first_name' => array('label' => 'First name', 'rules' => 'required', 'input' => $first_name),
			'last_name' => array('label' => 'Last name', 'rules' => 'required', 'input' => $last_name),
			'email' => array('label' => 'Email', 'rules' => 'required|email', 'input' => $email, 'verify_message' => 'Please enter a valid email.')
		);
		$validation_result = $this->CI->text_validate->text_validate_array($validate_array);
		
		if(!$validation_result){
			$error['status'] = 'error';
			$error['error'] = 'verify';
			$validate_errors = array();
			foreach($validate_array as $key => $value){
				if(!$value['passed']){
					$validate_errors[$key] = $value['error_message'];
				}
			}
			$error['error_messages'] = $validate_errors;
			$result['error'] = $error;
		} else {
			// if (!$user_image = $this->CI->socialhappen->upload_image('user_image')){
				$user_image = $this->CI->facebook->get_profile_picture($user_facebook_id);
			// }

			$user_timezone = $timezone ? $timezone : 'UTC';
			$this->CI->load->library('timezone_lib');
			if(!$minute_offset = $this->CI->timezone_lib->get_minute_offset_from_timezone($user_timezone)){
				$minute_offset = 0;
			}

			$this->CI->load->model('user_model','users');
			$post_data = array(
				'user_first_name' => $first_name,
				'user_last_name' => $last_name,
				'user_email' => $email,
				'user_image' => $user_image,
				'user_facebook_id' => $user_facebook_id,
		       	'user_timezone_offset' => $minute_offset
			);
			
			if(!$user_id = $this->CI->users->add_user($post_data)){
				//TODO : erase uploaded image
				log_message('error','add user failed, $post_data : '. print_r($post_data, TRUE));
				log_message('error','$user_id : '. print_r($user_id, TRUE));
				echo 'Error occured';
				$error['status'] = 'error';
				$error['error'] = 'add_user';
				$result['error'] = $error;
			} else {
				$this->CI->socialhappen->login();
				$result['success'] = TRUE;
				
				$this->CI->load->library('audit_lib');
				$action_id = $this->CI->socialhappen->get_k('audit_action','User Register SocialHappen');
				$this->CI->audit_lib->add_audit(
					0,
					$user_id,
					$action_id,
					'', 
					'',
					array(
						'app_install_id' => 0,
						'user_id' => $user_id,
						'page_id' => $page_id
					)
				);
				
				$this->CI->load->library('achievement_lib');
				$info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id'=>$page_id);
				$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
			}
			
		}
		return $result;
	
    }

    function signup_page_submit($user_id = NULL, $user_facebook_id = NULL, $app_install_id = NULL, $page_id = NULL, $user_data = NULL){
    	$result = array('success' => FALSE);
    	$data = array();
    	$post_data = array(
	    	'user_id' => $user_id,
	    	'page_id' => $page_id,
	    	'user_data' => $user_data
	    );
		if(!isset($app_install_id)){ // mode = page
			$this->CI->load->model('page_model','page');
			$page = $this->CI->page->get_page_profile_by_page_id($page_id);
			if(!$facebook_tab_url = $page['facebook_tab_url']){
				$facebook_tab_url = $this->CI->facebook_page($page_id, TRUE, TRUE);
			}
		} else { // mode = app
			$this->CI->load->model('installed_apps_model','installed_app');
			$app = $this->CI->installed_app->get_app_profile_by_app_install_id($app_install_id);
			if(!$facebook_tab_url = $app['facebook_tab_url']){
				$facebook_tab_url = $this->CI->facebook_app($page_id, TRUE, TRUE);
			}
		}
		
		$this->CI->load->model('page_user_data_model','page_users');
		if(!$this->CI->page_users->add_page_user($post_data)){
			log_message('error','add page user failed');
			log_message('error','$post_data : '. print_r($post_data, TRUE));
			$data['status'] = 'error';
			$data['error'] = 'add_page_user';
			$result['error'] = $data;
		} else {
			$result['success'] = TRUE;
			$data['status'] = 'ok';
			$data['redirect_url'] = $facebook_tab_url;
			$result['data'] = $data;
			
			$action_id = $this->CI->socialhappen->get_k('audit_action','User Register Page');
			$this->CI->load->library('audit_lib');
			$audit_info = array('page_id' => $page_id);
			if(isset($app_install_id)){
				$audit_info['app_install_id'] = $app_install_id;
			}
			$this->CI->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				$audit_info
			);
			
			$this->CI->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>$app_install_id, 'page_id'=>$page_id);
			$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);

			//Begin : check pending invite
				if(isset($app_install_id)){ // mode = app
					$this->CI->load->library('campaign_lib');
					$current_campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
					if($current_campaign['in_campaign'] == TRUE){
						$campaign_id = $current_campaign['campaign_id'];
						$this->CI->load->model('invite_pending_model', 'invite_pending');
						if($invite_key = $this->CI->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id)){
							$this->CI->load->library('invite_component_lib');
							if($accept_and_give_score_result = $this->CI->invite_component_lib->accept_all_invite_page_level($invite_key, $user_facebook_id)){
								//success
							} else {
								log_message('error','accept_invite error '.print_r($accept_and_give_score_result,TRUE));
							}
						} else {
							log_message('debug', 'not found invite_key');
						}
					} else {
						log_message('debug', 'campaign ended');
					}
				} else { //mode = page
					$this->CI->load->model('invite_pending_model', 'invite_pending');
					if($pending_invites = $this->CI->invite_pending->get_by_user_facebook_id_and_facebook_page_id($user_facebook_id, $page['facebook_page_id'])){
						$invite_key = $pending_invites[0]['invite_key'];
						$this->CI->load->library('invite_component_lib');
						if($accept_and_give_score_result = $this->CI->invite_component_lib->accept_all_invite_page_level($invite_key, $user_facebook_id)){
							//success;
						} else {
							log_message('debug','accept_invite debug '.print_r($accept_and_give_score_result,TRUE));
						}
					} else {
						log_message('debug', 'not found invite_keys');
					}
				}
			//End
		}
		return $result;
    }

    function signup_campaign_submit($user_id = NULL, $user_facebook_id = NULL, $campaign_id = NULL){
    	$result = array('success' => FALSE);
    	$this->CI->load->model('user_campaigns_model','user_campaigns');
		if($this->CI->user_campaigns->add_user_campaign(array(
			'user_id' => $user_id,
			'campaign_id' => $campaign_id
		))){
			$this->CI->load->model('invite_pending_model', 'invite_pending');
			if($invite_key = $this->CI->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id)){
				$this->CI->load->library('invite_component_lib');
				$result = $this->CI->invite_component_lib->accept_invite_campaign_level($invite_key, $user_facebook_id);
				if(isset($result['error'])){
					$data = array(
						'status' => 'error',
						'error' => 'accept_invite_error',
						'message' => 'cannot accept invite'
					);
					log_message('error','accept_invite error '.print_r($result,TRUE));
					$result['error'] = $data;
				} else {
					$data = array(
						'status' => 'ok'
					);
					$result['data'] = $data;
					$result['success'] = TRUE;
				}
			} else {
				$data = array(
					'status' => 'ok'
				);
				$result['data'] = $data;
				$result['success'] = TRUE;
			}
		} else {
			$data = array(
				'status' => 'error',
				'error' => 'add_user_campaign error',
				'message' => 'cannot add user campaign'
			);
			log_message('error', 'cannot add user campaign');
			$result['error'] = $data;
		}
		return $result;
    }
}

/* End of file tab_ctrl.php */
/* Location: ./application/libraries/controller/tab_ctrl.php */