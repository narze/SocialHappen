<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Challenge_structure_test extends CI_Controller {

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
  		if(preg_match("/(test|should)/",$method)){
  			$this->$method();
  			flush();
  		}
		}
	}

	function apiv3($path = '') {
		return base_url('testmode/apiv3/'.$path);
	}

	function apiv4($path = '') {
		return base_url('testmode/apiv4/'.$path);
	}

	function getAPI($v, $method, $params = array()) {
		if(!$params) { $params = array(); }
		$method_and_params = $method . '?' . http_build_query($params);
		$url = $this->{$v}($method_and_params);

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

	function postAPI($v, $method, $params = array()) {
		$url = $this->{$v}($method);

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

	function _setup() {
  	# add challenge with actions via apiv3/saveChallenge
  	$model = json_decode('
  		{
  			"detail":{
  				"name":"Test Challenge",
	  			"description":"",
	  			"image":"https://lh3.googleusercontent.com/XBLfCOS_oKO-XjeYiaOAuIdukQo9wXMWsdxJZLJO8hvWMBLFwCU3r_0BrRMn_c0TnEDarKuxDg=s640-h400-e365"
  			},
			  "hash":null,
			  "branches":[],
			  "branches_data":[],
			  "verify_location":true,
			  "custom_location":false,
			  "all_branch":true,
			  "criteria":[
			  	{"query":{"action_id":206},"count":1,"name":"Video","action_data":{"data":{},"action_id":206},"sonar_code":""}
			  ],
			  "active":true,
			  "company_id":"2",
			  "reward_items":[
			  	{"name":"Redeeming Points","image":"https://socialhappen.dyndns.org/socialhappen/assets/images/blank.png","value":10,"status":"published","type":"challenge","description":"10 Points for redeeming rewards in this company","is_points_reward":true,"redeem_method":"in_store"}
			  ],
			  "score":10,
			  "start_date":1362543804,
			  "end_date":1394079804,
			  "repeat":1,
			  "short_url":null,
			  "location":[0,0],
			  "done_count_max":0,
			  "done_count":0,
			  "sonar_frequency":""
			}', TRUE);

		$params = array('model' => json_encode($model));

		$result = $this->postAPI('apiv3', 'saveChallenge', $params);
	}

  function challenge_should_get_locations_and_codes_from_actions() {
  	$this->_setup();

  	# it should have 1 challenge
  	$this->load->library('challenge_lib');
  	$result = $this->challenge_lib->get(array());
  	$this->unit->run(count($result) === 1, TRUE, "count(\$result) should be 1", count($result));

  	# it should have locations (from actions)

  	# it should have codes (from actions)
  }

  function challenge_should_update_locations_and_codes_when_actions_are_updated() {

  }
}
/* End of file chalenge_structure_test.php */
/* Location: ./application/controllers/test/chalenge_structure_test.php */
