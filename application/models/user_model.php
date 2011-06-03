<?php
class User_model extends CI_Model {
	var $user_id = '';
	var $user_facebook_id = '';
	var $user_register_date = '';
	var $user_last_seen = '';

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get page users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id($page_id =NULL) {
		$this -> db -> join('user_apps', 'user_apps.user_id=user.user_id');
		$this -> db -> join('installed_apps', 'installed_apps.app_install_id=user_apps.app_install_id');
		return $this -> db -> get_where('user', array('page_id' => $page_id)) -> result_array();
	}

	/**
	 * Get user profile
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_id($user_id =NULL) {
		$profiles = $this -> db -> get_where('user', array('user_id' => $user_id)) -> result_array();
		return $profiles[0];
	}

	/**
	 * Get user id by user_facebook_id
	 * @param $user_facebook_id
	 * @author Wachiraph C.
	 */
	function get_user_id($user_facebook_id =NULL) {
		return $this -> db ->select('user_id') -> get_where('user', array('user_facebook_id' => $user_facebook_id))-> result();
	}
	
	/**
	 * Get user id
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function get_user_id_by_user_facebook_id($user_facebook_id =NULL){
		$user = $this -> db -> select('user_id') -> get_where('user', array('user_facebook_id' => $user_facebook_id)) -> result_array();
		return $user[0]['user_id'];
	}

	/**
	 * Check if user is company admin
	 * @param $user_id
	 * @param $use_user_facebook_id
	 * @author Manassarn M.
	 * @todo Fix conditions
	 */
	function is_company_admin($user_id =NULL, $company_id, $use_user_facebook_id =FALSE) {
		if(!$user_id){
			return FALSE;
		}
		$this -> db -> join('user_companies', 'user_companies.user_id=user.user_id');
		if($use_user_facebook_id){
			$this -> db -> where(array('user_facebook_id' => $user_id, 'company_id'=>$company_id));
		} else {
			$this -> db -> where(array('user.user_id' => $user_id, 'company_id'=>$company_id));
		}
		return $this -> db -> count_all_results('user') == 1;
	}

	/**
	 * Adds user
	 * @param array $data
	 * @return $user_id
	 * @author Manassarn M.
	 */
	function add_user($data = array()){
		$this -> db -> insert('user', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Removes user
	 * @param $user_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user($user_id = NULL){
		$this->db->delete('user', array('user_id' => $user_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get user profile
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_facebook_id($user_facebook_id =NULL) {
		$profiles = $this -> db -> get_where('user', array('user_facebook_id' => $user_facebook_id)) -> result_array();
		return $profiles[0];
	}

	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('user', $this);
		return $this -> db -> insert_id();
	}

	function add_by_facebook_id($user_facebook_id) {
		$this -> db -> from('user');
		$this -> db -> where('user_facebook_id', $user_facebook_id);
		if($this -> db -> count_all_results() == 0) {
			$this -> db -> insert('user', array('user_facebook_id' => $user_facebook_id));
			return $this -> db -> insert_id();
		}
		return FALSE;
	}

	function check_exist($user_facebook_id) {
		$this -> db -> from('user');
		$this -> db -> where( array('user_facebook_id' => $user_facebook_id));
		$count = $this -> db -> count_all_results();
		return ($count != 0);
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('user', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('user', $data, $where);
	}

	function update_user_last_seen($user_facebook_id) {
		$this -> update( array('user_last_seen' => date("Y-m-d H:i:s", time())), array('user_facebook_id' => $user_facebook_id));
	}

	function delete($id) {
		$this -> db -> delete('user', array('user_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('user');
	}

	function get_user_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}

}
