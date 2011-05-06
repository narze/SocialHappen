<?php
class Cron_job_model extends CI_Model {
	var $job_id = '';
	var $job_name = '';
	var $job_start = '';
	var $job_finish = '';
	var $job_status = '';
	
	function __construct() {
		parent::__construct();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('cron_job', $this);
		return $this->db->insert_id();
	}
	
	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('cron_job', $where, $limit, $offset);
		return $query -> result();
	}
	
	function update($data = array(), $where = array()) {
		$this -> db -> update('cron_job', $data, $where);
	}
	
	function delete($id) {
		$this -> db -> delete('cron_job', array('job_id' => $id));
	}
	
	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('cron_job');
	}
	
	function get_job_list($limit = 20, $offset = 0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_job_by_id($job_id){
		$job = $this -> _get( array('job_id'=>$job_id), 1, 0);
		if(isset($job) && count($job) >= 1)
			return $job[0];
		else
			return NULL;
	}
	
	function set_job_status($job_id, $status = "running"){
		$this->update($job_id, $status);
	}
	
	function start_job($job_id){
		$this->update(array('job_start' => date ("Y-m-d H:i:s", time()),
										'job_status' => 'running')
								, array('job_id' => $job_id));
	}
	
	function finish_job($job_id){
		$this->update(array('job_finish' => date ("Y-m-d H:i:s", time()),
										'job_status' => 'finish')
								, array('job_id' => $job_id));
	}
}