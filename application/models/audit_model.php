<?php
/**
 * audit model class for audit object
 * @author Metwara Narksook
 */
class Audit_model extends CI_Model {
	var $_id = '';
	var $timestamp = '';
	var $subject = '';
	var $action = '';
	var $object = '';
	var $objecti = '';
	var $type = '';
	
	function __construct() {
		parent::__construct();
		
		// connect to database
		$this->connection = new Mongo('localhost:27017');
		
		// select audit database
		$this->db = $this->connection->audit;
		
		// select audit collection
		$this->audits = $this->db->audits;
	}
	
	function add_audit($data = array()){
		$data['timestamp'] = time();
		// add new 
		$this->audits->insert($data);
	}
	
	function list_audit($limit, $offset){
		
	}
	
	function list_recent_audit($limit = 100){
		return $this->audits->find()->limit($limit);
	}
	
}

/* End of file audit_model.php */
/* Location: ./application/models/audit_model.php */