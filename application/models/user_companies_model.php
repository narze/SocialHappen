<?php
class User_companies_model extends CI_Model {
	var $user_facebook_id = '';
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
	 * Get user/s information
	 * @param array $company_id
	 * @param $limit
	 * @param $offset
	 * @author Wachiraph C.
	 */
	function _get($where = array(), $limit =0, $offset =0) {
		if (array_key_exists('user_facebook_id', $where)) {
			$this->db->join('sh_user', 'user_facebook_id = sh_user.user_facebook_id ');
			unset($where['user_facebook_id']);
		}
		$query = $this -> db -> get_where('user_companies', $where, $limit, $offset);
		return $query -> result();
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
	
	function get_user_admin_companies_list_by_company($company_id, $limit =20, $offset =0) {
		return $this -> _get( array('company_id'=>$company_id), $limit, $offset);
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