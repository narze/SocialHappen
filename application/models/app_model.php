<?php
class App_model extends CI_Model {
	var $app_id = '';
	var $app_name = '';
	var $app_maintainance = '';
	var $app_show_in_list = '';
	var $app_description = '';
	var $app_secret_key = '';
	var $app_url = '';
	var $app_install_url = '';
	var $app_config_url = '';
	var $app_support_page_tab = '';

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

	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('app', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('app', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('app', $data, $where);
	}

	function delete($id) {
		$this -> db -> delete('app', array('app_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('app');
	}

	function get_app_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_app($app_id,$limit =20, $offset =0){
		return $this -> _get( array('app_id'=>$app_id), $limit, $offset);
	}
	
}