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
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_false', 'add audit action fail - invalid args', '');
		
		$app_id = 0;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app id 0, action id 1';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 1', '');
		
		$app_id = 0;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'app id 0, action id 2';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 2', '');
		
		$app_id = 0;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$description = 'app id 0, action id 3';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 3', '');
		
		$app_id = 0;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 0, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 4', '');
		
		$app_id = 1;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'app id 1, action id 2';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 2', '');
		
		$app_id = 1;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$description = 'app id 1, action id 3';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 3', '');
		
		$app_id = 2;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$description = 'app id 2, action id 4';
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $stat_app, $stat_page, $stat_campaign, $description);
		$this->unit->run($result, 'is_true', 'add audit action app id 2, action id 4', '');
		
	}

	function edit_audit_action_test(){
		
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