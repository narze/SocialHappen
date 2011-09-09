<?php

/**
 * Package_apps_model
 */

class Package_apps_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Add package app
	 * @param array $data
	 * @author Manassarn M.
	 */
	function add_package_app($data = array()){
		if(!$data){
			return FALSE;
		}
		return $this -> db -> insert('package_apps', $data);
	}
	
	/**
	 * Get apps
	 * @param $package_id
	 * @author Manassarn M.
	 */
	function get_apps_by_package_id($package_id = NULL){
		$this->db->join('app', 'app.app_id=package_apps.app_id');
		$result = $this->db->get_where('package_apps', array('package_id' => $package_id))->result_array();
		return $this->socialhappen->map_v($result, 'app_type');
	}
	
	/**
	 * Get package apps
	 * @param $package_id
	 * @author Manassarn M.
	 */
	function get_package_apps_by_package_id($package_id = NULL){
		return $this->db->get_where('package_apps', array('package_id' => $package_id))->result_array();
	}
	
	/**
	 * Remove package app
	 * @param $app_id
	 * @author Manassarn M.
	 */
	function remove_package_app_by_app_id($app_id = NULL){
		$this->db->delete('package_apps', array('app_id' => $app_id));
		return $this->db->affected_rows() == 1;
	}
}
/* End of file package_apps_model.php */
/* Location: ./application/models/package_apps_model.php */