<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiv4_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
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
  			flush();
  		}
		}
	}

	function apiv4($path = '') {
		return base_url('testmode/apiv4/'.$path);
	}

	function get($method, $params = array()) {
		if(!$params) { $params = array(); }
		$method_and_params = $method . '?' . http_build_query($params);
		$url = $this->apiv4($method_and_params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response, TRUE);
	}

	function post($method, $params = array()) {
		$url = $this->apiv4($method);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response, TRUE);
	}

	function check_user_test() {
		$method = 'check_user';

		$params = array(
			'facebook_user_id' => '713558190'
		);
		$result = $this->get($method, $params);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['success'], 'is_true', "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_array', "\$result['data']", $result['data']);
		$this->unit->run($result['data']['user_id'] == 1, 'is_true', "\$result['data']['user_id']", $result['data']['user_id']);

		$params = array(
			'facebook_user_id' => '0000'
		);
		$result = $this->get($method, $params);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['success'] === FALSE, 'is_true', "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);

		//Failing test
		$params = array(

		);
		$result = $this->get($method, $params);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['success'] === FALSE, 'is_true', "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
	}

	function signup_post_test() {
		$method = 'signup';

		$params = array(
			'email' => '1@gotmail.com',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '12345678123',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_array', "\$result['data']", $result['data']);
		$this->unit->run($result['data']['user_id'], 'is_int', "\$result['data']['user_id']", $result['data']['user_id']);
		$this->unit->run($result['data']['token'], 'is_string', "\$result['data']['token']", $result['data']['token']);
		$this->unit->run(strlen($result['data']['token']) === 32, TRUE, "\$result['data']['token']", $result['data']['token']);

		$this->token = $result['data']['token'];

		//Check user's token
		$this->load->model('user_mongo_model');
		$user = $this->user_mongo_model->getOne(array('user_id' => $result['data']['user_id']));
		$this->unit->run($user['tokens'][0] === $this->token, TRUE, "\$user['tokens'][0]", $user['tokens'][0]);

		//false facebook : error
		$params = array(
			'email' => '2@gotmail.com',
			'password' => 'asdfjkl;',
			'facebook_user_id' => FALSE,
			'facebook_user_first_name' => FALSE,
			'facebook_user_last_name' => FALSE,
			'facebook_user_image' => FALSE
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'Please connect facebook before signing up', "\$result['data']", $result['data']);

		//undefined facebook : error
		$params = array(
			'email' => '3@gotmail.com',
			'password' => 'asdfjkl;'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'Please connect facebook before signing up', "\$result['data']", $result['data']);

		//facebook user id already registered : error
		$params = array(
			'email' => '4@gotmail.com',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '12345678123',
			'facebook_user_first_name' => 'NarzE',
			'facebook_user_last_name' => 'Nz',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'Facebook account already used', "\$result['data']", $result['data']);

		//email already registered : error
		$params = array(
			'email' => '1@gotmail.com',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'Email already used', "\$result['data']", $result['data']);

		//undefined email : error
		$params = array(
			'email' => '',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'No email and/or password', "\$result['data']", $result['data']);

		//undefined password : error
		$params = array(
			'email' => '6@gotmail.com',
			'password' => '',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'No email and/or password', "\$result['data']", $result['data']);
	}

	function signin_post_test() {
		$method = 'signin';

		//With facebook
		$params = array(
			'type' => 'facebook',
			'facebook_user_id' => 713558190
		);

		$result = $this->post($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['user_id'], 1, "\$result['data']['user_id']", $result['data']['user_id']);
		$this->unit->run($result['data']['token'], 'is_string', "\$result['data']['token']", $result['data']['token']);
		$this->unit->run($result['data']['user'], 'is_array', "\$result['data']['user']", $result['data']['user']);
		$this->unit->run(strlen($result['data']['token']), 32, "strlen(result['data']['token'])", strlen($result['data']['token']));

		//With facebook : not user
		$params = array(
			'type' => 'facebook',
			'facebook_user_id' => '713558190000'
		);

		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);

		//With email
		$params = array(
			'type' => 'email',
			'email' => '1@gotmail.com',
			'password' => 'asdfjkl;'
		);

		$result = $this->post($method, $params);
		$this->unit->run($result['success'], TRUE, "result['success']", $result['success']);
		$this->unit->run($result['data']['user_id'], 7, "result['data']['user_id']", $result['data']['user_id']);
		$this->unit->run($result['data']['token'], 'is_string', "result['data']['token']", $result['data']['token']);
		$this->unit->run($result['data']['user'], 'is_array', "result['data']['user']", $result['data']['user']);
		$this->unit->run(strlen($result['data']['token']), 32, "strlen(result['data']['token'])", strlen($result['data']['token']));

		//With email : not found
		$params = array(
			'type' => 'email',
			'email' => 'youarenotuser@gotmail.com',
			'password' => 'asdfjkl;'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "result['data']", $result['data']);

		//With email : wrong password
		$params = array(
			'type' => 'email',
			'email' => '1@gotmail.com',
			'password' => ';kljfdsa'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "result['data']", $result['data']);
	}

	function companies_get_test() {
		$method = 'companies';

		$params = NULL;

		$result = $this->get($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));
		$this->unit->run($result['data'][0]['company_id'] == 1, TRUE, "\$result['data'][0]['company_id']", $result['data'][0]['company_id']);
	}

	function _add_company_challenges_test() {
		//Add reward

	  $this->load->model('reward_item_model', 'reward_item');
	  $name = 'Chalenge reward';
	  $status = 'published';
	  $challenge_id = 'asdf';
	  $image = base_url().'assets/images/cam-icon.png';
	  $value = '200THB';
	  $description = 'This is pasta!!!';
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description');

	  $this->reward_item_id = $result = $this->reward_item->add_challenge_reward($input);
	  $this->unit->run($result, 'is_string', "\$result", $result);

	  $count = $this->reward_item->count_all();
	  $this->unit->run($count, 1, 'count', $count);

	  $reward = $this->reward_item->get_one(array('_id' => new MongoId($this->reward_item_id)));
	  $this->unit->run($reward['type'], 'challenge', "\$reward['type']", $reward['type']);

	  //Add challenges
	  $this->challenge = array(
	    'company_id' => 1,
	    'start' => time(),
	    'end' => time() + 86400,
	    'detail' => array(
	      'name' => 'Challenge name 1',
	      'description' => 'Challenge description',
	      'image' => 'Challenge image url'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C1',
	        'query' => array('page_id' => 1, 'app_id'=>1, 'action_id'=>1),
	        'count' => 1
	      ),
	      array(
	        'name' => 'C2',
	        'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
	        'count' => 2
	      )
	    ),
	  );

	  $this->challenge2 = array(
	    'company_id' => 1,
	    'start' => time(),
	    'end' => time() + 86400,
	    'detail' => array(
	      'name' => 'Challenge name 2',
	      'description' => 'Challenge description',
	      'image' => 'Challenge image url'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C3',
	        'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
	        'count' => 3
	      )
	    ),
	  );

	  $this->challenge3 = array(
	    'company_id' => 1,
	    'start' => time(),
	    'end' => time() + 86400,
	    'detail' => array(
	      'name' => 'Challenge name 3',
	      'description' => 'Challenge description',
	      'image' => 'Challenge image url'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C3',
	        'query' => array('action_id' => 201),
	        'count' => 2,
	        'is_platform_action' => TRUE
	      )
	    )
	  );

	  $this->challenge4 = array(
	    'company_id' => 2,
	    'start' => time(),
	    'end' => time() + 864000,
	    'detail' => array(
	      'name' => 'Daily Challenge',
	      'description' => 'You can play every day',
	      'image' => 'Challengeimage'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C4',
	        'query' => array('action_id' => 203),
	        'count' => 1,
	        'is_platform_action' => TRUE
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id)))
	  );

	  $this->load->library('challenge_lib');

	  $result = $this->challenge_lib->add($this->challenge);
	  $this->unit->run($result, TRUE, "\$result", $result);
	  $this->challenge_id = $result;

	  $result = $this->challenge_lib->add($this->challenge2);
	  $this->unit->run($result, TRUE, "\$result", $result);
	  $this->challenge_id2 = $result;

	  $result = $this->challenge_lib->add($this->challenge3);
	  $this->unit->run($result, TRUE, "\$result", $result);
	  $this->challenge_id3 = $result;

	  $result = $this->challenge_lib->add($this->challenge4);
	  $this->unit->run($result, TRUE, "\$result", $result);
	  $this->challenge_id4 = $result;
	}

	function challenges_get_test() {
		$method = 'challenges';

		$params = array();

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 4, TRUE, "count(\$result['data'])", count($result['data']));
	}

	function get_company_challenges_test() {
		$method = 'get_company_challenges';

		$params = array(
			'company_id' => 1
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result)", count($result['data']));

		$params = array(
			'company_id' => 2
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));

		//Failing test
		$params = array();

		$result = $this->get($method, $params);
		$this->unit->run($result['success'] === FALSE, TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result", $result['data']);
	}

	function _add_redeem_reward_test() {
		$this->load->model('reward_item_model');
		//Draft reward
		$name = 'name' . '1';
		$status = 'draft';
		$type = 'redeem';
		$company_id = 1;
		$redeem = array(
			'point' => 20,
			'amount' => 5,
			'once' => 1
		);
		$start_timestamp = time() - 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'company';
		$criteria_id = 1;
		$image = base_url().'assets/images/cam-icon.png';
		$value = '200';
		$description = 'This is pasta!!!';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id');

		$this->reward_item_1 = $result = $this->reward_item_model->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		//Published reward
		$name = 'name' . '2';
		$status = 'published';
		$type = 'redeem';
		$company_id = 1;
		$redeem = array(
			'point' => 20,
			'amount' => 5,
			'once' => 1
		);
		$start_timestamp = time() - 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'company';
		$criteria_id = 1;
		$image = base_url().'assets/images/cam-icon.png';
		$value = '200';
		$description = 'This is pasta!!!';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id');

		$this->reward_item_2 = $result = $this->reward_item_model->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);
	}

	function rewards_get_test() {
		$method = 'rewards';

		$params = array();

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']), 1, "count(\$result['data'])", count($result['data']));
	}

}
/* End of file apiv4_test.php */
/* Location: ./application/controllers/test/apiv4_test.php */