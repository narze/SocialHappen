<?php
class User_role_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get user role
	 * @param $user_role_id
	 * @author Manassarn M.
	 */
	function get_user_role_by_user_role_id($user_role_id =NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		return $this -> db -> get_where('user_role', array('user_role_id' => $user_role_id)) -> row_array();
	}

	/**
	 * Get all user role
	 * @param $limit
	 * @param $offset
	 * @author Manassarn M.
	 */
	function get_all_user_role($limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		return $this->db->get_where('user_role',array())->result_array();
	}

	/**
	 * Adds user role
	 * @param array $data
	 * @return $user_role_id
	 * @author Manassarn M.
	 */
	function add_user_role($data = array()){
		if(!$data){
			return FALSE;
		}
		$this -> db -> insert('user_role', $data);
		return $this->db->insert_id();
	}

	/**
	 * Removes user role
	 * @param $user_role_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user_role($user_role_id = NULL){
		$this->db->delete('user_role', array('user_role_id' => $user_role_id));
		return $this->db->affected_rows() === 1;
	}

	/**
	 * Update user role
	 * @param $user_role_id
	 * @author Manassarn M.
	 */
	function update_user_role($user_role_id = NULL, $data = array()){
		return $this->db->update('user_role', $data, array('user_role_id' => $user_role_id));
	}
}
