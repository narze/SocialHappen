<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiv3_test extends CI_Controller {

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

	function apiv3($path = '') {
		return base_url('testmode/apiv3/'.$path);
	}

	function get($method, $params = array()) {
		if(!$params) { $params = array(); }
		$method_and_params = $method . '?' . http_build_query($params);
		$url = $this->apiv3($method_and_params);

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
		$url = $this->apiv3($method);

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

	function credit_add_test() {
		//check credits before add
		$this->load->model('company_model');
		$result = $this->company_model->get_company_profile_by_company_id(1);
		$this->unit->run($result['credits'] == 0, TRUE, "\$result['credits']", $result['credits']);

		$method = 'credit_add';

		$params = array(
			'credit' => 3,
			'company_id' => 1
		);

		$result = $this->post('credit_add', $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['company_id'] == 1, TRUE, "\$result['data']['company_id']", $result['data']['company_id']);
		$this->unit->run($result['data']['credits'] == 3, TRUE, "\$result['data']['credits']", $result['data']['credits']);

		$result = $this->post('credit_add', $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['company_id'] == 1, TRUE, "\$result['data']['company_id']", $result['data']['company_id']);
		$this->unit->run($result['data']['credits'] == 6, TRUE, "\$result['data']['credits']", $result['data']['credits']);

		$params['credit'] = -2;

		$result = $this->post('credit_add', $params);

		$this->unit->run($result['success'], TRUE, "\$result['success']", $result['success']);
		$this->unit->run($result['data']['company_id'] == 1, TRUE, "\$result['data']['company_id']", $result['data']['company_id']);
		$this->unit->run($result['data']['credits'] == 4, TRUE, "\$result['data']['credits']", $result['data']['credits']);

		//check credits after add
		$result = $this->company_model->get_company_profile_by_company_id(1);
		$this->unit->run($result['credits'] == 4, TRUE, "\$result['credits']", $result['credits']);
	}
}
