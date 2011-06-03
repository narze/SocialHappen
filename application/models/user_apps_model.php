<?php
class User_apps_model extends CI_Model {
	var $user_facebook_id = '';
	var $app_install_id = '';
	var $user_apps_register_date = '';
	var $user_apps_last_seen = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get app users
	 * @param $app_install_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_app_users_by_app_install_id($app_install_id = NULL){
		$this->db->join('user','user.user_id=user_apps.user_id');
		return $this->db->get_where('user_apps',array('app_install_id'=>$app_install_id))->result_array();
	}
	
	/**
	 * Get user apps
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_apps_by_user_id($user_id = NULL){
		$this->db->join('user','user.user_id=user_apps.user_id');
		return $this->db->get_where('user_apps',array('user.user_id'=>$user_id))->result_array();
	}
	
	/**
	 * Adds user_app
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_app($data = array()){
		return $this -> db -> insert('user_apps', $data);
	}
	
	/**
	 * Removes user_app
	 * @param $user_id
	 * @param $app_install_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user_app($user_id = NULL, $app_install_id = NULL){
		$this->db->delete('user_apps', array('user_id' => $user_id, 'app_install_id' => $app_install_id));
		return $this->db->affected_rows();
	}
	
	function add($data = array()) {		
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('user_apps', $this);
		return $this->db->insert_id();
	}
	
	function add_new($user_facebook_id, $app_install_id){
		$this->db->from('user_apps');
		$this->db->where(array('user_facebook_id' =>$user_facebook_id , 'app_install_id' => $app_install_id));
	    if ($this->db->count_all_results() == 0) {
	      $this->db->insert('user_apps', array('user_facebook_id' => $user_facebook_id,
	      								'app_install_id' => $app_install_id,
	      								'user_apps_last_seen' => date ("Y-m-d H:i:s", time())));
		  return $this->db->insert_id();
	    }
		return FALSE;
	}
	
	function check_exist($user_facebook_id, $app_install_id){
		$this->db->from('user_apps');
		$this->db->where(array('user_facebook_id' =>$user_facebook_id , 'app_install_id' => $app_install_id));
		$count = $this->db->count_all_results();
		return ($count != 0);
	}

	function _get($where = array(), $limit = 0, $offset = 0) {
		$query = $this -> db -> get_where('user_apps', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('user_apps', $data, $where);
	}
	
	function update_user_last_seen($user_facebook_id, $app_install_id){
		$this->update(array('user_apps_last_seen' => date ("Y-m-d H:i:s", time())),
						array('user_facebook_id' => $user_facebook_id,
							 'app_install_id' => $app_install_id));
	}

	function delete($user_facebook_id, $app_install_id) {
		$this -> db -> delete('user_apps', array('user_facebook_id' =>$user_facebook_id, 'app_install_id' => $app_install_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('user_apps');
	}

	function get_user_apps_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_user_apps_list_by_app_install_id($app_install_id, $limit = 20, $offset = 0) {
		return $this -> _get( array('app_install_id' => $app_install_id), $limit, $offset);
	}
	
	function get_user_apps_in_time_range($time_pass = 0, $limit = 1000, $offset = 0){
		return $this -> _get( array('user_apps_last_seen >' => date ("Y-m-d H:i:s", time() - $time_pass)), $limit, $offset);
	}
}