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
		$this->connection = new Mongo('localhost:27017');
		
		// select audit database
		$this->db = $this->connection->audit;
		
		// select actions collection
		$this->actions = $this->db->actions;
	}
	
	/**
	 * add new audit action
	 * @param audit action object in array format
	 */
	function add_action($data = array()){
		// add new 
		$this->actions->insert($data);
	}
	
	/**
	 * edit audit action
	 * @param app_id
	 * @param action
	 * @param data 
	 */
	function edit_action($app_id, $action, $data){
		$criteria = array('app_id' => $app_id,
						'action' => $action,
						'$atomic' => true);
		$this->actions->update($criteria, $data);
	}
	
	/**
	 * delete audit action
	 * @param app_id
	 * @param action
	 */
	function delete_action($app_id, $action){
		$criteria = array('app_id' => $app_id,
						'action' => $action);
		$this->actions->remove($criteria, array('$atomic' => true));
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
		return $this->actions->find(array('app_id' => $app_id));
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