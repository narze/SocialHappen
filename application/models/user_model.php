<?php
class User_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get user profile
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_id($user_id =NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$user = $this -> db -> get_where('user', array('user_id' => $user_id)) -> row_array();
		unset($user['user_password']);
		return $this->socialhappen->map_one_v($user, 'user_gender');
	}

	/**
	 * DEPRECATED
	 * Get user id by user_facebook_id
	 * @param $user_facebook_id
	 * @author Wachiraph C.
	 */
	function get_user_id($user_facebook_id =NULL) {
		$result = $this -> db ->select('user_id') -> get_where('user', array('user_facebook_id' => $user_facebook_id))-> result_array();
		return issetor($result[0]);
	}

	/**
	 * Get user id
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function get_user_id_by_user_facebook_id($user_facebook_id =NULL){
		$user = $this -> db -> select('user_id') -> get_where('user', array('user_facebook_id' => $user_facebook_id)) -> result_array();
		return issetor($user[0]['user_id']);
	}

	/**
	 * Get user facebook id
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_user_facebook_id_by_user_id($user_id =NULL){
		$user = $this -> db -> select('user_facebook_id') -> get_where('user', array('user_id' => $user_id)) -> result_array();
		return issetor($user[0]['user_facebook_id']);
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
		if(!$data || $this->check_exist(issetor($data['user_facebook_id']))){
			return FALSE;
		}
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
		return $this->db->affected_rows() == 1;
	}

	/**
	 * Get user profile
	 * @param $user_facebook_id
	 * @author Manassarn M.
	 */
	function get_user_profile_by_user_facebook_id($user_facebook_id =NULL) {
		$user = $this -> db -> get_where('user', array('user_facebook_id' => $user_facebook_id)) -> row_array();
		unset($user['user_password']);
		return $this->socialhappen->map_one_v($user, 'user_gender');
	}

	/**
	 * Get all user profile
	 * @param $limit
	 * @param $offset
	 * @author Metwara Narksook
	 */
	function get_all_user_profile($limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$result = $this->db->get_where('user',array())->result_array();
		return $this->socialhappen->map_v($result, 'user_gender');
	}


	/**
	 * Try to insert new user by user's facebook id
	 * @param $user_facebook_id
	 * @return user_id on successful, False otherwise
	 * @author Wachiraph C. - revise May 2011
	 */
	function add_by_facebook_id($user_facebook_id) {
		$this -> db -> from('user');
		$this -> db -> where('user_facebook_id', $user_facebook_id);
		if($this -> db -> count_all_results() == 0) {
			$this -> db -> insert('user', array('user_facebook_id' => $user_facebook_id));
			return $this -> db -> insert_id();
		}
		return FALSE;
	}

	/**
	 * Check if user is existed
	 * @param $user_facebook_id
	 * @return TRUE if user exists
	 * @author Wachiraph C. - revise May 2011
	 */
	function check_exist($user_facebook_id) {
		$this -> db -> from('user');
		$this -> db -> where( array('user_facebook_id' => $user_facebook_id));
		$count = $this -> db -> count_all_results();
		return ($count != 0);
	}

	/**
	 * Count users
	 * @author Manassarn M.
	 */
	function count_users(){
		return $this->db->count_all_results('user');
	}

	/**
	 * Count users
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function count_users_by_app_install_id($app_install_id = NULL){
		$this->db->where(array('app_install_id' => $app_install_id));
		$this -> db -> join('user_apps', 'user_apps.user_id=user.user_id');
		return $this->db->count_all_results('user');
	}

	/**
	 * Count users
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function count_users_by_campaign_id($campaign_id = NULL){
		$this->db->where(array('campaign_id' => $campaign_id));
		$this -> db -> join('user_campaigns', 'user_campaigns.user_id=user.user_id');
		return $this->db->count_all_results('user');
	}

	/**
	 * Update user
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function update_user($user_id = NULL, $data = array()){
		return $this->db->update('user', $data, array('user_id' => $user_id));
	}

	/**
	 * Update user last seen
	 * @param $user_id
	 * @author Wachiraph C. - revise May 2011
	 * @author Manassarn M. - Fix bugs
	 */
	function update_user_last_seen($user_id) {
	  date_default_timezone_set('UTC');
		return $this -> db -> update('user', array('user_last_seen' => date("Y-m-d H:i:s", time())), array('user_id' => $user_id));
	}

	function findOne($where) {
		$user = $this->db->get_where('user', $where)->row_array();
		unset($user['user_password']);
		return $user;
	}
}
