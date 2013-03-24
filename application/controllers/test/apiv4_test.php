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
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf',
			'device_token' => 'adfsgv23'
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
		// $this->load->model('user_mongo_model');
		// $this->user_mongo_model->recreateIndex();
		// $user = $this->user_mongo_model->getOne(array('user_id' => $result['data']['user_id']));
		// $this->unit->run($user['tokens'][0] === $this->token, TRUE, "\$user['tokens'][0]", $user['tokens'][0]);
		$this->load->model('user_token_model');
		$criteria = array(
			'user_id' => $this->user_id,
			'device' => 'ios',
			'device_id' => 'asdf',
			'device_token' => 'adfsgv23'
		);
		$user_token = $this->user_token_model->getOne($criteria);
		$this->unit->run($user_token['login_token'] === $this->token, TRUE, "\$user_token['login_token']", $user_token['login_token']);

		//facebook user id already registered : error
		$params = array(
			'email' => '4@gotmail.com',
			'phone' => '098765432',
			'password' => 'asdfjkl;',
			'facebook_user_id' => '12345678123',
			'facebook_user_first_name' => 'NarzE',
			'facebook_user_last_name' => 'Nz',
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf2',
			'device_token' => 'adsfa24'
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
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf3',
			'device_token' => '1y5vdfsag'
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
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf4',
			'device_token' => '125342t5exa'
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
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf5',
			'device_token' => '6y3534dc'
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
			'facebook_user_image' => 'https://graph.facebook.com/4/picture',
			'device' => 'ios',
			'device_id' => 'asdf6',
			'device_token' => '1248r72bcs'
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
		// $this->load->model('user_mongo_model');
		// $user = $this->user_mongo_model->get_user($this->user_id);
		// $this->unit->run(count($user['tokens']) === 2, TRUE, "count(\$user['tokens'])", count($user['tokens']));
		$this->load->model('user_token_model');
		$user_tokens = $this->user_token_model->get(array('user_id' => $this->user_id));
		$this->unit->run(count($user_tokens) === 2, TRUE, "count(\$user_tokens)", count($user_tokens));

		//Wrong token is success too ?
		$params = array(
			'user_id' => $this->user_id,
			'token' => $this->token . 'asdf'
		);

		$result = $this->post($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		//Check user's token removal : not removed because using invalid token
		// $user = $this->user_mongo_model->get_user($this->user_id);
		// $this->unit->run(count($user['tokens']) === 2, TRUE, "count(\$user['tokens'])", count($user['tokens']));
		$this->load->model('user_token_model');
		$user_tokens = $this->user_token_model->get(array('user_id' => $this->user_id));
		$this->unit->run(count($user_tokens) === 2, TRUE, "count(\$user_tokens)", count($user_tokens));

		//Use valid token
		$params = array(
			'user_id' => $this->user_id,
			'token' => $this->token
		);

		$result = $this->post($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		//Check user's token removal : removed
		// $user = $this->user_mongo_model->get_user($this->user_id);
		// $this->unit->run(count($user['tokens']) === 1, TRUE, "count(\$user['tokens'])", count($user['tokens']));
		$this->load->model('user_token_model');
		$user_tokens = $this->user_token_model->get(array('user_id' => $this->user_id));
		$this->unit->run(count($user_tokens) === 1, TRUE, "count(\$user_tokens)", count($user_tokens));

		//Failing test : non user's signout
		$params = array(
			'user_id' => $this->user_id + 123,
			'token' => $this->token
		);

		$result = $this->post($method, $params);

		// $this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);

		$this->load->model('user_token_model');
		$user_tokens = $this->user_token_model->get(array('user_id' => $this->user_id));
		$this->unit->run(count($user_tokens) === 1, TRUE, "count(\$user_tokens)", count($user_tokens));
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

		$params = array(
			'skip_system_company' => TRUE
		);

		$result = $this->get($method, $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 0, TRUE, "count(\$result)", count($result['data']));
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
	  $redeem_method = 'in_store';
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description', 'is_points_reward', 'redeem_method');

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
	  $redeem_method = 'in_store';
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description', 'is_points_reward', 'redeem_method');

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
	  $this->action_data_id_6_1 = $this->action_data_lib->add_action_data(204, array());
	  $this->action_data_id_6_2 = $this->action_data_lib->add_action_data(204, array());

		# add branch
    $this->load->library('branch_lib');

		$branch = array(
      'company_id' => 1,
      'title' => 'branch 1',
      'location' => array(0.001, 0.002),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch = $this->branch_lib->add($branch);
    $this->branch_id = $this->branch['_id'];

		$branch_2 = array(
      'company_id' => 1,
      'title' => 'branch 2',
      'location' => array(0.002, -0.004),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch_2 = $this->branch_lib->add($branch_2);
    $this->branch_id_2 = $this->branch_2['_id'];

		$branch_3 = array(
      'company_id' => 1,
      'title' => 'branch 3',
      'location' => array(-0.003, 0.001),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch_3 = $this->branch_lib->add($branch_3);
    $this->branch_id_3 = $this->branch_3['_id'];

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
	    )
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
	        'count' => 3,
	        'branches' => array($this->branch_id)
	      )
	    ),
	    // 'locations' => array(array(0.001, 0.002))
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
	        'action_data' => array('action_id' => $this->action_data_id),
	        'branches' => array($this->branch_id_2)
	      )
	    ),
	    'reward_items' => array(array('_id' => new MongoId($this->reward_item_id2))),
	    // 'locations' => array(array(0.002, -0.004))
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
	        'action_data' => array('action_id' => $this->action_data_id3),
	        'branches' => array($this->branch_id_3)
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id))),
	    // 'locations' => array(array(-0.003, 0.001))
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
	        'action_data' => array('action_id' => $this->action_data_id3),
	        'branches' => array($this->branch_id_3)
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id))), //54 points
	    // 'locations' => array(array(-0.003, 0.001)),
	    'done_count_max' => 54 //can play 1 time
	  );

	  $this->challenge6 = array(
	    'company_id' => 1,
	    'start' => time(),
	    'end' => time() + 864000,
	    'detail' => array(
	      'name' => 'Multi action challenge',
	      'description' => 'You can play every day',
	      'image' => 'Challengeimage'
	    ),
	    'criteria' => array(
	      array(
	        'name' => 'C61',
	        'query' => array('action_id' => 204),
	        'count' => 1,
	        'is_platform_action' => TRUE,
	        'action_data_id' => $this->action_data_id_6_1,
	        'action_data' => array('action_id' => $this->action_data_id_6_1),
	        'branches' => array($this->branch_id_3)
	      ),
	      array(
	        'name' => 'C62',
	        'query' => array('action_id' => 204),
	        'count' => 1,
	        'is_platform_action' => TRUE,
	        'action_data_id' => $this->action_data_id_6_2,
	        'action_data' => array('action_id' => $this->action_data_id_6_2),
	        'branches' => array($this->branch_id_3)
	      )
	    ),
	    'repeat' => 1,
	    'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id))), //54 points
	    // 'locations' => array(array(-0.003, 0.001)),
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

		$params = array('skip_system_company' => TRUE);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'] === $this->challenge_id4, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
	}

	function challenges_get_with_location_test() {
		$method = 'challenges';

		$params = array('lon' => 0, 'lat' => 0, 'max_distance' => NULL);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'], $this->challenge_id2, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
		$this->unit->run($result['data'][1]['_id'], $this->challenge_id4, "\$result['data'][1]['_id']", $result['data'][1]['_id']);
		$this->unit->run($result['data'][2]['_id'], $this->challenge_id3, "\$result['data'][2]['_id']", $result['data'][2]['_id']);

		$params = array('lon' => 0, 'lat' => 0, 'max_distance' => 0.003, 'and_without_location' => TRUE);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['_id'], $this->challenge_id2, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
		$this->unit->run($result['data'][1]['_id'], $this->challenge_id, "\$result['data'][1]['_id']", $result['data'][1]['_id']);

		//and_without_location will get challenges those don't have location set or location is not set
		$params = array('lon' => 100, 'lat' => 100, 'max_distance' => 0.003, 'and_without_location' => TRUE);

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
		$redeem_method = 'in_store';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id', 'redeem_method');

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
		$redeem_method = 'in_store';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id', 'redeem_method');

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
		$redeem_method = 'in_store';
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id','image','value','description','company_id', 'redeem_method');

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

	function _add_offer_reward_test() {
		$this->load->model('reward_item_model');
		//Draft reward
		$name = 'name' . '1';
		$status = 'draft';
		$type = 'offer';
		$company_id = 1;
		$start_timestamp = time() - 3600;
		$end_timestamp = time() + 7200;
		$image = base_url().'assets/images/cam-icon.png';
		$value = 'free';
		$description = 'This is pasta!!!';
		$input = compact('name', 'status', 'type', 'start_timestamp', 'end_timestamp','image','value','description','company_id');

		$this->offer_reward_1 = $result = $this->reward_item_model->add_offer_reward($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		//Published reward
		$name = 'name' . '2';
		$status = 'published';
		$type = 'offer';
		$company_id = 1;
		$start_timestamp = time() - 3600;
		$end_timestamp = time() + 7200;
		$image = base_url().'assets/images/cam-icon.png';
		$value = 'free';
		$description = 'This is pasta!!!';
		$input = compact('name', 'status', 'type', 'start_timestamp', 'end_timestamp','image','value','description','company_id');

		$this->offer_reward_2 = $result = $this->reward_item_model->add_offer_reward($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

	}

	function offers_get_test() {
		$method = 'offers';

		$params = array();

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

	function _init_company_2_credits_test() {
		$this->load->model('company_model');
		$company = array(
			'creator_user_id' => '1',
			'company_name' => 'test',
			'company_detail' => 'test',
			'company_address' => 'test',
			'company_email' => 'test@test.com',
			'company_telephone' => '021234567',
			'company_register_date' => '2011-05-09 17:52:17',
			'company_username' => 'test',
			'company_password' => 'test',
			'company_image' => 'test.jpg'
		);
		$company_id = $this->company_model->add_company($company);
		$this->unit->run($company_id,'is_int','add_company()');

		$company = $this->company_model->get_company_profile_by_company_id(2);
		$this->unit->run($company['credits'] == 0, TRUE, "\$company['credits']", $company['credits']);

		//set credits to 300
		$this->company_model->update_company_profile_by_company_id(2, array('credits' => 300));
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
	  $this->unit->run(count($result), 3 + 6 + 1, "count(\$result)", count($result)); //1 from signup, 2 from signin

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
  	// edit : it should invoke *failing* audit
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 6 + 1 + 1, "count(\$result)", count($result));
	  // $this->unit->run($result[0]['action_id'] === 118, TRUE, "\$result[0]['action_id']", $result[0]['action_id']);

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
	  $this->unit->run(count($result), 3 + 6 + 1 + 1 + 1 + 2, "count(\$result)", count($result)); //for action 203 and for completing challenge

  	//4. Check user for coupons/points/statuses
  	//@TODO
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(count($user['daily_challenge_completed'][$this->challenge_id4]) === 2, TRUE, "count(\$user['daily_challenge_completed'][$this->challenge_id4])", count($user['daily_challenge_completed'][$this->challenge_id4]));
  }

  function challenges_get_with_challenge_id_test_action_completed_time() {
  	$method = 'challenges';

  	$params = array(
  		'challenge_id' => $this->challenge_id3,
  		'user_id' => $this->user_id,
  		'token' => $this->token2
		);

		$result = $this->get($method, $params);

		$action = $result['data'][0]['criteria'][0];
		$this->unit->run(isset($action['completed']), TRUE, "\$action['completed']", $action['completed']);
		$this->unit->run($action['completed'] <= time() && $action['completed'] >= time() - 10, TRUE, "\$action['completed']", $action['completed']);
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

  	//company 2 doesn't exist :(
  	// $this->unit->run($result['data'][0]['company'], 'is_null', "\$result['data'][0]['company']", $result['data'][0]['company']);
  	$this->unit->run($result['data'][0]['challenge'], 'is_array', "\$result['data'][0]['challenge']", $result['data'][0]['challenge']);
  	// $this->unit->run($result['data'][0]['reward_item']['redeem_method'] === 'in_store', TRUE, "\$result['data'][0]['reward_item']['redeem_method']", $result['data'][0]['reward_item']['redeem_method']);
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
  		'challenge_id' => $this->challenge_id5,
  		'location' => '100.123,13.345' //long,lat
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);

  	//check audit count
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 12+ 2, "count(\$result)", count($result));

	  $this->unit->run($result[3]['action_id'] === 203, TRUE, "\$result[3]['action_id']", $result[3]['action_id']);
	  $this->unit->run($result[3]['subject'] === $params['location'], TRUE, "\$result[3]['subject']", $result[3]['subject']);

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
  	//edit : it should ad another *failing* audit
	  $this->load->library('audit_lib');
	  $result = $this->audit_lib->list_recent_audit(50);
	  $this->unit->run(count($result), 3 + 14 + 1, "count(\$result)", count($result)); //no audit add
	  $this->unit->run($result[0]['action_id'] === 120, TRUE, "\$result[0]['action_id']", $result[0]['action_id']);

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
  	//inject last_active
  	$this->load->model('user_token_model');
  	$user_criteria = array('user_id' => $this->user_id, 'login_token' => $this->token2);
		$this->user_token_model->update($user_criteria, array('$set' => array('last_active' => 0)));

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

  	//check last_active
  	$user = $this->user_token_model->getOne($user_criteria);
  	$this->unit->run($user['last_active'], 'is_int', "\$user['last_active']", $user['last_active']);
  }

  function _company_credit_check_test() {
  	//(company 2) from 300 points, should have 300 - 54*3 = 138 credits left
  	$this->load->model('company_model');
  	$company = $this->company_model->get_company_profile_by_company_id(2);
  	$this->unit->run($company['credits'] == 138, TRUE, "\$company['credits']", $company['credits']);
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

  function coupons_get_test_2_before() {
  	$method = 'coupons';

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 4, TRUE, "count(\$result['data'])", count($result['data']));
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
  	// and have 8 in total
  }

  function coupons_get_test_2_after() {
  	$method = 'coupons';

  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 8, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['company_id'] !== 0, TRUE, "\$result['data'][0]['company_id']", $result['data'][0]['company_id']);
  	$this->unit->run($result['data'][1]['company_id'] !== 0, TRUE, "\$result['data'][1]['company_id']", $result['data'][1]['company_id']);
  	$this->unit->run($result['data'][2]['company_id'] !== 0, TRUE, "\$result['data'][2]['company_id']", $result['data'][2]['company_id']);
  	$this->unit->run($result['data'][3]['company_id'] !== 0, TRUE, "\$result['data'][3]['company_id']", $result['data'][3]['company_id']);
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

  function notice_get_test() {
  	$method = 'notice';

  	$params = array(
  		'version' => 0.0
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['message'] === 'This is a test message', TRUE, "\$result['data']['message']", $result['data']['message']);

  	$params = array(
  		'version' => 1.1
  	);

  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'] === FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data'] === 'No notice', TRUE, "\$result['data']", $result['data']);
  }

  function mobile_config_get() {
    $method = 'mobile_config';

    $params = array();

    $result = $this->get($method, $params);
    $this->unit->run($result['success'] === TRUE, TRUE, "\$result['success']", $result['success']);
    $this->unit->run($result['data'] === $this->config->item('mobile_config'), TRUE, "\$result['data']", $result['data']);
  }

  /**
   * All challenges are out of stock if company credits is <= 0
   */
  function out_of_stock_test() {
		$method = 'challenges';

		//company 1
		$params = array(
			'company_id' => 1
		);

		//set credits > 0
		$this->load->model('company_model');
		$this->company_model->update_company_profile_by_company_id(1, array('credits' => 1));

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['is_out_of_stock'], FALSE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
		$this->unit->run($result['data'][1]['is_out_of_stock'], FALSE, "\$result['data'][1]['is_out_of_stock']", $result['data'][1]['is_out_of_stock']);
		$this->unit->run($result['data'][2]['is_out_of_stock'], FALSE, "\$result['data'][2]['is_out_of_stock']", $result['data'][2]['is_out_of_stock']);

		//set credits = 0
		$this->load->model('company_model');
		$this->company_model->update_company_profile_by_company_id(1, array('credits' => 0));

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 3, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['is_out_of_stock'], TRUE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
		$this->unit->run($result['data'][1]['is_out_of_stock'], TRUE, "\$result['data'][1]['is_out_of_stock']", $result['data'][1]['is_out_of_stock']);
		$this->unit->run($result['data'][2]['is_out_of_stock'], TRUE, "\$result['data'][2]['is_out_of_stock']", $result['data'][2]['is_out_of_stock']);

		//company 2
		$params = array(
			'company_id' => 2
		);

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['is_out_of_stock'], TRUE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
		$this->unit->run($result['data'][1]['is_out_of_stock'], FALSE, "\$result['data'][1]['is_out_of_stock']", $result['data'][1]['is_out_of_stock']);

		//set credits = 0
		$this->load->model('company_model');
		$this->company_model->update_company_profile_by_company_id(2, array('credits' => 0));

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['is_out_of_stock'], TRUE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
		$this->unit->run($result['data'][1]['is_out_of_stock'], TRUE, "\$result['data'][1]['is_out_of_stock']", $result['data'][1]['is_out_of_stock']);

		//set credits < 0
		$this->load->model('company_model');
		$this->company_model->update_company_profile_by_company_id(2, array('credits' => -1));

		$result = $this->get($method, $params);
		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run(count($result['data']) === 2, TRUE, "count(\$result['data'])", count($result['data']));
		$this->unit->run($result['data'][0]['is_out_of_stock'], TRUE, "\$result['data'][0]['is_out_of_stock']", $result['data'][0]['is_out_of_stock']);
		$this->unit->run($result['data'][1]['is_out_of_stock'], TRUE, "\$result['data'][1]['is_out_of_stock']", $result['data'][1]['is_out_of_stock']);
  }

  function challenges_get_for_walkin_test() {
  	$max_distance = 5;
  	$lat = 2;
  	$lng = 2;
  	$skip_system_company = TRUE; //challenge 4, 5
  	$and_without_location = TRUE;
  	$doable_date = date('Ymd', time() + 0);
  	$user_id = $this->user_id;
  	$token = $this->token2;

  	$params = compact('max_distance', 'lat', 'lng', 'skip_system_company', 'and_without_location', 'doable_date', 'user_id', 'token', 'params');
  	$method = 'challenges';
  	$result = $this->get($method, $params);

  	$this->unit->run(count($result['data']) === 2, TRUE, "\$result['data']", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] . '' === $this->challenge_id5, TRUE, "\$result['data'][0]['_id'] . ''", $result['data'][0]['_id'] . '');
  	$this->unit->run($result['data'][1]['_id'] . '' === $this->challenge_id4, TRUE, "\$result['data'][1]['_id'] . ''", $result['data'][1]['_id'] . '');

  	// Check next_date
  	$this->unit->run($result['data'][0]['next_date'] === date('Ymd', time() + 24*60*60), TRUE, "\$result['data'][0]['next_date']", $result['data'][0]['next_date']);
  	$this->unit->run($result['data'][1]['next_date'] === date('Ymd', time() + 24*60*60), TRUE, "\$result['data'][1]['next_date']", $result['data'][1]['next_date']);

  }

  function do_action_with_action_data_id_test() {
  	# remove user progress
  	$this->load->model('user_mongo_model');
  	$result = $this->user_mongo_model->update(array('user_id' => $this->user_id), array('$unset' => array('challenge_completed' => 1, 'daily_challenge_completed' => 1, 'challenge_redeeming' => 1, 'challenge_progress' => 1)));
  	$this->unit->run($result, TRUE, "reset user challenge progress", $result);

  	# add new challenge
 		$this->load->library('challenge_lib');

  	$result = $this->challenge_lib->add($this->challenge6);
  	$this->unit->run($result, TRUE, "\$result", $result);
  	$this->challenge_id6 = $result;

  	$method = 'do_action';

  	// User check
  	$this->load->model('user_mongo_model');
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(isset($user['challenge_progress'][$this->challenge_id6]['action_data']), FALSE, "action_data of challenge progress should not be set", isset($user['challenge_progress'][$this->challenge_id6]['action_data']));
  	$this->unit->run(isset($user['daily_challenge_completed'][$this->challenge_id6]), FALSE, "isset(\$user['daily_challenge_completed'][$this->challenge_id6])", isset($user['daily_challenge_completed'][$this->challenge_id6]));

  	# do the challenge's first action
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'challenge_id' => $this->challenge_id6,
  		'action_data_id' => $this->action_data_id_6_1,
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], FALSE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['action_completed'], TRUE, "action completed should be true", $result['data']['action_completed']);

  	$this->unit->run(isset($result['data']['reward_items'][0]['value']), FALSE, "reward items shouldn't be set", isset($result['data']['reward_items'][0]['value']));

  	//check challenge action progress
  	$result = $this->get('challenges', array('user_id' => $this->user_id,'token' => $this->token2,'challenge_id' => $this->challenge_id6));
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id6, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run($result['data'][0]['criteria'][0]['action_completed'] === TRUE, TRUE, "first action should be completed", $result['data'][0]['criteria'][0]['action_completed']);
  	$this->unit->run($result['data'][0]['criteria'][1]['action_completed'] === FALSE, TRUE, "second action should not be completed yet", $result['data'][0]['criteria'][1]['action_completed']);

  	// User check
  	$this->load->model('user_mongo_model');
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(count($user['challenge_progress'][$this->challenge_id6]['action_data']) === 1, TRUE, "action_data of challenge progress should be 1 (of 2)", count($user['challenge_progress'][$this->challenge_id6]['action_data']));
  	$this->unit->run($user['challenge_progress'][$this->challenge_id6]['action_data'] === array($params['action_data_id']), TRUE, "", $user['challenge_progress'][$this->challenge_id6]['action_data']);
  	$this->unit->run(isset($user['daily_challenge_completed'][$this->challenge_id6]), FALSE, "isset(\$user['daily_challenge_completed'][$this->challenge_id6])", isset($user['daily_challenge_completed'][$this->challenge_id6]));

  	# do the challenge's first action again
  	$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'challenge_id' => $this->challenge_id6,
  		'action_data_id' => $this->action_data_id_6_1,
  		'timestamp' => time(), //for test
  	);

  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], FALSE, "\$result['success']", $result['success']);
  	$this->unit->run($result['code'] === 2, TRUE, "code should be 2 (action already done)", $result['code']);

  	//check challenge action progress (should not be changed)
  	$result = $this->get('challenges', array('user_id' => $this->user_id,'token' => $this->token2,'challenge_id' => $this->challenge_id6));
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id6, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run($result['data'][0]['criteria'][0]['action_completed'] === TRUE, TRUE, "first action should be completed", $result['data'][0]['criteria'][0]['action_completed']);
  	$this->unit->run($result['data'][0]['criteria'][1]['action_completed'] === FALSE, TRUE, "second action should not be completed yet", $result['data'][0]['criteria'][1]['action_completed']);

  	// User check (should not be changed)
  	$this->load->model('user_mongo_model');
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(count($user['challenge_progress'][$this->challenge_id6]['action_data']) === 1, TRUE, "action_data of challenge progress should be 1 (of 2)", count($user['challenge_progress'][$this->challenge_id6]['action_data']));
  	$this->unit->run($user['challenge_progress'][$this->challenge_id6]['action_data'] === array($params['action_data_id']), TRUE, "", $user['challenge_progress'][$this->challenge_id6]['action_data']);
  	$this->unit->run(isset($user['daily_challenge_completed'][$this->challenge_id6]), FALSE, "isset(\$user['daily_challenge_completed'][$this->challenge_id6])", isset($user['daily_challenge_completed'][$this->challenge_id6]));

 		# do the challenge's second action
 		$params = array(
  		'user_id' => $this->user_id,
  		'token' => $this->token2,
  		'challenge_id' => $this->challenge_id6,
  		'action_data_id' => $this->action_data_id_6_2,
  		'timestamp' => time(), //for test
  	);

  	#challenge should be completed
  	$result = $this->post($method, $params);
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run($result['data']['challenge_completed'], TRUE, "\$result['data']['challenge_completed']", $result['data']['challenge_completed']);
  	$this->unit->run($result['data']['action_completed'], TRUE, "action completed should be true", $result['data']['action_completed']);

  	$this->unit->run($result['data']['reward_items'][0]['value'] === 54, TRUE, "\$result['data']['reward_items'][0]['value']", $result['data']['reward_items'][0]['value']);
  	$this->unit->run($result['data']['reward_items'][0]['is_points_reward'] === TRUE, TRUE, "\$result['data']['reward_items'][0]['is_points_reward']", $result['data']['reward_items'][0]['is_points_reward']);


  	//check challenge action progress
  	$result = $this->get('challenges', array('user_id' => $this->user_id,'token' => $this->token2,'challenge_id' => $this->challenge_id6));
  	$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
  	$this->unit->run(count($result['data']) === 1, TRUE, "count(\$result['data'])", count($result['data']));
  	$this->unit->run($result['data'][0]['_id'] === $this->challenge_id6, TRUE, "\$result['data'][0]['_id']", $result['data'][0]['_id']);
  	$this->unit->run($result['data'][0]['criteria'][0]['action_completed'] === FALSE, TRUE, "action progress cleared after challenge done", $result['data'][0]['criteria'][0]['action_completed']);
  	$this->unit->run($result['data'][0]['criteria'][1]['action_completed'] === FALSE, TRUE, "action progress cleared after challenge done", $result['data'][0]['criteria'][1]['action_completed']);

  	//User check
  	$this->load->model('user_mongo_model');
  	$user = $this->user_mongo_model->get_user($this->user_id);
  	$this->unit->run($user['user_id'], $this->user_id, "\$user['user_id']", $user['user_id']);
  	$this->unit->run(isset($user['challenge_progress'][$this->challenge_id6]), FALSE, "action_data of challenge progress should be 0 because challenge is completed", isset($user['challenge_progress'][$this->challenge_id6]));
  	$this->unit->run(count($user['daily_challenge_completed'][$this->challenge_id6]) === 1, TRUE, "count(\$user['daily_challenge_completed'][$this->challenge_id6])", count($user['daily_challenge_completed'][$this->challenge_id6]));
  }

  # claim reward tests
  # add more rewards before claiming
  function _add_machine_reward_test() {
	  //Add reward machine
	  $this->load->library('reward_machine_lib');
	  $reward_machine = array(
	  	'name' => 'Reward Machine A',
	  	'description' => NULL,
	  	'location' => array(0,0)
  	);
	  $this->reward_machine_id = $this->reward_machine_lib->add($reward_machine);
	  $this->unit->run($this->reward_machine_id, 'is_string', "reward machine id should be string", $this->reward_machine_id);

		//Add reward with reward_machine_id
	  $this->load->model('reward_item_model', 'reward_item');
	  $name = 'Instant Reward A';
	  $status = 'published';
	  $challenge_id = 'asdf';
	  $image = base_url().'assets/images/cam-icon.png';
	  $value = '0';
	  $description = 'From Gashapon Machine A';
	  $is_instant_reward = TRUE;
	  $reward_machine_id = $this->reward_machine_id;
	  $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description', 'is_instant_reward', 'reward_machine_id');

	  $this->instant_reward_item_id = $result = $this->reward_item->add_challenge_reward($input);
	  $this->unit->run($result, 'is_string', "\$result", $result);
  }

  function user_could_claim_reward_test() {
  	$user_id = 1;
  	$reward_item_id = $this->instant_reward_item_id;
  	$params = compact('user_id', 'reward_item_id');

  	$method = 'claim_reward';

  	$result = $this->post($method, $params);

  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run($result['data']['transaction_id'], 'is_string', "transaction_id should be a string", $result['data']['transaction_id']);
  	$this->unit->run($result['data']['reward_machine_id'], 'is_string', "reward_machine_id should be a string", $result['data']['reward_machine_id']);
  	$this->unit->run($result['data']['reward_machine_id'] === $this->reward_machine_id, TRUE, "reward_machine_id should match", $result['data']['reward_machine_id']);
  	$this->unit->run($result['timestamp'], 'is_int', "timestamp should be int", $result['timestamp']);

  	$this->instant_reward_transaction_id = $result['data']['transaction_id'];

  	$this->load->library('instant_reward_queue_lib');
  	$transaction = $this->instant_reward_queue_lib->get_by_id($this->instant_reward_transaction_id);
  	$this->unit->run($transaction['status'] === 'waiting', TRUE, "reward queue status should be 'waiting'", $transaction['status']);
  }

  function user_could_not_claim_reward_if_claimed_already_test() {
  	// TODO
  }

	function user_could_not_claim_reward_if_claiming_test() {
		$user_id = 1;
		$reward_item_id = $this->instant_reward_item_id;
		$params = compact('user_id', 'reward_item_id');

		$method = 'claim_reward';

		$result = $this->post($method, $params);

		$this->unit->run($result['success'], FALSE, "success should be false", $result['success']);
		$this->unit->run($result['data'], 'is_string', "data should be string (error message)", $result['data']);
		$this->unit->run($result['data'] === 'Reward claimed already', TRUE, "", $result['data']);
  }

  function user_could_not_claim_reward_if_user_has_not_owned_that_item_test() {
  	// TODO
  }

  function user_could_not_claim_reward_if_reward_is_not_instant_reward_type_test() {
  	$user_id = 1;
  	$reward_item_id = $this->reward_item_id;
  	$params = compact('user_id', 'reward_item_id');

  	$method = 'claim_reward';

  	$result = $this->post($method, $params);

  	$this->unit->run($result['success'], FALSE, "success should be false", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "data should be string (error message)", $result['data']);
  	$this->unit->run($result['data'] === 'Invalid reward', TRUE, "", $result['data']);
  }

  # reward released poll tests
  function should_not_return_success_if_transaction_dont_have_released_status_test() {
  	$user_id = 1;
  	$reward_item_id = $this->instant_reward_item_id;
  	$transaction_id = $this->instant_reward_transaction_id;
  	$params = compact('user_id', 'reward_item_id', 'transaction_id');

  	$method = 'reward_released_poll';

  	$result = $this->get($method, $params);

  	$this->unit->run($result['success'], FALSE, "result should be false", $result['success']);
  	$this->unit->run($result['data'], 'is_string', "data should be error message", $result['data']);
  	$this->unit->run($result['data'] === 'Reward not released yet', TRUE, "", $result['data']);
  }

  function should_return_success_if_transaction_have_released_status_test() {
  	$user_id = 1;
  	$reward_item_id = $this->instant_reward_item_id;
  	$transaction_id = $this->instant_reward_transaction_id;

  	# change transaction status to released
  	$this->load->model('instant_reward_queue_model');
  	$this->instant_reward_queue_model->update(array('_id' => new MongoId($transaction_id)), array('$set' => array('status' => 'released')));

  	$params = compact('user_id', 'reward_item_id', 'transaction_id');

  	$method = 'reward_released_poll';

  	$result = $this->get($method, $params);

  	$this->unit->run($result['success'], TRUE, "result should be true", $result['success']);

  	# change transaction status back to waiting
  	$this->load->model('instant_reward_queue_model');
  	$this->instant_reward_queue_model->update(array('_id' => new MongoId($transaction_id)), array('$set' => array('status' => 'waiting')));
  }

  # instant reward machine poll tests
  function should_not_return_release_if_the_machine_has_no_queue_test() {
  	$reward_machine_id = $this->reward_machine_id . 'asdf';
  	$params = compact('reward_machine_id');
  	$method = 'instant_reward_machine_poll';
  	$result = $this->get($method, $params);

  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run($result['data']['release'], FALSE, "release should be false", $result['data']['release']);
  }

  function should_return_release_if_the_machine_has_queue_test() {
  	$reward_machine_id = $this->reward_machine_id;
  	$params = compact('reward_machine_id');
  	$method = 'instant_reward_machine_poll';
  	$result = $this->get($method, $params);
  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run($result['data']['release'], TRUE, "release should be true", $result['data']['release']);
  	$this->unit->run($result['data']['transaction_id'] === $this->instant_reward_transaction_id, TRUE, "transaction_id should match", $result['data']['transaction_id']);
  	$this->unit->run($result['data']['user_id'] === 1, TRUE, "user_id should match", $result['data']['user_id']);
  }

  # instant reward machine released tests
  function should_request_with_released_and_change_transaction_status_to_released_test() {
  	$reward_machine_id = $this->reward_machine_id;
  	$transaction_id = $this->instant_reward_transaction_id;
  	$released = TRUE;
  	$params = compact('reward_machine_id', 'released', 'transaction_id');
  	$method = 'instant_reward_machine_released';
  	$result = $this->post($method, $params);

  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run(isset($result['data']['release']), FALSE, "release should not be set", isset($result['data']['release']));
  	$this->unit->run($result['data'], 'is_array', "data should be array", $result['data']);
  	$this->unit->run($result['data']['released'], TRUE, "released should be true", $result['data']);

  	# transaction status should be released
  	$this->load->library('instant_reward_queue_lib');
  	$transaction = $this->instant_reward_queue_lib->get_by_id($this->instant_reward_transaction_id);
  	$this->unit->run($transaction['status'] === 'released', TRUE, "status should be released", $transaction['status']);
  }

  # instant reward machine poll tests (again)
  function should_not_return_release_if_the_machine_has_queue_but_already_released_test() {
  	$reward_machine_id = $this->reward_machine_id;
  	$params = compact('reward_machine_id');
  	$method = 'instant_reward_machine_poll';
  	$result = $this->get($method, $params);

  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run($result['data']['release'], FALSE, "release should be false", $result['data']['release']);
  }

  # instant reward machine taken tests
  function should_request_with_taken_and_change_transaction_status_to_taken_test() {
  	$reward_machine_id = $this->reward_machine_id;
  	$transaction_id = $this->instant_reward_transaction_id;
  	$taken = TRUE;
  	$params = compact('reward_machine_id', 'taken', 'transaction_id');
  	$method = 'instant_reward_machine_taken';
  	$result = $this->post($method, $params);

  	$this->unit->run($result['success'], TRUE, "success should be true", $result['success']);
  	$this->unit->run(isset($result['data']['release']), FALSE, "release should not be set", isset($result['data']['release']));
  	$this->unit->run($result['data'], 'is_array', "data should be array", $result['data']);
  	$this->unit->run($result['data']['taken'], TRUE, "taken should be true", $result['data']);

  	# transaction status should be taken
  	$this->load->library('instant_reward_queue_lib');
  	$transaction = $this->instant_reward_queue_lib->get_by_id($this->instant_reward_transaction_id);
  	$this->unit->run($transaction['status'] === 'taken', TRUE, "status should be released", $transaction['status']);
  }
}
/* End of file apiv4_test.php */
/* Location: ./application/controllers/test/apiv4_test.php */
