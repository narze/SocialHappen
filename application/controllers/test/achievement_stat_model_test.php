<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class achievement_stat_model_test extends CI_Controller {
	
	var $achievement_stat;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('achievement_stat_model','achievement_stat');
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->achievement_stat))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->achievement_stat->drop_collection();
	}
	
	function create_index_test(){
		$this->achievement_stat->create_index();
	}
	
	function increment_invalid_test(){
		$app_id = 1;
		$user_id = 2;
		$info = array();
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$user_id = NULL;
		$info = array();
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_false', 'increment', print_r($result, TRUE));
		
		
		$app_id = NULL;
		$user_id = 2;
		$info = array();
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_false', 'increment', print_r($result, TRUE));
		
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 0, 'increment', print_r($result, TRUE));
	}
	
	function increment_test(){
		$app_id = 1;
		$user_id = 2;
		$info = array('action_id' => 2);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$user_id = 2;
		$info = array('action_id' => 3,
									'app_install_id' => 4);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$user_id = 2;
		$info = array('action_id' => 3,
									'app_install_id' => 5);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$user_id = 2;
		$info = array('action_id' => 3,
									'app_install_id' => 6);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$user_id = 2;
		$info = array('action_id' => 4,
									'app_install_id' => 5);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$info = array('action_id' => 6,
									'app_install_id' => 7,
									'page_id' => 9);
		$amount = 1;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$info = array('action_id' => 9,
									'app_install_id' => 7,
									'page_id' => 9,
									'campaign_id' => 10);
		$amount = 10;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$info = array('action_id' => 9,
									'app_install_id' => 7,
									'page_id' => 9,
									'campaign_id' => 10);
		$amount = 10;
		
		$result = $this->achievement_stat->increment($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($result, TRUE));
	}
	
	function set_invalid_test(){
		$app_id = 2;
		$user_id = 2;
		$info = array('action' => 100);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = NULL;
		$user_id = NULL;
		$info = array('action' => 100);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$info = array('user_id' => 100);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$info = array('user_id' => 100,
									'app_id' => 200);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
	}
	
	function set_test(){
		$app_id = 2;
		$user_id = 2;
		$info = array('friend' => 100);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($result, TRUE));
		
		$app_id = 10;
		$user_id = 2;
		$info = array('friend' => 100);
		
		$result = $this->achievement_stat->set($app_id, $user_id, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 3, 'increment', print_r($result, TRUE));
	}
	
	
	function get_test(){
		
		$app_id = 10;
		$user_id = 2;

		$result = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run($result['app_id'], 10, 'get', print_r($result, TRUE));
		$this->unit->run($result['friend'], 100, 'get', print_r($result, TRUE));
		$this->unit->run($result['user_id'], 2, 'get', print_r($result, TRUE));
		
		$app_id = 10;
		$user_id = 200;
		$result = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run($result, 'is_null', 'get', print_r($result, TRUE));
		
		$app_id = NULL;
		$user_id = NULL;
		$result = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run($result, 'is_false', 'get', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;

		$result = $this->achievement_stat->get($app_id, $user_id);
		$this->unit->run($result['app_id'], 2, 'get', print_r($result, TRUE));
		$this->unit->run($result['friend'], 100, 'get', print_r($result, TRUE));
		$this->unit->run($result['user_id'], 2, 'get', print_r($result, TRUE));
		$this->unit->run($result['action']['6']['page']['9']['count'], 1, 'get', print_r($result, TRUE));
		$this->unit->run($result['action']['9']['app_install']['7']['count'], 20, 'get', print_r($result, TRUE));
	}
	
	function end_test(){
		// $this->achievement_stat->drop_collection();
	}
}
/* End of file achievement_stat_model_test.php */
/* Location: ./application/controllers/test/achievement_stat_model_test.php */