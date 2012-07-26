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
		$this->unit->reset_dbs();
	}

	function __destruct(){
		echo $this->unit->report_with_counter();
	}

	function index(){
		$class_methods = get_class_methods($this);
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
		$format_string = 'format_string';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_false', 'add audit action fail - invalid args', '');

		$app_id = 0;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 0, action id 1';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 1', '');

		$app_id = 0;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$format_string = 'format_string';
		$description = 'app id 0, action id 2';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 2', '');

		$app_id = 0;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$format_string = 'format_string';
		$description = 'app id 0, action id 3';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 3', '');

		$app_id = 0;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 0, action id 4';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 0, action id 4', '');

		$app_id = 1;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$format_string = 'format_string';
		$description = 'app id 1, action id 2';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 2', '');

		$app_id = 1;
		$action_id = 3;
		$stat_app = FALSE;
		$stat_page = TRUE;
		$stat_campaign = FALSE;
		$format_string = 'format_string';
		$description = 'app id 1, action id 3';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 1, action id 3', '');

		$app_id = 2;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 2, action id 4';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 2, action id 4', '');

		$app_id = 3;
		$action_id = 4;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 2, action id 4';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 3, action id 4', '');

		$app_id = 3;
		$action_id = 5;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 2, action id 4';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
		$this->unit->run($result, 'is_true', 'add audit action app id 3, action id 5', '');

		$app_id = 4;
		$action_id = 6;
		$stat_app = FALSE;
		$stat_page = FALSE;
		$stat_campaign = TRUE;
		$format_string = 'format_string';
		$description = 'app id 2, action id 4';
		$score = 1;
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);
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
		$result = $this->audit_lib->list_audit_action();
		$this->unit->run(count($result), 11-2, 'list all audit action', count($result));

		$app_id = 3;
		$result = $this->audit_lib->list_audit_action($app_id);
		$this->unit->run(count($result), 2, 'list audit action - 3', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

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
    // action id < 1000, chang to platform with app_id = 0
		$match = $result['app_id'] == 0 && $result['action_id'] == 4
		&& $result['stat_app'] == FALSE && $result['stat_page'] == FALSE
		&& $result['stat_campaign'] == TRUE	&& $result['description'] == 'app id 0, action id 4';
		$this->unit->run($match, 'is_true', 'get_audit_action - 2 - 4', '<pre>' . print_r($result, TRUE) . '</pre>');
		//this get action from platform app_id : 0
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

		for ($i=0; $i < 5; $i++) {

			$app_id = 0;
			$action_id = 2;
			$subject = 'A';
			$object = 'B';
			$objecti = 'C';
			$additional_data = array('app_install_id' => 1,
									 'campaign_id' => 2,
									 'company_id' => 3,
									 'page_id' => 4);
			$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
			$this->unit->run($result, 'is_string', 'add audit - 0 - 2', '');
		}

		for ($i=0; $i < 3; $i++) {

			$app_id = 0;
			$action_id = 1;
			$subject = 'A';
			$object = 'B';
			//$objecti = 'C';
			$additional_data = array('app_install_id' => 1,
									 'campaign_id' => 2,
									 'company_id' => 3,
									 'page_id' => 4);
			$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
			$this->unit->run($result, 'is_string', 'add audit - 0 - 1', '');
		}

		for ($i=0; $i < 3; $i++) {

			$app_id = 2;
			$action_id = 4;
			$subject = 'A';
			$object = 'B';
			//$objecti = 'C';
			$additional_data = array('app_install_id' => 1,
									 'campaign_id' => 2,
									 'company_id' => 3,
									 'page_id' => 4);
			$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
			$this->unit->run($result, 'is_string', 'add audit - 2 - 4', '');
		}
	}

	function list_audit_test(){
		$criteria = array();
		$limit = 100;
		$offset = 0;
		$result = $this->audit_lib->list_audit();
		$this->unit->run(count($result), 11, 'list audit no criteria', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array();
		$limit = 2;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 2, 'list audit no criteria, limit 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array('subject' => 'A', 'app_id' => 0);
		$limit = 0;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 8, 'list audit criteria \'subject\' => \'A\' , \'app_id\' => 0', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array('subject' => 'A', 'app_id' => 999);
		$limit = 0;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 0, 'list audit criteria \'subject\' => \'A\' , \'app_id\' => 999', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function list_recent_audit_test(){
		$limit = 0;
		$result = $this->audit_lib->list_recent_audit();
		$this->unit->run(count($result), 11, 'list recent audit no criteria', 'count: ' . count($result) . '<pre>' /*. print_r($result, TRUE)*/ . '</pre>');

		$limit = 5;
		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run(count($result), 5, 'list recent audit no criteria', 'count: ' . count($result) . '<pre>' /*. print_r($result, TRUE)*/ . '</pre>');
	}

	function _date(){
		date_default_timezone_set('UTC');
		return Date('Ymd');
	}

	function list_stat_app_test(){

		$app_install_id = 1;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app();
		$this->unit->run($result, 'is_false', 'list stat app fail', '');

		$app_install_id = 1;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id);
		$this->unit->run(count($result), 2, 'list stat app $app_install_id = 1', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$app_install_id = NULL;
		$action_id = 2;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat app $action_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$app_install_id = 1;
		$action_id = 2;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat app $app_install_id = 1 and $action_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$app_install_id = 100;
		$action_id = 2;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id);
		$this->unit->run(count($result), 0, 'list stat app $app_install_id = 100 and $action_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$today = (int)$this->_date();
		$app_install_id = 1;
		$action_id = NULL;
		$start_date = $today;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 2, 'list stat app $app_install_id = 1 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$app_install_id = 1;
		$action_id = NULL;
		$start_date = $today +1;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 0, 'list stat app $app_install_id = 1 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$app_install_id = 1;
		$action_id = NULL;
		$start_date = $today -11;
		$end_date = $today + 140;
		$result = $this->audit_lib->list_stat_app($app_install_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 2, 'list stat app $app_install_id = 1 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function list_stat_page_test(){

		$page_id = 1;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page();
		$this->unit->run($result, 'is_false', 'list stat page fail', '');

		$page_id = 4;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id);
		$this->unit->run(count($result), 1, 'list stat page $page_id = 4', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$page_id = NULL;
		$action_id = 1;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat page $action_id = 1', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$page_id = 4;
		$action_id = 1;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat page $page_id = 4 and $action_id = 1', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$page_id = 100;
		$action_id = 2;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id);
		$this->unit->run(count($result), 0, 'list stat page $page_id = 100 and $action_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$today = (int)$this->_date();
		$page_id = 4;
		$action_id = NULL;
		$start_date = $today;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 1, 'list stat page $page_id = 4 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$page_id = 4;
		$action_id = NULL;
		$start_date = $today +1;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 0, 'list stat page $page_id = 4 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$page_id = 4;
		$action_id = NULL;
		$start_date = $today -11;
		$end_date = $today + 140;
		$result = $this->audit_lib->list_stat_page($page_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 1, 'list stat page $page_id = 4 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function list_stat_campaign_test(){

		$campaign_id = 1;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign();
		$this->unit->run($result, 'is_false', 'list stat campaign fail', '');

		$campaign_id = 2;
		$action_id = NULL;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id);
		$this->unit->run(count($result), 2, 'list stat campaign $campaign_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$campaign_id = NULL;
		$action_id = 4;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat campaign $action_id = 4', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$campaign_id = 2;
		$action_id = 4;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id);
		$this->unit->run(count($result), 1, 'list stat campaign $campaign_id = 2 and $action_id = 4', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$campaign_id = 100;
		$action_id = 2;
		$start_date = NULL;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id);
		$this->unit->run(count($result), 0, 'list stat campaign $campaign_id = 100 and $action_id = 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$today = (int)$this->_date();
		$campaign_id = 2;
		$action_id = NULL;
		$start_date = $today;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 2, 'list stat campaign $campaign_id = 2 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$campaign_id = 2;
		$action_id = NULL;
		$start_date = $today +1;
		$end_date = NULL;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 0, 'list stat campaign $campaign_id = 2 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$campaign_id = 2;
		$action_id = NULL;
		$start_date = $today -11;
		$end_date = $today + 140;
		$result = $this->audit_lib->list_stat_campaign($campaign_id, $action_id, $start_date, $end_date);
		$this->unit->run(count($result), 2, 'list stat campaign $campaign_id = 2 and $start_date = ' . $start_date, 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	//test audit_add()
	function audit_add_test(){
		$input = array();
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_false', 'audit add - fail', '');

		$input = NULL;
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_false', 'audit add - fail', '');

		$input = 'string';
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_false', 'audit add - fail', '');

		$app_id = NULL;
		$subject = NULL;
		$action_id = NULL;
		$object = NULL;
		$objecti = NULL;
		$input = compact('app_id', 'subject', 'action_id', 'object', 'objecti');
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_false', 'audit add - fail', '');

		for ($i=0; $i < 5; $i++) {

			$app_id = 0;
			$action_id = 2;
			$subject = 'A';
			$object = 'B';
			$objecti = 'C';
			$app_install_id = 1;
			$campaign_id = 2;
			$company_id = 3;
			$page_id = 4;
			$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'app_install_id', 'campaign_id', 'company_id','page_id');
			$result = $this->audit_lib->audit_add($input);
			$this->unit->run($result, 'is_string', 'audit add - 0 - 2', '');
		}

		for ($i=0; $i < 3; $i++) {

			$app_id = 0;
			$action_id = 1;
			$subject = 'A';
			$object = 'B';
			$objecti = 'C';
			$app_install_id = 1;
			$campaign_id = 2;
			$company_id = 3;
			$page_id = 4;
			$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'app_install_id', 'campaign_id', 'company_id','page_id');
			$result = $this->audit_lib->audit_add($input);
			$this->unit->run($result, 'is_string', 'audit add - 0 - 1', '');
		}

		for ($i=0; $i < 3; $i++) {

			$app_id = 2;
			$action_id = 4;
			$subject = 'A';
			$object = 'B';
			$objecti = 'C';
			$app_install_id = 1;
			$campaign_id = 2;
			$company_id = 3;
			$page_id = 4;
			$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'app_install_id', 'campaign_id', 'company_id','page_id');
			$result = $this->audit_lib->audit_add($input);
			$this->unit->run($result, 'is_string', 'audit add - 2 - 4', '');
		}
	}

	function list_audit_2_test(){
		$criteria = array();
		$limit = 100;
		$offset = 0;
		$result = $this->audit_lib->list_audit();
		$this->unit->run(count($result), 11*2, 'list audit no criteria', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array();
		$limit = 2;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 2, 'list audit no criteria, limit 2', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array('subject' => 'A', 'app_id' => 0);
		$limit = 0;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 8*2, 'list audit criteria \'subject\' => \'A\' , \'app_id\' => 0', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');

		$criteria = array('subject' => 'A', 'app_id' => 999);
		$limit = 0;
		$offset = 0;
		$result = $this->audit_lib->list_audit($criteria, $limit, $offset);
		$this->unit->run(count($result), 0, 'list audit criteria \'subject\' => \'A\' , \'app_id\' => 999', 'count: ' . count($result) . '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function list_recent_audit_2_test(){
		$limit = 0;
		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run(count($result), 11*2, 'list recent audit no criteria', 'count: ' . count($result) . '<pre>' /*. print_r($result, TRUE)*/ . '</pre>');

		$limit = 5;
		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run(count($result), 5, 'list recent audit no criteria', 'count: ' . count($result) . '<pre>' /*. print_r($result, TRUE)*/ . '</pre>');
	}

	function _get_names_for_test(){
		$app_install_id = 1;
		$user_id = 1;
		$this->load->model('installed_apps_model');
		$app = $this->installed_apps_model->get_app_profile_by_app_install_id($app_install_id);
		$this->app_name = $app['app_name'];
		$this->load->model('user_model');
		$user = $this->user_model->get_user_profile_by_user_id($user_id);
		$this->user_name = $user['user_first_name'].' '.$user['user_last_name'];

	}

	function translate_format_string_test(){
		$base_url = base_url();
		$url_prefix = '';
		$use_backend_links = TRUE;
		$use_frontend_links = FALSE;
		$app_id = 1;
		$app_install_id = 1;
		$user_id = 1;
		$audit = compact('app_id', 'app_install_id', 'user_id');
		$format_string = '1{app:app_id}';
		$format_string .= '2{app_install:app_install_id}';
		$format_string .= '3{user:user_id}';
		//Too lazy to do it now :P
		// $format_string .= '4{campaign:campaign_id}';
		// $format_string .= '5{page:page_id}';
		// $format_string .= '6{company:company_id}';
		// $format_string .= '7{package:package_id}';
		// $format_string .= '8{number:app_id}';
		// $format_string .= '9{string:app_install_id}';

		$expected = '1<span class="type_app">'.$this->app_name.'</span>';
		$expected .= '2<span class="type_app_install"><a href="'.$base_url.
			$url_prefix.'app/'.$app_install_id.'">'.$this->app_name.'</a></span>';
		$expected .= '3<span class="type_user"><a href="'.base_url().$url_prefix
			.'passport/'.$user_id.'/">'.$this->user_name.'</a></span>';
		$result = $this->audit_lib->translate_format_string($format_string, $audit, $use_backend_links);
		$this->unit->run($result, $expected, "\$result", $result);

		$url_prefix = 'r/';
		$expected = '1<span class="type_app">'.$this->app_name.'</span>';
		$expected .= '2<span class="type_app_install"><a href="'.$base_url.
			$url_prefix.'app/'.$app_install_id.'">'.$this->app_name.'</a></span>';
		$expected .= '3<span class="type_user"><a href="'.base_url()
			.'passport/'.$user_id.'/">'.$this->user_name.'</a></span>';
		$result = $this->audit_lib->translate_format_string($format_string, $audit, $use_frontend_links);
		$this->unit->run($result, $expected, "\$result", $result);
	}

	function audit_add_chain_test() {
		$app_id = 0;
		$action_id = 1;
		$subject = 'Add chain test';
		$object = 'B';
		$objecti = 'C';
		$campaign_id = 1;
		$app_install_id = 1;
		$page_id = 1;
		$company_id = 1;
		$limit = 1;
		$image = 'test.jpg';

		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'campaign_id', 'image');
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with campaign_id only', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), TRUE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), TRUE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), TRUE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), TRUE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));
		$this->unit->run(isset($result[0]['image']), TRUE, "isset(\$result[0]['image'])", isset($result[0]['image']));


		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'app_install_id');
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with app_install_id only', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), FALSE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), TRUE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), TRUE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), TRUE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));


		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'page_id');
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with page_id only', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), FALSE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), FALSE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), TRUE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), TRUE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));


		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'company_id');
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with company_id only', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), FALSE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), FALSE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), FALSE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), TRUE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));


		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'campaign_id');
		$input['page_id'] = '0';
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with campaign_id=1, page_id=0', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), TRUE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), TRUE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), TRUE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), TRUE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));
		$this->unit->run($result[0]['campaign_id'], 1, "\$result[0]['campaign_id']", $result[0]['campaign_id']);
		$this->unit->run($result[0]['app_install_id'], 1, "\$result[0]['app_install_id']", $result[0]['app_install_id']);
		$this->unit->run($result[0]['page_id'], 0, "\$result[0]['page_id']", $result[0]['page_id']);
		$this->unit->run($result[0]['company_id'], 1, "\$result[0]['company_id']", $result[0]['company_id']); //mapped from app_install_id


		$input = compact('app_id', 'action_id', 'subject', 'object', 'objecti', 'campaign_id');
		$input['app_install_id'] = '0';
		$result = $this->audit_lib->audit_add($input);
		$this->unit->run($result, 'is_string', 'audit add (with campaign_id=1, app_install_id=0', '');

		$result = $this->audit_lib->list_recent_audit($limit);
		$this->unit->run($result[0]['subject'], 'Add chain test', "\$result[0]['subject']", $result[0]['subject']);
		$this->unit->run(isset($result[0]['campaign_id']), TRUE, "isset(\$result[0]['campaign_id'])", isset($result[0]['campaign_id']));
		$this->unit->run(isset($result[0]['app_install_id']), TRUE, "isset(\$result[0]['app_install_id'])", isset($result[0]['app_install_id']));
		$this->unit->run(isset($result[0]['page_id']), FALSE, "isset(\$result[0]['page_id'])", isset($result[0]['page_id']));
		$this->unit->run(isset($result[0]['company_id']), FALSE, "isset(\$result[0]['company_id'])", isset($result[0]['company_id']));
		$this->unit->run($result[0]['campaign_id'], 1, "\$result[0]['campaign_id']", $result[0]['campaign_id']);
		$this->unit->run($result[0]['app_install_id'], 0, "\$result[0]['app_install_id']", $result[0]['app_install_id']);
	}
}
/* End of file audit_lib_test.php */
/* Location: ./application/controllers/test/audit_lib_test.php */