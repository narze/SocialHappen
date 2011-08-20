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
		$this->db->join('app_type', 'app.app_type_id = app_type.app_type_id');
		return $this->db->get('app')->result_array();
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
		return issetor($result[0]);				
	}
	
	/**
	 * Get application profile by fb_app_api_key
	 * @param $fb_app_api_key
	 * @author Prachya P.
	 */
	function get_app_by_api_key($fb_app_api_key = NULL){
		$result = $this->db->get_where('app', array('app_facebook_api_key' => $fb_app_api_key))->result_array();
		return issetor($result[0]);				
	}
}