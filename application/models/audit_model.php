<?php
/**
 * audit model class for audit object
 * @author Metwara Narksook
 */
class Audit_model extends CI_Model {

	// optional data
	var $app_id = '';
	var $app_install_id = '';
	var $campaign_id = '';
	var $page_id = '';
	var $company_id = '';
	// basic data
	var $timestamp = '';
	var $subject = '';
	var $action_id = '';
	var $object = '';
	var $objecti = '';
	
	
	var $DEFAULT_LIMIT;
	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		
		// initialize value
		$this->DEFAULT_LIMIT = 0;
		
		// connect to database
		$this->connection = new Mongo();
		
		// select audit database
		$this->db = $this->connection->audit;
		
		// select audit collection
		$this->audits = $this->db->audits;
	}
	
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		$this->audits->ensureIndex(array('timestamp' => -1,
										 'action_id' => 1,
										 'app_id' => 1,
										 'app_install_id' => 1,
										 'campaign_id' => 1,
										 'page_id' => 1,
										 'company_id' => 1));
	}
	
	/**
	 * add new audit entry to database
	 * 
	 * @param data array of attribute to be added
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_audit($data = array()){
		$check_args = isset($data['action_id']);
		if($check_args){
			date_default_timezone_set('Asia/Bangkok');
			$data_to_add = array('timestamp' => time());
			$data_to_add = array_merge($data_to_add, $data);
			// add new 
			$this->audits->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
		
	}
	
	/**
	 * list audit data by input criteria to query
	 * 
	 * @param criteria array of attribute to query
	 * @param limit int number of results
	 * @param offset int offset number
	 * 
	 * @return result
	 * 
	 * @author Metwara Narksook
	 */
	function list_audit($criteria = array(), $limit = NULL, $offset = 0){
		if(empty($limit)){
			$limit = $this->DEFAULT_LIMIT;
		}
		$res = $this->audits->find($criteria)->sort(array('timestamp' => -1, '_id' => -1))->skip($offset)->limit($limit);
		
		$result = array();
		foreach ($res as $audit) {
			$result[] = $audit;
		}
		return $result;
	}
	
	/**
	 * list recent audit entry
	 * 
	 * @param limit number of entries to get
	 * 
	 * @return result array of audit
	 * 
	 * @author Metwara Narksook
	 */
	function list_recent_audit($limit = NULL){
		if(empty($limit)){
			$limit = $this->DEFAULT_LIMIT;
		}
		$res = $this->audits->find()->sort(array('timestamp' => -1, '_id' => -1))->limit($limit);
		
		$result = array();
		foreach ($res as $audit) {
			$result[] = $audit;
		}
		return $result;
	}
	
	function _get_start_day_time($timestamp = NULL){
		date_default_timezone_set('Asia/Bangkok');
		$start = mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $start;
	}
	
	function _get_end_day_time($timestamp = NULL){
		date_default_timezone_set('Asia/Bangkok');
		$end = mktime(24, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $end;
	}
	
	function count_distinct_audit($key = NULL, $criteria = NULL, $date = NULL){
		$check_args = isset($key) && isset($criteria);
		if(!$check_args){
			return NULL;
		}
		$db_criteria = array();
		if(isset($criteria['app_id'])){
			$db_criteria['app_id'] = $criteria['app_id'];
		}
		if(isset($criteria['action_id'])){
			$db_criteria['action_id'] = $criteria['action_id'];
		}
		if(isset($criteria['app_install_id'])){
			$db_criteria['app_install_id'] = $criteria['app_install_id'];
		}
		if(isset($criteria['page_id'])){
			$db_criteria['page_id'] = $criteria['page_id'];
		}
		if(isset($criteria['campaign_id'])){
			$db_criteria['campaign_id'] = $criteria['campaign_id'];
		}
		
		$start_time = $this->_get_start_day_time($date);
		$end_time = $this->_get_end_day_time($date);
		
		$db_criteria['timestamp'] = array('$gte' => $start_time, '$lt' => $end_time);
		//echo 'count_distinct_audit criteria<pre>';
		//var_dump($db_criteria);
		//echo '</pre>';
		
		
		$cursor = $this->db->command(array('distinct' => 'audits', 'key' => $key, 'query' => $db_criteria));
		$result = array();
		foreach ($cursor as $audit) {
			$result[] = $audit;
		}
		 
		return count($result[0]);
	}
	
	/**
	 * drop entire collection
	 * you will lost all audit data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		$this->audits->drop();
	}
}

/* End of file audit_model.php */
/* Location: ./application/models/audit_model.php */