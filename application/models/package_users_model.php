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
		$package = $this->db->get_where('package_users',array('user_id' => $user_id))->row_array();
		return issetor($package, NULL);
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

	/**
	 * Check package expire
	 * @param $user_id
	 * @return boolean
	 * @author Weerapat P.
	 */
	function is_package_expire($user_id = NULL){
		$this->db->join('package_users','package_users.package_id=package.package_id');
		$this->db->where(array('package_users.user_id'=>$user_id));
		$package = $this->db->get('package')->row_array();

		if($package['package_duration'] == 'unlimited' || $package['package_price'] == 0) return FALSE; //Free package

		return (date('Y-m-d H:i:s') > $package['package_expire']);
	}

	/**
	 * Count user mambers
	 * @param $user_id
	 * @return int
	 * @author Weerapat P.
	 */
	function count_user_members_by_user_id($user_id = NULL){
		$this->db->join('company','company.company_id=page.company_id');
		$this->db->join('page_user_data','page_user_data.page_id=page.page_id');
		return $this->db->where(array('company.creator_user_id'=>$user_id))->count_all_results('page');

	}
}
/* End of file package_users_model.php */
/* Location: ./application/models/package_users_model.php */
