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
							, $invite_key = NULL){
							
		$check_args = issetor($app_install_id) && isset($facebook_page_id) && isset($campaign_id)
							 && issetor($invite_type) && issetor($user_facebook_id) 
							 && issetor($invite_key);
							 
		if($check_args){
			$invite_record = array();
			
			/**
			 * keys
			 */
			if($app_install_id){
				$invite_record['app_install_id'] = (int) $app_install_id;
			}			
			if($facebook_page_id){
				$invite_record['facebook_page_id'] = $facebook_page_id;
			}
			if($campaign_id){
				$invite_record['campaign_id'] = (int) $campaign_id;
			}
			$invite_record['user_facebook_id'] =  $user_facebook_id;					
			if($target_facebook_id_list){
				$invite_record['target_facebook_id_list'] =  $target_facebook_id_list;
			}
			$invite_record['invite_key'] = $invite_key;
			$invite_record['invite_type'] = $invite_type;
					
			/**
			 * info fields
			 */
			$invite_record['invite_count'] = (int) 0;
			
			date_default_timezone_set('UTC');
			$invite_record = array_merge($invite_record, array('timestamp' => time()));
						
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
							
		$check_args = isset($invite_key) > 0 && sizeof($data) > 0;
		
		if($check_args){
			$invite_record = array();
			
			/**
			 * keys
			 */
			if(isset($data['app_install_id'])){
				$invite_record['app_install_id'] = (int) $data['app_install_id'];
			}			
			if(isset($data['facebook_page_id'])){
				$invite_record['facebook_page_id'] =  $data['facebook_page_id'];
			}
			if(isset($data['campaign_id'])){
				$invite_record['campaign_id'] = (int) $data['campaign_id'];
			}
			if(isset($data['user_facebook_id'])){
				$invite_record['user_facebook_id'] = $data['user_facebook_id'];
			}
			if(isset($data['target_facebook_id_list'])){
				$invite_record['target_facebook_id_list'] = $data['target_facebook_id_list'];
			}
			$invite_record['invite_key'] = $data['invite_key'];
			$invite_record['invite_type'] = $data['invite_type'];
			
			
			/**
			 * info fields
			 */
			if(isset($data['invite_count'])){
				$invite_record['invite_count'] = (int) $data['invite_count'];
			}
			if(isset($data['public_accepted_target_facebook_id'])){
				$invite_record['public_accepted_target_facebook_id'] = $data['public_accepted_target_facebook_id'];
				
			}
			if(isset($data['accepted_target_facebook_id_list'])){
				$invite_record['accepted_target_facebook_id_list'] = $data['accepted_target_facebook_id_list'];
			}
			$invite_record['timestamp'] = $data['timestamp'];
			
			return $this->invite->update(array('invite_key' => $invite_key),	$invite_record);
			
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
}

/* End of file invite_model.php */
/* Location: ./application/models/invite_model.php */