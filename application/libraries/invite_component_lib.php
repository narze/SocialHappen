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
				
		$redirect_url = $app_install['facebook_tab_url'];
		//$redirect_url = $this->generate_redirect_url($facebook_tab_url, $invite_key);
		$check_args = //($campaign['app_install_id'] == $app_install_id) &&
						($page['page_id'] == $app_install['page_id']);
		
		// if($check_args){
		
			$target_facebook_id_list = $this->_extract_target_id($target_facebook_id);
			
			$exist_invite = $this->CI->invite_model->get_invite_by_criteria(
															array(
																'campaign_id' => (int) $campaign_id,
																'app_install_id' => (int) $app_install_id,
																'facebook_page_id' =>(string) $facebook_page_id,
																'user_facebook_id' => (string)$user_facebook_id,
																'invite_type' => $invite_type,
															)														
														);
		
			$upsert_flag = FALSE;
			if(issetor($exist_invite)){
				$invite_key = $exist_invite['invite_key'];
				if($invite_type==1){
					$new_target_facebook_id = array_diff($target_facebook_id_list,$exist_invite['target_facebook_id_list']);
					$target_facebook_id_list = array_merge($exist_invite['target_facebook_id_list'], $new_target_facebook_id);
				}
				$upsert_flag = TRUE;
			}
			
			
			if($this->CI->invite_model->add_invite($campaign_id, $app_install_id, $facebook_page_id
								, $invite_type, $user_facebook_id, $target_facebook_id_list
								, $invite_key, $redirect_url, $upsert_flag)){
				return $invite_key;
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
		if($invite_key==NULL)
			return FALSE;
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
			return FALSE;
		} else if($invite['invite_type'] == 2 || ($invite['invite_type'] == 1 && in_array($user_facebook_id, $invite['target_facebook_id_list']))){ // if key is public invite OR private and user is in the invitee list
			$campaign_id = $invite['campaign_id']; //TODO : Check if this is current campaign_id
			$this->CI->load->model('invite_pending_model','invite_pending');
			$pending_invite_key = $this->CI->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id);
			if($pending_invite_key === $invite_key){// if already entered this key before
				return TRUE;
			} else if($pending_invite_key){
				// echo 'You have already received another invite key';
				return FALSE;
			} else if($add_result = $this->CI->invite_pending->add($user_facebook_id, $campaign_id, $invite_key)){
				return TRUE;
			} else {
				// echo 'exception, please try again';
				return FALSE;
			}
		} else {
			// echo 'You are not invited by this key';
			return FALSE;
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
		$this->CI->load->model('invite_pending_model','invite_pending');
		if(!$this->CI->invite_pending->remove_by_user_facebook_id_and_campaign_id($user_facebook_id, $invite['campaign_id'])){
			log_message('error', 'cannot remove pending invite');
			return FALSE;
		}
		if(!$increment_result = $this->CI->invite_model->increment_invite_count_by_invite_key($invite_key)){
			log_message('error', 'cannot increment invite_count');
			return FALSE;
		}
		return TRUE;
		
	}
}
/* End of file invite_component_lib.php */
/* Location: ./application/controllers/libraries/invite_component_lib.php */