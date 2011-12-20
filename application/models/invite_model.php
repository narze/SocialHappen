<?php
/**
 * invitation details and status
 * @author Wachiraph C.
 */
class Invite_model extends CI_Model {
	
	var $invite_record;
	
	/**
	 * constructor
	 * 
	 * @author Wachiraph C.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->invite = sh_mongodb_load( array(
			'database' => 'invite',
			'collection' => 'invites'
		));
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Wachiraph C.
	 */
	function create_index(){
		return $this->invite->ensureIndex(array(
										'invite_key' => 1,
										'campaign_id' => 1,
										'app_install_id' => 1,
										'page_id' => 1,
										'user_id' => 1
										));
	}
	
	/**
	 * add new invite
	 *
	 */
	function add_invite($campaign_id = NULL, $app_install_id = NULL, $facebook_page_id = NULL
							, $invite_type = NULL, $user_facebook_id = NULL, $target_facebook_id_list = array()
							, $invite_key = NULL, $redirect_url = NULL){
							
		$check_args = allnotempty(func_get_args()) && !$this->get_invite_by_criteria(array('invite_key'=>$invite_key));
							 
		if($check_args){
			$invite_record = array();
			
			$invite_record['invite_key'] = $invite_key;
			$invite_record['app_install_id'] = (int) $app_install_id;
			$invite_record['facebook_page_id'] = (string) $facebook_page_id;
			$invite_record['campaign_id'] = (int) $campaign_id;
			$invite_record['user_facebook_id'] =  (string) $user_facebook_id;	
			$invite_record['invite_type'] = $invite_type;
			$invite_record['redirect_url'] = $redirect_url;
			$invite_record['campaign_accepted'] = array();
			$invite_record['page_accepted'] = array();
			$invite_record['invite_count'] = (int) 0;

										
			if($invite_type == 1 && $target_facebook_id_list){
				$invite_record['target_facebook_id_list'] =  $target_facebook_id_list;
			} else if($invite_type == 2){
				$invite_record['target_facebook_id_list'] =  array();
			} else {
				return FALSE;
			}
			
					
			date_default_timezone_set('UTC');
			$invite_record['timestamp'] = time();
						
			try	{
				return $this->invite->insert($invite_record, array('safe' => TRUE));
			} catch(MongoCursorException $e){
				log_message('error', 'Mongo error : '. $e);
				return FALSE;
			}
			
		}else{
			return FALSE;
		}
	}
	
