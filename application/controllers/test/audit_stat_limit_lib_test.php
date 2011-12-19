<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_stat_limit_lib_test extends CI_Controller {
	var $audit;
	var $audit_stat_limit_lib;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('audit_stats_model','stats');
		$this->load->library('audit_stat_limit_lib');
		$this->unit->reset_dbs();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function start_test(){
		$this->stats->drop_collection();
	}
	
	function create_index_test(){
		$this->audit_stat_limit_lib->create_index();
	}
	
	function add_invalid_test(){
		$user_id = 1;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no);
		$result = $this->audit_stat_limit_lib->add($user_id, NULL, $app_install_id, $campaign_id);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
		
		$data = array('user_id' => $user_id);
		$result = $this->audit_stat_limit_lib->add($user_id, $action_no, NULL, $campaign_id);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
		
		$data = array();
		$result = $this->audit_stat_limit_lib->add(NULL, $action_no, $app_install_id, $campaign_id);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
	}
	
	function add_test(){
		$user_id = 1;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id);
		$result = $this->audit_stat_limit_lib->add($user_id, $action_no, $app_install_id, $campaign_id);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 1, 'add', print_r($result, TRUE));
		
		$user_id = 2;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id);
		$result = $this->audit_stat_limit_lib->add($user_id, $action_no, $app_install_id, $campaign_id);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 2, 'add', print_r($result, TRUE));
		
		$user_id = 3;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id);
		$result = $this->audit_stat_limit_lib->add($user_id, $action_no, $app_install_id, $campaign_id);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 3, 'add', print_r($result, TRUE));
	}

	function count_test(){
		$user_id = 1;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 3600);
		$this->unit->run($result, 1, 'count', print_r($result, TRUE));
		
		$user_id = 2;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 3600);
		$this->unit->run($result, 1, 'count', print_r($result, TRUE));
		
		$user_id = 3;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 3600);
		$this->unit->run($result, 1, 'count', print_r($result, TRUE));
		
		$user_id = 99;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 3600);
		$this->unit->run($result, 0, 'count', print_r($result, TRUE));
	}

	function count2_test(){
		$this->stats->drop_collection();
		$this->stats->create_index();
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		date_default_timezone_set('UTC');
		$timestamp = time() - 1000;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id,
									'timestamp' => $timestamp);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		date_default_timezone_set('UTC');
		$timestamp = time() - 10000;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id,
									'timestamp' => $timestamp);
		$result = $this->stats->add_stat($data);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		date_default_timezone_set('UTC');
		$timestamp = time() - 100000;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id,
									'timestamp' => $timestamp);
		$result = $this->stats->add_stat($data);
		$result = $this->stats->add_stat($data);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = NULL;
		date_default_timezone_set('UTC');
		$timestamp = time() - 1000;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'timestamp' => $timestamp);
		$result = $this->stats->add_stat($data);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 70;
		$campaign_id = NULL;
		date_default_timezone_set('UTC');
		$timestamp = time() - 1000;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'timestamp' => $timestamp);
		$result = $this->stats->add_stat($data);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 100001);
		$this->unit->run($result, 6, 'count', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 10001);
		$this->unit->run($result, 3, 'count', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 1001);
		$this->unit->run($result, 1, 'count', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = NULL;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 1001);
		$this->unit->run($result, 3, 'count', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 70;
		$campaign_id = NULL;
		$result = $this->audit_stat_limit_lib->count($user_id, $action_no, $app_install_id, $campaign_id, 1001);
		$this->unit->run($result, 2, 'count', print_r($result, TRUE));
	}
	
	function prune_test(){
		$back_time_interval = 500;
		$result = $this->audit_stat_limit_lib->prune();
		$this->unit->run($result, 'is_true', 'prune_test', print_r($result, TRUE));
		
		$result = $this->stats->count_stat();
		$this->unit->run($result, 7, 'prune_test', print_r($result, TRUE));
		
		$result = $this->audit_stat_limit_lib->prune($back_time_interval);
		$this->unit->run($result, 'is_true', 'prune_test', print_r($result, TRUE));
		
		$result = $this->stats->count_stat();
		$this->unit->run($result, 0, 'prune_test', print_r($result, TRUE));
	}
	
	function drop_test(){
		//$this->stats->drop_collection();
	}
}

/* End of file audit_stat_limit_lib_test.php */
/* Location: ./application/controllers/test/audit_stat_limit_lib_test.php */