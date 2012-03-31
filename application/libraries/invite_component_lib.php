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
		$result = array('success' => FALSE);
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
			
		// $check_args = //($campaign['app_install_id'] == $app_install_id) &&
						// ($page['page_id'] == $app_install['page_id']);
		
		// if($check_args){
		$this->CI->load->model('user_model');
		$user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($user_facebook_id);
		$invite_limit_criteria = compact('user_id', 'app_install_id', 'campaign_id', 'user_facebook_id');
		if(!$cooled_down_timestamp = $this->_check_invite_limit($invite_limit_criteria)){
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
		
			$add_invite_audit = FALSE;
			if($invite_exists){
				$invite_key = $invite_exists['invite_key'];
				if($invite_type==2){
					$this->_increment_invite_limit($invite_limit_criteria);
					$result['data'] = array('invite_key' => $invite_key); //no update
					$result['success'] = TRUE;
				} else if($invite_type==1 && $this->CI->invite_model->add_into_target_facebook_id_list($invite_key, $target_facebook_id_list)){
					$this->_increment_invite_limit($invite_limit_criteria);
					$result['data'] = array('invite_key' => $invite_key);
					$result['success'] = TRUE;
					$add_invite_audit = TRUE;
				} else {
				 	$result['error'] = 'Invalid invite type';
				}
			} else {
				if($this->CI->invite_model->add_invite($campaign_id, $app_install_id, $facebook_page_id
									, $invite_type, $user_facebook_id, $target_facebook_id_list
									, $invite_key)){
					$this->_increment_invite_limit($invite_limit_criteria);
					$result['data'] = array('invite_key' => $invite_key);
					$result['success'] = TRUE;
					$add_invite_audit = TRUE;
				} else {
					$result['error'] = 'Cannot add invite';
				}
			}

			if($add_invite_audit){
				$this->CI->load->library('audit_lib');
				$this->CI->audit_lib->audit_add(array(
					'app_id' => $app_install['app_id'],
					'app_install_id' => $app_install_id,
					'campaign_id' => $campaign_id,
					'action_id' => $this->CI->socialhappen->get_k('audit_action', 'User Invite'),
					'user_id' => $user_id,
					'page' => $page['page_id']
				));
			}
		} else {
			$result['error'] = 'Invite limited';
			$result['timestamp'] = $cooled_down_timestamp;
		}
		// }
		return $result;
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
			if($user_id = $this->CI->user->get_user_id_by_user_facebook_id($user_facebook_id)){
				$this->CI->load->model('user_campaigns_model', 'user_campaign');
				if($this->CI->user_campaign->is_user_in_campaign($user_id, $campaign_id)){
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
		if(!$this->_give_campaign_score_to_inviter($invite, $user_facebook_id)){
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
		$campaign_id = $master_invite['campaign_id'];

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
		return  $this->_give_page_score_to_all_inviters($facebook_page_id, $inviters, $campaign_id, $user_facebook_id);
	}

	function _give_page_score_to_all_inviters($facebook_page_id = NULL, $inviters = NULL, $campaign_id = NULL, $invitee_facebook_id = NULL){
		if(!allnotempty(func_get_args()) || !allnotempty($inviters)){
			return FALSE;
		}
		$this->CI->load->model('page_model');
		$this->CI->load->model('user_model');
		if(!$page_id = $this->CI->page_model->get_page_id_by_facebook_page_id($facebook_page_id)){
			log_message('error', '_give_page_score_to_all_inviters : no page');
			return FALSE;
		}

		$this->CI->load->library('audit_lib');
		$this->CI->load->library('achievement_lib');
		$invitee_user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($invitee_facebook_id);
		$user_id = $invitee_user_id;
		$inviters = array_unique($inviters); //Score should be given once per facebook_user_id
		foreach($inviters as $inviter_facebook_id){
			if(!$inviter_user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($inviter_facebook_id)){
				log_message('error', '_give_page_score_to_all_inviters : no inviter id');
				return FALSE;
			}
			$action_id = $this->CI->socialhappen->get_k('audit_action','Invitee Accept Page Invite');
			$app_id = 0;
			$subject = $inviter_user_id;
			$audit_info = compact('app_id','subject','action_id','user_id','campaign_id','page_id');

			if(!$this->CI->audit_lib->audit_add($audit_info)){
				// log_message('error', 'no audit ');
				return FALSE;
			}

			$achievement_info = array(
				'action_id' => $action_id, 
				'page_id' => $page_id,
				'app_install_id' => 0,
				'campaign_id' => $campaign_id
			);
			if(!$this->CI->achievement_lib->increment_achievement_stat(0, 0, $inviter_user_id, $achievement_info, 1)){
				// log_message('error', 'no inc');
				return FALSE;
			}
		}
		return TRUE;
	}

	function _give_campaign_score_to_inviter($invite = NULL, $user_facebook_id = NULL){
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
		$app_id = 0;
		$subject = $inviter_user_id;
		$user_id = $this->CI->user_model->get_user_id_by_user_facebook_id($user_facebook_id);
		$audit_info = compact('app_id','subject','action_id','user_id','campaign_id');

		if(!$this->CI->audit_lib->audit_add($audit_info)){
			// log_message('error', 'no audit ');
			return FALSE;
		}

		$achievement_info = array(
			'action_id' => $action_id, 
			'campaign_id' => $campaign_id,
			'app_install_id' => 0
		);
		if(!$this->CI->achievement_lib->increment_achievement_stat(0, 0, $inviter_user_id, $achievement_info, 1)){
			// log_message('error', 'no inc');
			return FALSE;
		}
		return TRUE;
	}

	function _increment_invite_limit($input = array()){
		$user_id = issetor($input['user_id']);
		$app_install_id = issetor($input['app_install_id']);
		$campaign_id = issetor($input['campaign_id']);

		$action_id = $this->CI->socialhappen->get_k('audit_action', 'User Invite');
		$this->CI->load->library('audit_stat_limit_lib');
		return $this->CI->audit_stat_limit_lib->add($user_id, $action_id, $app_install_id, $campaign_id);
	}

	function _check_invite_limit($input = array()){
		$user_id = issetor($input['user_id']);
		$user_facebook_id = issetor($input['user_facebook_id']);
		$app_install_id = issetor($input['app_install_id']);
		$campaign_id = issetor($input['campaign_id']);
		$action_id = $this->CI->socialhappen->get_k('audit_action', 'User Invite');

		$this->CI->load->model('app_component_model');
		if(!$invite_component = $this->CI->app_component_model->get_invite_by_campaign_id($campaign_id)){
			return FALSE;
		} 
		if(!isset($invite_component['criteria']['maximum']) || !isset($invite_component['criteria']['cooldown'])){
			return FALSE;
		}
		$invite_cooldown = $invite_component['criteria']['cooldown'] * 60;
		$invite_limit = $invite_component['criteria']['maximum'];

		$this->CI->load->library('audit_stat_limit_lib');
		$invite_count = $this->CI->audit_stat_limit_lib->count($user_id, $action_id, $app_install_id, $campaign_id, $invite_cooldown);

		if($invite_count >= $invite_limit){
			$first_invite_timestamp = $this->CI->audit_stat_limit_lib->find_nth_timestamp($user_id, $action_id, $app_install_id, $campaign_id, $invite_limit);
			return $first_invite_timestamp + $invite_cooldown;
		} else {
			return FALSE;
		}
	}
}
/* End of file invite_component_lib.php */
/* Location: ./application/controllers/libraries/invite_component_lib.php */