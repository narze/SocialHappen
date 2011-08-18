<?php

/**
 * Package_apps_model
 */

class Package_apps_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	function add_package_app($data = array()){
		if(!$data){
			return FALSE;
		}
		return $this -> db -> insert('package_apps', $data);
	}
	
	function remove_package_app_by_app_id($app_id = NULL){
		$this->db->delete('package_apps', array('app_id' => $app_id));
		return $this->db->affected_rows() == 1;
	}
}
/* End of file package_apps_model.php */
/* Location: ./application/models/package_apps_model.php */