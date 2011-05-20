<?php
class User_model extends CI_Model {
	var $user_id = '';
	var $user_facebook_id = '';
	var $user_register_date = '';
	var $user_last_seen = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get page members
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_members($page_id = NULL){
		if(!$page_id) return array();
		$this->db->join('user_apps','user_apps.user_id=user.user_id');
		$this->db->join('installed_apps','installed_apps.app_install_id=user_apps.app_install_id');
		return $this->db->get_where('user',array('page_id'=>$page_id))->result();
	}
	
	/**
	 * Get user profile by id
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_id($user_id = NULL){
		if (!$user_id)
			return array();
		return $this->db->get_where('user',array('user_id'=>$user_id))->result();
	}
	
	
	function add($data = array()) {		
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('user', $this);
		return $this->db->insert_id();
	}
	
	function add_by_facebook_id($user_facebook_id){
		$this->db->from('user');
		$this->db->where('user_facebook_id',$user_facebook_id);
	    if ($this->db->count_all_results() == 0) {
	      $this->db->insert('user', array('user_facebook_id' => $user_facebook_id));
		  return $this->db->insert_id();
	    }
		return FALSE;
	}
	
	function check_exist($user_facebook_id){
		$this->db->from('user');
		$this->db->where(array('user_facebook_id' =>$user_facebook_id ));
		$count = $this->db->count_all_results();
		return ($count != 0);
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('user', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('user', $data, $where);
	}
	
	function update_user_last_seen($user_facebook_id){
		$this->update(array('user_last_seen' => date ("Y-m-d H:i:s", time())),
						array('user_facebook_id' => $user_facebook_id));
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