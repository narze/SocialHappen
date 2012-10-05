<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiv4_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
  	$this->unit->reset_dbs();
  	$this->socialhappen->reindex();
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
		if(json_decode($response, TRUE) === NULL) {
			echo '<pre>';
			var_dump($response);
			echo '</pre>';
			exit('Unexpected error');
		}
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
		if(json_decode($response, TRUE) === NULL) {
			echo '<pre>';
			var_dump($response);
			echo '</pre>';
			exit('Unexpected error');
		}
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
			'phone' => '098765432',
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
		$this->unit->run($result['data']['user'], 'is_array', "\$result['data']['user']", $result['data']['user']);
		$this->unit->run(strlen($result['data']['token']) === 32, TRUE, "\$result['data']['token']", $result['data']['token']);

		$this->user_id = $result['data']['user_id'];
		$this->token = $result['data']['token'];

		//Check user's token
		$this->load->model('user_mongo_model');
		$this->user_mongo_model->recreateIndex();
		$user = $this->user_mongo_model->getOne(array('user_id' => $result['data']['user_id']));
		$this->unit->run($user['tokens'][0] === $this->token, TRUE, "\$user['tokens'][0]", $user['tokens'][0]);

		//facebook user id already registered : error
		$params = array(
			'email' => '4@gotmail.com',
			'phone' => '098765432',
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
			'phone' => '098765432',
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
			'phone' => '098765432',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'No email, phone, password', "\$result['data']", $result['data']);

		//undefined password : error
		$params = array(
			'email' => '6@gotmail.com',
			'phone' => '098765432',
			'password' => '',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'No email, phone, password', "\$result['data']", $result['data']);

		//undefined phone : error
		$params = array(
			'email' => '6@gotmail.com',
			'phone' => '',
			'password' => '1234',
			'facebook_user_id' => '4',
			'facebook_user_first_name' => 'Zark',
			'facebook_user_last_name' => 'Muckerburg',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture'
		);
		$result = $this->post($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'No email, phone, password', "\$result['data']", $result['data']);
	}

	function _check_points_and_badges_in_new_users_test() {
		$user_id = $this->user_id;
		$this->load->model('user_mongo_model');
		$user = $this->user_mongo_model->get_user($user_id);

		// 10 from signup
		$this->unit->run($user['points'] === 10, TRUE, "\$user['points']", $user['points']);

		// user should have first badge 'Just Arrived'
		$this->load->library('achievement_lib');
		$achievements = $this->achievement_lib->list_user_achieved_by_user_id($user_id);
		$this->unit->run(count($achievements) === 1, TRUE, "count(\$achievements)", count($achievements));
		$this->unit->run($achievements[0]['achievement_info']['info']['name'] === "Just Arrived", TRUE, "\$achievements[0]['achievement_info']['info']['name']", $achievements[0]['achievement_info']['info']['name']);
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
		$this->token2 = $result['data']['token'];

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

	function signout_post_test() {
		$method = 'signout';

		//Check user's token before removal (1 from signup, 1 from signin)
		$this->load->model('user_mongo_model');
		$user = $this->user_mongo_model->get_user($this->user_id);
		$this->unit->run(count($user['tokens']) === 2, TRUE, "count(\$user['tokens'])", count($user['tokens']));

		//Wrong token is success too ?
		$params = array(
			'user_id' => $this->user_id,
			'token' => $this->token . 'asdf'
		);

		$result = $this->post($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		//Check user's token removal : not removed because using invalid token
		$user = $this->user_mongo_model->get_user($this->user_id);
		$this->unit->run(count($user['tokens']) === 2, TRUE, "count(\$user['tokens'])", count($user['tokens']));

		//Use valid token
		$params = array(
			'user_id' => $this->user_id,
			'token' => $this->token
		);

		$result = $this->post($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		//Check user's token removal : removed
		$user = $this->user_mongo_model->get_user($this->user_id);
		$this->unit->run(count($user['tokens']) === 1, TRUE, "count(\$user['tokens'])", count($user['tokens']));

		//Failing test : non user's signout
		$params = array(
			'user_id' => $this->user_id + 123,
			'token' => $this->token
		);

		$result = $this->post($method, $params);

		// $this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
	}

	function companies_get_test() {
		$method = 'companies';

		$params = NULL;

		$result = $this->get($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));
		$this->unit->run($result['data'][0]['company_id'] == 1, TRUE, "\$result['data'][0]['company_id']", $result['data'][0]['company_id']);

		$params = array(
			'company_id' => 1
		);

		$result = $this->get($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));
		$this->unit->run($result['data'][0]['company_id'] == 1, TRUE, "\$result['data'][0]['company_id']", $result['data'][0]['company_id']);

		$params = array(
			'company_id' => 55
		);

		$result = $this->get($method, $params);

		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
	}

	function _add_company_challenges_test() {
		//Add reward

	  $this->load->model('reward_item_model', 'reward_item');
	  $name = '54 Points reward';
	  $status = 'published';
	  $challenge_id = 'asdf';
	  $image = base_url().'assets/images/cam-icon.png';
	  $value = '54';
	  $description = 'This is pasta!!!';
	  $is_points_reward = TRUE;
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description', 'is_points_reward');

	  $this->reward_item_id = $result = $this->reward_item->add_challenge_reward($input);
	  $this->unit->run($result, 'is_string', "\$result", $result);

	  $count = $this->reward_item->count_all();
	  $this->unit->run($count, 1, 'count', $count);

	  $reward = $this->reward_item->get_one(array('_id' => new MongoId($this->reward_item_id)));
	  $this->unit->run($reward['type'], 'challenge', "\$reward['type']", $reward['type']);

	  $name = 'Regular reward';
	  $status = 'published';
	  $challenge_id = 'fasdfas';
	  $image = base_url().'assets/images/cam-icon.png';
	  $value = '500 THB';
	  $description = 'This is pasta!!!';
	  $is_points_reward = FALSE;
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description', 'is_points_reward');

	  $this->reward_item_id2 = $result = $this->reward_item->add_challenge_reward($input);
	  $this->unit->run($result, 'is_string', "\$result", $result);

	  $count = $this->reward_item->count_all();
	  $this->unit->run($count, 2, 'count', $count);

	  $reward = $this->reward_item->get_one(array('_id' => new MongoId($this->reward_item_id)));
	  $this->unit->run($reward['type'], 'challenge', "\$reward['type']", $reward['type']);

	  //Add empty action data
	  $this->load->library('action_data_lib');
	  $this->action_data_id = $this->action_data_lib->add_action_data(201, array());
	  $this->action_data_id2 = $this->action_data_lib->add_action_data(202, array());
	  $this->action_data_id3 = $this->action_data_lib->add_action_data(203, array());
	  $this->action_data_id4 = $this->action_data_lib->add_action_data(204, array());


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
	    'location' => array(0, 0)
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
	    'location' => array(0.001, 0.002)
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
	        'is_platform_action' => TRUE,
	        'action_data_id' => $this->action_data_id,
	        'action_data' => array('action_id' => $this->action_data_id)
	      )
	    ),
	    'reward_items' => array(array('_id' => new MongoId($this->reward_item_id2))),
	    'location' => array(0.002, -0.004)
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
	        'is_platform_action' => TRUE,
	        'action_data_id' => $this->action_data_id3,
	        'action_data' => array('action_id' => $this->action_data_id3)
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id))),
	    'location' => array(-0.003, 0.001)
	  );

	  $this->challenge5 = array(
	    'company_id' => 2,
	    'start' => time(),
	    'end' => time() + 864000,
	    'detail' => array(
	      'name' => 'Limited Daily Challenge',
	      'description' => 'You can play every day',
	      'image' => 'Challengeimage'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C4',
	        'query' => array('action_id' => 203),
	        'count' => 1,
	        'is_platform_action' => TRUE,
	        'action_data_id' => $this->action_data_id3,
	        'action_data' => array('action_id' => $this->action_data_id3)
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id))), //54 points
	    'location' => array(-0.003, 0.001),
	    'done_count_max' => 54 //can play 1 time
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

	function challenges_get_with_location_test() {
		$method = 'challenges';

		$params = array('lon' => 0, 'lat' => 0, 'max_distance' => NULL);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 4, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'], $this->challenge_id, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
		$this->unit->run($result['data'][1]['_id'], $this->challenge_id2, "\$result['data'][1]['_id']", $result['data'][1]['_id']);
		$this->unit->run($result['data'][2]['_id'], $this->challenge_id4, "\$result['data'][2]['_id']", $result['data'][2]['_id']);
		$this->unit->run($result['data'][3]['_id'], $this->challenge_id3, "\$result['data'][3]['_id']", $result['data'][3]['_id']);

		$params = array('lon' => 0, 'lat' => 0, 'max_distance' => 0.003);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'], $this->challenge_id, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
		$this->unit->run($result['data'][1]['_id'], $this->challenge_id2, "\$result['data'][1]['_id']", $result['data'][1]['_id']);

		$params = array('lon' => 0, 'lat' => 0, 'max_distance' => 0.003, 'limit' => 1);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'], $this->challenge_id, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
	}

	function challenges_get_with_company_id_test() {
		$method = 'challenges';

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
	}

	function challenges_get_with_challenge_id_test() {
		$method = 'challenges';

		$params = array(
			'challenge_id' => $this->challenge_id
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));

		$params = array(
			'challenge_id' => $this->challenge_id2
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result)", count($result['data']));
	}

	function challenges_get_with_doable_test_1() {
		$method = 'challenges';

		$params = array(
			'doable_date' => date('Ymd', time() + 0),
			'user_id' => $this->user_id,
			'token' => $this->token2,
			'company_id' => 1
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'] === $this->challenge_id3, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
		$this->unit->run(isset($result['data'][0]['next_date']), FALSE, "isset(\$result['data'][0]['next_date'])", isset($result['data'][0]['next_date']));
		$this->unit->run($result['data'][1]['_id'] === $this->challenge_id2, TRUE, "\$result['data'][1]['_id']", $result['data'][1]['_id']);
		$this->unit->run(isset($result['data'][1]['next_date']), FALSE, "isset(\$result['data'][1]['next_date'])", isset($result['data'][1]['next_date']));
		$this->unit->run($result['data'][2]['_id'] === $this->challenge_id, TRUE, "\$result['data'][2]['_id']", $result['data'][2]['_id']);
		$this->unit->run(isset($result['data'][2]['next_date']), FALSE, "isset(\$result['data'][2]['next_date'])", isset($result['data'][2]['next_date']));

		$params = array(
			'doable_date' => date('Ymd', time() + 0),
			'user_id' => $this->user_id,
			'token' => $this->token2,
			'company_id' => 2
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'] === $this->challenge_id4, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
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
			'amount' => 1,
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
			'amount' => 2,
			'once' => 0
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

		//Published reward
		$name = 'name' . '3';
		$status = 'published';
		$type = 'redeem';
		$company_id = 1;
		$redeem = array(
			'point' => 100,
			'amount' => 2,
			'once' => 0
		);
		$start_timestamp = time() - 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'company';
		$criteria_id = 1;
		$image = base_url().'assets/images/cam-icon.png';
		$value = '200';
		$description = 'This is pasta!!!';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id');

		$this->reward_item_3 = $result = $this->reward_item_model->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);
	}

	function rewards_get_test() {
		$method = 'rewards';

		$params = array();

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']), 2, "count(\$result['data'])", count($result['data']));

		$params = array(
			'reward_item_id' => $this->reward_item_1
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']), 1, "count(\$result['data'])", count($result['data']));
	}

	/**********************
	 * Requires user token
	 **********************/

	function check_token_get_test() {
		$method = 'check_token';
		$params = array(
			'user_id' => $this->user_id,
			'token' => $this->token2
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		$params['user_id'] = 123456;

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['data'] === 'Token invalid', TRUE, "\$result['data']", $result['data']);
	}

  function do_action_post_test() {
  	$method = 'do_action';

  	//1. challenge 4, requires 203 action, 1 time
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 203,
  		'challenge_id' => $this->challenge_id4,
  		'timestamp' => time(), //for test
  	);


  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['reward_items'][0]['value'] === 54, TRUE, "\$result['data']['reward_items'][0]['value']", $result['data']['reward_items'][0]['value']);
  	$this->unit->run($result['data']['reward_items'][0]['is_points_reward'] === TRUE, TRUE, "\$result['data']['reward_items'][0]['is_points_reward']", $result['data']['reward_items'][0]['is_points_reward']);

  	//User check
  	$this->load->model('user_mongo_model');
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(count($user['daily_challenge_completed'][$this->challenge_id4]) === 1, TRUE, "count(\$user['daily_challenge_completed'][$this->challenge_id4])", count($user['daily_challenge_completed'][$this->challenge_id4]));

  	//Fail : challenge invalid
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 203,
  		'challenge_id' => $this->challenge_id4 . 'asdf',
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], "Challenge invalid", "\$result['data']", $result['data']);

  	//2. challenge 3, requires 203 action, 2 times
  	//Fire 201 1 time : not yet completed
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 201,
  		'challenge_id' => $this->challenge_id3,
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], FALSE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['reward_items'][0] === NULL, TRUE, "\$result['data']['reward_items'][0]", $result['data']['reward_items'][0]);

  	//Fire wrong action_id : nothing happened
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 104,
  		'challenge_id' => $this->challenge_id3,
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], FALSE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['reward_items'][0] === NULL, TRUE, "\$result['data']['reward_items'][0]", $result['data']['reward_items'][0]);

  	//Fire 201 1 time : now completed, got non-point reward
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 201,
  		'challenge_id' => $this->challenge_id3,
  		'timestamp' => time(), //for test
  	);


  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['reward_items'][0]['is_points_reward'] === FALSE, TRUE, "\$result['data']['reward_items'][0]['is_points_reward']", $result['data']['reward_items'][0]['is_points_reward']);
  	$this->unit->run(get_mongo_id($result['data']['reward_items'][0]) === $this->reward_item_id2, TRUE, "\$result['data']['reward_items'][0]['_id']", get_mongo_id($result['data']['reward_items'][0]));

  	//check audit count
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 6, "count(\$result)", count($result)); //1 from signup, 2 from signin

  	//3.Since challenge_id 4 is a daily challenge, so user can play in tomorrow's time
  	//Fire today's challenge action again : error (already done)
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 203,
  		'challenge_id' => $this->challenge_id4,
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'Challenge done already (daily)', "\$result['data']", $result['data']);

  	//check audit count again, the last do_action method should not invoke audit_add
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 6, "count(\$result)", count($result));

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 203,
  		'challenge_id' => $this->challenge_id4,
  		'timestamp' => time() + 24*60*60, //for test
  	);

  	//Fire in tomorrow's time
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['reward_items'][0]['value'] === 54, TRUE, "\$result['data']['reward_items'][0]['value']", $result['data']['reward_items'][0]['value']);
  	$this->unit->run($result['data']['reward_items'][0]['is_points_reward'] === TRUE, TRUE, "\$result['data']['reward_items'][0]['is_points_reward']", $result['data']['reward_items'][0]['is_points_reward']);

  	//check audit count
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 6 + 2, "count(\$result)", count($result)); //for action 203 and for completing challenge

  	//4. Check user for coupons/points/statuses
  	//@TODO
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(count($user['daily_challenge_completed'][$this->challenge_id4]) === 2, TRUE, "count(\$user['daily_challenge_completed'][$this->challenge_id4])", count($user['daily_challenge_completed'][$this->challenge_id4]));
  }

  function challenges_get_with_doable_test_2() {
  	$method = 'challenges';

  	//Done 2 challenges [3,4] , remain 2 challenge today [1,2]
  	$params = array(
  		'doable_date' => date('Ymd', time() + 0),
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'company_id' => 1
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id3, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run(isset($result['data'][0]['next_date']), TRUE, "isset(\$result['data'][0]['next_date'])", isset($result['data'][0]['next_date']));
  	$this->unit->run($result['data'][1]['_id'] === $this->challenge_id2, TRUE, "\$result['data'][1]['_id']", $result['data'][1]['_id']);
  	$this->unit->run(isset($result['data'][1]['next_date']), FALSE, "isset(\$result['data'][1]['next_date'])", isset($result['data'][1]['next_date']));
  	$this->unit->run($result['data'][2]['_id'] === $this->challenge_id, TRUE, "\$result['data'][2]['_id']", $result['data'][2]['_id']);
  	$this->unit->run(isset($result['data'][2]['next_date']), FALSE, "isset(\$result['data'][2]['next_date'])", isset($result['data'][2]['next_date']));

  	$this->unit->run($result['data'][0]['next_date'] === FALSE, TRUE, "\$result['data'][0]['next_date']", $result['data'][0]['next_date']);

  	$params = array(
  		'doable_date' => date('Ymd', time() + 0),
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'company_id' => 2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id4, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run(isset($result['data'][0]['next_date']), TRUE, "isset(\$result['data'][0]['next_date'])", isset($result['data'][0]['next_date']));

  	$this->unit->run($result['data'][0]['next_date'] === date('Ymd', time() + 24*60*60), TRUE, "\$result['data'][0]['next_date']", $result['data'][0]['next_date']);

  	//Done 1(+1 cannot do anymore) challenge, remain 2 challenges tomorrow
  	$params = array(
  		'doable_date' => date('Ymd', time() + 24*60*60),
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'company_id' => 1
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id3, TRUE, "\$result['data'][0]['_id']['\$id']", $result['data'][0]['_id']);
  	$this->unit->run(isset($result['data'][0]['next_date']), TRUE, "isset(\$result['data'][0]['next_date'])", isset($result['data'][0]['next_date']));
  	$this->unit->run($result['data'][1]['_id'] === $this->challenge_id2, TRUE, "\$result['data'][1]['_id']['\$id']", $result['data'][1]['_id']);
  	$this->unit->run(isset($result['data'][1]['next_date']), FALSE, "isset(\$result['data'][1]['next_date'])", isset($result['data'][1]['next_date']));
  	$this->unit->run($result['data'][2]['_id'] === $this->challenge_id, TRUE, "\$result['data'][2]['_id']['\$id']", $result['data'][2]['_id']);
  	$this->unit->run(isset($result['data'][2]['next_date']), FALSE, "isset(\$result['data'][2]['next_date'])", isset($result['data'][2]['next_date']));

  	$this->unit->run($result['data'][0]['next_date'] === FALSE, TRUE, "\$result['data'][0]['next_date']", $result['data'][0]['next_date']);

  	$params = array(
  		'doable_date' => date('Ymd', time() + 24*60*60),
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'company_id' => 2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id4, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run(isset($result['data'][0]['next_date']), TRUE, "isset(\$result['data'][0]['next_date'])", isset($result['data'][0]['next_date']));

  	$this->unit->run($result['data'][0]['next_date'] === date('Ymd', time() + 2 * 24*60*60), TRUE, "\$result['data'][0]['next_date']", $result['data'][0]['next_date']);
  }

  function coupons_get_test() {
  	$method = 'coupons';

  	$params = array();

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'] === 'Invalid parameters', TRUE, "\$result['data']", $result['data']);

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));

  	$this->coupon_id = ''.$result['data'][0]['_id'];

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token //expired
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'] === 'Token invalid', TRUE, "\$result['data']", $result['data']);

  	// get with coupon_id
  	$params = array(
  		'coupon_id' => $this->coupon_id
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['company'], 'is_array', "\$result['data'][0]['company']", $result['data'][0]['company']);
  	$this->unit->run($result['data'][0]['challenge'], 'is_array', "\$result['data'][0]['challenge']", $result['data'][0]['challenge']);
  }

  function do_action_post_test_2() {
  	//add new challenge
  	$this->load->library('challenge_lib');
  	$result = $this->challenge_lib->add($this->challenge5);
  	$this->unit->run($result, TRUE, "\$result", $result);
  	$this->challenge_id5 = $result;

  	$method = 'do_action';

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'action_id' => 203,
  		'challenge_id' => $this->challenge_id5
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);

  	//check audit count
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 8 + 2, "count(\$result)", count($result));

	  //do_action in tomorrow : cannot do action which done_count >= done_count_max
	  $params = array(
	  	'user_id' => $this->user_id,
	  	'token' => $this->token2,
	  	'action_id' => 203,
	  	'challenge_id' => $this->challenge_id5,
	  	'timestamp' => time() + 24*60*60
	  );

	  $result = $this->post($method, $params);
	  $this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
	  $this->unit->run($result['data'] === 'Reward out of stock', TRUE, "\$result['data']", $result['data']);

  	//check audit count
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 10, "count(\$result)", count($result)); //no audit add
  }

  function challenges_get_test3() {
  	$method = 'challenges';
  	$params = array(
  		'company_id' => 2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][1]['_id'] === $this->challenge_id4, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id5, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run($result['data'][0]['is_out_of_stock'], TRUE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
  }

  function badges_get_test() {
  	$method = 'badges';
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['success']['data']) === 0, TRUE, "count(\$result['success']['data'])", count($result['success']['data']));
  }

  function profile_get_test() {
  	$method = 'profile';
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['user_id'] == $this->user_id, TRUE, "\$result['data']['user_id']", $result['data']['user_id']);
  	$this->unit->run($result['data']['points'] === 10 + 54 * 3, TRUE, "\$result['data']['points']", $result['data']['points']);
  	$this->unit->run($result['data']['challenge_completed'], 'is_array', "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['daily_challenge_completed'], 'is_array', "\$result['data']['daily_challenge_completed']", $result['data']['daily_challenge_completed']);
  	$this->unit->run($result['data']['shipping'], 'is_array', "\$result['data']['shipping']", $result['data']['shipping']);
  }

  function profile_post_test() {
  	$method = 'profile';
  	$params = array(
  		'model' => json_encode(array(
	  		'user_id' => $this->user_id,
	  		'token' => $this->token2,
	  		'user_first_name' => 'new first name',
	  		'user_last_name' => 'new last name',
	  		'user_email' => 'email@new.com',
	  		'user_phone' => '5555',
	  		'user_address' => 'new address',
	  		'shipping' => array(
	  			'name' => 'shipping name'
	  		)
	  	))
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['user_first_name'], 'new first name', "\$result['data']['user_first_name']", $result['data']['user_first_name']);
  	$this->unit->run($result['data']['user_last_name'], 'new last name', "\$result['data']['user_last_name']", $result['data']['user_last_name']);
  	$this->unit->run($result['data']['user_email'], 'email@new.com', "\$result['data']['user_email']", $result['data']['user_email']);
  	$this->unit->run($result['data']['user_phone'], '5555', "\$result['data']['user_phone']", $result['data']['user_phone']);
  	$this->unit->run($result['data']['user_address'], 'new address', "\$result['data']['user_address']", $result['data']['user_address']);
  	$this->unit->run($result['data']['shipping']['name'] === 'shipping name', TRUE, "\$result['data']['shipping']['name']", $result['data']['shipping']['name']);
  }

  function redeem_reward_post_test() {
  	$method = 'redeem_reward';
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'reward_item_id' => $this->reward_item_1,
  		'address' => json_encode(array('name' => 'name'))
  	);

  	// fail : unpublished reward
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
  	$this->unit->run($result['code'], 0, "\$result['code']", $result['code']);

  	// edit reward to published
  	$this->load->model('reward_item_model');
  	$result = $this->reward_item_model->update($this->reward_item_1, array('status' => 'published'));
  	$this->unit->run($result, TRUE, "\$result", $result);

  	// first redeem : success
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['coupon_id'], 'is_string', "\$result['data']['coupon_id']", $result['data']['coupon_id']);
  	$this->unit->run($result['data']['points_remain'] === 10 + 54 * 3 - 20, 'TRUE', "\$result['data']['points_remain']", $result['data']['points_remain']);

  	// redeem again : fail (that reward can be redeemed once)
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
  	$this->unit->run($result['code'], 1, "\$result['code']", $result['code']);

  	// redeem another reward
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'reward_item_id' => $this->reward_item_2,
  		'address' => json_encode(array('name' => 'name'))
  	);

  	// success
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['coupon_id'], 'is_string', "\$result['data']['coupon_id']", $result['data']['coupon_id']);
  	$this->unit->run($result['data']['points_remain'] === 10 + 54 * 3 - 20 - 20, 'TRUE', "\$result['data']['points_remain']", $result['data']['points_remain']);

  	// success : this reward can redeem more than one time
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['coupon_id'], 'is_string', "\$result['data']['coupon_id']", $result['data']['coupon_id']);
  	$this->unit->run($result['data']['points_remain'] === 10 + 54 * 3 - 20 - 20 - 20, 'TRUE', "\$result['data']['points_remain']", $result['data']['points_remain']);

  	// fail : reward out of stock
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
  	$this->unit->run($result['code'], 2, "\$result['code']", $result['code']);

  	// redeem another reward
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'reward_item_id' => $this->reward_item_3,
  		'address' => json_encode(array('name' => 'name'))
  	);

  	// success
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['coupon_id'], 'is_string', "\$result['data']['coupon_id']", $result['data']['coupon_id']);
  	$this->unit->run($result['data']['points_remain'] === 10 + 54 * 3 - 20 - 20 - 20 - 100, 'TRUE', "\$result['data']['points_remain']", $result['data']['points_remain']);

  	// fail : insufficient point (use 100, remaining 2)
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);
  	$this->unit->run($result['code'], 3, "\$result['code']", $result['code']);

  	// user should have coupons of...
  	// reward_item_1 : 1
  	// reward_item_2 : 2
  	// reward_item_3 : 1
  }

  function cards_get_test() {
  	$method = 'cards';

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'], 'is_array', "\$result['data']", $result['data']);
  	//1 from company 1, 3 from company 2
  	$this->unit->run(count($result['data']) === 4, TRUE, "count(\$result['data'])", count($result['data']));

  	// check card's challenge
  	$this->unit->run($result['data'][0]['challenge']['_id'], 'is_string', "\$result['data'][0]['challenge']['_id']", $result['data'][0]['challenge']['_id']);
  	$this->unit->run($result['data'][1]['challenge']['_id'], 'is_string', "\$result['data'][1]['challenge']['_id']", $result['data'][1]['challenge']['_id']);
  	$this->unit->run($result['data'][2]['challenge']['_id'], 'is_string', "\$result['data'][2]['challenge']['_id']", $result['data'][2]['challenge']['_id']);
  	$this->unit->run($result['data'][3]['challenge']['_id'], 'is_string', "\$result['data'][3]['challenge']['_id']", $result['data'][3]['challenge']['_id']);
  }
}
/* End of file apiv4_test.php */
/* Location: ./application/controllers/test/apiv4_test.php */