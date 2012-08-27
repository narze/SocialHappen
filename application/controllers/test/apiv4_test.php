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

	function get($method_and_params) {
		$url = base_url('apiv4/'.$method_and_params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response, TRUE); //Normal JSON
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

}
/* End of file apiv4_test.php */
/* Location: ./application/controllers/test/apiv4_test.php */