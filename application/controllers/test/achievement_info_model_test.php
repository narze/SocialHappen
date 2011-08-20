<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class achievement_info_model_test extends CI_Controller {
	
	var $achievement_info;
	
	var $added_info = array();
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('achievement_info_model','achievement_info');
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->achievement_info))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->achievement_info->drop_collection();
	}
	
	function create_index_test(){
		$this->achievement_info->create_index();
	}
	
	function add_invalid_test(){
		$app_id = 1;
		$app_install_id = NULL;
		$info = array();
		$criteria = array();
		
		$result = $this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_false', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 0, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'criteria_string' => array('a', 'b'));
		$criteria = array();
		
		$result = $this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_false', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 0, 'increment', print_r($result, TRUE));
		
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'criteria_string' => array());
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_false', 'increment', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 0, 'increment', print_r($result, TRUE));
	}

	function add_test(){
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'add_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 1, 'add_test', print_r($result, TRUE));
		
		$app_id = 1;
		$app_install_id = 3;
		$info = array('name' => 'name',
									'description' => 'game',
									'hidden' => TRUE,
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'add_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'add_test', print_r($result, TRUE));
	}

	function set_test(){
		$added = $this->achievement_info->list_info();
		
		$achievement_id = $added[0]['_id'];
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_info->set($achievement_id, $app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'set_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'set_test', print_r($result, TRUE));
		
		$achievement_id = $added[1]['_id'];
		$app_id = 1;
		$app_install_id = 3;
		$info = array('name' => 'name',
									'description' => 'game',
									'hidden' => TRUE,
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('s' => 6);
		
		$result = $this->achievement_info->set($achievement_id, $app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'set_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'set_test', print_r($result, TRUE));
	}

	function get_test(){
		$added = $this->achievement_info->list_info();
		
		$achievement_id = $added[0]['_id'];
		$info = array('name' => 'name',
									'hidden' => FALSE,
									'description' => 'game',
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		$result = $this->achievement_info->get($achievement_id);
		$this->unit->run($result['app_id'], 1, 'get', print_r($result, TRUE));
		$this->unit->run($result['app_install_id'], 2, 'get', print_r($result, TRUE));
		$this->unit->run($result['info'], $info, 'get', print_r($result, TRUE));
		$this->unit->run($result['criteria'], $criteria, 'get', print_r($result, TRUE));
		
		$achievement_id = $added[1]['_id'];
		$info = array('name' => 'name',
									'description' => 'game',
									'hidden' => TRUE,
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('s' => 6);
		$result = $this->achievement_info->get($achievement_id);
		$this->unit->run($result['app_id'], 1, 'get', print_r($result, TRUE));
		$this->unit->run($result['app_install_id'], 3, 'get', print_r($result, TRUE));
		$this->unit->run($result['info'], $info, 'get', print_r($result, TRUE));
		$this->unit->run($result['criteria'], $criteria, 'get', print_r($result, TRUE));
		
		$achievement_id = 'sadgsadksa';

		$result = $this->achievement_info->get($achievement_id);
		$this->unit->run($result, 'is_null', 'get', print_r($result, TRUE));
	}

	function delete_test(){
		$added = $this->achievement_info->list_info();
		$achievement_id = $added[0]['_id'];
		$result = $this->achievement_info->delete($achievement_id);
		$this->unit->run($result, 'is_true', 'delete', print_r($result, TRUE));
		
		$result = $this->achievement_info->get($achievement_id);
		$this->unit->run($result, 'is_null', 'delete', print_r($result, TRUE));
		
		$result = $this->achievement_info->delete($achievement_id);
		$this->unit->run($result, 'is_true', 'delete', print_r($result, TRUE));
		
		$result = $this->achievement_info->get($achievement_id);
		$this->unit->run($result, 'is_null', 'delete', print_r($result, TRUE));
	}

	function end_test(){
		$this->achievement_info->drop_collection();
	}
}
/* End of file achievement_info_model_test.php */
/* Location: ./application/controllers/test/achievement_info_model_test.php */