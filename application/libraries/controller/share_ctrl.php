<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Share_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($app_install_id = NULL, $share_link = NULL){
    	$result = array('success'=>FALSE);
    	if(!$user = $this->CI->socialhappen->get_user()){
    		$result['error'] = 'cannot share, please login';
    	} else if(!$app_install_id){
    		$result['error'] = 'cannot share, not in app';
    	} else if(!$share_link){
    		$result['error'] = 'cannot share, no share link';
    	} else {
			$this->CI->load->library('campaign_lib');
			$campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			if(!issetor($campaign['in_campaign'])){
				$result['error'] = 'cannot share, no campaign';
			} else {
				$campaign_id = $campaign['campaign_id'];
				$this->CI->load->model('app_component_model','app_component');
				$sharebutton = $this->CI->app_component->get_sharebutton_by_campaign_id($campaign_id);
				$this->CI->load->library('sharebutton_lib');
				if(!$sharebutton || !isset($sharebutton['message']['text'])){
					$result['error'] = 'cannot share, no share message';
				} else {
					$share_message = $sharebutton['message']['text'];
					$result['data'] = array(
						'user' => $user,
						'twitter_checked' => !empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret']),
						'facebook_checked' => TRUE,
						'share_message' => $share_message,
						'share_link' => $share_link,
						'app_install_id' => $app_install_id
					);
					$result['success'] = TRUE;
				}
			}
		}
		return $result;
    }

    function share_submit($user_id = NULL, $app_install_id = NULL, $app_id = NULL){
    	$share_action = $this->CI->socialhappen->get_k('audit_action','User Share');
		$this->CI->load->library('audit_lib');
		$audit_additional_data = array(
			'user_id'=> $user_id,
			'app_install_id' => $app_install_id
		);
		$result = array('success' => FALSE);
		$audit_result = $this->CI->audit_lib->add_audit(
			$app_id,
			$user_id,
			$share_action,
			NULL, 
			NULL,
			$audit_additional_data
		);
		if($audit_result){
			$result['audit_success'] = TRUE;
		} else {
			$result['error'] = 'add audit failed';
		}
	
		$this->CI->load->library('achievement_lib');
		$achievement_info = array('action_id'=> $share_action,'app_install_id'=>$app_install_id);

		$inc_result = $this->CI->achievement_lib->increment_achievement_stat($app_id, $user_id, $achievement_info, 1);
		if($inc_result){
			$result['achievement_stat_success'] = TRUE;
		} else {	
			$result['error'] = 'increment stat failed';
		}

		if($result['audit_success'] && $result['achievement_stat_success']){
			$result['success'] = TRUE;
		}
		return $result;
    }
	
}

/* End of file share_ctrl.php */
/* Location: ./application/libraries/controller/share_ctrl.php */