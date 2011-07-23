<?php
/**
 * audit action model class for audit action object
 * @author Metwara Narksook
 */
class Audit_action_model extends CI_Model {

	var $app_id = '';
	var $action_id = '';
	var $description = '';
	var $stat_app = '';
	var $stat_page = '';
	var $stat_campaign = '';
	
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
			."@".$mongo_host.":".$mongo_port);//."/".$mongo_db);
			
			// select audit database
			$this->db = $this->connection->$mongo_db;
			
			// select actions collection
			$this->actions = $this->db->actions;
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
		$this->actions->ensureIndex(array('app_id' => 1, 'action_id' => 1));
	}
	
	/**
	 * add new audit action
	 * 
	 * @param audit action object in array format
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_action($data = array()){
		// add new
		$check_args = isset($data['app_id']) && isset($data['action_id']) 
		&& isset($data['description']);
		if($check_args){
			$data_to_add = array('app_id' => $data['app_id'],
								'action_id' => $data['action_id'],
								'description' => $data['description']);
			if(isset($data['stat_app'])){
				$data_to_add['stat_app'] = $data['stat_app'];
			}
			if(isset($data['stat_page'])){
				$data_to_add['stat_page'] = $data['stat_page'];
			}
			if(isset($data['stat_campaign'])){
				$data_to_add['stat_campaign'] = $data['stat_campaign'];
			}

			$this->actions->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * edit audit action
	 * 
	 * @param app_id
	 * @param action_id
	 * @param data 
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function edit_action($app_id = NULL, $action_id = NULL, $data = NULL){
		$check_args = isset($app_id) && isset($action_id) && isset($data);
		if($check_args){
			$criteria = array('app_id' => $app_id,
							'action_id' => $action_id,
							'$atomic' => TRUE);
			$this->actions->update($criteria, array('$set' => $data));
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * delete audit action
	 * 
	 * @param app_id
	 * @param action_id - [optional]
	 * 
	 * @return result oolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete_action($app_id = NULL, $action_id = NULL){
		$check_args = isset($app_id);
		$app_id = (int)$app_id;
		if($check_args){
			if(isset($action_id)){
				$action_id = (int)$action_id;
				$criteria = array('app_id' => $app_id,
							  	  'action_id' => $action_id);
			}else{
				$criteria = array('app_id' => $app_id);
			}
			
			$this->actions->remove($criteria, array('$atomic' => TRUE));
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * get all audit action
	 * 
	 * @return audit action list
	 * 
	 * @author Metwara Narksook
	 */
	function get_action_list(){
		$res = $this->actions->find();
		$result = array();
		foreach ($res as $entry) {
			$result[] = $entry;
		}
		return $result;
	}
	
	/**
	 * get audit action for app_id
	 * 
	 * @param app_id int app_id
	 * @param action_id int action_id [optional]
	 * 
	 * @return audit action list
	 * 
	 * @author Metwara Narksook
	 */
	function get_action($app_id = NULL, $action_id = NULL){
		if(isset($app_id)){
			if(empty($action_id)){
				$criteria = array('app_id' => $app_id);
			}else{
				$criteria = array('app_id' => $app_id, 'action_id' => $action_id);
			}
			$res = $this->actions->find($criteria);
			$result = array();
			foreach ($res as $entry) {
				$result[] = $entry;
			}
			return $result;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * get platform audit action
	 * 
	 * @return audit action list
	 * 
	 * @author Metwara Narksook
	 */
	function get_platform_action(){
		$res = $this->get_action(0);
		
		$result = array();
		foreach ($res as $entry) {
			$result[] = $entry;
		}
		return $result;
	}
	
	/**
	 * drop entire collection
	 * you will lost all audit action data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		$this->actions->drop();
	}
}

/* End of file audit_action_model.php */
/* Location: ./application/models/audit_action_model.php */