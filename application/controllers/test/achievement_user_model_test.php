<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class achievement_user_model_test extends CI_Controller {
	
	var $achievement_user;
	
	var $added_info = array();
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('achievement_user_model','achievement_user');
		$this->unit->reset_dbs();
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->achievement_user))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->achievement_user->drop_collection();
	}
	
	function create_index_test(){
		$this->achievement_user->create_index();
	}
	
	function add_invalid_test(){
		
		$user_id = 1;
		$achievement_id = 'sadasdsad';
		$app_id = 1;
		$app_install_id = NULL;
		$info = array();
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_false', 'add_invalid', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 0, 'add_invalid', print_r($result, TRUE));
		
		$user_id = 1;
		$achievement_id = 'sadasdsad';
		$app_id = NULL;
		$app_install_id = 6;
		$info = array();
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_false', 'add_invalid', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 0, 'add_invalid', print_r($result, TRUE));
		
		
		$user_id = NULL;
		$achievement_id = NULL;
		$app_id = 1;
		$app_install_id = NULL;
		$info = array();
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_false', 'add_invalid', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 0, 'add_invalid', print_r($result, TRUE));
		
		
	}

	function add_test(){
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$app_id = 2;
		$app_install_id = 3;
		$info = array();
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 1, 'add', print_r($result, TRUE));
		
		
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$app_id = 2;
		$app_install_id = 3;
		$info = array();
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_false', 'add_dup', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 1, 'add_dup', print_r($result, TRUE));
		
		$user_id = 2;
		$achievement_id = '4e4ff0e36803faf002000002';
		$app_id = 2;
		$app_install_id = 3;
		$info = array('page_id' => 20,
									'campaign_id' => 30);
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 2, 'add', print_r($result, TRUE));
	}

	function delete_test(){
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$result = $this->achievement_user->delete($user_id, $achievement_id);
		$this->unit->run($result, 'is_true', 'delete', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 1, 'add', print_r($result, TRUE));
		
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$result = $this->achievement_user->delete($user_id, $achievement_id);
		$this->unit->run($result, 'is_true', 'delete', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 1, 'add', print_r($result, TRUE));
	}

	function end_test(){
		// $this->achievement_user->drop_collection();
	}
}
/* End of file achievement_user_model_test.php */
/* Location: ./application/controllers/test/achievement_user_model_test.php */