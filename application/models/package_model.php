<?php

/**
 * Package_model
 */

class Package_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function get_package_by_package_id($package_id = NULL){
		$result = $this->db->get_where('package', array('package_id'=>$package_id))->result_array();
		return issetor($result[0]);
	}
	
	function add_package($data = array()){
		$this -> db -> insert('package', $data);
		return $this->db->insert_id();
	}
	
	function update_package_by_package_id($package_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('package', $data, array('package_id' => $package_id));
	}
	
	function remove_package($package_id = NULL){
		$this->db->delete('package', array('package_id' => $package_id));
		return $this->db->affected_rows();
	}
	
	function add_package_user($data = array()){
		$this -> db -> insert('package_users', $data);
		return $this->db->insert_id();
	}
	
	function update_package_user_by_user_id($user_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('package_users', $data, array('user_id' => $user_id));
	}
	
	function remove_package_user_by_user_id($user_id = NULL){
		$this->db->delete('package_users', array('user_id' => $user_id));
		return $this->db->affected_rows();
	}
	
	function add_package_app($data = array()){
		$this -> db -> insert('package_apps', $data);
		return $this->db->insert_id();
	}
	
	function remove_package_app_by_app_id($app_id = NULL){
		$this->db->delete('package_apps', array('app_id' => $app_id));
		return $this->db->affected_rows();
	}
	
	function get_package_by_user_id($user_id = NULL){
		$this->db->join('package','package.package_id=package_users.package_id');
		return $this->db->get_where('package_users',array('user_id' => $user_id))->result_array();
	}
	
	function check_user_package_can_add_company($user_id = NULL){
		$package = $this->get_package_by_user_id($user_id);
		$company_max_count = (int) $package['package_max_companies'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($company_max_count - $count > 0);
	}
	
	function check_user_package_can_add_page($user_id = NULL){
		$package = $this->get_package_by_user_id($user_id);
		$page_max_count = (int) $package['package_max_pages'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($page_max_count - $count > 0);
	}
	
	function check_user_package_can_add_user($user_id = NULL){
		$package = $this->get_package_by_user_id($user_id);
		$user_max_count = (int) $package['package_max_users'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($user_max_count - $count > 0);
	}
}

/* End of file package_model.php */
/* Location: ./application/models/package_model.php */