	/**
	 * update
	 *
	 */
	function update_invite($invite_key = NULL, $data = array()){
		$data = filter_array($data, array('app_install_id','facebook_page_id','campaign_id','target_facebook_id_list','invite_key','invite_type','invite_count','redirect_url'), TRUE);	
		$check_args = $invite_key && sizeof($data) > 0;
		
		if($check_args){

			/**
			 * keys
			 */
			if(isset($data['app_install_id'])){
				$data['app_install_id'] = (int) $data['app_install_id'];
			}
			if(isset($data['campaign_id'])){
				$data['campaign_id'] = (int) $data['campaign_id'];
			}
			
			/**
			 * info fields
			 */
			if(isset($data['invite_count'])){
				$data['invite_count'] = (int) $data['invite_count'];
			}
			
			return $this->invite->update(array('invite_key' => $invite_key), array('$set' => $data));
			
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 * Get invite detail
	 * 
	 */
	function get_invite_by_criteria($criteria = array()){
		$check_args = sizeof($criteria) > 0;
		
		if($check_args){
			$res = $this->invite	->find($criteria)
									->limit(1);
							
			$result = array();
			foreach ($res as $stat) {
				$result[] = $stat;
			}
			return count($result) > 0 ? $result[0] : NULL;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * List invites
	 */
	function list_invites($criteria = array()){
		if(isset($criteria['app_install_id'])){
			$criteria['app_install_id'] = (int) $criteria['app_install_id'];
		}
		if(isset($criteria['campaign_id'])){
			$criteria['campaign_id'] = (int) $criteria['campaign_id'];
		}
		if(isset($criteria['invite_count'])){
			$criteria['invite_count'] = (int) $criteria['invite_count'];
		}
		
		$res = $this->invite->find($criteria);
		
		$result = array();
		foreach ($res as $stat) {
			$result[] = $stat;
		}
		return $result;
	}
	
	/**
	 * drop entire collection
	 * you will lost all achievement_info data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->invite->drop();
	}

	function _push_into_accepted($mode = NULL, $invite_key = NULL, $user_facebook_id = NULL){
		if(!$mode || !$invite_key || !$user_facebook_id){
			return FALSE;
		} else if(!$invite = $this->get_invite_by_criteria(array('invite_key' => $invite_key))){ //Invalid invite
			return FALSE;
		} else if($invite['invite_type'] == 1 && !in_array($user_facebook_id, $invite['target_facebook_id_list'])){ //Private invite : user is not in target list
			return FALSE;
		} else if(in_array($user_facebook_id, $invite[$mode . '_accepted'])){ //Already accepted
			return FALSE;
		} else {
			return $this->invite->update(array('invite_key' => $invite_key), array('$push' => array($mode . '_accepted' => (string) $user_facebook_id)));
		}
	}

	/**
	 * Add user_facebook_id into page_accepted set
	 * @param $invite_key
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function push_into_page_accepted($invite_key = NULL, $user_facebook_id = NULL){
		return $this->_push_into_accepted('page', $invite_key, $user_facebook_id);
	}

	/**
	 * Add user_facebook_id into campaign_accepted set
	 * @param $invite_key
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function push_into_campaign_accepted($invite_key = NULL, $user_facebook_id = NULL){
		return $this->_push_into_accepted('campaign', $invite_key, $user_facebook_id);
	}

	/**
	 * Increment invite_count
	 * @param $invite_key
	 * @author Manassarn M.
	 */
	function increment_invite_count_by_invite_key($invite_key = NULL){
		if(!$invite_key){
			return FALSE;
		} else {
			if($result = $this->invite->findOne(array('invite_key' => $invite_key))){
		      	return $this->invite->update(array('invite_key' => $invite_key), array('$inc' => array('invite_count' => 1)), array('fsync' => TRUE));
		    } else {
		    	return FALSE;	
		    }
		}
	}

	/**
	 * Add user_facebook_id (array) into target_facebook_id_list
	 * @param $invite_key
	 * @param array $user_facebook_id_array
	 * @author Manassarn M.
	 */
	function add_into_target_facebook_id_list($invite_key = NULL, $user_facebook_id_array = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		}
		if((!$invite = $this->get_invite_by_criteria(array('invite_key' => $invite_key))) || ($invite['invite_type'] == 2)){ 
			//Invalid invite or public invite : cannot push into list
			return FALSE;
		} else {
			foreach($user_facebook_id_array as &$user_facebook_id){
				$user_facebook_id = (string) $user_facebook_id;
			}
			unset($user_facebook_id);
			return $this->invite->update(array('invite_key' => $invite_key), array('$addToSet' => array('target_facebook_id_list' => array('$each' => $user_facebook_id_array))));
		}
	}

	/** 
	 * Get invite by facebook_page_id, having user_facebook_id in target_facebook_id_list
	 * @param $facebook_page_id
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function get_by_facebook_page_id_having_user_facebook_id_in_target_facebook_id_list($facebook_page_id = NULL, $user_facebook_id = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		}
		$criteria = array(
			'facebook_page_id' => (string) $facebook_page_id,
			'target_facebook_id_list' => (string) $user_facebook_id
			);
		$cursor = $this->invite->find($criteria);
		$result = array();
		foreach($cursor as $value){
			$result[] = $value;
		}
		return $result;
	}

	/**
	 * Push user_facebook_id into all page_accepted with invite_key in specified list
	 * @param $user_facebook_id
	 * @param array $invite_key_array
	 * @return FALSE if exception, otherwise TRUE
	 * @author Manassarn M.
	 */
	function push_into_all_page_accepted($user_facebook_id = NULL, $invite_key_array = NULL){
		if(!allnotempty(func_get_args()) || !allnotempty($invite_key_array)){
			return FALSE;
		}
		try {
			//
			return $this->invite->update(array('invite_key' => array('$in' => $invite_key_array)), array('$addToSet' => array('page_accepted' => (string) $user_facebook_id)), array('multiple' => TRUE, 'safe' => TRUE));
		} catch(MongoCursorException $e){
			log_message('error', 'Mongo error : '. $e);
			return FALSE;
		}
	}
}

/* End of file invite_model.php */
/* Location: ./application/models/invite_model.php */