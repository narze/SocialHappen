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
      'location' => array(0, -30),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    $this->branch_2 = $this->branch_lib->add($branch_2);
    $this->branch_id_2 = $this->branch_2['_id'];

    # add sonar_box
    $this->load->library('sonar_box_lib');

		$sonar_box = array(
			'company_id' => 1,
			'challenge_id' => NULL, # $this->challenge_id."",
			'action_data_id' => NULL, # $this->action_data_id,
			'id' => 1,
			'title' => 'Sonar Box',
			'data' => "0123",
		);
		$this->sonar_box_id = $this->sonar_box_lib->add($sonar_box);

    $sonar_box_2 = array(
      'company_id' => 1,
      'challenge_id' => NULL,
      'action_data_id' => NULL,
      'id' => 2,
      'title' => 'Sonar Box 2',
      'data' => "3210",
    );
    $this->sonar_box_id_2 = $this->sonar_box_lib->add($sonar_box_2);

		$sonar_box_3 = array(
			'company_id' => 1,
			'challenge_id' => NULL,
			'action_data_id' => NULL,
			'id' => 3,
			'title' => 'Sonar Box 3',
			'data' => "3333",
		);
		$this->sonar_box_id_3 = $this->sonar_box_lib->add($sonar_box_3);

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
            "action_id" => 204
          ),
          "count" => 1,
          "name" => "Walkin",
          "action_data" => array(
            "data" => array(),
            "action_id" => 204
          ),
          "sonar_code" => ""
        ),
        array(
          "query" => array(
            "action_id" => 204
          ),
          "count" => 1,
          "name" => "Walkin",
          "action_data" => array(
            "data" => array(),
            "action_id" => 204
          ),
          "sonar_code" => ""
        ),
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
      "company_id" => 1,
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

    // Walkin action
    $model['criteria'][0]['sonar_boxes'] = array($this->sonar_box_id);
    // $model['criteria'][0]['codes'] = array(); // Codes should be derived from sonar_boxes
    $model['criteria'][0]['branches'] = array($this->branch_id);
    // $model['criteria'][0]['locations'] = array(); // Locations should be derived from branches
    $model['criteria'][0]['all_branches'] = FALSE;
    $model['criteria'][0]['custom_locations'] = array();
    $model['criteria'][0]['use_only_custom_locations'] = FALSE;
    $model['criteria'][0]['verify_location'] = TRUE;

    // Walkin action 2
    $model['criteria'][1]['sonar_boxes'] = array();
    $model['criteria'][1]['codes'] = array('1102', '1230'); // Codes should NOT be derived from sonar_boxes (empty sonar_boxes)
    $model['criteria'][1]['branches'] = array();
    // $model['criteria'][1]['locations'] = array(); // Locations should be derived from branches
    $model['criteria'][1]['all_branches'] = FALSE;
    $model['criteria'][1]['custom_locations'] = array();
    $model['criteria'][1]['use_only_custom_locations'] = FALSE;
    $model['criteria'][1]['verify_location'] = TRUE;

    // Video action
    $model['criteria'][2]['sonar_boxes'] = array($this->sonar_box_id_3);
    $model['criteria'][2]['codes'] = array('0220', '2002'); // Codes should NOT be derived from sonar_boxes
    $model['criteria'][2]['branches'] = array();
    // $model['criteria'][2]['locations'] = array(); // Locations should be derived from branches
    $model['criteria'][2]['all_branches'] = FALSE;
    $model['criteria'][2]['custom_locations'] = array();
    $model['criteria'][2]['use_only_custom_locations'] = FALSE;
    $model['criteria'][2]['verify_location'] = TRUE;

    $this->unit->run($model['criteria'], TRUE, "", $model['criteria']);
    $params = array('model' => json_encode($model));
    $save_challenge_result = $this->postAPI('apiv3', 'saveChallenge', $params);

    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get(array());
    $this->challenge_id = $result[0]['_id'];
    $this->action_data_id = $result[0]['criteria'][0]['action_data_id'];
    $this->action_data_id_2 = $result[0]['criteria'][1]['action_data_id'];
    $this->action_data_id_3 = $result[0]['criteria'][2]['action_data_id'];
  }

  function challenge_should_get_locations_and_codes_from_actions() {
  	$this->_setup();

  	# it should have 1 challenge
  	$this->load->library('challenge_lib');
  	$result = $this->challenge_lib->get(array());
  	$this->unit->run(count($result) === 1, TRUE, "count(\$result) should be 1", count($result));

  	# it should have 3 sonar_box
  	$this->load->library('sonar_box_lib');
  	$boxes = $this->sonar_box_lib->get(array());
    $boxes = array_reverse($boxes);
  	$this->unit->run(count($boxes) === 3, TRUE, "should have 3 sonar box", count($boxes));
  	$this->unit->run($boxes[0]['_id'].'' === $this->sonar_box_id, TRUE, "", $boxes[0]['_id'].'');
    $this->unit->run($boxes[1]['_id'].'' === $this->sonar_box_id_2, TRUE, "", $boxes[1]['_id'].'');
  	$this->unit->run($boxes[2]['_id'].'' === $this->sonar_box_id_3, TRUE, "", $boxes[2]['_id'].'');
    # and it should have challenge_id and action_data_id for querying at ease
    $this->unit->run($boxes[0]['challenge_id'] === $this->challenge_id.'', TRUE, "", $boxes[0]['challenge_id']);
    $this->unit->run($boxes[0]['action_data_id'] === $this->action_data_id, TRUE, "", $boxes[0]['action_data_id']);
    # it should not have challenge_id and action_data_id in box 3, because it is a video action
    $this->unit->run($boxes[2]['challenge_id'] === NULL, TRUE, "", $boxes[2]['challenge_id']);
    $this->unit->run($boxes[2]['action_data_id'] === NULL, TRUE, "", $boxes[2]['action_data_id']);

  	# it should have 2 branch
  	$this->load->library('branch_lib');
  	$branches = $this->branch_lib->get(array());
  	$this->unit->run(count($branches) === 2, TRUE, "should have 2 branch", count($branches));
  	$this->unit->run($branches[0]['_id'].'' === $this->branch_id_2, TRUE, "", $branches[0]['_id'].'');
  	$this->unit->run($branches[1]['_id'].'' === $this->branch_id, TRUE, "", $branches[1]['_id'].'');

    # it should have locations (from actions)
    $this->unit->run($result[0]['locations'], "is_array", "locations should be array", $result[0]['locations']);
    $this->unit->run($result[0]['locations'] === array(array(40,40)), TRUE, "locations should have action's location", $result[0]['locations']);

    # it should have codes (from actions)
    $this->unit->run($result[0]['codes'], "is_array", "codes should be array", $result[0]['codes']);
    $this->unit->run($result[0]['codes'] === array("0123",'1102','1230','0220','2002'), TRUE, "codes should match", print_r($result[0]['codes'], TRUE));

    ## action 1 (Walkin action)
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

    ## action 2 (Walkin action)
    # it should have criteria (action) with new properties
    $this->unit->run($result[0]['criteria'][1]['sonar_boxes'], "is_array", "sonar_boxes should be array", $result[0]['criteria'][1]['sonar_boxes']);
    $this->unit->run($result[0]['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_boxes should be array", $result[0]['criteria'][1]['sonar_boxes']);
    $this->unit->run($result[0]['criteria'][1]['branches'], "is_array", "branches should be array", $result[0]['criteria'][1]['branches']);
    $this->unit->run($result[0]['criteria'][1]['all_branches'], "is_bool", "all_branches should be boolean", $result[0]['criteria'][1]['all_branches']);
    $this->unit->run($result[0]['criteria'][1]['custom_locations'], "is_array", "custom_locations should be array", $result[0]['criteria'][1]['custom_locations']);
    $this->unit->run($result[0]['criteria'][1]['use_only_custom_locations'], "is_bool", "use_only_custom_locations should be boolean", $result[0]['criteria'][1]['use_only_custom_locations']);
    $this->unit->run($result[0]['criteria'][1]['verify_location'], "is_bool", "verify_location should be boolean", $result[0]['criteria'][1]['verify_location']);
    $this->unit->run($result[0]['criteria'][1]['locations'], "is_array", "locations should be array", $result[0]['criteria'][1]['locations']);
    $this->unit->run($result[0]['criteria'][1]['locations'] === array(), TRUE, "locations should be array", $result[0]['criteria'][1]['locations']);
    $this->unit->run($result[0]['criteria'][1]['codes'], "is_array", "codes should be array", $result[0]['criteria'][1]['codes']);
    $this->unit->run($result[0]['criteria'][1]['codes'] === array('1102', '1230'), TRUE, "codes should be array", $result[0]['criteria'][1]['codes']);

    ## action 3 (Video action)
  	# it should have criteria (action) with new properties
  	$this->unit->run($result[0]['criteria'][2]['sonar_boxes'], "is_array", "sonar_boxes should be array", $result[0]['criteria'][2]['sonar_boxes']);
  	$this->unit->run($result[0]['criteria'][2]['sonar_boxes'] === array(), TRUE, "sonar_boxes should be array", $result[0]['criteria'][2]['sonar_boxes']);
  	$this->unit->run($result[0]['criteria'][2]['branches'], "is_array", "branches should be array", $result[0]['criteria'][2]['branches']);
  	$this->unit->run($result[0]['criteria'][2]['all_branches'], "is_bool", "all_branches should be boolean", $result[0]['criteria'][2]['all_branches']);
  	$this->unit->run($result[0]['criteria'][2]['custom_locations'], "is_array", "custom_locations should be array", $result[0]['criteria'][2]['custom_locations']);
  	$this->unit->run($result[0]['criteria'][2]['use_only_custom_locations'], "is_bool", "use_only_custom_locations should be boolean", $result[0]['criteria'][2]['use_only_custom_locations']);
  	$this->unit->run($result[0]['criteria'][2]['verify_location'], "is_bool", "verify_location should be boolean", $result[0]['criteria'][2]['verify_location']);
  	$this->unit->run($result[0]['criteria'][2]['locations'], "is_array", "locations should be array", $result[0]['criteria'][2]['locations']);
  	$this->unit->run($result[0]['criteria'][2]['locations'] === array(), TRUE, "locations should be array", $result[0]['criteria'][2]['locations']);
  	$this->unit->run($result[0]['criteria'][2]['codes'], "is_array", "codes should be array", $result[0]['criteria'][2]['codes']);
  	$this->unit->run($result[0]['criteria'][2]['codes'] === array("0220", "2002"), TRUE, "codes should be array", $result[0]['criteria'][2]['codes']);
  }

  function challenge_should_update_locations_and_codes_when_actions_are_updated() {
  	$this->load->library('challenge_lib');
  	$this->load->library('sonar_box_lib');
  	$this->load->library('branch_lib');

  	## branch
  	# add more branch into challenge action
  	$this->challenge_lib->update(array('_id' => $this->challenge_id), array('$set' => array('criteria.0.branches' => array($this->branch_id, $this->branch_id_2))));

  	# locations should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['locations']) === 2, TRUE, "challenge's action should have 2 locations", count($challenge['criteria'][0]['locations']));
  	$this->unit->run(count($challenge['locations']) === 2, TRUE, "challenge should have 2 locations as well", count($challenge['locations']));
  	$this->unit->run($challenge['locations'][0] === array(40,40), TRUE, "location should match", $challenge['locations'][0]);
  	$this->unit->run($challenge['locations'][1] === array(0,-30), TRUE, "location should match", $challenge['locations'][1]);
  	$this->unit->run($challenge['criteria'][0]['locations'][0] === array(40,40), TRUE, "location should match", $challenge['criteria'][0]['locations'][0]);
  	$this->unit->run($challenge['criteria'][0]['locations'][1] === array(0,-30), TRUE, "location should match", $challenge['criteria'][0]['locations'][1]);

  	# edit branch location
  	$this->branch_lib->update(array('_id' => new MongoId($this->branch_id_2)), array('location' => array(-10, 25)));

  	# locations should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['locations']) === 2, TRUE, "challenge's action should have 2 locations", count($challenge['criteria'][0]['locations']));
  	$this->unit->run(count($challenge['locations']) === 2, TRUE, "challenge should have 2 locations as well", count($challenge['locations']));
  	$this->unit->run($challenge['locations'][0] === array(40,40), TRUE, "location should match", $challenge['locations'][0]);
  	$this->unit->run($challenge['locations'][1] === array(-10,25), TRUE, "location should match", $challenge['locations'][1]);
  	$this->unit->run($challenge['criteria'][0]['locations'][0] === array(40,40), TRUE, "location should match", $challenge['criteria'][0]['locations'][0]);
  	$this->unit->run($challenge['criteria'][0]['locations'][1] === array(-10,25), TRUE, "location should match", $challenge['criteria'][0]['locations'][1]);

  	# remove branch 1 from challenge
  	$this->challenge_lib->update(array('_id' => $this->challenge_id), array('$set' => array('criteria.0.branches' => array($this->branch_id_2))));

  	# locations should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['locations']) === 1, TRUE, "challenge's action should have 1 location", count($challenge['criteria'][0]['locations']));
  	$this->unit->run(count($challenge['locations']) === 1, TRUE, "challenge should have 1 location as well", count($challenge['locations']));
  	$this->unit->run($challenge['locations'][0] === array(-10,25), TRUE, "location should match", $challenge['locations'][0]);
  	$this->unit->run($challenge['criteria'][0]['locations'][0] === array(-10,25), TRUE, "location should match", $challenge['criteria'][0]['locations'][0]);

  	# remove branch 2 from system
  	$this->branch_lib->remove(array('_id' => new MongoId($this->branch_id_2)));

  	# locations & branches should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['branches']) === 0, TRUE, "challenge's action should have 0 branch", count($challenge['criteria'][0]['branches']));
  	$this->unit->run(count($challenge['criteria'][0]['locations']) === 0, TRUE, "challenge's action should have 0 location", count($challenge['criteria'][0]['locations']));
  	$this->unit->run(count($challenge['locations']) === 0, TRUE, "challenge should have 0 location as well", count($challenge['locations']));

  	## sonar_box
  	# add more sonar box into challenge action by updating the sonar box itself
  	// $sonar_box_update = array(
  	// 	'$set' => array(
  	// 		'challenge_id' => $this->challenge_id."",
  	// 		'action_data_id' => $this->action_data_id
  	// 	)
  	// );
  	// $this->sonar_box_lib->update(array('_id' => new MongoId($this->sonar_box_id_2)), $sonar_box_update);
    # add by challenge update
    $this->challenge_lib->update(array('_id' => $this->challenge_id), array('$set' => array('criteria.0.sonar_boxes' => array($this->sonar_box_id, $this->sonar_box_id_2))));

  	# codes should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['codes']) === 2, TRUE, "challenge's action should have 2 codes now", count($challenge['criteria'][0]['codes']));
  	$this->unit->run(count($challenge['codes']) === 6, TRUE, "challenge should have 5+1=6 codes", count($challenge['codes']));
  	$this->unit->run($challenge['codes'][0] === "0123", TRUE, "code should match", $challenge['codes'][0]);
  	$this->unit->run($challenge['codes'][1] === "3210", TRUE, "code should match", $challenge['codes'][1]);
  	$this->unit->run($challenge['criteria'][0]['codes'][0] === "0123", TRUE, "code should match", $challenge['criteria'][0]['codes'][0]);
  	$this->unit->run($challenge['criteria'][0]['codes'][1] === "3210", TRUE, "code should match", $challenge['criteria'][0]['codes'][1]);

    # sonar box challenge_id & action_data_id should be changed
    $sonar_box = $this->sonar_box_lib->get_one(array('_id' => new MongoId($this->sonar_box_id_2)));
    $this->unit->run($sonar_box['challenge_id'] === $this->challenge_id.'', TRUE, "sonar box's challenge id should match", $sonar_box['challenge_id']);
    $this->unit->run($sonar_box['action_data_id'] === $this->action_data_id, TRUE, "sonar box's action data id should match", $sonar_box['action_data_id']);

  	# edit sonar box codes
  	$this->sonar_box_lib->update(array('_id' => new MongoId($this->sonar_box_id_2)), array('data' => "0000"));

  	# codes should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['codes']) === 2, TRUE, "challenge's action should have 2 codes", count($challenge['criteria'][0]['codes']));
  	$this->unit->run(count($challenge['codes']) === 6, TRUE, "challenge should have 5+1=6 codes", count($challenge['codes']));
  	$this->unit->run($challenge['codes'][0] === "0123", TRUE, "code should match", $challenge['codes'][0]);
  	$this->unit->run($challenge['codes'][1] === "0000", TRUE, "code should match", $challenge['codes'][1]);
  	$this->unit->run($challenge['criteria'][0]['codes'][0] === "0123", TRUE, "code should match", $challenge['criteria'][0]['codes'][0]);
  	$this->unit->run($challenge['criteria'][0]['codes'][1] === "0000", TRUE, "code should match", $challenge['criteria'][0]['codes'][1]);

  	# remove sonar box 1 from challenge by updating the sonar box itself
  	// $sonar_box_update = array(
  	// 	'$set' => array(
  	// 		'challenge_id' => NULL,
  	// 		'action_data_id' => NULL,
  	// 	)
  	// );
  	// $this->sonar_box_lib->update(array('_id' => new MongoId($this->sonar_box_id)), $sonar_box_update);
    $this->challenge_lib->update(array('_id' => $this->challenge_id), array('$set' => array('criteria.0.sonar_boxes' => array($this->sonar_box_id_2))));

  	# codes should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['codes']) === 1, TRUE, "challenge's action should have 1 code", count($challenge['criteria'][0]['codes']));
  	$this->unit->run(count($challenge['codes']) === 5, TRUE, "challenge should have 6-1=5 code", count($challenge['codes']));
  	$this->unit->run($challenge['codes'][0] === "0000", TRUE, "code should match", $challenge['codes'][0]);
  	$this->unit->run($challenge['criteria'][0]['codes'][0] === "0000", TRUE, "code should match", $challenge['criteria'][0]['codes'][0]);

    # sonar box 1 should have challenge_id and action_data_id changed to null
    $box = $this->sonar_box_lib->get_one(array('_id' => new MongoId($this->sonar_box_id)));
    $this->unit->run($box['challenge_id'] === NULL, TRUE, "", $box['challenge_id']);
    $this->unit->run($box['action_data_id'] === NULL, TRUE, "", $box['action_data_id']);

  	# remove sonar box 2 from system
  	$this->sonar_box_lib->remove(array('_id' => new MongoId($this->sonar_box_id_2)));

  	# codes & sonar_boxes should be updated
  	$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($this->challenge_id)));
  	$this->unit->run(count($challenge['criteria'][0]['sonar_boxes']) === 0, TRUE, "challenge's action should have 0 sonar_box", count($challenge['criteria'][0]['sonar_boxes']));
  	$this->unit->run(count($challenge['criteria'][0]['codes']) === 0, TRUE, "challenge's action should have 0 code", count($challenge['criteria'][0]['codes']));
  	$this->unit->run(count($challenge['codes']) === 4, TRUE, "challenge should have 5-1=4 code", count($challenge['codes']));

  }
}
/* End of file chalenge_structure_test.php */
/* Location: ./application/controllers/test/chalenge_structure_test.php */