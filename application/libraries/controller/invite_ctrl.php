<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Invite_component_lib', NULL, 'invite');
    }

	function main($input = array()){
		$app_install_id = $input['app_install_id'];
		$page_id = $input['page_id'];
		$facebook_page_id = $input['facebook_page_id'];
		$error_status = array(
			'success' => FALSE
		);
		if($app_install_id){
			$this->CI->load->library('campaign_lib');
			$campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			$campaign_id = FALSE;

			if(issetor($campaign['in_campaign'])){
				$campaign_id = $campaign['campaign_id'];
				$this->CI->load->model('app_component_model','app_component');
				$invite = $this->CI->app_component->get_invite_by_campaign_id($campaign_id);
				if($invite && isset($invite['message']['text'])){
					$invite_message = $invite['message']['text'];
					return array(
						'success' => TRUE,
						'app_install_id' => $app_install_id,
						'invite_message' => $invite_message,
						'campaign_id' => $campaign_id,
						'facebook_page_id' => $facebook_page_id,
						'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
						'facebook_channel_url' => $this->CI->facebook->channel_url
					);
				} else {
					$error_status['error'] = 'cannot invite : no campaign invite message';
				}
			} else {
				$error_status['error'] = 'cannot invite : not in campaign';
			}
		} else {
			$error_status['error'] = 'cannot invite : not in app';
		}
		return $error_status;
	}

	function create_invite($input = array()){
		$app_install_id = $input['app_install_id'];
		$campaign_id = $input['campaign_id'];
		$facebook_page_id = $input['facebook_page_id'];
		$invite_type = $input['invite_type'];
		$target_facebook_id = $input['target_facebook_id'];
		$invite_message = $input['invite_message'];

		$error_status = array(
			'success' => FALSE
		);

		if($app_install_id){
			$user = $this->CI->socialhappen->get_user();
			$user_facebook_id = $user['user_facebook_id'];
			$this->CI->load->library('campaign_lib');
			$campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			if(issetor($campaign['in_campaign'])){
				if($invite_key = $this->CI->invite->add_invite($campaign_id,$app_install_id,$facebook_page_id,$invite_type,$user_facebook_id,$target_facebook_id)){
					$this->CI->load->helper('form');
					return array(
						'success' => TRUE,
						'invite_link' => base_url().'invite/accept?invite_key='.$invite_key,
						'invite_message' => $invite_message,
						'public_invite' => $invite_type === 1 ? FALSE : TRUE,
						'app_install_id' => $app_install_id
					);
				} else {
					$error_status['error'] = 'failed creating invite';
				}
			} else {
				$error_status['error'] = 'not in campaign';
			}
		} else {
			$error_status['error'] = 'not in app';
		}
		return $error_status;
	}
	
	function accept($input = array()){
		$invite_key = $input['invite_key'];
		$facebook_user_id = $input['facebook_user_id'];
		$error_status = array(
			'success' => FALSE
		);

		$reserve_invite = $this->CI->invite->reserve_invite($invite_key, $facebook_user_id);
		
		if(!isset($reserve_invite['error'])){
			$invite = $this->CI->invite->get_invite_by_invite_key($invite_key);
			$this->CI->load->model('installed_apps_model');
			if(($page = $this->CI->installed_apps_model->get_app_profile_by_app_install_id($invite['app_install_id'])) && issetor($page['facebook_tab_url'])){
				return array(
					'success' => TRUE,
					'facebook_tab_url' => $page['facebook_tab_url']
				);
			} else {
				$error_status['error'] = 'No facebook url to redirect';
			}
		} else {
			$error_status['error'] = 'This invite key is invalid : '.$reserve_invite['error'];
		}
		return $error_status;
	}
}  

/* End of file invite_ctrl.php */
/* Location: ./application/libraries/controller/invite_ctrl.php */