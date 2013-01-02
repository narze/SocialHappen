<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branch_lib_test extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->library('branch_lib');
    $this->load->library('challenge_lib');
    $this->unit->reset_dbs();
    $this->socialhappen->reindex();
  }

  function __destruct() {
    $this->unit->report_with_counter();
  }

  function index() {
    $class_methods = get_class_methods($this);
    //echo 'Functions : '.(count(get_class_methods($this->achievement_lib))-3).' Tests :'.count($class_methods);
    foreach ($class_methods as $method) {
        if(preg_match("/(_test)/",$method)) {
          $this->$method();
        }
    }
  }

  function setup_before_test(){
    $criteria = array();
    $result = $this->branch_lib->remove($criteria);

    $this->branch1 = array(
      'company_id' => 1,
      'title' => 'branch 1',
      'location' => array(40, 40),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );

    $this->branch2 = array(
      'company_id' => 1,
      'title' => 'branch 2',
      'location' => array(40, 50),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );

    $this->branch3 = array(
      'company_id' => 1,
      'title' => 'branch 3',
      'location' => array(50, 40),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );

    $this->branch4 = array(
      'company_id' => 2,
      'title' => 'branch 4',
      'location' => array(50, 50),
      'telephone' => '0123456789',
      'photo' => 'https://fbcdn-sphotos-c-a.akamaihd.net/hphotos-ak-ash3/s480x480/67314_421310064605065_636864263_n.jpg',
      'address' => 'thailand ja'
    );
  }

  function add_test() {
    $result = $this->branch_lib->add($this->branch1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data1 = $result;

    $result = $this->branch_lib->add($this->branch2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data2 = $result;

    $result = $this->branch_lib->add($this->branch3);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data3 = $result;

    $result = $this->branch_lib->add($this->branch4);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data4 = $result;
  }

  function setup_challenge_test(){
    $this->challenge1 = array(
      'company_id' => 1,
      'all_branch' => true,
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
      'location' => null,
      'locations' => array(),
      'custom_locations' => array(),
      'branches' => array()
    );

    $this->challenge2 = array(
      'company_id' => 1,
      'all_branch' => false,
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
      'location' => array(45, 60),
      'locations' => array(),
      'custom_locations' => array(array(140,110)),
      'branches' => array($this->branch_data1['_id'] . '', $this->branch_data2['_id'] . '')
    );

    $result = $this->challenge_lib->add($this->challenge1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id1 = $result;

    $result = $this->challenge_lib->add($this->challenge2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id2 = $result;
  }

  function get_test() {
    $criteria = array('company_id' => '1');
    $result = $this->branch_lib->get($criteria);

    $this->unit->run(count($result), 3, "\$result", count($result));
    $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
  }

  function update_test() {
    // return;
    $criteria = array('_id' => new MongoId($this->branch_data1['_id']));
    $update = array(
      '$set' => array(
        'title' => 'branch 1 updated',
        'location' => array(41, 41)
      )
    );
    $result = $this->branch_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    $criteria = array('_id' => new MongoId($this->branch_data4['_id']));
    $update = array(
      '$set' => array(
        'location' => array(51, 51)
      )
    );
    $result = $this->branch_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    $criteria = array('company_id' => '1');
    $result = $this->challenge_lib->get($criteria);
    $this->unit->run($result, 'is_array', "\$result", $result);

    $challenge1 = $result[1];
    $challenge2 = $result[0];

    $this->unit->run(count($challenge1['locations']), 3, "\$result", count($challenge1['locations']));
    $this->unit->run(count($challenge2['locations']), 4, "\$result", count($challenge2['locations']));

    $this->unit->run($challenge1['locations'][0], array(50, 40), "\$result", $challenge1['locations'][0]);
    $this->unit->run($challenge1['locations'][1], array(40, 50), "\$result", $challenge1['locations'][0]);
    $this->unit->run($challenge1['locations'][2], array(41, 41), "\$result", $challenge1['locations'][0]);

    $this->unit->run($challenge2['locations'][0], array(45, 60), "\$result", $challenge2['locations'][0]);
    $this->unit->run($challenge2['locations'][1], array(140, 110), "\$result", $challenge2['locations'][0]);
    $this->unit->run($challenge2['locations'][2], array(41, 41), "\$result", $challenge2['locations'][0]);
    $this->unit->run($challenge2['locations'][3], array(40, 50), "\$result", $challenge2['locations'][0]);

    // echo '<pre>';
    // var_dump($result);
  }

  function get_one_test() {
    // return;
    $criteria = array('company_id' => '2');
    $result = $this->branch_lib->get_one($criteria);
    $this->unit->run($result, 'is_array', "\$result", $result);
    $this->unit->run($result['location'], array(51, 51), "\$result", $result);
  }

  function remove_test() {
    // echo 'remove test<br>';
    $criteria = array('company_id' => '1');
    $result = $this->branch_lib->remove($criteria);
    $this->unit->run($result, TRUE, "\$result", $result);

    $criteria = array('company_id' => '2');
    $result = $this->branch_lib->remove($criteria);
    $this->unit->run($result, TRUE, "\$result", $result);

    $all_branch = $this->branch_lib->get(array());
    $this->unit->run(count($all_branch), 0, "count(\$all_branch)", count($all_branch));

    $criteria = array('company_id' => '1');
    $result = $this->challenge_lib->get($criteria);
    $this->unit->run($result, 'is_array', "\$result", $result);

    $challenge1 = $result[1];
    $challenge2 = $result[0];

    $this->unit->run(isset($challenge1['locations']), FALSE, "\$result", isset($challenge1['locations']));
    $this->unit->run(count($challenge2['locations']), 2, "\$result", count($challenge2['locations']));

    $this->unit->run($challenge2['locations'][0], array(45, 60), "\$result", $challenge2['locations'][0]);
    $this->unit->run($challenge2['locations'][1], array(140, 110), "\$result", $challenge2['locations'][0]);
  }
}