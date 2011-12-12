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
		
		$this->config->load('mongo_db');
		$mongo_user = $this->config->item('mongo_user');
		$mongo_pass = $this->config->item('mongo_pass');
		$mongo_host = $this->config->item('mongo_host');
		$mongo_port = $this->config->item('mongo_port');
		$mongo_db = $this->config->item('mongo_db');
		
		try{
			// connect to database
			$this->connection = new Mongo("mongodb://".$mongo_user.":"
			.$mongo_pass
			."@".$mongo_host.":".$mongo_port);
			
			// select database
			$this->db = $this->connection->invite;
			
			// select collection
			$this->invite = $this->db->invites;
			
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
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
							
		$check_args = issetor($app_install_id) && isset($facebook_page_id) && isset($campaign_id)
							 && issetor($invite_type) && issetor($user_facebook_id) 
							 && issetor($invite_key) && $redirect_url && !$this->get_invite_by_criteria(array('invite_key'=>$invite_key));
							 
		if($check_args){
			$invite_record = array();
			
			/**
			 * keys
			 */
			$invite_record['invite_key'] = $invite_key;
			if($app_install_id){
				$invite_record['app_install_id'] = (int) $app_install_id;
			}			
			if($facebook_page_id){
				$invite_record['facebook_page_id'] = (string) $facebook_page_id;
			}
			if($campaign_id){
				$invite_record['campaign_id'] = (int) $campaign_id;
			}
			$invite_record['user_facebook_id'] =  $user_facebook_id;					
			if($invite_type == 1 && $target_facebook_id_list){
				$invite_record['target_facebook_id_list'] =  $target_facebook_id_list;
			} else if($invite_type == 2){
				$invite_record['target_facebook_id_list'] =  array();
			} else {
				return FALSE;
			}
			$invite_record['invite_type'] = $invite_type;
			$invite_record['redirect_url'] = $redirect_url;
					
			$invite_record['campaign_accepted'] = array();
			$invite_record['page_accepted'] = array();
			/**
			 * info fields
			 */
			$invite_record['invite_count'] = (int) 0;
			
			date_default_timezone_set('UTC');
			$invite_record['timestamp'] = time();
						
			return $this->invite->insert($invite_record);
			
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
		} else if(in_array($user_facebook_id, $invite[$mode . '_accepted'])){
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

}

/* End of file invite_model.php */
/* Location: ./application/models/invite_model.php */