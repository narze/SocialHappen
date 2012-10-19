<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_token_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_token_model');
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
		$this->user_token_model->recreateIndex();
	}

	function add_user_token_test() {
		$data = array(
			'user_id' => 1,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);
		$result = $this->user_token_model->add_user_token($data);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run(strlen($result['login_token']) === 32, TRUE, "strlen(\$result['login_token'])", strlen($result['login_token']));

		//same device, overwrite existing
		$data = array(
			'user_id' => 2,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);
		$result = $this->user_token_model->add_user_token($data);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run(strlen($result['login_token']) === 32, TRUE, "strlen(\$result['login_token'])", strlen($result['login_token']));

		//count should equal 1, not 2
		$count = count($this->user_token_model->get(array()));
		$this->unit->run($count === 1, TRUE, "\$count", $count);

		//another device
		$data = array(
			'user_id' => 2,
			'device' => 'android',
			'device_token' => 'djsaf82334',
		);
		$result = $this->user_token_model->add_user_token($data);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run(strlen($result['login_token']) === 32, TRUE, "strlen(\$result['login_token'])", strlen($result['login_token']));

		//count should equal 2
		$count = count($this->user_token_model->get(array()));
		$this->unit->run($count === 2, TRUE, "\$count", $count);
	}

	function add_push_message_test() {
		$criteria = array(
			'user_id' => 2,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);
		$message = 'This is a push message';

		$result = $this->user_token_model->add_push_message($criteria, $message);
		$this->unit->run($result, TRUE, "\$result", $result);

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run(count($user['message_queue']) === 1, TRUE, "count(\$user['message_queue'])", count($user['message_queue']));
		$this->unit->run($user['message_queue'][0] === 'This is a push message', TRUE, "\$user['message_queue'][0]", $user['message_queue'][0]);

		$message = 'This is another push message';

		$result = $this->user_token_model->add_push_message($criteria, $message);
		$this->unit->run($result, TRUE, "\$result", $result);

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run(count($user['message_queue']) === 2, TRUE, "count(\$user['message_queue'])", count($user['message_queue']));
		$this->unit->run($user['message_queue'][1] === 'This is another push message', TRUE, "\$user['message_queue'][1]", $user['message_queue'][1]);

		//add message to another device : djsaf82334
		$criteria = array(
			'user_id' => 2,
			'device' => 'android',
			'device_token' => 'djsaf82334',
		);
		$message = 'This is a push message for another device';

		$result = $this->user_token_model->add_push_message($criteria, $message);
		$this->unit->run($result, TRUE, "\$result", $result);

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run(count($user['message_queue']) === 1, TRUE, "count(\$user['message_queue'])", count($user['message_queue']));
		$this->unit->run($user['message_queue'][0] === 'This is a push message for another device', TRUE, "\$user['message_queue'][0]", $user['message_queue'][0]);
	}

	function update_last_active_test() {
		$criteria = array(
			'user_id' => 2,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);

		//inject last_active to test
		$this->user_token_model->update($criteria, array('$set' => array('last_active' => 0)));

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run($user['last_active'] === 0, TRUE, "\$user['last_active']", $user['last_active']);

		$result = $this->user_token_model->update_last_active($criteria);

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run($user['last_active'] === time(), TRUE, "\$user['last_active']", $user['last_active']);
	}

	function list_all_user_with_message_test() {
		$result = $this->user_token_model->list_all_user_with_message();

		//should find 2 users
		$this->unit->run(count($result) === 2, TRUE, "count(\$result)", count($result));
	}

	function pull_active_user_message_test() {
		$criteria = array(
			'user_id' => 2,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);

		//inject last_update to test
		$this->user_token_model->update($criteria, array('$set' => array('last_active' => 0)));

		//device token djsaf82334 should have latest last_active, its message should be pulled
		$result = $this->user_token_model->pull_active_user_message();
		$this->unit->run($result['user']['device_token'] === 'djsaf82334', TRUE, "\$result['user']['device_token']", $result['user']['device_token']);
		$this->unit->run($result['message'] === 'This is a push message for another device', TRUE, "\$result['message']", $result['message']);

		//get user
		$criteria = array(
			'user_id' => 2,
			'device' => 'android',
			'device_token' => 'djsaf82334',
		);
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run($user['last_message_sent'] === time(), TRUE, "\$user['last_message_sent']", $user['last_message_sent']);
		$this->unit->run(count($user['message_queue']) === 0, TRUE, "count(\$user['message_queue'])", count($user['message_queue']));

		//device token djsaf82334 should have latest last_active, but it didn't have message so another device's message will be pulled
		$result = $this->user_token_model->pull_active_user_message();
		$this->unit->run($result['user']['device_token'] === '124adfshb8l', TRUE, "\$result['user']['device_token']", $result['user']['device_token']);
		$this->unit->run($result['message'] === 'This is a push message', TRUE, "\$result['message']", $result['message']);

		//pull again
		$result = $this->user_token_model->pull_active_user_message();
		$this->unit->run($result['user']['device_token'] === '124adfshb8l', TRUE, "\$result['user']['device_token']", $result['user']['device_token']);
		$this->unit->run($result['message'] === 'This is another push message', TRUE, "\$result['message']", $result['message']);

		//get user
		$user = $this->user_token_model->getOne($criteria);
		$this->unit->run($user['last_message_sent'] === time(), TRUE, "\$user['last_message_sent']", $user['last_message_sent']);
		$this->unit->run(count($user['message_queue']) === 0, TRUE, "count(\$user['message_queue'])", count($user['message_queue']));

		//pull again will return NULL (no more message in queue)
		$result = $this->user_token_model->pull_active_user_message();
		$this->unit->run($result === NULL, TRUE, "\$result", $result);
	}

	function list_all_user_with_message_after_pull_test() {
		$result = $this->user_token_model->list_all_user_with_message();

		//should find 0 user
		$this->unit->run(count($result) === 0, TRUE, "count(\$result)", count($result));
	}

	function remove_user_token_test() {
		$criteria = array(
			'user_id' => 2,
			'device' => 'ios',
			'device_token' => '124adfshb8l',
		);

		$result = $this->user_token_model->remove_user_token($criteria);
		$this->unit->run($result, TRUE, "\$result", $result);

		//count should equal 1, not 2
		$count = count($this->user_token_model->get(array()));
		$this->unit->run($count === 1, TRUE, "\$count", $count);
	}
}
/* End of file user_token_model_test.php */
/* Location: ./application/controllers/test/user_token_model_test.php */