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