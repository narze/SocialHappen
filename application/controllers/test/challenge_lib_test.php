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

  function _add_challenge_reward_test(){
    $this->load->model('reward_item_model', 'reward_item');
    $name = 'Chalenge reward';
    $status = 'published';
    $challenge_id = 'asdf';
    $image = base_url().'assets/images/cam-icon.png';
    $value = '200THB';
    $description = 'This is pasta!!!';
    $input = compact('name', 'status', 'type', 'challenge_id', 'image', 'value', 'description');

    $this->reward_item_id = $result = $this->reward_item->add_challenge_reward($input);
    $this->unit->run($result, 'is_string', "\$result", $result);

    $count = $this->reward_item->count_all();
    $this->unit->run($count, 1, 'count', $count);

    $reward = $this->reward_item->get_one(array('_id' => new MongoId($this->reward_item_id)));
    $this->unit->run($reward['type'], 'challenge', "\$reward['type']", $reward['type']);
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
      'repeat' => 1,
      'reward_items' => array(0 => array('_id' => new MongoId($this->reward_item_id)))
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

  function __user_join_challenge_1() {
    $user_id = 1;
    $challenge_id = $this->challenge_id;
    $this->load->library('user_lib');
    return $this->user_lib->join_challenge_by_challenge_id($user_id, $challenge_id);
  }

  function __user_join_challenge_2() {
    $user_id = 1;
    $challenge_id = $this->challenge_id2;
    $this->load->library('user_lib');
    return $this->user_lib->join_challenge_by_challenge_id($user_id, $challenge_id);
  }

  function __user_join_challenge_3($day_delay = 0) {
    $user_id = 1;
    $challenge_id = $this->challenge_id4;
    $this->load->library('user_lib');
    return $this->user_lib->join_challenge_by_challenge_id($user_id, $challenge_id, $day_delay);
  }

  function check_challenge_test() {
    //1. Check challenge without joining any challenge, should get empty in all fields
    $company_id = 1;
    $info = array(
      'company_id' => $company_id
    );
    $user_id = 1;
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //2. After join a challenge, should have in_progress array that contains the challenge's id
    $this->unit->run($this->__user_join_challenge_1(), TRUE, "\$this->__user_join_challenge_1()", 'join challenge 1');

    $company_id = 1;
    $info = array(
      'company_id' => $company_id
    );
    $user_id = 1;
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array($this->challenge_id),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //3. Join another challenge, now in_progress has 2 ids
    $this->unit->run($this->__user_join_challenge_2(), TRUE, "\$this->__user_join_challenge_2()", 'join challenge 2');

    $company_id = 1;
    $info = array(
      'company_id' => $company_id
    );
    $user_id = 1;
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array($this->challenge_id, $this->challenge_id2),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //4. Count user's achievement before complete a challenge
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 0, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));


    //5. Invote check_challenge by calling increment_achievement_stat
    // stat 1 : 1 time
    $app_id = 1;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat1);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    // stat 2 : 2 times
    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    //a challenge should be done and found in completed array instead of in_progress array
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id), //get completed challenge id
      'in_progress' => array($this->challenge_id2),
      'completed_today' => array(),
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count user's achievement again, it should be increment by 1
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 1 + 1 /*FirstChallengeAchievement included*/, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));

    //6. Increment stat (that's invoke check_challenge) again
    $app_id = 2;
    $user_id = 1;
    $company_id = 1;
    $inc_result = $this->achievement_lib->increment_achievement_stat($company_id, $app_id, $user_id,
      $this->achievement_stat2);
    $this->unit->run($inc_result, TRUE, "\$inc_result", $inc_result);

    //another challenge should be done as well
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE, //no error checking challenges
      'completed' => array($this->challenge_id, $this->challenge_id2), //get completed challenge id
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //Count user's achievement again
    $count = $this->achievement_lib->count_user_achieved_by_user_id($user_id);
    $this->unit->run($count, 2 + 1, 'count_user_achieved_by_user_id_test', print_r($count, TRUE));
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
    $this->unit->run($user['challenge_redeeming'], array($this->challenge_id,$this->challenge_id2), "\$user['challenge_redeeming']", $user['challenge_redeeming']);
    $this->unit->run($user['challenge_completed'], array($this->challenge_id,$this->challenge_id2), "\$user['challenge_completed']", $user['challenge_completed']);
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
    $this->unit->run($user['challenge_redeeming'], array($this->challenge_id2), "\$user['challenge_redeeming']", $user['challenge_redeeming']);
    $this->unit->run($user['challenge_completed'], array($this->challenge_id,$this->challenge_id2), "\$user['challenge_completed']", $user['challenge_completed']);
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
    $this->unit->run(count($result), 4, "count(\$result)", count($result));
    $this->unit->run($result[0]['action_id'], 118, "\$result[0]['action_id']", $result[0]['action_id']);
    $this->unit->run($result[1]['action_id'], 118, "\$result[1]['action_id']", $result[1]['action_id']);
    $this->unit->run($result[2]['action_id'], 117, "\$result[2]['action_id']", $result[2]['action_id']);
    $this->unit->run($result[3]['action_id'], 117, "\$result[3]['action_id']", $result[3]['action_id']);

    //Check audit image
    $this->unit->run($result[0]['image'], 'Challenge image url', "\$result[0]['image']", $result[0]['image']);
    $this->unit->run($result[1]['image'], 'Challenge image url', "\$result[1]['image']", $result[1]['image']);
    $this->unit->run($result[2]['image'], 'Challenge image url', "\$result[2]['image']", $result[2]['image']);
    $this->unit->run($result[3]['image'], 'Challenge image url', "\$result[3]['image']", $result[3]['image']);
  }

  function user_score_test() {
    $company_id = 1;
    $user_id = 1;
    $this->load->model('achievement_stat_company_model');

    $result = $this->achievement_stat_company_model->get((int)$company_id, (int)$user_id);
    $this->unit->run($result['company_score'], 0, '$result[company_score]', $result['company_score']);
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
    $this->load->library('audit_lib');

    //1. Check challenge before joining
    $user_id = 1;
    $challenge_id = $this->challenge_id4;
    $app_id = 0;
    $company_id = 2;

    $info = array(
      'company_id' => $company_id
    );

    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);
    //2. User join challenge with time = now
    $this->__user_join_challenge_3();

    //3. Check challenge, 'in_progress' should have challenge id
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array($this->challenge_id4),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //4. Try adding yesterday's audit, increment stat, it will not effect today's challenge progress
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

    //Challenge status : in_progress
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array($this->challenge_id4),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //5. Add tomorrow's audit, increment stat
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

    //Challenge status : in_progress as well
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array($this->challenge_id4),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //6. Add today's audit, increment stat
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

    //Challenge is now completed, 'completed' and 'completed_today' array should have challenge id
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4),
      'in_progress' => array(),
      'completed_today' => array($this->challenge_id4)
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //7. Check tomorrow's challenge, challenge should not be completed, because user has not join tomorrow's challenge yet
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info, time() + 24*60*60);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //8. So join tomorrow's challenge
    $this->__user_join_challenge_3(1);

    //9. Check challenge in tomorrow's time, challenge should be completed again becaused we have tomorrow's audit
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info, time() + 24*60*60);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4, $this->challenge_id4),
      'in_progress' => array(),
      'completed_today' => array($this->challenge_id4)
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //10. Check after tomorrow's challenge
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info, time() + 2*24*60*60);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4),
      'in_progress' => array(),
      'completed_today' => array()
    );
    $this->unit->run($result, $expected_result, "\$result", $result);

    //11. Join after tomorrow's challenge
    $this->__user_join_challenge_3(2);

    //12. Check challenge again
    $result = $this->challenge_lib->check_challenge($company_id, $user_id, $info, time() + 2*24*60*60);
    $expected_result = array(
      'success' => TRUE,
      'completed' => array($this->challenge_id4),
      'in_progress' => array($this->challenge_id4),
      'completed_today' => array()
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

  function get_coupon_test() {
    $user_id = 1;
    $challenge_id = $this->challenge_id4;
    $this->load->model('coupon_model');
    $result = $this->coupon_model->get_by_user_and_challenge($user_id, $challenge_id);
    $this->unit->run($result, 'is_array', "\$result", $result);
    $this->unit->run(count($result), 2, "\$result", count($result));

    //Today's coupon
    $this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
    $this->unit->run($result[0]['_id'], TRUE, "\$result[0]['_id']", $result[0]['_id']);
    $this->unit->run($result[0]['reward_item'], 'is_array', "\$result[0]['reward_item']", $result[0]['reward_item']);

    //Tomorrow's coupon
    $this->unit->run($result[1], 'is_array', "\$result[1]", $result[1]);
    $this->unit->run($result[1]['_id'], TRUE, "\$result[1]['_id']", $result[1]['_id']);
    $this->unit->run($result[1]['reward_item'], 'is_array', "\$result[1]['reward_item']", $result[1]['reward_item']);
  }
}