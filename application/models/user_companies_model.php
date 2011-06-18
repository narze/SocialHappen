<?php
class User_companies_model extends CI_Model {
	var $user_id = '';
	var $company_id = '';
	var $user_role = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('user_companies', $this);
		return $this->db->insert_id();
	}
	
	/**
	 * Adds user company
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_company($data = array()){
		return $this -> db -> insert('user_companies', $data);
	}
	
	/**
	 * Removes user_company
	 * @param $user_id
	 * @param $company_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user_company($user_id = NULL, $company_id = NULL){
		$this->db->delete('user_companies', array('user_id' => $user_id, 'company_id' => $company_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get user/s information
	 * @param array $company_id
	 * @param $limit
	 * @param $offset
	 * @author Wachiraph C.
	 */
	function _get($where = array(), $limit =0, $offset =0) {
		if (array_key_exists('user_facebook_id', $where)) {
			$this->db->join('user', 'user_companies.user_id = user.user_id');
			$where = array('user_facebook_id'=>$where['user_facebook_id']);
		}
		$query = $this -> db -> get_where('user_companies', $where, $limit, $offset);
		return $query -> result();
	}
	
	/**
	 * Get user companies
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_companies_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->join('company','user_companies.company_id=company.company_id');
		return $this->db->get_where('user_companies', array('user_id' => $user_id))->result_array();
	}

	/**
	 * Get admins by company id
	 * @param $company_id
	 * @author Wachiraph C.
	 */
	function get_user_companies_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		return $this->db->get_where('user_companies', array('company_id' => $company_id))->result_array();
	}
	
	function update_role($user_id, $company_id, $new_role) {
		$this -> db -> update('user_companies', $new_role, array('company_id'=>$company_id, 'user_facebook_id'=>$user_facebook_id));
	}
	
	function delete($user_facebook_id,$company_id) {
		$this->db-> delete('user_companies', array('user_facebook_id' => $user_id,'company_id'=>$company_id));
	}
	
	function delete_admin($company_id){
		$this->db-> delete('user_companies', array('user_role' => 1,'company_id'=>$company_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('user_companies');
	}
	
	function get_user_companies_list($user_facebook_id, $limit =20, $offset =0) {
		return $this -> _get( array('user_facebook_id'=>$user_facebook_id), $limit, $offset);
	}
	
	function get_user_companies_list_by_company($company_id, $limit =20, $offset =0) {
		return $this -> _get( array('company_id'=>$company_id, 'user_role' => 1), $limit, $offset);
	}
	
	function get_user_companies_admin($company_id){
		$admin =  $this -> _get( array('company_id'=>$company_id, 'user_role' => 0), 1, 0);
		return $admin[0]->user_facebook_id;
	}
	
	function get_company_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function is_user_company_admin($user_facebook_id, $company_id){
		if($this -> _get( array('company_id'=>$company_id, 'user_facebook_id' => $user_facebook_id),1,0)==null)
			return false;
		
		return true;
	}
}