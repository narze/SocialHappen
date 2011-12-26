<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Invite class library
 * @author Wachiraph C.
 */
class Invite_component_lib {
	private $CI;

	public function __construct($params = array()){
		$this->CI =& get_instance();
		$this->CI->load->model('invite_model');
    }
	
	/**
	 * Add new invite
	 *
	 */
    public function add_invite($campaign_id = NULL, $app_install_id = NULL, $facebook_page_id = NULL
							, $invite_type = NULL, $user_facebook_id = NULL, $target_facebook_id = NULL){
							
		$this->CI->invite_model->create_index();
		
		$invite_key = $this->_generate_invite_key();
		if($invite_type == 2)
			$target_facebook_id = NULL;
			
		//relation check
		$this->CI->load->model('campaign_model');
		$this->CI->load->model('page_model');
		$this->CI->load->model('installed_apps_model');
		
		$campaign = $this->CI->campaign_model->get_campaign_profile_by_campaign_id($campaign_id);
		$page = $this->CI->page_model->get_page_profile_by_facebook_page_id($facebook_page_id);
		$app_install = $this->CI->installed_apps_model->get_app_profile_by_app_install_id($app_install_id);
			
		$check_args = //($campaign['app_install_id'] == $app_install_id) &&
						($page['page_id'] == $app_install['page_id']);
		
		// if($check_args){
		
			$target_facebook_id_list = $this->_extract_target_id($target_facebook_id);
			
			$invite_exists = $this->CI->invite_model->get_invite_by_criteria(
															array(
																'campaign_id' => (int) $campaign_id,
																'app_install_id' => (int) $app_install_id,
																'facebook_page_id' =>(string) $facebook_page_id,
																'user_facebook_id' => (string)$user_facebook_id,
																'invite_type' => $invite_type,
															)														
															);
		
			if($invite_exists){
				$invite_key = $invite_exists['invite_key'];
				if($invite_type==2){
					return $invite_key; //no update
				} else if($invite_type==1 && $this->CI->invite_model->add_into_target_facebook_id_list($invite_key, $target_facebook_id_list)){
					return $invite_key;
				} else {
				 	return FALSE;
				}
			} else {
				if($this->CI->invite_model->add_invite($campaign_id, $app_install_id, $facebook_page_id
									, $invite_type, $user_facebook_id, $target_facebook_id_list
									, $invite_key)){
					
					return $invite_key;
				} else {
					return FALSE;
				}
			}
		// }
		return FALSE;
    }
	
	/**
	 * 
	 * DEPRECATED : Separated into accept_invite_page_level and accept_invite_campaign_level
	 * Accept invite and update invite status
	 *
	 */
	public function accept_invite($invite_key, $target_facebook_id = NULL){
		
		$exist_check = $this->get_invite_by_invite_key($invite_key);
		
		if($exist_check){
			
			$data = $exist_check;
			$data['invite_count'] = $data['invite_count'] + 1;
			$data['timestamp'] = time();
			
			if($data['invite_type']==1){
			//private
			
				if(!isset($data['accepted_target_facebook_id_list']))
						$data['accepted_target_facebook_id_list'] = array();
				
				if(!in_array($target_facebook_id, $data['target_facebook_id_list'] ) ){
					return array('error' => 'invalid target_facebook_id');
				}else if (in_array($target_facebook_id, $data['accepted_target_facebook_id_list'])){
					return array('error' => 'invite is accepted before');
				}else{
					array_push($data['accepted_target_facebook_id_list'], $target_facebook_id);
				}
				
				
			}else{
			//public
			
				if(!isset($data['public_accepted_target_facebook_id']))
						$data['public_accepted_target_facebook_id'] = array();
						
				if(in_array($target_facebook_id, $data['public_accepted_target_facebook_id'])){
					return array('error' => 'you have accepted this invite before');
				}else{
					array_push($data['public_accepted_target_facebook_id'], $target_facebook_id);
				}
			}
			
			if($this->CI->invite_model->update_invite($invite_key, $data))
				return $data;
			else
				return array('error' => 'accept invite failed');
			
		
		}else
			return array('error' => 'invite_key is incorrect');
		
	}
	
	/**
	 * List invites of a user
	 *
	 */
    public function list_invite($criteria = array()){
		$criteria_arr = array();
		
		foreach($criteria as $criteria_key=>$criteria_item){
			if($criteria_item != "")
				$criteria_arr[$criteria_key] = $criteria_item;
		}
		
		if(sizeof($criteria_arr) > 0){
			return $this->CI->invite_model->list_invites($criteria_arr);
		} else {
			return FALSE;
		}
    }
	
	/**
	 * Get an invite's details
	 *
	 */
    public function get_invite_by_invite_key($invite_key = NULL){
		return $this->CI->invite_model->get_invite_by_criteria(array('invite_key' => $invite_key));
		
    }
	
	/**
	 * Post public invite to user's wall
	 *
	 */
    public function share_invite(){
		//call platform Sharing component
	
    }
	
	/**
	 * Tweet public invite to user's timeline
	 *
	 */
    public function tweet_invite(){
		//call platform Sharing component
	
    }
	
