<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class achievement_lib_test extends CI_Controller {
	
	var $achievement_lib;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('achievement_info_model','achievement_info');
		$this->load->model('achievement_stat_model','achievement_stat');
		$this->load->model('achievement_user_model','achievement_user');
		$this->load->library('achievement_lib');
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		//echo 'Functions : '.(count(get_class_methods($this->achievement_lib))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->achievement_info->drop_collection();
		$this->achievement_stat->drop_collection();
		$this->achievement_user->drop_collection();
	}
	
	function create_index_test(){
		$this->achievement_lib->create_index();
	}
	
	function add_achievement_info_test(){
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_lib->add_achievement_info($app_id, $app_install_id, $info, $criteria);
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
		
		$result = $this->achievement_lib->add_achievement_info($app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'add_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'add_test', print_r($result, TRUE));
	}
	
	function set_achievement_info_test(){
		$added = $this->achievement_info->list_info();
		
		$achievement_id = $added[0]['_id'];
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'name',
									'description' => 'game',
									'campaign_id' => 23,
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('a' => 5, 'b' => 2);
		
		$result = $this->achievement_lib->set_achievement_info($achievement_id, $app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'set_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'set_test', print_r($result, TRUE));
		
		$achievement_id = $added[1]['_id'];
		$app_id = 1;
		$app_install_id = 3;
		$info = array('name' => 'name',
									'description' => 'game',
									'hidden' => TRUE,
									'page_id' => 52,
									'criteria_string' => array('a >= 5', 'b >=2'));
		$criteria = array('s' => 6);
		
		$result = $this->achievement_lib->set_achievement_info($achievement_id, $app_id, $app_install_id, $info, $criteria);
		$this->unit->run($result, 'is_true', 'set_test', print_r($result, TRUE));
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 2, 'set_test', print_r($result, TRUE));
	}

	function list_achievement_info_by_app_id_test(){
		$app_id = 1;
		$result = $this->achievement_lib->list_achievement_info_by_app_id($app_id);
		$total = count($this->achievement_info->list_info(array('app_id' => $app_id)));
		$this->unit->run($total, 2, 'list_test', print_r($result, TRUE));
	}
	
	function list_achievement_info_by_page_id_test(){
		$page_id = 52;
		$result = $this->achievement_lib->list_achievement_info_by_page_id($page_id);
		$total = count($this->achievement_info->list_info(array('page_id' => $page_id)));
		$this->unit->run($total, 1, 'list_test', print_r($result, TRUE));
	}
	
	function list_achievement_info_by_campaign_id_test(){
		$campaign_id = 23;
		$result = $this->achievement_lib->list_achievement_info_by_campaign_id($campaign_id);
		$total = count($this->achievement_info->list_info(array('campaign_id' => $campaign_id)));
		$this->unit->run($total, 1, 'list_test', print_r($result, TRUE));
	}
	
	function delete_achievement_info_test(){
		$added = $this->achievement_info->list_info();
		$achievement_id = $added[0]['_id'];
		$result = $this->achievement_lib->delete_achievement_info($achievement_id);
		$this->unit->run($result, 'is_true', 'delete', print_r($result, TRUE));
		
		$total = count($this->achievement_info->list_info());
		$this->unit->run($total, 1, 'list_test', print_r($result, TRUE));
	}
		
	function reward_user_test(){
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$app_id = 2;
		$app_install_id = 3;
		$info = array();
		
		$result = $this->achievement_lib->reward_user($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_true', 'reward_user_test', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 1, 'reward_user_test', print_r($result, TRUE));
		
		$user_id = 2;
		$achievement_id = '4e4ff0e36803faf002000002';
		$app_id = 2;
		$app_install_id = 3;
		$info = array('page_id' => 20,
									'campaign_id' => 30);
		
		$result = $this->achievement_lib->reward_user($user_id, $achievement_id, $app_id, $app_install_id, $info);
		$this->unit->run($result, 'is_true', 'reward_user_test', print_r($result, TRUE));
		$total = count($this->achievement_user->list_user());
		$this->unit->run($total, 2, 'reward_user_test', print_r($result, TRUE));
	}
	
	function list_user_achieved_by_user_id_test(){
		$user_id = 2;
		$result = $this->achievement_lib->list_user_achieved_by_user_id($user_id);
		$this->unit->run(count($result), 1, 'list_user_achieved_by_user_id_test', print_r($result, TRUE));
		
		$user_id = 2222;
		$result = $this->achievement_lib->list_user_achieved_by_user_id($user_id);
		$this->unit->run(count($result), 0, 'list_user_achieved_by_user_id_test', print_r($result, TRUE));
	}
	
	function list_user_achieved_in_page_test(){
		$user_id = 2;
		$page_id = 20;
		$result = $this->achievement_lib->list_user_achieved_in_page($user_id, $page_id);
		$this->unit->run(count($result), 1, 'list_user_achieved_in_page_test', print_r($result, TRUE));
		$this->unit->run($result[0]['page_id'], $page_id, 'list_user_achieved_in_page_test', print_r($result[0]['page_id'], TRUE));
		$page_id = 22220;
		$result = $this->achievement_lib->list_user_achieved_in_page($user_id, $page_id);
		$this->unit->run(count($result), 0, 'list_user_achieved_in_page_test', print_r($result, TRUE));
	}
	
	function list_user_achieved_in_campaign_test(){
		$user_id = 2;
		$campaign_id = 30;
		$result = $this->achievement_lib->list_user_achieved_in_campaign($user_id, $campaign_id);
		$this->unit->run(count($result), 1, 'list_user_achieved_in_campaign_test', print_r($result, TRUE));
		
		$campaign_id = 30000;
		$result = $this->achievement_lib->list_user_achieved_in_campaign($user_id, $campaign_id);
		$this->unit->run(count($result), 0, 'list_user_achieved_in_campaign_test', print_r($result, TRUE));
	}
	
	function delete_user_achieved_test(){
		$user_id = 1;
		$achievement_id = '4e4ff0e36803faf002000002';
		$result = $this->achievement_lib->delete_user_achieved($user_id, $achievement_id);
		$this->unit->run($result, 'is_true', 'delete_user_achieved_test', print_r($result, TRUE));
	}
	
	function set_achievement_stat_test(){
		$app_id = 2;
		$user_id = 2;
		$info = array('friend' => 100);
		
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 10;
		$user_id = 2;
		$info = array('friend' => 100);
		
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($result, TRUE));
	}
	
	function get_achievement_stat_of_user_in_app_test(){
		$app_id = 10;
		$user_id = 2;

		$result = $this->achievement_lib->get_achievement_stat_of_user_in_app($app_id, $user_id);
		$this->unit->run($result['app_id'], 10, 'get', print_r($result, TRUE));
		$this->unit->run($result['friend'], 100, 'get', print_r($result, TRUE));
		$this->unit->run($result['user_id'], 2, 'get', print_r($result, TRUE));
		
		$app_id = 10;
		$user_id = 200;
		$result = $this->achievement_lib->get_achievement_stat_of_user_in_app($app_id, $user_id);
		$this->unit->run($result, 'is_null', 'get', print_r($result, TRUE));
		
		$app_id = NULL;
		$user_id = NULL;
		$result = $this->achievement_lib->get_achievement_stat_of_user_in_app($app_id, $user_id);
		$this->unit->run($result, 'is_null', 'get', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;

		$result = $this->achievement_lib->get_achievement_stat_of_user_in_app($app_id, $user_id);
		$this->unit->run($result['app_id'], 2, 'get', print_r($result, TRUE));
		$this->unit->run($result['friend'], 100, 'get', print_r($result, TRUE));
		$this->unit->run($result['user_id'], 2, 'get', print_r($result, TRUE));
	}

	function end_test(){
		// $this->achievement_info->drop_collection();
		// $this->achievement_stat->drop_collection();
		// $this->achievement_user->drop_collection();
	}
}
/* End of file achievement_lib_test.php */
/* Location: ./application/controllers/test/achievement_lib_test.php */