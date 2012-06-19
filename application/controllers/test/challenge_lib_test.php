<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Challenge_lib_test extends CI_Controller {
  
  function __construct() {
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->library('challenge_lib');
    $this->unit->reset_dbs();
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

  function setup_before_test() {
    $this->challenge = array(
      'company_id' => 1,
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
    );

    $this->challenge2 = array(
      'company_id' => 1,
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
    );

    $this->challenge3 = array(
      'company_id' => 1,
      'start' => time(),
      'end' => time() + 86400,
      'detail' => array(
        'name' => 'Challenge name 3',
        'description' => 'Challenge description',
        'image' => 'Challenge image url'
      ),
      'criteria' => array(
        array(
          'name' => 'C3',
          'query' => array('action_id' => 201),
          'count' => 2,
          'is_platform_action' => TRUE
        )
      )
    );

    $this->challenge4 = array(
      'company_id' => 2,
      'start' => time(),
      'end' => time() + 864000,
      'detail' => array(
        'name' => 'Daily Challenge',
        'description' => 'You can play every day',
        'image' => 'Challengeimage'
      ),
      'criteria' => array(
        array(
          'name' => 'C4',
          'query' => array('action_id' => 203),
          'count' => 1,
          'is_platform_action' => TRUE
        )
      ),
      'repeat' => 'daily'
    );

    $this->achievement_stat1 = array(
      'action_id' => 1,
      'page_id' => 1,
      'app_install_id' => 1
    );

    $this->achievement_stat2 = array(
      'action_id' => 2,
      'page_id' => 1,
      'app_install_id' => 2
    );

    $this->achievement_stat3 = array(
      'action_id' => 201,
      'app_install_id' => 0
    );

    $this->achievement_stat4 = array(
      'action_id' => 203,
      'app_install_id' => 0
    );
  }

  function _create_user_test() {
    $user_id = 1;
    $additional_data = array('challenge' => array()); //TODO should check challenge in this array
    $this->load->library('user_lib');
    $result = $this->user_lib->create_user($user_id, $additional_data);
    $this->unit->run($result, 'is_string', "\$result", $result);
    $this->user_id_1 = $result;
  }

  function add_test() {
    $result = $this->challenge_lib->add($this->challenge);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id = $result;

    $result = $this->challenge_lib->add($this->challenge2);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id2 = $result;

    $result = $this->challenge_lib->add($this->challenge3);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id3 = $result;

    $result = $this->challenge_lib->add($this->challenge4);
    $this->unit->run($result, TRUE, "\$result", $result);
    $this->challenge_id4 = $result;
  }

  function get_test() {
    $criteria = array('company_id' => '1');
    $result = $this->challenge_lib->get($criteria);
    $this->unit->run(count($result), 3, "\$result", count($result));
    $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
    $this->unit->run($result[0]['hash'], strrev(sha1($this->challenge_id3)), "\$result[0]['hash']", $result[0]['hash']);
    $this->unit->run($result[1]['hash'], strrev(sha1($this->challenge_id2)), "\$result[1]['hash']", $result[1]['hash']);
    $this->unit->run($result[2]['hash'], strrev(sha1($this->challenge_id)), "\$result[2]['hash']", $result[2]['hash']);
    $this->hash = $result[2]['hash'];
  }

  function get_one_test() {
    $criteria = array('company_id' => '1');
    $result = $this->challenge_lib->get_one($criteria);
    $this->unit->run($result, 'is_array', "\$result", $result);
    $this->unit->run($result['detail']['name'], 'Challenge name 1', "\$result['detail']['name']", $result['detail']['name']);
  }

  function update_test() {
    $criteria = array('company_id' => '1');
    $update = array(
      '$set' => array(
        'start' => time() + 86400
      )
    );
    $result = $this->challenge_lib->update($criteria, $update);
    $this->unit->run($result, TRUE, "\$result", $result);

    $update = array(
      '$set' => array(
        'end' => time()
      )
    );
    $result = $this->challenge_lib->update($criteria, $update);
    $this->unit->run($result, FALSE, "\$result", $result); // end < start
  }

  function get_updated_test() {
    $criteria = array('company_id' => '1');
    $result = $this->challenge_lib->get($criteria);
    $this->unit->run(count($result), 3, "\$result", count($result));
    $this->unit->run($result[2], 'is_array', "\$result[2]", $result[2]);
    $this->unit->run($result[2]['start'], time() + 86400, "\$result[2]['start']", $result[2]['start']);
    
    //Only first element will be updated
    $this->unit->run($result[1], 'is_array', "\$result[1]", $result[1]);
    $this->unit->run($result[1]['start'], time(), "\$result[1]['start']", $result[1]['start']);
    $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
    $this->unit->run($result[0]['start'], time(), "\$result[0]['start']", $result[0]['start']);
  }

  function get_challenge_progress_test() {
    $user_id = 1;
    $challenge_id = $this->challenge_id;
    $result = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
    $criteria_1_expect = array(
      'action_data' => array(
        'name' => 'C1',
        'query' => array('page_id' => 1, 'app_id'=>1, 'action_id'=>1),
        'count' => 1
      ),
      'action_done' => FALSE,
      'action_count' => 0
    );
    $criteria_2_expect = array(
      'action_data' => array(
        'name' => 'C2',
        'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
        'count' => 2
      ),
      'action_done' => FALSE,
      'action_count' => 0
    );
    $this->unit->run($result[0], $criteria_1_expect, "\$result[0]", $result[0]);
    $this->unit->run($result[1], $criteria_2_expect, "\$result[1]", $result[1]);
  }

  function check_challenge_test() {
    $company_id = 1;
    $info = array(
      'company_id' => $company_id
    );
    $user_id = 1;
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Unrelated achievement_stat
    $this->load->library('achievement_lib');

    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat1);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $info = array();
    $company_id = 1;
    $user_id = 1;
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);
    $app_id = 1;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat1);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);
    
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array(),
      'in_progress' => array($this->challenge_id),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);
    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array(),
      'in_progress' => array($this->challenge_id2, $this->challenge_id),
      'completed_today' => array(),
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count achieved before complete challenge
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 0, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    //Check challenge invoked by increment_achievement_stat already
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id), //get completed challenge id     
      'in_progress' => array($this->challenge_id2),
      'completed_today' => array(),
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count achieved after complete
      $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 1 + 1 /*FirstChallengeAchievement*/, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id, $this->challenge_id2), //get completed challenge id
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count again
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 2 + 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
    $app_id = 0;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat3);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id, $this->challenge_id2), //get completed challenge id
      'in_progress' => array($this->challenge_id3),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count again
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 2 + 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
    $app_id = 0;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat3);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id, $this->challenge_id2, $this->challenge_id3), //get completed challenge id
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count again
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 3 + 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
    $app_id = 0;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat3);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id, $this->challenge_id2, $this->challenge_id3), //get completed challenge id
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count again
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 3 + 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
  }

  function get_challenge_progress_test_2() {
    $user_id = 1;
    $challenge_id = $this->challenge_id;
    $result = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
    $criteria_1_expect = array(
      'action_data' => array(
        'name' => 'C1',
        'query' => array('page_id' => 1, 'app_id'=>1, 'action_id'=>1),
        'count' => 1
      ),
      'action_done' => TRUE,
      'action_count' => 1
    );
    $criteria_2_expect = array(
      'action_data' => array(
        'name' => 'C2',
        'query' => array('page_id' => 1, 'app_id'=>2, 'action_id'=>2),
        'count' => 2
      ),
      'action_done' => TRUE,
      'action_count' => 2 //it should be 3 but we should not make it over action's count
    );
    $this->unit->run($result[0], $criteria_1_expect, "\$result[0]", $result[0]);
    $this->unit->run($result[1], $criteria_2_expect, "\$result[1]", $result[1]);
  }

  function get_by_hash_test() {
    $result = $this->challenge_lib->get_by_hash($this->hash);
    $id = $result['_id']['$id'];
    $this->unit->run($id, $this->challenge_id, "\$result['_id']['$id']", $id);
  }

  function _check_user_model_test() {
    $user_id = 1;
    $this->load->library('user_lib');
    $user = $this->user_lib->get_user($user_id);
    $this->unit->run($user['challenge_redeeming'], array($this->challenge_id,$this->challenge_id2,$this->challenge_id3), "\$user['challenge_redeeming']", $user['challenge_redeeming']);
    $this->unit->run($user['challenge_completed'], array($this->challenge_id,$this->challenge_id2,$this->challenge_id3), "\$user['challenge_completed']", $user['challenge_completed']);
  }

  function redeem_challenge_test() {
    $user_id = 1;
    $challenge_id = $this->challenge_id;
    $result = $this->challenge_lib->redeem_challenge($user_id, $challenge_id);
    $this->unit->run($result, TRUE, "\$result", $result);

    $result = $this->challenge_lib->redeem_challenge($user_id, $challenge_id);
    $this->unit->run($result, FALSE, "Redeem again", $result);

    $user_id = 2;
    $challenge_id = $this->challenge_id;
    $result = $this->challenge_lib->redeem_challenge($user_id, $challenge_id);
    $this->unit->run($result, FALSE, "Redeem without finishing challenge", $result);
  }

  function _check_user_model_test_2() {
    $user_id = 1;
    $this->load->library('user_lib');
    $user = $this->user_lib->get_user($user_id);
    $this->unit->run($user['challenge_redeeming'], array($this->challenge_id2,$this->challenge_id3), "\$user['challenge_redeeming']", $user['challenge_redeeming']);
    $this->unit->run($user['challenge_completed'], array($this->challenge_id,$this->challenge_id2,$this->challenge_id3), "\$user['challenge_completed']", $user['challenge_completed']);
  }

  function remove_test() {
    // $criteria = array('page_id' => '1');
    // $result = $this->challenge_lib->remove($criteria);
    // $this->unit->run($result, TRUE, "\$result", $result);

    // $all_challenge = $this->challenge_lib->get(array());
    // $this->unit->run(count($all_challenge), 0, "count(\$all_challenge)", count($all_challenge));
  }

  function get_distinct_company_test() {
    $result = $this->challenge_lib->get_distinct_company();
    $this->unit->run($result, array(1,2), "\$result", $result);
  }

  function audit_check_test() {
    $this->load->library('audit_lib');
    $result = $this->audit_lib->list_recent_audit(10);
    $this->unit->run(count($result), 3, "count(\$result)", count($result));
    $this->unit->run($result[0]['action_id'], 118, "\$result[0]['action_id']", $result[0]['action_id']);
    $this->unit->run($result[1]['action_id'], 118, "\$result[1]['action_id']", $result[1]['action_id']);
    $this->unit->run($result[2]['action_id'], 118, "\$result[2]['action_id']", $result[2]['action_id']);
  }

  function user_score_test() {
    $company_id = 1;
    $user_id = 1;
    $this->load->model('achievement_stat_company_model');
    
    $result = $this->achievement_stat_company_model->get((int)$company_id, (int)$user_id);
    $this->unit->run($result['company_score'], 50 * 3, '$result[company_score]', $result['company_score']);
  }

  function get_challengers_test() {
    $user_id = 1;
    $this->load->model('user_mongo_model');
    $user = $this->user_mongo_model->getOne(array('user_id' => $user_id));
    $expected_result = array(
      'in_progress' => array(),
      'completed_today' => array(),
      'completed' => array($user)
    );

    $challenge_id = $this->challenge_id;
    $result = $this->challenge_lib->get_challengers($challenge_id);
    $this->unit->run($result['in_progress'], $expected_result['in_progress'], "\$result['in_progress']", $result['in_progress']);
    $this->unit->run(count($result['completed'][0]['user_id']),
      count($expected_result['completed'][0]['user_id']),
      "count(\$result['completed'][0]['user_id'])",
      count($result['completed'][0]['user_id'])
    );
  }

  function get_challenge_progress_test_for_daily_challenge() {
    $user_id = 1;
    $challenge_id = $this->challenge_id4;
    $result = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
    $criteria_expect = array(
      'action_data' => array(
        'name' => 'C4',
        'query' => array('action_id' => 203),
        'count' => 1,
        'is_platform_action' => TRUE
      ),
      'action_done' => FALSE,
      'action_count' => 0,
    );
    // $this->unit->run($result['daily_challenge'] === TRUE, 'is_true', "\$result['daily_challenge']", $result['daily_challenge']);
    $this->unit->run($result[0], $criteria_expect, "\$result[0]", $result[0]);
  }

  function check_challenge_test_for_daily_challenge() {
    //Increment stat to complete challenge
    $app_id = 0;
    $user_id = 1;
    $company_id = 2;
    
    $info = array(
      'company_id' => $company_id
    );

    //Add audit yesterday to complete yesterday's challenge
    $this->load->library('audit_lib');
    $this->audit_lib->create_index();
    $audit = array(
      'app_id' => 0,
      'company_id' => 2,
      'user_id' => 1,
      'action_id' => 203,
      'subject' => 'blah',
      'object' => 'blah',
      'objecti' => 'blah',
      'timestamp' => strtotime('yesterday')
    );
    $this->audit_lib->audit_add($audit);

    //Check challenge for daily challenge : incompleted
    $user_id = 1;
    $challenge_id = $this->challenge_id4;

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Add audit tomorrow
    $audit = array(
      'app_id' => 0,
      'company_id' => 2,
      'user_id' => 1,
      'action_id' => 203,
      'subject' => 'blah',
      'object' => 'blah',
      'objecti' => 'blah',
      'timestamp' => strtotime('tomorrow')
    );
    $this->audit_lib->audit_add($audit);

    //Check challenge for daily challenge : incompleted
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Add audit today
    $audit = array(
      'app_id' => 0,
      'company_id' => 2,
      'user_id' => 1,
      'action_id' => 203,
      'subject' => 'blah',
      'object' => 'blah',
      'objecti' => 'blah',
      'timestamp' => strtotime('now')
    );
    $this->audit_lib->audit_add($audit);
    //, and increment stat
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat4);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    //Check challenge again
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4), //challenge_id4 is here (check stat)
      'in_progress' => array(),
      'completed_today' => array($this->challenge_id4) //challenge_id4 is here as well (check audit)
    );
    $this->unit->run($result, $expected_result, "\$result", $result);
  }

  function get_challenge_progress_done_test_for_daily_challenge() {
    $user_id = 1;
    $challenge_id = $this->challenge_id4;
    $result = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
    $criteria_expect = array(
      'action_data' => array(
        'name' => 'C4',
        'query' => array('action_id' => 203),
        'count' => 1,
        'is_platform_action' => TRUE
      ),
      'action_done' => TRUE,
      'action_count' => 1,
    );
    // $this->unit->run($result['daily_challenge'] === TRUE, 'is_true', "\$result['daily_challenge']", $result['daily_challenge']);
    $this->unit->run($result[0], $criteria_expect, "\$result[0]", $result[0]);
  }
}