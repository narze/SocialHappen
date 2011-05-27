<?php
/**
 * audit action model class for audit action object
 * @author Metwara Narksook
 */
class Audit_action_model extends CI_Model {
	var $_id = '';
	var $app_id = '';
	var $action = '';
	var $description = '';
	var $stat = '';
	
	function __construct() {
		parent::__construct();
		
		// connect to database
		$this->connection = new Mongo();
		
		// select audit database
		$this->db = $this->connection->audit;
		
		// select actions collection
		$this->actions = $this->db->actions;
	}
	/**
	 * create index for collection
	 * 
	 */
	function create_index(){
		$this->actions->ensureIndex(array('app_id' => 1, 'action' => 1));
	}
	
	/**
	 * add new audit action
	 * @param audit action object in array format
	 */
	function add_action($data = array()){
		// add new
		$check_args = isset($data['app_id']) && isset($data['action']) && isset($data['description']) && isset($data['stat']);
		if($check_args){
			$data_to_add = array('app_id' => $data['app_id'],
								'action' => $data['action'],
								'description' => $data['description'],
								'stat' => $data['stat']);
			$this->actions->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * edit audit action
	 * @param app_id
	 * @param action
	 * @param data 
	 */
	function edit_action($app_id, $action, $data){
		$check_args = isset($app_id) && isset($action) && isset($data);
		if($check_args){
			$criteria = array('app_id' => $app_id,
							'action' => $action,
							'$atomic' => TRUE);
			$this->actions->update($criteria, $data);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * delete audit action
	 * @param app_id
	 * @param action
	 */
	function delete_action($app_id, $action){
		$check_args = isset($app_id) && isset($action);
		if($check_args){
			$criteria = array('app_id' => $app_id,
							  'action' => $action);
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
	 */
	function get_action_list(){
		return $this->actions->find();
	}
	
	/**
	 * get audit action for app_id
	 * @param app_id
	 * 
	 * @return audit action list
	 */
	function get_action_by_app_id($app_id){
		if(isset($app_id)){
			return $this->actions->find(array('app_id' => $app_id));
		}else{
			return FALSE;
		}
	}
	
	/**
	 * get platform audit action
	 * 
	 * @return audit action list
	 */
	function get_platform_action(){
		return $this->get_action_by_app_id(0);
	}
}

/* End of file audit_action_model.php */
/* Location: ./application/models/audit_action_model.php */