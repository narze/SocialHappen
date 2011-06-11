<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_action_model_test extends CI_Controller {
	
	var $audit_action;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('audit_action_model','audit_action');
	}

	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->audit_action))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->audit_action->drop_collection();
	}
	
	function create_index_test(){
		$this->audit_action->create_index();
	}
	
	function add_action_test(){
		$audit_action = array('app_id' => 0,
							  'action_id' => 1,
							  'description' => 'test audit action',
							  'stat_app' => TRUE,
							  'stat_page' => TRUE,
							  'stat_campaign' => TRUE);
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_true', 'add audit action full parameter 1', '');
		
		$audit_action = array('app_id' => 3,
							  'action_id' => 1,
							  'description' => 'test audit action',
							  'stat_app' => TRUE,
							  'stat_page' => TRUE,
							  'stat_campaign' => TRUE);
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_true', 'add audit action full parameter 2', '');
		
		$audit_action = array('app_id' => 3,
							  'action_id' => 2,
							  'description' => 'test audit action',
							  'stat_app' => TRUE,
							  'stat_page' => TRUE,
							  'stat_campaign' => TRUE);
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_true', 'add audit action full parameter 3', '');
		
		$audit_action = array('app_id' => 0,
							  'action_id' => 2,
							  'description' => 'test audit action');
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_true', 'add audit action', '');
		
		$audit_action = array('action_id' => 1,
							  'description' => 'test audit action',
							  'stat_app' => TRUE,
							  'stat_page' => TRUE,
							  'stat_campaign' => TRUE);
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_false', 'add audit action invalid parameter 1', '');
		
		$audit_action = array('app_id' => 0,
							  'action_id' => 1,
							  'stat_page' => TRUE,
							  'stat_campaign' => TRUE);
		$result = $this->audit_action->add_action($audit_action);
		$this->unit->run($result, 'is_false', 'add audit action invalid parameter 2', '');
	}
	
	function edit_action_test(){
		$app_id = 0;
		$action_id = 2;
		$data = array('description' => 'new description');
		$result = $this->audit_action->edit_action($app_id, $action_id, $data);
		$this->unit->run($result, 'is_true', 'edit audit action', '');
		
		$data = array('stat_app' => TRUE);
		$result = $this->audit_action->edit_action($app_id, $action_id, $data);
		$this->unit->run($result, 'is_true', 'edit audit action', '');
		
		$result = $this->audit_action->edit_action(NULL, $action_id, NULL);
		$this->unit->run($result, 'is_false', 'edit audit action invalid parameter 1', '');
		
		$result = $this->audit_action->edit_action(NULL, NULL, NULL);
		$this->unit->run($result, 'is_false', 'edit audit action invalid parameter 2', '');
	}
	
	function get_action_test(){
		$app_id = 0;
		$action_id = 1;
		$result = $this->audit_action->get_action($app_id, $action_id);
		$result = $result[0];
		$match = $result['app_id'] == $app_id && $result['action_id'] == $action_id
				 && $result['description'] == 'test audit action';
		$this->unit->run($match, 'is_true', 'get single audit action', '');
		
		$app_id = 0;
		$action_id = 2;
		$result = $this->audit_action->get_action($app_id, $action_id);
		$result = $result[0];
		$match = $result['app_id'] == $app_id && $result['action_id'] == $action_id
				 && $result['description'] == 'new description' && $result['stat_app'] == TRUE;
		$this->unit->run($match, 'is_true', 'get modified audit action', '');
		
		$app_id = 3;
		$result = $this->audit_action->get_action($app_id);
		$this->unit->run(count($result), 2, 'get list of audit action', '');
		$result = $result[0];
		$match = $result['app_id'] == $app_id;
		$this->unit->run($match, 'is_true', 'verify get list of audit action', '');
	}
	
	function delete_action_test(){
		
		$app_id = 0;
		$action_id = 2;
		$result = $this->audit_action->delete_action($app_id, $action_id);
		$this->unit->run($result, 'is_true', 'delete audit action', '');
		
		$result = $this->audit_action->get_action($app_id, $action_id);
		$this->unit->run(count($result), 0, 'verify delete audit action', '');
	}
	
	function get_action_list_test(){
		$result = $this->audit_action->get_action_list();
		$this->unit->run(count($result), 3, 'get list of all action', '');
	}

	function get_platform_action_test(){
		$result = $this->audit_action->get_platform_action();
		$this->unit->run(count($result), 1, 'get list of all action', '');
		$result = $result[0];
		$match = $result['app_id'] == 0;
		$this->unit->run($match, 'is_true', 'verify platform action', '');
	}
	
	function end_test(){
		$this->audit_action->drop_collection();
	}
}
/* End of file audit_action_model_test.php */
/* Location: ./application/controllers/test/audit_action_model_test.php */