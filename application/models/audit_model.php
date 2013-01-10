<?php
/**
 * audit model class for audit object
 * @author Metwara Narksook
 */
class Audit_model extends CI_Model {

	// optional data
	var $app_id = '';
	var $user_id = '';
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
		$this->DEFAULT_LIMIT = 0;
		$this->load->helper('mongodb');
		$this->collection = sh_mongodb_load( array(
			'collection' => 'audits'
		));
	}

	/**
	 * create index for collection
	 *
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array('timestamp' => -1,
											 'action_id' => 1,
											 'app_id' => 1,
											 'app_install_id' => 1,
											 'user_id' => 1,
											 'campaign_id' => 1,
											 'page_id' => 1,
											 'company_id' => 1));

	}

	/**
	 * add new audit entry to database
	 *
	 * @param data array of attribute to be added
	 *
	 * @return audit_id
	 *
	 * @author Metwara Narksook
	 */
	function add_audit($data = array()){
		$check_args = isset($data['action_id']);
		if($check_args){
			date_default_timezone_set('UTC');
			$time = isset($data['timestamp']) ? $data['timestamp'] : time();
			$data_to_add = array('timestamp' => $time);
			$data_to_add = array_merge($data_to_add, $data);
			// add new
			return $this->add($data_to_add);
		}else{
			return FALSE;
		}

	}

	function add($data = array())	{
		try	{
			$this->collection->insert($data, array('safe' => TRUE));
			return ''.$data['_id'];
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}

	function get($query, $sort = -1, $limit = NULL){
		$result = $this->collection->find($query)->sort(array('_id'=> $sort))->limit($limit);
		return cursor2array($result);
	}

	function getOne($query){
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}

	function update($query, $data) {
	  try {
	    $update_result = $this->collection->update($query, $data, array('safe' => TRUE));
	    return isset($update_result['n']) && ($update_result['n'] > 0);
	  } catch(MongoCursorException $e){
	    log_message('error', 'Mongodb error : '. $e);
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
	function list_audit($criteria = array(), $limit = NULL, $offset = 0, $sort = NULL) {
		if(empty($limit)){
			$limit = $this->DEFAULT_LIMIT;
		}

		foreach($criteria as $key => $value){
			if(preg_match('/(_id)$/i',$key) && !is_array($value)){
				$criteria[$key] = (int) $value;
			}
		}

		if(!$sort || !is_array($sort)) {
			$sort = array('timestamp' => -1, '_id' => -1);
		}
		$res = $this->collection->find($criteria)->sort($sort)->skip($offset)->limit($limit);

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
		$res = $this->collection->find()->sort(array('timestamp' => -1, '_id' => -1))->limit($limit);

		$result = array();
		foreach ($res as $audit) {
			$result[] = $audit;
		}
		return $result;
	}

	function _get_start_day_time($timestamp = NULL){
		date_default_timezone_set('UTC');
		$start = mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $start;
	}

	function _get_end_day_time($timestamp = NULL){
		date_default_timezone_set('UTC');
		$end = mktime(24, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $end;
	}

	function count_distinct_audit($key = NULL, $criteria = NULL, $start_date = NULL, $end_date = NULL){
		$check_args = isset($criteria) && isset($start_date);
		if(!$check_args){
			return NULL;
		}

		$start_time = $this->_get_start_day_time($start_date);
		if(isset($end_date)){
			$end_time = $this->_get_end_day_time($end_date);
		}else{
			$end_time = $this->_get_end_day_time($start_date);
		}

		$criteria['timestamp'] = array('$gte' => $start_time, '$lt' => $end_time);

		// unset($criteria['user_id']);
		// unset($criteria['company_id']);
		// unset($criteria['action_id']);
		// unset($criteria['timestamp']);
		// unset($criteria['app_id']);
// debug_print_backtrace();
		// echo 'count_distinct_audit criteria<pre>';
		// var_dump($criteria);
		// echo '</pre>';

		//Distinct count
		if($key) {
			$cursor = $this->mongo_db->command(array('distinct' => 'audits', 'key' => $key, 'query' => $criteria));
			$result = array();
			foreach ($cursor as $audit) {
				$result[] = $audit;
			}
			return count($result[0]);
		} else { //Non-distinct count
			$result = array();
			$cursor = $this->collection->find($criteria);
			foreach ($cursor as $audit) {
				$result[] = $audit;
			}
			return count($result);
		}
	}


  function list_distinct_audit($key = NULL, $criteria = NULL){
    $check_args = isset($key) && isset($criteria);
    if(!$check_args){
      return NULL;
    }

    $db_criteria = array();
    if(isset($criteria['subject'])){
      $db_criteria['subject'] = $criteria['subject'];
    }

    if(isset($criteria['object'])){
      $db_criteria['object'] = $criteria['object'];
    }

    if(isset($criteria['objecti'])){
      $db_criteria['objecti'] = $criteria['objecti'];
    }

    if(isset($criteria['app_id'])){
      $db_criteria['app_id'] = $criteria['app_id'];
    }
    if(isset($criteria['action_id'])){
      $db_criteria['action_id'] = $criteria['action_id'];
    }
    if(isset($criteria['app_install_id'])){
      $db_criteria['app_install_id'] = $criteria['app_install_id'];
    }
    if(isset($criteria['user_id'])){
      $db_criteria['user_id'] = $criteria['user_id'];
    }
    if(isset($criteria['page_id'])){
      $db_criteria['page_id'] = $criteria['page_id'];
    }
    if(isset($criteria['campaign_id'])){
      $db_criteria['campaign_id'] = $criteria['campaign_id'];
    }

    // construct map and reduce functions
		$map = new MongoCode("function() { emit(this.".$key.",this.timestamp); }");
		$reduce = new MongoCode("function(k, vals) { ".
		    "var max = vals[0];".
		    "for (var i in vals) {".
		    	"if(vals[i] > max)".
		      	"max = vals[i];".
		      "}".
		    "}".
		    "return max; }");

		$cursor = $this->mongo_db->command(array(
		    "mapreduce" => "audits",
		    "map" => $map,
		    "reduce" => $reduce,
		    "query" => count($db_criteria) == 0 ? NULL : $db_criteria,
		    "out" => array("inline" => 1)));

    $result = array();
    foreach ($cursor as $audit) {
      $result[] = $audit;
    }

		$out_array = $result[0];

		// if something wrong from database query, just return
		if(!is_array($out_array)){
			return array();
		}

		/*
		 * sort output by timestamp
		 */
		function cmp($a, $b) {
	    if ($a['value'] == $b['value']) {
	        return 0;
	    }
	    return ($a['value'] < $b['value']) ? 1 : -1;
		}
		uasort($out_array, 'cmp');

		/*
		 * filter result array
		 */
    $out_array = array_map(function($a){ return $a['_id']; }, $out_array);

		/*
		 * correct array keys
		 */
    return array_values($out_array);
  }

	/**
	 * drop entire collection
	 * you will lost all audit data
	 *
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		$this->collection->drop();
	}

	function count($criteria = array()) {
		return $this->collection->count($criteria);
	}
}

/* End of file audit_model.php */
/* Location: ./application/models/audit_model.php */