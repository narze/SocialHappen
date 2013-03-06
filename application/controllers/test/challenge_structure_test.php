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
		# add sonar_box
		$this->load->library('sonar_box_lib');

		$sonar_box = array(
			'company_id' => 1,
			'branch_id' => NULL,
			'id' => 1,
			'title' => 'Sonar Box',
			'data' => "0123",
		);
		$this->sonar_box_id = $this->sonar_box_lib->add($sonar_box);

		$sonar_box_2 = array(
			'company_id' => 1,
			'branch_id' => NULL,
			'id' => 2,
			'title' => 'Sonar Box 2',
			'data' => "3210",
		);
		$this->sonar_box_id_2 = $this->sonar_box_lib->add($sonar_box_2);

		# add branch
    $this->load->library('branch_lib');

		$branch = array(
      'company_id' => 1,
      'title' => 'branch 1',
      'location' => array(40, 40),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch = $this->branch_lib->add($branch);
    $this->branch_id = $this->branch['_id'];

		$branch_2 = array(
      'company_id' => 1,
      'title' => 'branch 2',
      'location' => array(40, 40),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch_2 = $this->branch_lib->add($branch_2);
    $this->branch_id_2 = $this->branch_2['_id'];

  	# add challenge with actions via apiv3/saveChallenge
		$old_challenge_model = array(
			"detail" => array(
				"name" => 'Test Challenge',
				"description" => "",
				"image" => "https://lh3.googleusercontent.com/XBLfCOS_oKO-XjeYiaOAuIdukQo9wXMWsdxJZLJO8hvWMBLFwCU3r_0BrRMn_c0TnEDarKuxDg=s640-h400-e365"
			),
			"hash" => NULL,
			"branches" => array(),
			"branches_data" => array(),
			"verify_location" => true,
			"custom_location" => false,
			"all_branch" => true,
			"criteria" => array(
				array(
					"query" => array(
						"action_id" => 206
					),
					"count" => 1,
					"name" => "Video",
					"action_data" => array(
						"data" => array(),
						"action_id" => 206
					),
					"sonar_code" => ""
				)
			),
			"active" => true,
			"company_id" => "2",
			"reward_items" => array(
				array(
					"name" => "Redeeming Points",
					"image" => "https://socialhappen.dyndns.org/socialhappen/assets/images/blank.png",
					"value" => 10,
					"status" => "published",
					"type" => "challenge",
					"description" => "10 Points for redeeming rewards in this company",
					"is_points_reward" => true,
					"redeem_method" => "in_store"
				)
			),
			"score" => 10,
			"start_date" => 1362543804,
			"end_date" => 1394079804,
			"repeat" => 1,
			"short_url" => null,
			"location" => array(0,0),
			"done_count_max" => 0,
			"done_count" => 0,
			"sonar_frequency" => ""
		);

		$model = $old_challenge_model;

		$model['criteria'][0]['sonar_boxes'] = array($this->sonar_box_id);
		// $model['criteria'][0]['codes'] = array(); // Codes should be derived from sonar_boxes
		$model['criteria'][0]['branches'] = array($this->branch_id);
		// $model['criteria'][0]['locations'] = array(); // Locations should be derived from branches
		$model['criteria'][0]['all_branches'] = FALSE;
		$model['criteria'][0]['custom_locations'] = array();
		$model['criteria'][0]['use_only_custom_locations'] = FALSE;
		$model['criteria'][0]['verify_location'] = TRUE;

		$this->unit->run($model['criteria'], TRUE, "", $model['criteria']);
		$params = array('model' => json_encode($model));

		$result = $this->postAPI('apiv3', 'saveChallenge', $params);
	}

  function challenge_should_get_locations_and_codes_from_actions() {
  	$this->_setup();

  	# it should have 1 challenge
  	$this->load->library('challenge_lib');
  	$result = $this->challenge_lib->get(array());
  	$this->unit->run(count($result) === 1, TRUE, "count(\$result) should be 1", count($result));

  	# it should have 2 sonar_box
  	$this->load->library('sonar_box_lib');
  	$boxes = $this->sonar_box_lib->get(array());
  	$this->unit->run(count($boxes) === 2, TRUE, "should have 2 sonar box", count($boxes));
  	$this->unit->run($boxes[0]['_id'].'' === $this->sonar_box_id_2, TRUE, "", $boxes[0]['_id'].'');
  	$this->unit->run($boxes[1]['_id'].'' === $this->sonar_box_id, TRUE, "", $boxes[1]['_id'].'');

  	# it should have 2 branch
  	$this->load->library('branch_lib');
  	$branches = $this->branch_lib->get(array());
  	$this->unit->run(count($branches) === 2, TRUE, "should have 2 branch", count($branches));
  	$this->unit->run($branches[0]['_id'].'' === $this->branch_id_2, TRUE, "", $branches[0]['_id'].'');
  	$this->unit->run($branches[1]['_id'].'' === $this->branch_id, TRUE, "", $branches[1]['_id'].'');

  	# it should have criteria (action) with new properties
  	$this->unit->run($result[0]['criteria'][0]['sonar_boxes'], "is_array", "sonar_boxes should be array", $result[0]['criteria'][0]['sonar_boxes']);
  	$this->unit->run($result[0]['criteria'][0]['sonar_boxes'] === array($this->sonar_box_id), TRUE, "sonar_boxes should be array", $result[0]['criteria'][0]['sonar_boxes']);
  	$this->unit->run($result[0]['criteria'][0]['branches'], "is_array", "branches should be array", $result[0]['criteria'][0]['branches']);
  	$this->unit->run($result[0]['criteria'][0]['all_branches'], "is_bool", "all_branches should be boolean", $result[0]['criteria'][0]['all_branches']);
  	$this->unit->run($result[0]['criteria'][0]['custom_locations'], "is_array", "custom_locations should be array", $result[0]['criteria'][0]['custom_locations']);
  	$this->unit->run($result[0]['criteria'][0]['use_only_custom_locations'], "is_bool", "use_only_custom_locations should be boolean", $result[0]['criteria'][0]['use_only_custom_locations']);
  	$this->unit->run($result[0]['criteria'][0]['verify_location'], "is_bool", "verify_location should be boolean", $result[0]['criteria'][0]['verify_location']);
  	$this->unit->run($result[0]['criteria'][0]['locations'], "is_array", "locations should be array", $result[0]['criteria'][0]['locations']);
  	$this->unit->run($result[0]['criteria'][0]['locations'] === array(array(40,40)), TRUE, "locations should be array", $result[0]['criteria'][0]['locations']);
  	$this->unit->run($result[0]['criteria'][0]['codes'], "is_array", "codes should be array", $result[0]['criteria'][0]['codes']);
  	$this->unit->run($result[0]['criteria'][0]['codes'] === array("0123"), TRUE, "codes should be array", $result[0]['criteria'][0]['codes']);

  	# it should have locations (from actions)
  	$this->unit->run($result[0]['locations'], "is_array", "locations should be array", $result[0]['locations']);
  	$this->unit->run($result[0]['locations'] === array(array(40,40)), TRUE, "locations should have action's location", $result[0]['locations']);

  	# it should have codes (from actions)
  	$this->unit->run($result[0]['codes'], "is_array", "codes should be array", $result[0]['codes']);
  	$this->unit->run($result[0]['codes'] === array("0123"), TRUE, "codes should have action's location", $result[0]['codes']);
  }

  function challenge_should_update_locations_and_codes_when_actions_are_updated() {

  }
}
/* End of file chalenge_structure_test.php */
/* Location: ./application/controllers/test/chalenge_structure_test.php */
