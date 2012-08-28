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
    		}
		}
	}

	function apiv4($path = '') {
		return base_url('testmode/apiv4/'.$path);
	}

	function get($method_and_params) {
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

	function post($method, $params) {
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

		$params = '?facebook_user_id=713558190';
		$result = $this->get($method . $params);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['success'], 'is_true', "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_array', "\$result['data']", $result['data']);
		$this->unit->run($result['data']['user_id'] == 1, 'is_true', "\$result['data']['user_id']", $result['data']['user_id']);


		$params = '?facebook_user_id=0000'; //not found
		$result = $this->get($method . $params);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['success'] === FALSE, 'is_true', "\$result['success']", $result['success']);
		$this->unit->run($result['data'], 'is_string', "\$result['data']", $result['data']);

		//Failing test
		$params = '?facebook_user_id='; //not specified
		$result = $this->get($method . $params);
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
		$this->unit->run($result['data'], 'is_int', "\$result['data']", $result['data']);

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

}
/* End of file apiv4_test.php */
/* Location: ./application/controllers/test/apiv4_test.php */