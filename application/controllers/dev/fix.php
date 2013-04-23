<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Fix
 * @category Controller
 */
class Fix extends CI_Controller {

  function __construct(){
    parent::__construct();
    if (defined('ENVIRONMENT'))
    {
      if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing'))
      {
        if($this->input->get('happy') !== 'everyday'){
          redirect();
        }
      }
    }
  }

  function index(){
    
  }

  function build_sonar_box_data() {
    $this->load->model('sonar_box_model');
    $this->load->model('challenge_model');

    $all_challenges = $this->challenge_model->get(array(), 0);

    foreach($all_challenges as $challenge) {
      if(!$challenge['sonar_frequency']) {
        continue;
      }

      $challenge_id = get_mongo_id($challenge);
      $query = array('challenge_id' => $challenge_id);

      //if sonar data isn't present, create them
      if(!$this->sonar_box_model->get($query)) {
        $this->sonar_box_model->add(array(
          'name' => $challenge['detail']['name'],
          'info' => array(),
          'challenge_id' => $challenge_id,
          'data' => $challenge['sonar_frequency']
        ));
      }
    }

    echo 'Build completed';
  }

  function add_coupon_code() {
    $this->load->model('coupon_model');

    $all_coupons = $this->coupon_model->get();

    foreach($all_coupons as $coupon) {
      if(!$this->coupon_model->update_coupon_code_by_id(get_mongo_id($coupon))) {
         return FALSE;
      }
    }

    echo 'Add completed';
  }

  function add_test_challenge() {
    $this->challenge_name = 'Test Challenge [add_test_challenge]';
    $this->branch_title = 'branch 1 [add_test_challenge]';
    $this->branch_title_2 = 'branch 2 [add_test_challenge]';
    $this->sonar_box_title = 'sonar box 1 [add_test_challenge]';
    $this->sonar_box_title_2 = 'sonar box 2 [add_test_challenge]';

    $this->load->library('branch_lib');
    $this->load->library('sonar_box_lib');
    $this->load->library('challenge_lib');

    # Remove duplicates
    if($this->challenge_lib->remove(array('detail.name' => $this->challenge_name))) {
      var_dump("removed challenge");
    }
    if($this->sonar_box_lib->remove(array('title' => $this->sonar_box_title))) {
      var_dump("removed sonar");
    }
    if($this->sonar_box_lib->remove(array('title' => $this->sonar_box_title_2))) {
      var_dump("removed sonar");
    }
    if($this->branch_lib->remove(array('title' => $this->branch_title))) {
      var_dump("removed branch");
    }
    if($this->branch_lib->remove(array('title' => $this->branch_title_2))) {
      var_dump("removed branch");
    }

    # Add some branches
    $branch = array(
      'company_id' => 1,
      'title' => $this->branch_title,
      'location' => array(40, 40),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    if($this->branch = $this->branch_lib->add($branch)) {
      var_dump("Added branch");
    }
    $this->branch_id = $this->branch['_id'];

    $branch_2 = array(
      'company_id' => 1,
      'title' => $this->branch_title_2,
      'location' => array(0, -30),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
    if($this->branch_2 = $this->branch_lib->add($branch_2)) {
      var_dump("Added branch");
    }
    $this->branch_id_2 = $this->branch_2['_id'];

    # Add some sonar boxes

    $sonar_box = array(
      'company_id' => 1,
      'challenge_id' => NULL, # $this->challenge_id."",
      'action_data_id' => NULL, # $this->action_data_id,
      'id' => 1,
      'title' => $this->sonar_box_title,
      'data' => "31323",
    );
    if($this->sonar_box_id = $this->sonar_box_lib->add($sonar_box)) {
      var_dump("Added sonar box");
    }

    $sonar_box_2 = array(
      'company_id' => 1,
      'challenge_id' => NULL,
      'action_data_id' => NULL,
      'id' => 2,
      'title' => $this->sonar_box_title_2,
      'data' => "11311",
    );
    if($this->sonar_box_id_2 = $this->sonar_box_lib->add($sonar_box_2)) {
      var_dump("Added sonar box");
    }

    # Add a challenge with unique name
    $old_challenge_model = array(
      "detail" => array(
        "name" => $this->challenge_name,
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
          "name" => "Watch video 1",
          "action_data" => array(
            "data" => array(),
            "action_id" => 206
          ),
          "sonar_code" => ""
        ),
        array(
          "query" => array(
            "action_id" => 206
          ),
          "count" => 1,
          "name" => "Watch video 2",
          "action_data" => array(
            "data" => array(),
            "action_id" => 206
          ),
          "sonar_code" => ""
        ),
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

    $model['criteria'][1]['sonar_boxes'] = array($this->sonar_box_id_2);
    // $model['criteria'][1]['codes'] = array(); // Codes should be derived from sonar_boxes
    $model['criteria'][1]['branches'] = array($this->branch_id_2);
    // $model['criteria'][1]['locations'] = array(); // Locations should be derived from branches
    $model['criteria'][1]['all_branches'] = FALSE;
    $model['criteria'][1]['custom_locations'] = array();
    $model['criteria'][1]['use_only_custom_locations'] = FALSE;
    $model['criteria'][1]['verify_location'] = TRUE;

    $params = array('model' => json_encode($model));

    if($save_challenge_result = $this->_postAPI('apiv3', 'saveChallenge', $params)) {
      echo '<pre>';
      var_dump($save_challenge_result);
      echo '</pre>';
      echo 'Remove "Not signed in" error in apiv3 file';
    }
  }

  function _postAPI($v, $method, $params = array()) {
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

  function apiv3($path = '') {
    return base_url('apiv3/'.$path);
  }

  function apiv4($path = '') {
    return base_url('apiv4/'.$path);
  }
}
/* End of file fix.php */
/* Location: ./application/controllers/dev/fix.php */