	/**
	 * Send invite to specific user(s)
	 *
	 */
    public function send_invite(){
		//call platform Sharing component
	
    }
	
	private function _extract_target_id($target_id = NULL){
		$target_id_list = array();
		
		$tmp = explode(',', $target_id);
		
		foreach($tmp as $target_id_unit)
			$target_id_list[] = trim($target_id_unit);
		
		return $target_id_list;
	}
	
	private function _generate_invite_key($length = NULL){
		if(!isset($length))
			$length = 20;
			
		$result = '';
		$validCharacters = 'abcdefghijklmnopqrstuxyvwz0987654321ABCDEFGHIJKLMNOPQRSTUXYVWZ';
		$validCharNumber = strlen($validCharacters);
		
		for ($i = 0; $i < $length; $i++) {
			$index = mt_rand(0, $validCharNumber-1);
			$result .= $validCharacters[$index];
		}
		
		return $result;
	}

	/**
	 * Reserve invite
	 * @param $invite_key
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function reserve_invite($invite_key = NULL, $user_facebook_id = NULL){
		if(!$invite_key || !$user_facebook_id){
			return FALSE;
		}
		
		if(!$invite = $this->get_invite_by_invite_key($invite_key)){
			// echo 'invite key invalid';
			//return FALSE;
			return array('error' => 'invite_key is incorrect');
			
		} else if($invite['invite_type'] == 2 || ($invite['invite_type'] == 1 && in_array($user_facebook_id, $invite['target_facebook_id_list']))){ // if key is public invite OR private and user is in the invitee list
			$campaign_id = $invite['campaign_id']; //TODO : Check if this is current campaign_id
			$facebook_page_id = $invite['facebook_page_id'];
			$this->CI->load->model('invite_pending_model','invite_pending');
			$pending_invite_key = $this->CI->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
			$this->CI->load->model('user_model', 'user');
			if($user = $this->CI->user->get_user_id_by_user_facebook_id($user_facebook_id)){
				$this->CI->load->model('user_campaigns_model', 'user_campaign');
				if($this->CI->user_campaign->is_user_in_campaign($user['user_id'], $campaign_id)){
					//already in campaign
					//return FALSE;
					return array('error' => 'You are already in campaign');
				}
			}
			if($pending_invite_key === $invite_key){// if already entered this key before
				return TRUE;
			} else if($pending_invite_key){
				// echo 'You have already received another invite key';
				//return FALSE;
				return array('error' => 'You have already received another invite key');
			} else if($add_result = $this->CI->invite_pending->add($user_facebook_id, $campaign_id, $facebook_page_id, $invite_key)){
				if($invite['invite_type'] == 2){
					return $this->CI->invite_model->add_into_target_facebook_id_list($invite_key, array($user_facebook_id));
				}
				return TRUE;
			} else {
				// echo 'exception, please try again';
				//return FALSE;
				return array('error' => 'Accept invite failed, please try again');
			}
		} else {
			// echo 'You are not invited by this key';
			//return FALSE;
			return array('error' => 'You are not invited by this key');
		}
	}

	/**
	 * DEPRECATED
	 * Generate redirect url
	 * @param $facebook_tab_url
	 * @param $invite_key
	 * @author Manassarn M.
	 */
	function generate_redirect_url($facebook_tab_url = NULL, $invite_key = NULL){
		if(!$facebook_tab_url || !$invite_key){
			return FALSE;
		}
		return $facebook_tab_url.'&app_data='.urlencode(json_encode(
			array(
				'sh_invite_key' => $invite_key
			)
		));
	}

	/**
	 * DEPRECATED
	 * Parse invite key from facebook's app_data
	 * @param string $app_data
	 * @author Manassarn M.
	 */
	function parse_invite_key_from_app_data($app_data_string = NULL){
		if(!$app_data_string){
			return FALSE;
		}
		$app_data = json_decode(urldecode($app_data_string));
		return issetor($app_data->sh_invite_key);
	}

