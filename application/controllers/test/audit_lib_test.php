<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class audit_lib_test extends CI_Controller {
	
	var $stat_app;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('audit_model', 'audit');
		$this->load->model('audit_action_model', 'audit_action');
		$this->load->model('stat_page_model', 'stat_page');
		$this->load->model('stat_app_model', 'stat_app');
		$this->load->model('stat_campaign_model', 'stat_campaign');
		$this->load->library('audit_lib');
	}

	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->stat_app))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->audit->drop_collection();
		$this->audit_action->drop_collection();
		$this->stat_page->drop_collection();
		$this->stat_app->drop_collection();
		$this->stat_campaign->drop_collection();
	}
	
	function create_index_test(){
		$this->audit_lib->create_index();
	}
	
	function add_audit_action_test(){
		$app_id = NULL;
		$action_id = NULL;
		$stat_app = NULL;
		$stat_page = NULL;
		$stat_campaign = NULL;
		$description = NULL;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_false', 'add audit action fail - invalid args', '');
		
		$app_id = 0;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app id 0, action id 1';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 1', '');
		
		$app_id = 0;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'app id 0, action id 2';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 2', '');
		
		$app_id = 0;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$description = 'app id 0, action id 3';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 3', '');
		
		$app_id = 0;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 0, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 4', '');
		
		$app_id = 1;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'app id 1, action id 2';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 2', '');
		
		$app_id = 1;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$description = 'app id 1, action id 3';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 3', '');
		
		$app_id = 2;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 2, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 2, action id 4', '');
		
		$app_id = 3;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 2, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 3, action id 4', '');
		
		$app_id = 3;
		$action_id = 5;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 2, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 3, action id 5', '');
		
		$app_id = 4;
		$action_id = 6;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 2, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		$this->unit->run($result, 'is_true', 'add audit action app id 3, action id 6', '');
		
	}

	function edit_audit_action_test(){
		$app_id = 2;
		$action_id = 4;
		$data = array('stat_app' => TRUE,
					  'stat_page' => FALSE,
					  'description' => 'app id 2, action id 4 modded');
		$result = $this->audit_lib->edit_audit_action($app_id, $action_id, $data);
		$this->unit->run($result, 'is_true', 'modify audit action app id 2, action id 4', '');
		
		$result = $this->audit_lib->edit_audit_action($app_id, $action_id, array());
		$this->unit->run($result, 'is_false', 'modify audit action app id 2, action id 4 - fail', '');
	}
	
	function delete_audit_action_test(){
		$result = $this->audit_lib->delete_audit_action();
		$this->unit->run($result, 'is_false', 'delete audit action - fail', '');
		
		$app_id = 4;
		$action_id = 6;
		$result = $this->audit_lib->delete_audit_action($app_id, $action_id);
		$this->unit->run($result, 'is_true', 'delete audit action - 4, 6', '');
		
		$app_id = 3;
		$result = $this->audit_lib->delete_audit_action($app_id);
		$this->unit->run($result, 'is_true', 'delete audit action - 3', '');
	}
	
	function list_audit_action_test(){
		$app_id = 3;
		$result = $this->audit_lib->list_audit_action();
		$this->unit->run(count($result), 7, 'list audit action', '');
		
		$app_id = 3;
		$result = $this->audit_lib->list_audit_action($app_id);
		$this->unit->run(count($result), 0, 'list audit action - 3', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$app_id = 1;
		$result = $this->audit_lib->list_audit_action($app_id);
		$this->unit->run(count($result), 2, 'list audit action - 1', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}
	
	function get_audit_action_test(){
		$app_id = 4;
		$action_id = 6;
		$result = $this->audit_lib->get_audit_action($app_id, $action_id);
		$this->unit->run($result, 'is_null', 'get_audit_action - 4, 6', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$app_id = 4;
		$action_id = 6;
		$result = $this->audit_lib->get_audit_action($app_id);
		$this->unit->run($result, 'is_false', 'get_audit_action - 4 - invalid args', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		$app_id = 2;
		$action_id = 4;
		$result = $this->audit_lib->get_audit_action($app_id, $action_id);
		$match = $result['app_id'] == 2 && $result['action_id'] == 4
		&& $result['stat_app'] == TRUE && $result['stat_page'] == FALSE
		&& $result['stat_campaign'] == TRUE	&& $result['description'] == 'app id 2, action id 4 modded';
		$this->unit->run($match, 'is_true', 'get_audit_action - 2 - 4', '<pre>' . print_r($result, TRUE) . '</pre>');
	}
	
	function list_platform_audit_action_test(){
		$result = $this->audit_lib->list_platform_audit_action();
		$this->unit->run(count($result), 4, 'list_platform_audit_action', '<pre>' . print_r($result, TRUE) . '</pre>');
		$match = TRUE;
		$i = 1;
		foreach($result as $item){
			$match = $match && $item['action_id'] == $i++;
		}
		$this->unit->run($match, 'is_true', 'list_platform_audit_action check match', '');
	}
	
	function add_audit_test(){
		$app_id = NULL;
		$subject = NULL;
		$action_id = NULL;
		$object = NULL;
		$objecti = NULL;
		$additional_data = array();
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		$this->unit->run($result, 'is_false', 'add audit - fail', '');
		
		$app_id = 0;
		$subject = 'A';
		$action_id = 2;
		$object = 'B';
		$objecti = 'C';
		$additional_data = array('app_install_id' => 1,
								 'campaign_id' => 2,
								 'company_id' => 3,
								 'page_id' => 4);
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		$this->unit->run($result, 'is_true', 'add audit - 0 - 2', '');
	}
	
	
	function end_test(){
		/*
		$this->audit->drop_collection();
		$this->audit_action->drop_collection();
		$this->stat_page->drop_collection();
		$this->stat_app->drop_collection();
		$this->stat_campaign->drop_collection();
		*/
	}
}
/* End of file audit_lib_test.php */
/* Location: ./application/controllers/test/audit_lib_test.php */