<?php
/**
 * audit stats model class for audit object
 * @author Metwara Narksook
 */
class Audit_stats_model extends CI_Model {

	var $timestamp = '';
	var $user_id = '';
	var $action_no = '';
	var $app_install_id = '';
	var $campaign_id = '';
	
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
		$this->stats = sh_mongodb_load( array(
			'collection' => 'audit_stats'
		));
	}
	
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->stats->deleteIndexes() 
			&& $this->stats->ensureIndex(array('timestamp' => -1,
										 'user_id' => 1,
										 'app_install_id' => 1));
	}
	
	/**
	 * add new audit stat entry to database
	 * 
	 * @param data array of attribute to be added
	 * [user_id, action_no, app_install_id, campaign_id]
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_stat($data = array()){
		$check_args = isset($data['user_id']) && isset($data['action_no'])
		&& isset($data['app_install_id']);
		
		if($check_args){
			date_default_timezone_set('UTC');
			$data_to_add = array('timestamp' => time());
			$data_to_add = array_merge($data_to_add, $data);
			// add new 
			$result = $this->stats->insert($data_to_add);
			return $result;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * list audit stat data by input criteria to query
	 * 
	 * @param criteria array of attribute to query
	 * @param limit int number of results
	 * @param offset int offset number
	 * 
	 * @return result
	 * 
	 * @author Metwara Narksook
	 */
	function list_stat($criteria = array(), $limit = NULL, $offset = 0){
		if(empty($limit)){
			$limit = $this->DEFAULT_LIMIT;
		}
		
		$res = $this->stats->find($criteria)->sort(array('timestamp' => -1, '_id' => -1))->skip((int)$offset)->limit($limit);
		
		$result = array();
		foreach ($res as $audit) {
			$result[] = $audit;
		}
		return $result;
	}
	
	/**
	 * count stat by criteria
	 * @param criteria array
	 * 
	 * @return number
	 * @author Metwara Narksook
	 */
	function count_stat($criteria = array()){
		$result = $this->stats->count($criteria);
		return $result;
	}
	
	/**
	 * remove stat by criteria
	 * @param criteria array
	 * 
	 * @return boolean
	 * @author Metwara Narksook
	 */
	function remove($criteria = array()){
		if(empty($criteria)){
			return FALSE;
		}else{
			return $this->stats->remove($criteria);
		}
	}
	
	/**
	 * drop entire collection
	 * you will lost all audit data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		$this->stats->drop();
	}
}

/* End of file audit_stats_model.php */
/* Location: ./application/models/audit_stats_model.php */