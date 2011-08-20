<?php
class Page_user_data_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get page user
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL) {
		$page_users = $this -> db -> get_where('page_user_data', array('user_id' => $user_id, 'page_id' => $page_id)) -> result_array();
		if(isset($page_users[0])){
			$page_user = $page_users[0];
			$page_user['user_data'] = json_decode($page_user['user_data'],TRUE);
			$users = $this->db->get_where('user', array('user_id'=>$user_id))->result_array();
			return array_merge($page_user, issetor($users[0],array()));
		}
		return NULL;
	}
	
	/**
	 * Get page users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id($page_id = NULL) {
		$this->db->join('user','user.user_id=page_user_data.user_id');
		$page_users = $this -> db -> get_where('page_user_data', array('page_id' => $page_id)) -> result_array();
		foreach($page_users as &$page_user){
			$page_user['user_data'] = json_decode($page_user['user_data'],TRUE);
		}
		unset($page_user);
		return $page_users;
	}
	
	/**
	 * Adds add_page_user
	 * @param array $data
	 * @return $user_id
	 * @author Manassarn M.
	 */
	function add_page_user($data = array()){
		if(!$data || !isset($data['user_id']) || !$data['user_id'] || !isset($data['page_id']) || !$data['page_id']){
			return FALSE;
		}
		if($this->get_page_user_by_user_id_and_page_id($data['user_id'], $data['page_id'])){
			return FALSE;
		}
		if(!$fields = $this->db->get_where('page',array('page_id'=> $data['page_id']))->result_array()){
			return FALSE;
		}
		if(!$users = $this->db->get_where('user',array('user_id'=> $data['user_id']))->result_array()){
			return FALSE;
		}
		
		$fields = explode(",", issetor($fields[0]['page_user_fields']));
		
		$processed_data = array();
		foreach ($fields as $field){
			$processed_data[$field] = issetor($data['user_data'][$field], '');
		}
		$data['user_data'] = json_encode($processed_data);
		return $this -> db -> insert('page_user_data', $data);
	}
	
	/**
	 * Update page user
	 * @param $user_id
	 * @param $page_id
	 * @param array $data
	 * @author Manassarn M.
	 */
	function update_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL, $data = array()){
		if(!$data || !$page_id || !$user_id || !$page_user = $this->get_page_user_by_user_id_and_page_id($user_id, $page_id)) {
			return FALSE;
		}
		$processed_data = array();
		$fields = $this->db->get_where('page',array('page_id'=> $page_id))->result_array();
		$fields = explode(",", issetor($fields[0]['page_user_fields']));
		foreach ($fields as $field){
			$processed_data[$field] = issetor($data[$field],$page_user['user_data'][$field]);
		}
		$data = array();
		$data['user_data'] = json_encode($processed_data);
		return $this->db->update('page_user_data', $data, array('user_id' => $user_id, 'page_id' => $page_id));
	}
	
	/**
	 * Removes page user
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function remove_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL){
		$this->db->delete('page_user_data', array('user_id' => $user_id, 'page_id' => $page_id));
		return $this->db->affected_rows()==1;
	}

	// /**
	 // * Check if user is existed
	 // * @param $user_id
	 // * @param $page_id
	 // * @return TRUE if user exists
	 // * @author Manassarn M.
	 // */
	// function check_exist($user_id = NULL, $page_id = NULL) {
		// $this -> db -> from('user');
		// $this -> db -> where( array('user_id' => $user_id, 'page_id' => $page_id));
		// $count = $this -> db -> count_all_results();
		// return ($count != 0);
	// }
	
	// /**
	 // * Count users
	 // * @param $page_id
	 // * @author Manassarn M.
	 // */
	// function count_users_by_page_id($page_id = NULL){
		// $this->db->where(array('page_id' => $page_id));
		// $this -> db -> join('user_apps', 'user_apps.user_id=user.user_id');
		// return $this->db->count_all_results('user');
	// }
}
/* End of file page_user_data_model.php */
/* Location: ./application/models/page_user_data_model.php */
