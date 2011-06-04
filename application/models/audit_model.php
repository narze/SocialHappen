<?php
/**
 * audit model class for audit object
 * @author Metwara Narksook
 */
class Audit_model extends CI_Model {

	// optional date
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
										 'action' => 1,
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
		$data['timestamp'] = time();
		// add new 
		$this->audits->insert($data);
		return TRUE;
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
		$res = $this->audits->find($criteria)->sort(array('timestamp' => -1))->skip($offset)->limit($limit);
		
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
		$res = $this->audits->find()->sort(array('timestamp' => -1))->limit($limit);
		
		$result = array();
		foreach ($res as $audit) {
			$result[] = $audit;
		}
		return $result;
	}
}

/* End of file audit_model.php */
/* Location: ./application/models/audit_model.php */