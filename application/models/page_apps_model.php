<?php
class Page_apps_model extends CI_Model {
	var $facebook_page_id = '';
	var $app_install_id = '';

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get page apps
	 * @param $page_id
	 * @author Manassarn Manoonchai
	 */
	function get_page_apps($page_id = NULL){
		if(!$page_id) return array();
		return $this->db->get_where('page_apps', array('page_id' => $page_id))->result();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		
		$this -> db -> insert('page_apps', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('page_apps', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('page_apps', $data, $where);
	}

	function delete($app_install_id, $facebook_page_id) {
		$this -> db -> delete('page_apps', array('app_install_id' => $app_install_id, 'facebook_page_id' => $facebook_page_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('page_apps');
	}

	function get_page_apps_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_app_by_page($facebook_page_id,$limit = 20, $offset =0){
		return $this -> _get( array('facebook_page_id'=>$facebook_page_id), $limit, $offset);
	}
}