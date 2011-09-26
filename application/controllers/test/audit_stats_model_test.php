<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_stats_model_test extends CI_Controller {
	var $audit;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('audit_stats_model','stats');
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->stats))-3).' Tests :'.count($class_methods);
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
		$this->stats->create_index();
	}
	
	function add_stat_invalid_test(){
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
		
		$data = array('user_id' => $user_id);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
		
		$data = array();
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 0, 'add', print_r($result, TRUE));
	}
	
	function add_stat_test(){
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 1, 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 2, 'add', print_r($result, TRUE));
		
		$user_id = 100;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$data = array('user_id' => $user_id,
									'action_no' => $action_no,
									'app_install_id' => $app_install_id,
									'campaign_id' => $campaign_id);
		$result = $this->stats->add_stat($data);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		
		$result = $this->stats->list_stat();
		$this->unit->run(count($result), 3, 'add', print_r($result, TRUE));
	}

	function count_stat_test(){
		$result = $this->stats->count_stat();
		$this->unit->run($result, 3, 'add', print_r($result, TRUE));
		
		$user_id = 46813135435;
		$action_no = 50;
		$app_install_id = 60;
		$campaign_id = 70;
		$criteria = array('user_id' => $user_id);
		$result = $this->stats->count_stat($criteria);
		$this->unit->run($result, 0, 'add', print_r($result, TRUE));
	}
	
}

/* End of file audit_stat_model_test.php */
/* Location: ./application/controllers/test/audit_stat_model_test.php */