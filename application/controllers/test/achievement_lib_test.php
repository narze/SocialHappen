<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class achievement_lib_test extends CI_Controller {
	
	var $achievement_lib;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('achievement_info_model','achievement_info');
		$this->load->model('achievement_stat_model','achievement_stat');
    $this->load->model('achievement_stat_page_model','achievement_stat_page');
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
    	$this->achievement_stat_page->drop_collection();
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
		$this->unit->run(isset($result), 'is_true', 'add_test', print_r($result, TRUE));
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
		$this->unit->run(isset($result), 'is_true', 'add_test', print_r($result, TRUE));
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
		// invalid
		$app_id = 2;
		$user_id = 2;
		$data = array('action.6.count' => 6);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$data = array('action' => 6);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$data = array('score.6.count' => 6);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		$app_id = 2;
		$user_id = 2;
		$data = array('score' => 6);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_false', 'set', print_r($result, TRUE));
		
		// valid
		
		$app_id = 2;
		$user_id = 2;
		$data = array('friend' => 100);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 1, 'increment', print_r($result, TRUE));
		
		$app_id = 10;
		$user_id = 2;
		$data = array('friend' => 100);
		$info = array('app_install_id' => 5,
									'page_id' => 7,
									'campaign_id' => 5);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
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

	function clear_data_test(){
		$this->achievement_info->drop_collection();
		$this->achievement_stat->drop_collection();
		$this->achievement_user->drop_collection();
		
		$this->achievement_info->create_index();
		$this->achievement_stat->create_index();
		$this->achievement_user->create_index();
	}
	
	function prepare_increment_achievement_stat_test(){
		/**
		 * prepare info
		 */
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'most friend',
									'description' => 'friend mak mak',
									'hidden' => FALSE,
									'campaign_id' => 4,
									'page_id' => 6,
									'criteria_string' => array('friend >= 2'));
		$criteria = array('friend' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		
		$app_id = 7;
		$app_install_id = NULL;
		$info = array('name' => 'most score',
									'description' => 'score mak mak',
									'hidden' => FALSE,
									'campaign_id' => 4,
									'page_id' => 6,
									'criteria_string' => array('score >= 2'));
		$criteria = array('score' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		
		$app_id = 1;
		$app_install_id = 3;
		$info = array('name' => 'most visit',
									'description' => 'visit mak mak',
									'hidden' => FALSE,
									'campaign_id' => 5,
									'page_id' => 7,
									'criteria_string' => array('visit campaign >= 2', 'visti app_install >= 2'));
		$criteria = array('action.103.campaign.5.count' => 2,
										  'action.103.app_install.3.count' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		
		$app_id = 10;
		$app_install_id = NULL;
		$info = array('name' => 'most visit app',
									'description' => 'visit app mak mak',
									'hidden' => FALSE,
									'criteria_string' => array('visit app >= 2'));
		$criteria = array('action.103.count' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
				
		/**
		 * prepare user
		 */
		$user_id = 1;
		$res = $this->achievement_info->list_info(array('app_id' => 1, 'app_install_id' => 3));
		$achievement_id = $res[0]['_id'];
		$app_id = 1;
		$app_install_id = 3;
		$info = array('page_id' => 7,
									'campaign_id' => 5);
		
		$result = $this->achievement_user->add($user_id, $achievement_id, $app_id, $app_install_id, $info);
	}
	
	function increment_achievement_stat_duplicate_test(){
		$app_id = 1;
		$user_id = 1;
		$app_install_id = 3;
		$action_id = 103;
		$info = array('app_install_id' => $app_install_id,
									'page_id' => 7,
									'campaign_id' => 5,
									'action_id' => $action_id);
		$amount = 1;
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 2, 'increment', print_r($total, TRUE));
		
		$res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $app_install_id));
		$achievement_id = $res[0]['_id'];
		$result = $this->achievement_user->list_user(array('user_id' => $user_id));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
		
		$achievement_id_ref = MongoDBRef::create("achievement_info", 
																								new MongoId($achievement_id));
																								
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_user->list_user(array('achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
	}
	
	
	function increment_achievement_stat_test(){
		$app_id = 1;
		$user_id = 2;
		$app_install_id = 3;
		$action_id = 103;
		$info = array('app_install_id' => $app_install_id,
									'page_id' => 7,
									'campaign_id' => 5,
									'action_id' => $action_id);
		$amount = 1;
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 4, 'increment', print_r($total, TRUE));
		
		$res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $app_install_id));
		$achievement_id = $res[0]['_id'];
		
		$achievement_id_ref = MongoDBRef::create("achievement_info", 
																								new MongoId($achievement_id));

		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		// echo '<pre>';
		// print_r($achievement_id);
		// echo '</pre>';
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
		// print_r($result);
		
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
	}

	function increment_achievement_stat_app_test(){
		//echo '<b>';
		$app_id = 10;
		$user_id = 2;
		$app_install_id = 5;
		$action_id = 103;
		$info = array('app_install_id' => $app_install_id,
									'action_id' => $action_id);
		$amount = 1;
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 5, 'increment', print_r($total, TRUE));
		
		$res = $this->achievement_info->list_info(array('app_id' => $app_id));
		$achievement_id = $res[0]['_id'];
		
		$achievement_id_ref = MongoDBRef::create("achievement_info", 
																								new MongoId($achievement_id));

		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
	}

	function set_and_get_reward_test(){
		
		/*
		$app_id = 1;
		$app_install_id = 2;
		$info = array('name' => 'most friend',
									'description' => 'friend mak mak',
									'hidden' => FALSE,
									'campaign_id' => 4,
									'page_id' => 6,
									'criteria_string' => array('friend >= 2'));
		$criteria = array('friend' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		
		$app_id = 0;
		$app_install_id = NULL;
		$info = array('name' => 'most score',
									'description' => 'score mak mak',
									'hidden' => FALSE,
									'campaign_id' => 4,
									'page_id' => 6,
									'criteria_string' => array('score >= 2'));
		$criteria = array('score' => 2);
		$this->achievement_info->add($app_id, $app_install_id, $info, $criteria);
		*/
		
		$app_id = 1;
		$user_id = 2;
		$data = array('friend' => 1);
		$info = array('app_install_id' => 2,
									'page_id' => 6,
									'campaign_id' => 4);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat(array('app_id' => $app_id, 'user_id' => $user_id)));
		$this->unit->run($total, 1, 'set', print_r($result, TRUE));
		
		$res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $info['app_install_id']));
		$achievement_id = $res[0]['_id'];
		
		$achievement_id_ref = MongoDBRef::create("achievement_info", 
																								new MongoId($achievement_id));

		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
		
		//echo '<b>';
		
		$app_id = 1;
		$user_id = 2;
		$data = array('friend' => 2);
		$info = array('app_install_id' => 2,
									'page_id' => 6,
									'campaign_id' => 4);
		$result = $this->achievement_lib->set_achievement_stat($app_id, $user_id, $data, $info);
		$this->unit->run($result, 'is_true', 'set', print_r($result, TRUE));
		$total = count($this->achievement_stat->list_stat(array('app_id' => $app_id, 'user_id' => $user_id)));
		$this->unit->run($total, 1, 'set', print_r($result, TRUE));
		
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'set', print_r($result, TRUE));
	}

	function increment_score_test(){
		$app_id = 7;
		$user_id = 2;
		$app_install_id = 3;
		$action_id = 103;
		$info = array('app_install_id' => $app_install_id,
									'page_id' => 6,
									'campaign_id' => 4,
									'action_id' => $action_id);
		$amount = 1;
		$result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
		$this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
		
		$total = count($this->achievement_stat->list_stat());
		$this->unit->run($total, 6, 'increment', print_r($total, TRUE));
		
		$res = $this->achievement_info->list_info(array('app_id' => $app_id, 'campaign_id' => 4));
		$achievement_id = $res[0]['_id'];
		
		$achievement_id_ref = MongoDBRef::create("achievement_info", 
																								new MongoId($achievement_id));
		$result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
		$this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
	}

  function increment_page_score_by_share_test(){
    $app_id = 1;
    $page_id = 6;
    $user_id = 2;
    $app_install_id = 3;
    $campaign_id = 4;
    $action_id = 108; // share
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    $result = $this->achievement_stat_page->get($page_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 10, 'get', print_r($result, TRUE));
    $this->unit->run($result['page_score'], 10, 'get', print_r($result, TRUE));
    $this->unit->run($result['action'][$action_id]['count'], 1, 'get', print_r($result, TRUE));
    
    
    $app_id = 2;
    $page_id = 6;
    $user_id = 2;
    $app_install_id = 3;
    $campaign_id = 4;
    $action_id = 108; // share
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    $result = $this->achievement_stat_page->get($page_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 20, 'get', print_r($result, TRUE));
    $this->unit->run($result['page_score'], 20, 'get', print_r($result, TRUE));
    $this->unit->run($result['action'][$action_id]['count'], 2, 'get', print_r($result, TRUE));

  }

  function increment_page_score_by_invite_test(){
    $app_id = 1;
    $page_id = 6;
    $user_id = 2;
    $app_install_id = 3;
    $campaign_id = 4;
    $action_id = 113; // invite
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    $result = $this->achievement_stat_page->get($page_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 30, 'get', print_r($result, TRUE));
    $this->unit->run($result['page_score'], 30, 'get', print_r($result, TRUE));
    $this->unit->run($result['action'][$action_id]['count'], 1, 'get', print_r($result, TRUE));
    
  }
  
  function increment_page_score_by_invalid_test(){
    $app_id = 1;
    $page_id = 6;
    $user_id = 2;
    $app_install_id = 3;
    $campaign_id = 4;
    $action_id = 999999; // invalid
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    $result = $this->achievement_stat_page->get($page_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 30, 'get', print_r($result, TRUE));
    $this->unit->run($result['page_score'], 30, 'get', print_r($result, TRUE));
  }
  
  function got_reward_by_page_test(){
    // add achievement
    $app_id = 1;
    $app_install_id = 1;
    $page_id = 1;
    $info = array('name' => 'class a',
                  'description' => 'class a',
                  'criteria_string' => array('page invite >= 2', 'page share >= 2'),
                  'page_id' => $page_id);
    $criteria = array('page.action.113.count' => 2, 'page.action.108.count' => 2);
    
    $result = $this->achievement_lib->add_achievement_info($app_id, $app_install_id, $info, $criteria);
    $this->unit->run(isset($result), 'is_true', 'add_test', print_r($result, TRUE));
    $total = count($this->achievement_info->list_info());
    $this->unit->run($total, 5, 'add_test', print_r($result, TRUE));
    
    // start increment
    $app_id = 1;
    $page_id = 1;
    $user_id = 1;
    $app_install_id = 1;
    $campaign_id = 1;
    $action_id = 113; // invite
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $app_install_id));
    $achievement_id = $res[0]['_id'];
    $achievement_id_ref = MongoDBRef::create("achievement_info", 
                                                new MongoId($achievement_id));
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    $action_id = 108; // share
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    // echo '<b>';
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    // echo '</b>';
    
     // test must got achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 1, 'increment', print_r($result, TRUE));
  }
  
  function got_reward_by_page_and_platform_score_achievement_test(){
    // add achievement
    $app_id = 100;
    $app_install_id = 1;
    $page_id = 100;
    $info = array('name' => 'class b',
                  'description' => 'class b',
                  'criteria_string' => array('page invite >= 2', 'page share >= 2', 'score >= 1'),
                  'page_id' => $page_id);
    $criteria = array('page.action.113.count' => 2,
                      'page.action.108.count' => 2,
                      'score' => 1);
    
    $result = $this->achievement_lib->add_achievement_info($app_id, $app_install_id, $info, $criteria);
    $this->unit->run(isset($result), 'is_true', 'add_test', print_r($result, TRUE));
    $total = count($this->achievement_info->list_info());
    $this->unit->run($total, 6, 'add_test', print_r($result, TRUE));
    
    // start increment
    $app_id = 100;
    $user_id = 100;
    $app_install_id = 1;
    $campaign_id = 1;
    $action_id = 113; // invite
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $app_install_id));
    $achievement_id = $res[0]['_id'];
    $achievement_id_ref = MongoDBRef::create("achievement_info", 
                                                new MongoId($achievement_id));
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    $action_id = 108; // share
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    // echo '<b>';
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    // echo '</b>';
    
     // test must got achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 1, 'increment', print_r($achievement_id_ref, TRUE));
  }
	
  function got_reward_by_page_and_platform_score_achievement_and_action_mix_test(){
    // add achievement
    $app_id = 1000;
    $app_install_id = 1;
    $page_id = 1000;
    $info = array('name' => 'class b',
                  'description' => 'class b',
                  'criteria_string' => array('page invite >= 2', 'page share >= 2', 'do something >= 1', 'score >= 1'),
                  'page_id' => $page_id);
    $criteria = array('page.action.113.count' => 2,
                      'page.action.108.count' => 2,
                      'action.100.count' => 1,
                      'score' => 1);
    
    $result = $this->achievement_lib->add_achievement_info($app_id, $app_install_id, $info, $criteria);
    $this->unit->run(isset($result), 'is_true', 'add_test', print_r($result, TRUE));
    $total = count($this->achievement_info->list_info());
    $this->unit->run($total, 7, 'add_test', print_r($result, TRUE));
    
    // start increment
    $app_id = 1000;
    $user_id = 1000;
    $app_install_id = 1;
    $campaign_id = 1;
    $action_id = 113; // invite
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    $amount = 1;
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $res = $this->achievement_info->list_info(array('app_id' => $app_id, 'app_install_id' => $app_install_id));
    $achievement_id = $res[0]['_id'];
    $achievement_id_ref = MongoDBRef::create("achievement_info", 
                                                new MongoId($achievement_id));
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    $action_id = 108; // share
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    // increment again
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    // increment again
    // echo '<b>';
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    // echo '</b>';
    
    // test still not get achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 0, 'increment', print_r($result, TRUE));
    
    $action_id = 100; // something
    $info = array('app_install_id' => $app_install_id,
                  'page_id' => $page_id,
                  'campaign_id' => $campaign_id,
                  'action_id' => $action_id);
    // increment again
    echo '<b>';
    $result = $this->achievement_lib->increment_achievement_stat($app_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', print_r($result, TRUE));
    echo '</b>';
    
    // test must got achievement
    $result = $this->achievement_user->list_user(array('user_id' => $user_id, 'achievement_id' => $achievement_id_ref));
    $this->unit->run(count($result), 1, 'increment', print_r($achievement_id_ref, TRUE));
    
  }
  
  function decrement_page_score_test(){
    $page_id = '1000';
    $user_id = '1000';
    $amount = '20';
    $result = $this->achievement_lib->increment_page_score($page_id, $user_id, $amount);
    $this->unit->run($result, TRUE, 'decrement', print_r($result, TRUE));
    
    $result = $this->achievement_stat_page->get((int)$page_id, (int)$user_id);
    $this->unit->run($result['page_score'], 70, 'decrement');
    
    $amount = '-10';
    $result = $this->achievement_lib->increment_page_score($page_id, $user_id, $amount);
    $this->unit->run($result, TRUE, 'decrement');
    
    $result = $this->achievement_stat_page->get((int)$page_id, (int)$user_id);
    $this->unit->run($result['page_score'], 60, 'decrement', print_r($result, TRUE));
  }
  
  function get_page_stat_test(){
    $page_id = '1000';
    $user_id = '1000';
    
    $result = $this->achievement_lib->get_page_stat($page_id, $user_id);
    $this->unit->run($result['page_score'], 60, 'decrement');
  }
	function end_test(){
		// $this->achievement_info->drop_collection();
		// $this->achievement_stat->drop_collection();
		// $this->achievement_user->drop_collection();
	}
}
/* End of file achievement_lib_test.php */
/* Location: ./application/controllers/test/achievement_lib_test.php */