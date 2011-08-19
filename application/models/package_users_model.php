<?php

/**
 * Package_users_model
 */

class Package_users_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	function get_package_by_user_id($user_id = NULL){
		$this->db->join('package','package.package_id=package_users.package_id');
		$packages = $this->db->get_where('package_users',array('user_id' => $user_id))->result_array();
		return issetor($packages[0], NULL);
	}
	
	function add_package_user($data = array()){
		if(!$data){
			return FALSE;
		}
		return $this -> db -> insert('package_users', $data);
	}
	
	function update_package_user_by_user_id($user_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('package_users', $data, array('user_id' => $user_id));
	}
	
	function remove_package_user_by_user_id($user_id = NULL){
		$this->db->delete('package_users', array('user_id' => $user_id));
		return $this->db->affected_rows() == 1;
	}
	
	function check_user_package_can_add_company($user_id = NULL){
		if(!$package = $this->get_package_by_user_id($user_id)){
			return FALSE;
		}
		$company_max_count = (int) $package['package_max_companies'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($company_max_count - $count > 0);
	}
	
	function check_user_package_can_add_page($user_id = NULL){
		if(!$package = $this->get_package_by_user_id($user_id)){
			return FALSE;
		}
		$page_max_count = (int) $package['package_max_pages'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($page_max_count - $count > 0);
	}
	
	function check_user_package_can_add_user($user_id = NULL){
		if(!$package = $this->get_package_by_user_id($user_id)){
			return FALSE;
		}
		$user_max_count = (int) $package['package_max_users'];
		
		$this->db->where(array('user_id' => $user_id));
		$count = $this->db->count_all_results('user_companies');
		return ($user_max_count - $count > 0);
	}
	
	function is_package_expire($user_id = NULL){
		$results = $this->db->get_where('package_users',array('user_id' => $user_id))->result_array();
		return date('Y-m-d H:i:s') > $results[0]['package_expire'];
	}
}
/* End of file package_users_model.php */
/* Location: ./application/models/package_users_model.php */
