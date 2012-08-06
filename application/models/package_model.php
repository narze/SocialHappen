<?php

/**
 * Package_model
 */

class Package_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function get_packages(){
		$this->db->order_by("package_id", "asc");
		return  $this->db->get('package')->result_array();
	}

	function get_package_by_package_id($package_id = NULL){
		$result = $this->db->get_where('package', array('package_id'=>$package_id))->result_array();
		return issetor($result[0], NULL);
	}

	function get_free_package(){
		$result = $this->db->get_where('package', array('package_price'=>0))->result_array();
		return issetor($result[0], NULL);
	}

	function add_package($data = array()){
		if(!$data) {
			return FALSE;
		}
		$this -> db -> insert('package', $data);
		return $this->db->insert_id();
	}

	function update_package_by_package_id($package_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('package', $data, array('package_id' => $package_id));
	}

	function remove_package($package_id = NULL){
		$this->db->delete('package', array('package_id' => $package_id));
		return $this->db->affected_rows() == 1;
	}

	function count_packages(){
		return $this->db->count_all_results('package');
	}

	function is_upgradable($package_id = NULL) {
		$this->db->select_max('package_price');
		$result = $this->db->get('package')->row_array();
		$max_price = $result['package_price'];
		$current_package = $this->get_package_by_package_id($package_id);
		return $current_package['package_price'] < $max_price;
	}

	function is_the_most_expensive($package_id = NULL){
		if(!isset($package_id)){
			return FALSE;
		}
		return !$this->is_upgradable($package_id);
	}
}

/* End of file package_model.php */
/* Location: ./application/models/package_model.php */
