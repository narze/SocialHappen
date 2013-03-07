<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Sonar box lib tests
 * 1. Setup action datas and challenges
 * 2. Test adding 1 sonar box without action_data id
 * 3. Test adding 3 sonar boxes with action_data id
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
  // 1. Setup action datas and challenge
  //
    $this->load->library('action_data_lib');
    $this->action_data1 = array(

    );

    $this->action_data2 = array(

    );

    $result = $this->action_data_lib->add_action_data(206, $this->action_data1);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->action_data_id_1 = $result;

    $result = $this->action_data_lib->add_action_data(206, $this->action_data2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->action_data_id_2 = $result;

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
          'count' => 1,
          'action_data_id' => $this->action_data_id_1
        ),
        array(
          'name' => 'C2',
          'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
          'count' => 2,
          'action_data_id' => $this->action_data_id_2
        )
      ),
      'location' => null,
      'locations' => array(),
      'custom_locations' => array(),
      // 'branches' => array($this->branch_data1['_id'] . '', $this->branch_data2['_id'] . ''),
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
          'count' => 1,
          'action_data_id' => $this->action_data_id_1
        ),
        array(
          'name' => 'C2',
          'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
          'count' => 2,
          'action_data_id' => $this->action_data_id_2
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
     * No action_data id : not updating challenges with action_data id
     */
    $this->sonar_box1 = array(
      'id' => 'SH0000',
      'name' => 'sonar box 1',
      'data' => '0000',
      'info' => array()
    );

    /*
     * With action_data id : update challenges' custom_sonar with action_data id
     */
    $this->sonar_box2 = array(
      'id' => 'SH1111',
      'name' => 'sonar box 2',
      'data' => '1111',
      'info' => array(),
      'action_data_id' => $this->action_data_id_1 . '',
      'challenge_id' => $this->challenge_id1
    );

    /*
     * With action_data id : update challenges' custom_sonar with action_data id
     */
    $this->sonar_box3 = array(
      'id' => 'SH2222',
      'name' => 'sonar box 3',
      'data' => '2222',
      'info' => array(),
      'action_data_id' => $this->action_data_id_2 . '',
      'challenge_id' => $this->challenge_id1
    );

    /*
     * With action_data id : update challenges' custom_sonar with action_data id
     * Now action_data_2 have 2 boxes in different challenge
     */
    $this->sonar_box4 = array(
      'id' => 'SH3333',
      'name' => 'sonar box 4',
      'data' => '3333',
      'info' => array(),
      'action_data_id' => $this->action_data_id_1 . '',
      'challenge_id' => $this->challenge_id2
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
     * The first challenge is attached by sonar box no. 2-3
     * It should have code in corresponding actions and have both codes in challenge
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['codes'] === array('1111','2222'), TRUE, "challenge codes should match box2, box3", $result['codes']);
    $this->unit->run($result['criteria'][0]['codes'] === array('1111'), TRUE, "action code should match box2", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array($this->sonar_box_data2), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array('2222'), TRUE, "action code should match box3", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array($this->sonar_box_data3), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);

    /*
     * The first challenge is attached by sonar box no. 4
     * It should have code in corresponding actions and have both codes in challenge
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['codes'] === array('3333'), TRUE, "challenge codes should match box4", $result['codes']);
    $this->unit->run($result['criteria'][0]['codes'] === array('3333'), TRUE, "action code should match box4", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array($this->sonar_box_data4), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array(), TRUE, "action code should be empty because it is not matching with any sonar boxes", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);
  }

  function update_test() {
    /*
     * Update sonar box 1 by adding challenge_id 1 and action_data_id 1
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data1));
    $update = array(
      '$set' => array(
        'challenge_id' => $this->challenge_id1,
        'action_data_id' => $this->action_data_id_1. ''
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
     * Update sonar box 3 by changing action_data_id to other id
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data3));
    $update = array(
      '$set' => array(
        'action_data_id' => 'Badbranchid'
      )
    );
    $result = $this->sonar_box_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    /*
     * Update sonar box 4 by changing challenge_id to other id
     */
    $criteria = array('_id' => new MongoId($this->sonar_box_data4));
    $update = array(
      '$set' => array(
        'challenge_id' => 'Badbranchid'
      )
    );
    $result = $this->sonar_box_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);
  }

  function check_challenge_after_editing_sonar_box_test() {
    /*
     * The first challenge, that was attached with box no. 2-3
     * box 1 (0000) should be added into first action_data
     * box 2 (1111) should be changed into 0101
     * box 3 (2222) should be removed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['codes'] === array('0101','0000'), TRUE, "challenge codes should match box1, box2, box3", $result['codes']);
    $this->unit->run($result['criteria'][0]['codes'] === array('0101','0000'), TRUE, "action code should match box2", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array($this->sonar_box_data2, $this->sonar_box_data1), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array(), TRUE, "action code should match box3", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);

    /*
     * The second challenge, that was attached with box no. 4
     * box 3 (3333) should be removed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['codes'] === array(), TRUE, "challenge codes should be empty", $result['codes']);
    $this->unit->run($result['criteria'][0]['codes'] === array(), TRUE, "action code should be empty", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array(), TRUE, "action code should be empty because it is not matching with any sonar boxes", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);

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
     * The first challenge
     * data 0101 should be removed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id1);

    $this->unit->run($result['codes'] === array('0000'), TRUE, "\$result['codes']", var_export($result['codes'], TRUE));
    $this->unit->run($result['criteria'][0]['codes'] === array('0000'), TRUE, "action code should match box2", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array($this->sonar_box_data1), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array(), TRUE, "action code should match box3", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);

    /*
     * The second challenge
     * nothing changed
     */
    $this->load->library('challenge_lib');
    $result = $this->challenge_lib->get_by_id($this->challenge_id2);

    $this->unit->run($result['codes'] === array(), TRUE, "\$result['codes']", var_export($result['codes'], TRUE));
    $this->unit->run($result['criteria'][0]['codes'] === array(), TRUE, "action code should be empty", $result['criteria'][0]['codes']);
    $this->unit->run($result['criteria'][0]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][0]['sonar_boxes']);
    $this->unit->run($result['criteria'][1]['codes'] === array(), TRUE, "action code should be empty because it is not matching with any sonar boxes", $result['criteria'][1]['codes']);
    $this->unit->run($result['criteria'][1]['sonar_boxes'] === array(), TRUE, "sonar_box_id should match", $result['criteria'][1]['sonar_boxes']);
  }

}