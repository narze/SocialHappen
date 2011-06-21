<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_action_type_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('Audit_action_type_model','audit_action_types');
	}

	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * Tests get_audit_action_name_by_type_id()
	 * @author Wachiraph C.
	 */
	function get_audit_action_name_by_type_id_test(){
		$result = $this->audit_action_types->get_audit_action_by_type_id(1);
		
		$this->unit->run($result,'is_array', 'get_campaign_users_by_campaign_id()');
		$this->unit->run($result['audit_action_id'] ==1 ,'is_true', 'correct id');		
	}
	
	/**
	 * Tests get_active_audit_action_list()
	 * @author Wachiraph C.
	 */
	function get_active_audit_action_list_test(){
		$result = $this->audit_action_types->get_active_audit_action_list();
		
		$this->unit->run($result,'is_array', 'get_active_audit_action_list()');	
		$this->unit->run(sizeof($result)>0,'is_true', 'audit_action_type is not empty');	
	}
	
	/**
	 * Tests add_audit_auction_type()
	 * @author Wachiraph C.
	 */
	function add_audit_auction_type_test(){
		$data = array(
					'audit_action_id' => rand(300,5000),
					'audit_action_name' => 'Testing',
					'audit_action_active' => rand(0,1)
				);
		$result = $this->audit_action_types->add_audit_auction_type($data);
		
		$this->unit->run($result,'is_true', 'add_audit_auction_type()');	
	}
	
	/**
	 * Tests update_audit_action_type_by_id()
	 * @author Wachiraph C.
	 */
	function update_audit_action_type_by_id_test(){
		$data = array(
					'audit_action_name' => 'Changed',
					'audit_action_active' => rand(0,1)
				);
		$result = $this->audit_action_types->update_audit_action_type_by_id(1, $data);
		print_r($result);
		$this->unit->run($result,'is_true', 'update_audit_action_type_by_id()');	
	}
	
}
/* End of file audit_action_type_test.php */
/* Location: ./application/controllers/test/audit_action_type_test.php */