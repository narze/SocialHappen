<?php
class App_statistic_model extends CI_Model {
	var $app_install_id = '';
	var $job_time = '';
	var $job_id = '';
	var $active_user = '';
	
	function __construct() {
		parent::__construct();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('app_statistic', $this);
		return $this->db->insert_id();
	}
	
	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('app_statistic', $where, $limit, $offset);
		return $query -> result();
	}
	
	function update($data = array(), $where = array()) {
		$this -> db -> update('app_statistic', $data, $where);
	}
	
	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('app_statistic');
	}
	
	function get_statistic_list($limit = 20, $offset = 0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_statistic_by_app_install_id($app_install_id, $limit = 20, $offset = 0){
		return $this -> _get( array('app_install_id'=>$app_install_id, 'job_id' => '1'), $limit, $offset);
	}
	
	function get_day_statistic_by_app_install_id($app_install_id, $limit = 20, $offset = 0){
		return $this -> _get( array('app_install_id'=>$app_install_id, 'job_id' => '2'), $limit, $offset);
	}
}