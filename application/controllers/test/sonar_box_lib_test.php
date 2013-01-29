<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Sonar box lib tests
 * 1. Setup branches and challenges
 * 2. Test adding 1 sonar box without branch id
 * 3. Test adding 3 sonar boxes with branch id
 * 4. Test updating sonar box's data
 * 5. Test removing a sonar box
 */

class Sonar_box_lib_test extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->library('sonar_box_lib');
    $this->unit->reset_dbs();
    $this->socialhappen->reindex();
  }

  function __destruct() {
    $this->unit->report_with_counter();
  }

  function index() {
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method) {
      if(preg_match("/(_test)/",$method)) {
        $this->$method();
      }
    }
  }

  function setup_before_test(){
  // 1. Setup branches and challenge
  //
    $this->load->library('branch_lib');
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

    $result = $this->branch_lib->add($this->branch1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data1 = $result;

    $result = $this->branch_lib->add($this->branch2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->branch_data2 = $result;

    $this->load->library('challenge_lib');

    $this->challenge1 = array(
      'company_id' => 1,
      'all_branch' => FALSE,
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
      'branches' => array($this->branch_data1['_id'] . '', $this->branch_data2['_id'] . ''),
      'sonar_frequency' => '0123'
    );

    $result = $this->challenge_lib->add($this->challenge1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id1 = $result;

    $this->challenge2 = array(
      'company_id' => 1,
      'all_branch' => TRUE,
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
      'branches' => array(),
      'sonar_frequency' => '0123'
    );

    $result = $this->challenge_lib->add($this->challenge2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id2 = $result;
  }

  function prepare_sonar_box_test() {
    /*
     * No branch id : not updating challenges with branch id
     */
    $this->sonar_box1 = array(
      'id' => 'SH0000',
      'name' => 'sonar box 1',
      'data' => '0000',
      'info' => array()
    );

    /*
     * With branch id : update challenges' custom_sonar with branch id
     */
    $this->sonar_box2 = array(
      'id' => 'SH1111',
      'name' => 'sonar box 2',
      'data' => '1111',
      'info' => array(),
      'branch_id' => $this->branch_data1['_id'] . ''
    );

    /*
     * With branch id : update challenges' custom_sonar with branch id
     */
    $this->sonar_box3 = array(
      'id' => 'SH2222',
      'name' => 'sonar box 3',
      'data' => '2222',
      'info' => array(),
      'branch_id' => $this->branch_data2['_id'] . ''
    );

    /*
     * With branch id : update challenges' custom_sonar with branch id
     * Now branch 2 have 2 boxes
     */
    $this->sonar_box4 = array(
      'id' => 'SH3333',
      'name' => 'sonar box 4',
      'data' => '3333',
      'info' => array(),
      'branch_id' => $this->branch_data2['_id'] . ''
    );
  }

  function add_test() {
    $result = $this->sonar_box_lib->add($this->sonar_box1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->sonar_box_data1 = $result;

    $result = $this->sonar_box_lib->add($this->sonar_box2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->sonar_box_data2 = $result;

    $result = $this->sonar_box_lib->add($this->sonar_box3);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->sonar_box_data3 = $result;

    $result = $this->sonar_box_lib->add($this->sonar_box4);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->sonar_box_data4 = $result;
  }

  function get_test() {
      $criteria = array();
      $result = $this->sonar_box_lib->get($criteria);

      $this->unit->run(count($result), 4, "\$result", count($result));
      $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
    }


  function get_one_test() {
    $criteria = array('data' => '0000');
    $result = $this->sonar_box_lib->get_one($criteria);
    $this->unit->run($result, 'is_array', "\$result", $result);
    $this->unit->run($result['id'] === "SH0000", TRUE, "\$result['id']", $result['id']);
  }

  function check_challenge_after_adding_sonar_box_test() {
    /*
     * The first challenge (that's have branch 1-2 in it) should have sonar data of sonar box 2-4 (box 1 doesn't have branch)
     * In its array named branch_sonar_data
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['branch_sonar_data'] === array('1111','2222','3333'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));

    /*
     * The second challenge (that's including all branch) should have sonar data of sonar box 2-4 (box 1 doesn't have branch)
     * In its array named branch_sonar_data
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['branch_sonar_data'] === array('1111','2222','3333'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));
  }

  function update_test() {
    /*
     * Update sonar box 1 by adding branch_id 1
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data1));
    $update = array(
      '$set' => array(
        'branch_id' => $this->branch_data1['_id'] . ''
      )
    );
    $result = $this->sonar_box_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    /*
     * Update sonar box 2 by editing data from 1111 to 0101
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data2));
    $update = array(
      '$set' => array(
        'data' => '0101'
      )
    );
    $result = $this->sonar_box_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    /*
     * Update sonar box 3 by changing branch_id to other id
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data3));
    $update = array(
      '$set' => array(
        'branch_id' => 'Badbranchid'
      )
    );
    $result = $this->sonar_box_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);
  }

  function check_challenge_after_editing_sonar_box_test() {
    /*
     * The first challenge (that's have branch 1-2 in it) should have sonar data of sonar box 1-4
     * data 0000 should be added
     * data 1111 should be changed into 0101
     * and data 2222 should be removed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['branch_sonar_data'] === array('0000','0101','3333'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));

    /*
     * The second challenge (that's have branch 1-2 in it) should have sonar data of sonar box 1-4
     * data 0000 should be added
     * data 1111 should be changed into 0101
     * and data 2222 should be removed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['branch_sonar_data'] === array('0000','0101','3333'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));
  }

  function remove_test() {
    /*
     * Remove sonar box 2 (0101), 4 (3333)
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data2));
    $result = $this->sonar_box_lib->remove($criteria);
    $criteria = array('_id' => new MongoId($this->sonar_box_data4));
    $result = $this->sonar_box_lib->remove($criteria);
  }

  function get_test_2() {
    $criteria = array();
    $result = $this->sonar_box_lib->get($criteria);

    $this->unit->run(count($result), 2, "\$result", count($result));
    $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
  }

  function check_challenge_after_removing_sonar_box_test() {
    /*
     * The first challenge (that's have branch 1-2 in it) should have sonar data of sonar box 1
     * data 0101, 3333 should be removed from branch_sonar_data
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['branch_sonar_data'] === array('0000'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));

    /*
     * The second challenge (that's have branch 1-2 in it) should have sonar data of sonar box 1
     * data 0101, 3333 should be removed from branch_sonar_data
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['branch_sonar_data'] === array('0000'), TRUE, "\$result['branch_sonar_data']", var_export($result['branch_sonar_data'], TRUE));
  }

}