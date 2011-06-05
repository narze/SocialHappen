<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_model_test extends CI_Controller {
	
	var $audit;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('audit_model','audit');
	}

	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->audit))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->audit->drop_collection();
	}
	
	function create_index_test(){
		$this->audit->create_index();
	}
	
	/**
	 * 	// optional data
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
	 */
	function add_audit_test(){
		$data = array('subject' => 'a',
					  'action_id' => 0,
					  'object' => 'b',
					  'app_id' => 1);
		$result = $this->audit->add_audit($data);
		$this->unit->run($result, 'is_true', 'add audit', print_r($data, TRUE));
		
		$data = array('subject' => 'a',
					  'action_id' => 0,
					  'object' => 'c',
					  'app_id' => 1);
		$result = $this->audit->add_audit($data);
		$this->unit->run($result, 'is_true', 'add audit', print_r($data, TRUE));
		
		$data = array('subject' => 'a',
					  'action_id' => 1,
					  'object' => 'b',
					  'app_id' => 1);
		$result = $this->audit->add_audit($data);
		$this->unit->run($result, 'is_true', 'add audit', print_r($data, TRUE));
		
		$data = array('subject' => 'a',
					  'action_id' => 1,
					  'object' => 'c',
					  'app_id' => 2);
		$result = $this->audit->add_audit($data);
		$this->unit->run($result, 'is_true', 'add audit', print_r($data, TRUE));
		
		$data = array('subject' => 'a',
					  'object' => 'c',
					  'app_id' => 2);
		$result = $this->audit->add_audit($data);
		$this->unit->run($result, 'is_false', 'add audit fail - missing action_id', print_r($data, TRUE));
		
		$result = $this->audit->add_audit();
		$this->unit->run($result, 'is_false', 'add audit fail - missing data', print_r($data, TRUE));
	}
	
	function list_recent_audit_test(){
		$result = $this->audit->list_recent_audit();
		$this->unit->run(count($result), 4, 'count added audit', '');
		
		$expected = array(2, 1, 1, 1);
		$match = TRUE;
		for ($i=0; $i < count($result); $i++) { 
			$match = $match && $result[$i]['app_id'] == $expected[$i];
		}
		$this->unit->run($match, 'is_true', 'match recent audit result', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$result = $this->audit->list_recent_audit(2);
		$this->unit->run(count($result), 2, 'count added audit', '');
		$match = TRUE;
		for ($i=0; $i < count($result); $i++) { 
			$match = $match && $result[$i]['app_id'] == $expected[$i];
		}
		$this->unit->run($match, 'is_true', 'match recent audit result limit 2', '<pre>' . print_r($result, TRUE) . '</pre>');
	}
	
	function list_audit_test(){
		$result = $this->audit->list_audit();
		$this->unit->run(count($result), 4, 'count audit', '');
		
		$expected = array(2, 1, 1, 1);
		$match = TRUE;
		for ($i=0; $i < count($result); $i++) { 
			$match = $match && $result[$i]['app_id'] == $expected[$i];
		}
		$this->unit->run($match, 'is_true', 'match audit result', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$result = $this->audit->list_audit(array(), 2);
		$this->unit->run(count($result), 2, 'count audit', '');
		$match = TRUE;
		for ($i=0; $i < count($result); $i++) { 
			$match = $match && $result[$i]['app_id'] == $expected[$i];
		}
		$this->unit->run($match, 'is_true', 'match audit result action_id 1 limit 2', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$result = $this->audit->list_audit(array('action_id' => 1));
		$this->unit->run(count($result), 2, 'count audit', '');
		$match = TRUE;
		$expected = array(2, 1);
		for ($i=0; $i < count($result); $i++) { 
			$match = $match && $result[$i]['app_id'] == $expected[$i];
		}
		$this->unit->run($match, 'is_true', 'match audit result action_id 1 limit 2', '<pre>' . print_r($result, TRUE) . '</pre>');
	}
	

	
	function end_test(){
		//$this->audit->drop_collection();
	}
}
/* End of file audit_model_test.php */
/* Location: ./application/controllers/test/audit_model_test.php */