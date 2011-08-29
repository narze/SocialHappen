<?php
class App_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get all apps
	 * @author Manassarn M.
	 */
	function get_all_apps(){
		return $this->socialhappen->map_v($this->db->get('app')->result_array(), 'app_type');
	}
	
	/**
	 * Adds app
	 * @param array $data
	 * @author Manassarn M.
	 */
	function add_app($data = array()){
		$this -> db -> insert('app', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Removes app
	 * @param $app_id
	 * @author Manassarn M.
	 */
	function remove_app($app_id = NULL){
		$this->db->delete('app', array('app_id' => $app_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get application profile
	 * @param $app_id
	 * @author Wachiraph C.
	 */
	function get_app_by_app_id($app_id = NULL){
		$result = $this->db->get_where('app', array('app_id' => $app_id))->result_array();
		return $this->socialhappen->map_one_v($result[0], 'app_type');
	}
	
	/**
	 * Get application profile by fb_app_api_key
	 * @param $fb_app_api_key
	 * @author Prachya P.
	 */
	function get_app_by_api_key($fb_app_api_key = NULL){
		$result = $this->db->get_where('app', array('app_facebook_api_key' => $fb_app_api_key))->result_array();
		return $this->socialhappen->map_one_v($result[0], 'app_type');			
	}
	
	/**
	 * Update app
	 * @param $app_id
	 * @param $data
	 * @author Manassarn M.
	 */
	function update_app_by_app_id($app_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('app', $data, array('app_id' => $app_id));
	}
}