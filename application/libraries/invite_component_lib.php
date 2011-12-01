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
		if($invite_type == 1)
			$target_facebook_id = NULL;
			
		//relation check
		$this->CI->load->model('campaign_model');
		$this->CI->load->model('page_model');
		$this->CI->load->model('installed_apps_model');
		
		$campaign = $this->CI->campaign_model->get_campaign_profile_by_campaign_id($campaign_id);
		$page = $this->CI->page_model->get_page_profile_by_facebook_page_id($facebook_page_id);
		$app_install = $this->CI->installed_apps_model->get_app_profile_by_app_install_id($app_install_id);
	
		$check_args = ($campaign['app_install_id'] == $app_install_id) &&
						($page['page_id'] == $app_install['page_id']);
		
		if($check_args){
			if($this->CI->invite_model->add_invite($campaign_id, $app_install_id, $facebook_page_id
								, $invite_type, $user_facebook_id, $target_facebook_id
								, $invite_key)){
				return $invite_key;
			}
		}
		return FALSE;
    }
	
	/**
	 * Accept invite and update invite status
	 *
	 */
	public function accept_invite($invite_key, $target_facebook_id = NULL){
		
		$exist_check = $this->get_invite_by_invite_key($invite_key);
		
		if($exist_check)
			if($exist_check['invite_count'] > 0 && isset($exist_check['target_facebook_id'])){
				return array('error' => 'invite is accepted before');
				
			}else{
				$data = $exist_check;
				$data['invite_count'] = $data['invite_count'] + 1;
				
				if(!isset($data['target_facebook_id']) && $target_facebook_id != NULL){
				//public
					if(!isset($data['target_facebook_id_list']))
							$data['target_facebook_id_list'] = array();
							
					if(in_array($target_facebook_id, $data['target_facebook_id_list'])){
						return array('error' => 'you have accepted this invite before');
					}else{
						
						array_push($data['target_facebook_id_list'], $target_facebook_id);
					}
				}else{
				//private
					if($data['target_facebook_id'] != $target_facebook_id){
						return array('error' => 'invalid target_facebook_id');
					}
				}
				
				if($this->CI->invite_model->update_invite($invite_key, $data))
					return $data;
				else
					return array('error' => 'accept invite failed');
				
			}
		else
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
		
		if(sizeof($criteria_arr) > 0)
			return $this->CI->invite_model->list_invites($criteria_arr);
    }
	
	/**
	 * Get an invite's details
	 *
	 */
    public function get_invite_by_invite_key($invite_key = NULL){
		$invite_record = $this->CI->invite_model->get_invite_by_criteria(array('invite_key' => $invite_key));
		
		if($invite_record)	
			return $invite_record;
		
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
}

/* End of file InvireLib.php */