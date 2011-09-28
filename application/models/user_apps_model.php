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
	function get_app_users_by_app_install_id($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->join('user','user.user_id=user_apps.user_id');
		$result = $this->db->get_where('user_apps',array('app_install_id'=>$app_install_id))->result_array();
		return $this->socialhappen->map_v($result, 'user_gender');
	}
	
	/**
	 * Get user apps
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_apps_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->join('user','user.user_id=user_apps.user_id');
		$this->db->join('installed_apps','user_apps.app_install_id=installed_apps.app_install_id');
		$this->db->join('app','installed_apps.app_id=app.app_id');
		$result = $this->db->get_where('user_apps',array('user.user_id'=>$user_id))->result_array();
		return $this->socialhappen->map_v($result, array('app_type','app_install_status','user_gender'));
	}
	
	/**
	 * Count user apps
	 * @param $user_id
	 * @return int
	 * @author Manassarn M.
	 */
	function count_user_apps_by_user_id($user_id = NULL){
		$this->db->join('user','user.user_id=user_apps.user_id');
		$this->db->join('installed_apps','user_apps.app_install_id=installed_apps.app_install_id');
		$this->db->join('app','installed_apps.app_id=app.app_id');
		return $this->db->where(array('user.user_id'=>$user_id))->count_all_results('user_apps');
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
	
	/**
	 * Insert new user_apps record
	 * @param $user_id
	 * @param $app_install_id
	 * @return FALSE on failed
	 * @author Wachiraph C. - revise May 2011
	 */
	function add_new($user_id, $app_install_id){
		$this->db->from('user_apps');
		$this->db->where(array('user_id' =>$user_id , 'app_install_id' => $app_install_id));
	    if ($this->db->count_all_results() == 0) {
	      $this->db->insert('user_apps', array('user_id' => $user_id,
	      								'app_install_id' => $app_install_id,
	      								'user_apps_last_seen' => date ("Y-m-d H:i:s", time())));
		  return $this->db->insert_id();
	    }
		return FALSE;
	}
	
	/**
	 * Check if user is existed and belonged app_install_id
	 * @param $user_id
	 * @param $app_install_id
	 * @return TRUE if user exists
	 * @author Wachiraph C. - revise May 2011
	 */
	function check_exist($user_id, $app_install_id){
		$this->db->from('user_apps');
		$this->db->where(array('user_id' =>$user_id , 'app_install_id' => $app_install_id));
		$count = $this->db->count_all_results();
		return ($count != 0);
	}
	
	/**
	 * Update last seen date
	 * @param $user_id
	 * @param $app_install_id
	 * @author Wachiraph C. - revise May 2011
	 * @author Manassarn M. - Fix bugs
	 */
	function update_user_last_seen($user_id, $app_install_id){
		return $this->db->update('user_apps', array('user_apps_last_seen' => date ("Y-m-d H:i:s", time())),
						array('user_id' => $user_id,
							 'app_install_id' => $app_install_id));
	}
	
	/**
	 * Count user apps
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function count_user_apps_by_user_id_and_page_id($user_id = NULL, $page_id = NULL){
		$this->db->where(array('page_id' => $page_id, 'user_id' => $user_id));
		$this->db->join('installed_apps','installed_apps.app_install_id=user_apps.app_install_id');
		return $this->db->count_all_results('user_apps');
	}
}