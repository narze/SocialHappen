<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MongoDB Audit, Log and Stat Library
 *
 * A library to record audit to the NoSQL database MongoDB. 
 *
 * @author		Metwara Narksook
 */

class Audit_lib
{
	
	private $CI;
	
	/**
	 *	--------------------------------------------------------------------------------
	 *	CONSTRUCTOR
	 *	--------------------------------------------------------------------------------
	 *
	 *	Automatically check if the Mongo PECL extension has been installed/enabled.
	 */
	
	public function __construct(){
		if(!class_exists('Mongo')){
			show_error("The MongoDB PECL extension has not been installed or enabled", 500);
		}
		$this->CI =& get_instance();
	}
	
	/**
	 * add new audit action
	 * 
	 * @param app_id int id of app
	 * @param action int action number - unique
	 * @param stat boolean want to keep in stat or not
	 * @param description string description of action
	 */
	function add_audit_action($app_id, $action, $stat, $description){
		$check_args = isset($app_id) && isset($action) && isset($stat) && isset($description);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		$this->load->model('audit_action_model','audit_action');
		
		$data = array('app_id' => $app_id,
						'action' => $action,
						'stat' => $stat,
						'description' => $description);
		$result = $this->audit_action->add_action($data);
		if(!$result){
			show_error("add new audit action fail", 500);
		}
	}
	
	/**
	 * edit exists audit action
	 * 
	 * @param app_id int id of app
	 * @param action int action number
	 * 
	 * @param data array contain stat or description
	 */
	function edit_audit_action($app_id, $action, $data){
		$check_args = isset($app_id) && isset($action) && (isset($action['stat']) || isset($action['description']));
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		
		$this->load->model('audit_action_model','audit_action');
		$result = $this->audit_action->edit_action($app_id, $action, $data);
		if(!$result){
			show_error("edit audit action fail", 500);
		}
	}
	
	/**
	 * delete audit action
	 * 
	 * @param app_id int id of app
	 * @param action int action number
	 */
	function delete_audit_action($app_id, $action){
		$check_args = isset($app_id) && isset($action);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		
		$this->load->model('audit_action_model','audit_action');
		$result = $this->audit_action->delete_action($app_id, $action);
		return $result;
	}
	
	/**
	 * list audit action
	 * @param app_id int optional 
	 */
	function list_audit_action($app_id = NULL){
		$this->load->model('audit_action_model','audit_action');
		if(isset($app_id)){
			$result = $this->audit_action->get_action_by_app_id($app_id);
		}else{
			$result = $this->audit_action->get_action_list();
		}
		return $result;
	}
}

/* End of file audit_lib.php */
/* Location: ./application/libraries/audit_lib.php */