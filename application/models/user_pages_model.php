<?php
class User_pages_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Adds user page
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_page($data = array()){
		if(!$data){
			return FALSE;
		}
		return $this -> db -> insert('user_pages', $data);
	}
	
	/**
	 * Removes user_page
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function remove_user_page($user_id = NULL, $page_id = NULL){
		$this->db->delete('user_pages', array('user_id' => $user_id, 'page_id' => $page_id));
		return $this->db->affected_rows() == 1;
	}
	
	/**
	 * Get user pages
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_pages_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->join('page','user_pages.page_id=page.page_id');
		$this->db->join('user_role', 'user_pages.user_role = user_role.user_role_id','left');
		$result = $this->db->get_where('user_pages', array('user_id' => $user_id))->result_array();
		return $this->socialhappen->map_v($result, array('page_status'));
	}
	
	/**
	 * Get page users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id($page_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->join('user', 'user_pages.user_id = user.user_id');
		$this->db->join('user_role', 'user_pages.user_role = user_role.user_role_id','left');
		$result = $this->db->get_where('user_pages', array('page_id' => $page_id))->result_array();
		return $this->socialhappen->map_v($result, array('user_gender'));
	}
	
	/**
	 * Check if user is page admin
	 * @param $user_id
	 * @param $page_id
	 */
	function is_page_admin($user_id = NULL, $page_id = NULL){
		$this->db->where(array('user_id' => $user_id, 'page_id' => $page_id));
		$count = $this->db->count_all_results('user_pages');
		return $count == 1;
	}
}
/* End of file user_pages_model.php */
/* Location: ./application/models/user_pages_model.php */