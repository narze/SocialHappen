<?php
/**
 * Reference table for audit action types
 * @author Wachiraph C.
 */
class Audit_action_type_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get specified audit action type
	 * @param $audit_action_id
	 * @author Wachiraph C.
	 */
	function get_audit_action_by_type_id($audit_action_id = null){
		$result = $this->db->get_where('audit_action_type', array('audit_action_id'=>$audit_action_id))->result_array();
		return issetor($result[0]);
	}
	/**
	 * Get audiot action list
	 * @author Wachiraph C.
	 */
	function get_active_audit_action_list(){
		$result = $this->db->get_where('audit_action_type',array('audit_action_active'=>1))->result_array();
		return issetor($result);
	}
	
	/**
	* Add new audit action type
	* @param $data
	* @author Wachiraph C.
	*/
	function add_audit_auction_type($data = array()){
		if (array_key_exists('audit_action_id', $data)&&array_key_exists('audit_action_name', $data)) {
			return $this -> db -> insert('audit_action_type', $data);
		}
		return false;
	}
	
	/**
	* Update audit action type by audit action id
	* @param $data
	* @author Wachiraph C.
	*/
	function update_audit_action_type_by_id($audit_action_id = null, $data = array()){
		return $this->db->update('audit_action_type', $data, array('audit_action_id' => $audit_action_id));
	}
}

/* End of file audit_action_type.php */
/* Location: ./application/models/audit_action_type.php */