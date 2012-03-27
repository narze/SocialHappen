<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('USER_ID_1', 1);
define('USER_ID_2', 2);
define('USER_ID_3', 3);
class User_lib_test extends CI_Controller {
	
	var $user_lib;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('user_lib');
    $this->unit->reset_dbs();
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
	
	function create_index_test(){
		$this->user_lib->create_index();
	}
	
	function create_user_test() {
		$user_id = USER_ID_1;
		$additional_data = NULL;
		$result = $this->user_lib->create_user($user_id, $additional_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->user_id_1 = $result;

		$user_id = (string) USER_ID_2;
		$additional_data = array('challenge' => array('1234','5678'));
		$result = $this->user_lib->create_user($user_id, $additional_data);
		$this->unit->run($result, 'is_string', "\$result", $result);
		$this->user_id_2 = $result;

		$user_id = 'not_exist';
		$additional_data = array('challenge' => array('0000','1111'));
		$result = $this->user_lib->create_user($user_id, $additional_data);
		$this->unit->run($result, FALSE, "\$result", $result);
	}

	function _check_user_test() {
		$user = $this->user_mongo_model->getOne(array('_id' => new MongoId($this->user_id_1)));
		$this->unit->run($user['user_id'], USER_ID_1, "\$user['user_id']", $user['user_id']);

		$user = $this->user_mongo_model->getOne(array('_id' => new MongoId($this->user_id_2)));
		$this->unit->run($user['user_id'], USER_ID_2, "\$user['user_id']", $user['user_id']);
		$this->unit->run($user['challenge'][0], '1234', "\$user['challenge'][0]", $user['challenge'][0]);
	}

	function join_challenge_test() {
		$user_id = USER_ID_1;
		$challenge_hash = '1234';
		$result = $this->user_lib->join_challenge($user_id, $challenge_hash);
		$this->unit->run($result, TRUE, "\$result", $result);
		
		$user_id = (string) USER_ID_2;
		$challenge_hash = '4321';
		$result = $this->user_lib->join_challenge($user_id, $challenge_hash);
		$this->unit->run($result, TRUE, "\$result", $result);

		//This user did not have user record before, we'll add it
		$user_id = USER_ID_3;
		$challenge_hash = '5678';
		$result = $this->user_lib->join_challenge($user_id, $challenge_hash);
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function _check_user_test_2() {
		$user_id = USER_ID_1;
		$users = $this->user_mongo_model->get(array('user_id' => $user_id));
		$this->unit->run(count($users), 1, "count(\$users)", count($users));
		$this->unit->run($users[0]['challenge'], array('1234'), "\$users[0]['challenge']", print_r($users[0]['challenge'],TRUE));
	
		$user_id = USER_ID_2;
		$users = $this->user_mongo_model->get(array('user_id' => $user_id));
		$this->unit->run(count($users), 1, "count(\$users)", count($users));
		$this->unit->run($users[0]['challenge'], array('1234','5678','4321'), "\$users[0]['challenge']", print_r($users[0]['challenge'],TRUE));
	
		$user_id = USER_ID_3;
		$users = $this->user_mongo_model->get(array('user_id' => $user_id));
		$this->unit->run(count($users), 1, "count(\$users)", count($users));
		$this->unit->run($users[0]['challenge'], array('5678'), "\$users[0]['challenge']", print_r($users[0]['challenge'],TRUE));
	}

	function get_user_test() {
		$user_id = USER_ID_1;
		$user = $this->user_lib->get_user($user_id);
		$this->unit->run($user['user_id'], $user_id, "\$user['user_id']", $user['user_id']);
	}
}
/* End of file user_lib_test.php */
/* Location: ./application/controllers/test/user_lib_test.php */