	function _accept_invite_level($level = NULL, $invite_key = NULL, $user_facebook_id = NULL){
		if(allnotempty(func_get_args())){
			if($invite = $this->get_invite_by_invite_key($invite_key)){ //invite key validation
				$this->CI->load->model('invite_pending_model','invite_pending');
				$pending_invite_key = $this->CI->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $invite['campaign_id']);
				if(!$pending_invite_key || ($pending_invite_key !== $invite_key)){ //pending invite key validation
					return FALSE;
				}
				if($this->CI->invite_model->{'push_into_'.$level.'_accepted'}($invite_key, $user_facebook_id)){
					return TRUE;
				}

			}

			// log_message('error', $invite_key);
		}
		return FALSE;
	}

	function accept_invite_page_level($invite_key = NULL, $user_facebook_id = NULL){
		return $this->_accept_invite_level('page', $invite_key, $user_facebook_id);
	}

	function accept_invite_campaign_level($invite_key = NULL, $user_facebook_id = NULL){
		if(!$this->_accept_invite_level('campaign', $invite_key, $user_facebook_id)){
			return FALSE;
		}
		if(!$invite = $this->get_invite_by_invite_key($invite_key)){
			return FALSE;
		}
		if(!$this->_give_campaign_score_to_inviter($invite)){
			return FALSE;
		}
		$this->CI->load->model('invite_pending_model','invite_pending');
		if(!$this->CI->invite_pending->remove_by_user_facebook_id_and_campaign_id($user_facebook_id, $invite['campaign_id'])){
			// log_message('error', 'cannot remove pending invite');
			return FALSE;
		}
		if(!$increment_result = $this->CI->invite_model->increment_invite_count_by_invite_key($invite_key)){
			// log_message('error', 'cannot increment invite_count');
			return FALSE;
		}
		return TRUE;
		
	}

	function accept_all_invite_page_level($invite_key = NULL, $user_facebook_id = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		}
		if(!$this->_accept_invite_level('page', $invite_key, $user_facebook_id)){ //Check if already accepted by itself or by other invite with same page id
			return FALSE;
		}
		//Get facebook_page_id from master invite_key
		$master_invite = $this->get_invite_by_invite_key($invite_key);
		$facebook_page_id = $master_invite['facebook_page_id'];

		//Find all invite with same facebook_page_id that user_facebook_id is in target list
		if(!$invites = $this->CI->invite_model->get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id, $user_facebook_id)){
			return FALSE;
		}
		$invite_keys = $inviters = array();
		foreach($invites as $invite){
			$invite_keys[] = $invite['invite_key'];
			$inviters[] = $invite['user_facebook_id'];
		}

		if(!$push_all_result = $this->CI->invite_model->push_into_all_page_accepted($user_facebook_id, $invite_keys)){
			return FALSE;
		}
		return $give_page_score_result = $this->_give_page_score_to_all_inviters($facebook_page_id, $inviters);
	}

	function _give_page_score_to_all_inviters($facebook_page_id = NULL, $inviters = NULL){
		if(!allnotempty(func_get_args()) || !allnotempty($inviters)){
			return FALSE;
		}
		$this->CI->load->model('page_model');
		$this->CI->load->model('user_model');
		if(!$page_id = $this->CI->page_model->get_page_id_by_facebook_page_id($facebook_page_id)){
			log_message('error', '_give_page_score_to_all_inviters : no page');return FALSE;
		}

		$this->CI->load->library('audit_lib');
		$this->CI->load->library('achievement_lib');

		$inviters = array_unique($inviters); //Score should be given once per facebook_user_id
		foreach($inviters as $inviter_facebook_id){
			if(!$inviter_user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($inviter_facebook_id)){
				log_message('error', '_give_page_score_to_all_inviters : no inviter id');
				return FALSE;
			}
			$action_id = $this->CI->socialhappen->get_k('audit_action','Invitee Accept Page Invite');
			$audit_info = array(
				'page_id' => $page_id
			);
			if(!$this->CI->audit_lib->add_audit(
				0,
				$inviter_user_id,
				$action_id,
				'', 
				'',
				$audit_info
			)){
				// log_message('error', 'no audit ');
				return FALSE;
			}

			$achievement_info = array(
				'action_id' => $action_id, 
				'page_id' => $page_id,
				'app_install_id' => 0
			);
			if(!$this->CI->achievement_lib->increment_achievement_stat(0, $inviter_user_id, $achievement_info, 1)){
				// log_message('error', 'no inc');
				return FALSE;
			}
		}
		return TRUE;
	}

	function _give_campaign_score_to_inviter($invite = NULL){
		if(!is_array($invite) || (!$inviter_facebook_id = issetor($invite['user_facebook_id'])) || (!$campaign_id = issetor($invite['campaign_id']))){
			return FALSE;
		}
		$this->CI->load->model('app_component_model', 'app_component');
		if(!$campaign_invite = $this->CI->app_component->get_invite_by_campaign_id($campaign_id)){
			return FALSE; //cannot find invite for that campaign in app component
		}

		$this->CI->load->model('user_model');
		if(!$inviter_user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($inviter_facebook_id)){
			log_message('error', '_give_campaign_score_to_inviter : no inviter id');
			return FALSE;
		}
		$this->CI->load->library('audit_lib');
		$this->CI->load->library('achievement_lib');

		$action_id = $this->CI->socialhappen->get_k('audit_action','Invitee Accept Campaign Invite');
		$audit_info = array(
			'campaign_id' => $campaign_id
		);
		if(!$this->CI->audit_lib->add_audit(
			0,
			$inviter_user_id,
			$action_id,
			'', 
			'',
			$audit_info
		)){
			// log_message('error', 'no audit ');
			return FALSE;
		}

		$achievement_info = array(
			'action_id' => $action_id, 
			'campaign_id' => $campaign_id,
			'app_install_id' => 0
		);
		if(!$this->CI->achievement_lib->increment_achievement_stat(0, $inviter_user_id, $achievement_info, 1)){
			// log_message('error', 'no inc');
			return FALSE;
		}
		return TRUE;

	}
}
/* End of file invite_component_lib.php */
/* Location: ./application/controllers/libraries/invite_component_lib.php */