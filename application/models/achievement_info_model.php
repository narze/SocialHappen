<?php
/**
 * achievement info model class for achievement info object
 * @author Metwara Narksook
 */
class Achievement_info_model extends CI_Model {
	
	var $achievement_info;
	var $achievement_id;
	var $app_id;

	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
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
			$this->db = $this->connection->achievement;
			
			// select collection
			$this->achievement_info = $this->db->achievement_info;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->achievement_info->ensureIndex(array(
										'app_id' => 1,
										'app_install_id' => 1,
										'info.page_id' => 1));
	}
	
	
	/**
	 * add new achievement info
	 * 
	 * @param app_id int app_id
	 * @param app_install_id int app_install_id
	 * @param info array of info contains 
	 * 				['name',*
	 * 				 'description', *
	 * 				 'criteria_string' = >array('criteria1'...), *
	 * 				 'page_id', 
	 * 				 'campaign_id',
	 * 				 'hidden']
	 * @param criteria array of criteria and amount
	 * 				ex. array('friend' => 500),
	 * 				ex.	array('action.5.page.count' => 20,
	 * 									'action.10.count' => 23)
	 */
	function add($app_id = NULL, $app_install_id = NULL, $info = array()
							, $criteria = array()){
		$check_args = isset($app_id) && isset($app_install_id) && isset($info)
							 && isset($info['name']) && isset($info['description'])
							 && isset($info['criteria_string'])
							 && count($info['criteria_string']) > 0
							 && isset($criteria) && count($criteria) > 0;
		if($check_args){
			$achievement_info = array();
			
			/**
			 * keys
			 */
			$achievement_info['app_id'] = $app_id;
			$achievement_info['app_install_id'] = $app_install_id;
			
			if(isset($info['page_id'])){
				$achievement_info['page_id'] = $info['page_id'];
			}
			if(isset($info['campaign_id'])){
				$achievement_info['campaign_id'] = $info['campaign_id'];
			}
			/**
			 * info fields
			 */
			$info_to_add = array();
			$info_to_add['name'] = $info['name'];
			$info_to_add['description'] = $info['description'];
			$info_to_add['hidden'] = isset($info['hidden']) ? $info['hidden'] : FALSE;
			$info_to_add['criteria_string'] = $info['criteria_string'];
			$achievement_info['info'] = $info_to_add;
			
			$achievement_info['criteria'] = $criteria;
			
			return $this->achievement_info->insert($achievement_info);
			
		}else{
			return FALSE;
		}
	}
	
	/**
	 * set exists achievement info
	 * 
	 * @param achievement_id string achievement_id
	 * @param app_id int app_id
	 * @param app_install_id int app_install_id
	 * @param info array of info contains 
	 * 				['name',
	 * 				 'description', 
	 * 				 'criteria_string' = >array('criteria1'...), 
	 * 				 'page_id', 
	 * 				 'campaign_id',
	 * 				 'hidden']
	 * @param criteria array of criteria and amount
	 * 				ex. array('friend' => 500),
	 * 				ex.	array('action.5.page.count' => 20,
	 * 									'action.10.count' => 23)
	 */
	function set($achievement_id = NULL, $app_id = NULL, $app_install_id = NULL, $info = array()
							, $criteria = array()){
		$check_args = isset($achievement_id) && isset($app_id) 
							 && isset($app_install_id) && isset($info)
							 && isset($info['name']) && isset($info['description'])
							 && isset($info['criteria_string'])
							 && count($info['criteria_string']) > 0
							 && isset($criteria) && count($criteria) > 0;
		if($check_args){
			$achievement_info = array();
			
			/**
			 * keys
			 */
			$achievement_info['app_id'] = $app_id;
			$achievement_info['app_install_id'] = $app_install_id;
			
			if(isset($info['page_id'])){
				$achievement_info['page_id'] = $info['page_id'];
			}
			if(isset($info['campaign_id'])){
				$achievement_info['campaign_id'] = $info['campaign_id'];
			}
			/**
			 * info fields
			 */
			$info_to_add = array();
			$info_to_add['name'] = $info['name'];
			$info_to_add['description'] = $info['description'];
			$info_to_add['hidden'] = isset($info['hidden']) ? $info['hidden'] : FALSE;
			$info_to_add['criteria_string'] = $info['criteria_string'];
			$achievement_info['info'] = $info_to_add;
			
			$achievement_info['criteria'] = $criteria;
			
			return $this->achievement_info->update(array('_id' => $achievement_id),
																													 $achievement_info);
			
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 * get achievement info
	 * @param achievement_id
	 * 
	 * @return result array
	 * 
	 * @author Metwara Narksook
	 */
	function get($achievement_id = NULL){
		$check_args = isset($achievement_id);
		if($check_args){
			
			
			$res = $this->achievement_info
									->find(array('_id' => new MongoId($achievement_id)))
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
	 * list achievement info
	 * @param criteria array of criteria
	 */
	function list_info($criteria = array()){
		$res = $this->achievement_info->find($criteria);
		
		$result = array();
		foreach ($res as $stat) {
			$result[] = $stat;
		}
		return $result;
	}
	
	/**
	 * delete achievement info
	 * @param achievement_id
	 * 
	 * @return result bolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete($achievement_id = NULL){
		$check_args = isset($achievement_id);
		if($check_args){
			
			
			return $this->achievement_info
									->remove(array("_id" => new MongoId($achievement_id)), 
									array('$atomic' => TRUE));
		}else{
			return FALSE;
		}
	}
	
	/**
	 * drop entire collection
	 * you will lost all achievement_info data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->achievement_info->drop();
	}
}

/* End of file achievement_info_model.php */
/* Location: ./application/models/achievement_info_model.php */