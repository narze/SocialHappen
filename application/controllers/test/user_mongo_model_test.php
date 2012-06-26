<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_mongo_model_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_mongo_model');
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)/",$method)){
    			$this->$method();
    		}
		}
	}

	function setup_before_test(){
		$this->unit->reset_mongodb();
	}
	
	function create_index_test(){
		$this->user_mongo_model->recreateIndex();
	}

	function add_user_test() {
		$user = array(
			'user_id' => 1,
			'some_data' => 'blah'
		);
		return $this->user_mongo_model->add($user);
	}

	function add_reward_item_test() {
		$user_id = 1;
		$reward_item_id = '1234';
		$result = $this->user_mongo_model->add_reward_item($user_id, $reward_item_id);
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function get_user_test() {
		$user_id = 1;
		$user = $this->user_mongo_model->get_user($user_id);
		$this->unit->run($user['reward_items'][0], '1234', "\$user['reward_items'][0]", $user['reward_items'][0]);
	}
}
/* End of file user_mongo_model_test.php */
/* Location: ./application/controllers/test/user_mongo_model_test.php */