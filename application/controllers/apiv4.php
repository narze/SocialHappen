<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller for mobile native app
 * Coding style : snake_case
 */

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json', TRUE);
require_once(APPPATH . 'libraries/REST_Controller.php');
class Apiv4 extends REST_Controller {

  function __construct(){
    parent::__construct();
    if($this->uri->segment(1) === 'testmode') {
      $this->load->library('db_sync');
      $this->db_sync->use_test_db(TRUE);
    }
  }
  /**
   * Helper functions
   */
  function _error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code));
    return FALSE;
  }
  function error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code));
    return FALSE;
  }

  function _success($data = array(), $code = 1) {
    echo json_encode(array('success' => TRUE, 'data' => $data, 'code' => $code));
    return TRUE;
  }

  /**
  * Not requires token
  */

  /**
   * Check if user exists from facebook id
   * @method GET
   * @params facebook_user_id
   */
  function check_user_get() {
    $facebook_user_id = $this->get('facebook_user_id');

    if(!$facebook_user_id) {
      return $this->_error('undefined facebook_user_id');
    }

    //check facebook_user_id in user model
    $this->load->model('user_model');
    $user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user_id);

    if(!$user) {
      return $this->_error('user not found');
    }

    return $this->_success($user);
  }

  /**
   * Signup SocialHappen
   * @method POST
   * @params email
   *       , password
   *       , facebook_user_id
   *       , facebook_user_first_name
   *       , facebook_user_last_name
   *       , facebook_user_image
   */
  function signup_post() {
    $email = $this->post('email');
    $password = $this->post('password');
    $facebook_user_id = $this->post('facebook_user_id');
    $facebook_user_first_name = $this->post('facebook_user_first_name');
    $facebook_user_last_name = $this->post('facebook_user_last_name');
    $facebook_user_image = $this->post('facebook_user_image');

    if(!$email || !$password) {
      return $this->_error('No email and/or password');
    }

    if(!$facebook_user_id) {
      return $this->_error('Please connect facebook before signing up');
    }

    $this->load->model('user_model');

    if($this->user_model->findOne(array('user_email' => $email))) {
      return $this->_error('Email already used');
    }

    if($this->user_model->findOne(array('user_facebook_id' => $facebook_user_id))) {
      return $this->_error('Facebook account already used');
    }

    $presalt = 'tH!s!$Pr3Za|t';
    $postsalt = 'di#!zp0s+s4LT';
    $encrypted_password = sha1($presalt.$password.$postsalt);

    $user = array(
      'user_first_name' => $facebook_user_first_name,
      'user_last_name' => $facebook_user_last_name,
      'user_image' => $facebook_user_image,
      'user_email' => $email,
      'user_password' => $encrypted_password,
      'user_facebook_id' => $facebook_user_id
    );

    if(!$user_id = $this->user_model->add_user($user)) {
      return $this->_error('Add user failed');
    }

    //Generate token & add into user's mongo model
    $token = md5(uniqid(mt_rand(), true)); //32 chars
    $user_mongo = array(
      'user_id' => $user_id,
      'tokens' => array($token)
    );
    $this->load->model('user_mongo_model');
    if(!$this->user_mongo_model->add($user_mongo)) {
      return $this->_error('Add user failed');
    }

    return $this->_success(array('user_id' => $user_id, 'token' => $token));
  }

  /**
   * Signin SocialHappen
   * @method POST
   * @params type [facebook,email]
   *       , facebook_user_id (if type = facebook)
   *       , email (if type = email)
   *       , password (if type = email)
   * @return user_id, token
   */
  function signin_post() {
    $this->load->model('user_model');
    $type = $this->post('type');
    $facebook_user_id = $this->post('facebook_user_id');
    $email = $this->post('email');
    $password = $this->post('password');

    $signin_success = FALSE;

    if($type === 'facebook') {

      if($user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user_id)) {
        $signin_success = TRUE;
      } else {
        return $this->_error('Your facebook id are not a SocialHappen user');
      }

    } else if($type === 'email') {

      $presalt = 'tH!s!$Pr3Za|t';
      $postsalt = 'di#!zp0s+s4LT';
      $encrypted_password = sha1($presalt.$password.$postsalt);

      if($user = $this->user_model->passwordMatch(array('user_email' => $email), $encrypted_password)) {
        $signin_success = TRUE;
      } else {
        return $this->_error('Wrong email and password combination');
      }

    } else {
      return $this->_error('Wrong type');
    }

    if($signin_success) {
      //Generate token & add into user's mongo model
      $token = md5(uniqid(mt_rand(), true)); //32 chars
      $user_id = $user['user_id'];
      $user_mongo_update = array(
        '$addToSet' => array('tokens' => $token)
      );
      $criteria = array('user_id' => $user_id);
      $this->load->model('user_mongo_model');
      if(!$this->user_mongo_model->upsert($criteria, $user_mongo_update)) {
        return $this->_error('Update token failed');
      }

      return $this->_success(array('user_id' => $user_id, 'user' => $user, 'token' => $token));
    }

    return $this->_error('Sign in failed');
  }

  /**
   * Signout SocialHappen
   * @method POST
   * @params user_id, token
   */
  function signout_post() {
    $user_id = $this->post('user_id');
    $token = $this->post('token');

    $criteria = array('user_id' => $user_id);
    $update = array('$pull' => array('tokens' => $token));

    $this->load->model('user_mongo_model');
    if($this->user_mongo_model->update($criteria, $update)) {
      return $this->_success('Signout successful');
    }

    return $this->_error('Sign out failed');
  }

  /**
   * Get companies
   * @method GET
   * @params -
   */
  function companies_get() {
    $this->load->model('company_model');

    return $this->_success($this->company_model->get_all());
  }

  /**
   * Get rewards
   * @method GET
   * @params -
   */
  function rewards_get() {
    $this->load->model('reward_item_model');

    return $this->_success($this->reward_item_model->get(
      array(
        'status' => 'published',
        'type' => 'redeem'
      )
    ));
  }

  /**
   * Get challenges
   * @method GET
   * @params -
   */
  function challenges_get() {
    $this->load->library('challenge_lib');

    $challenge_id = $this->get('challenge_id');
    $company_id = $this->get('company_id');
    $lon = $this->get('lon');
    $lat = $this->get('lat');
    $max_distance = $this->get('max_distance');
    $limit = $this->get('limit') || NULL;

    $doable_date = $this->get('doable_date'); //[YYYYMMDD] if set, challenge that is not doable in the date will have [next_date] = next date available (requires user_id & token)
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    if($challenge_id) {
      $challenges = $this->challenge_lib->get(array('_id' => new MongoId($challenge_id)));
    } else if($company_id) {
      $challenges = $this->challenge_lib->get(array('company_id' => $company_id));
    } else if(($lon !== FALSE) && ($lat !== FALSE)) {
      $challenges = $this->challenge_lib->get_nearest_challenges(
        array($lon, $lat), $max_distance, $limit);
    } else {
      $challenges = $this->challenge_lib->get(array());
    }

    //Filter challenge if doable_date is set
    if($challenges && $doable_date && $user_id && $token) {
      if(!$this->_check_token($user_id, $token)) {
        return $this->error('Token invalid');
      }

      if(!$user = $this->user_mongo_model->get_user($user_id)) {
        return $this->error('User invalid');
      }

      foreach($challenges as &$challenge) {
        $challenge_id = get_mongo_id($challenge);

        //check completed
        if($is_daily_challenge = (isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && $days > 0)) {

          //Check if user completed already or not
          if(isset($user['daily_challenge_completed']) && isset($user['daily_challenge_completed'][$challenge_id])) {
            foreach($user['daily_challenge_completed'][$challenge_id] as $key => $daily_challenge) {
              if($daily_challenge['start_date'] <= $doable_date && $daily_challenge['end_date'] >= $doable_date) {
                $challenge['next_date'] = date('Ymd', date_create_from_format('Ymd', $doable_date)->getTimestamp() + $days * 24*60*60);
              }
            }
          }
        } else {
          if(isset($user['challenge_completed']) && in_array($challenge_id, $user['challenge_completed'])) {
            //@TODO - don't use date
            $challenge['next_date'] = '30000101';
          }
        }
      }
    }

    //Check challenge quota
    foreach($challenges as &$challenge) {
      if(isset($challenge['done_count_max']) && ($challenge['done_count_max'] > 0)) {
        $done_count = isset($challenge['done_count']) ? $challenge['done_count'] : 0;
        if($done_count >= $challenge['done_count_max']) {
          $challenge['is_out_of_stock'] = TRUE;
        }
      }
    }

    if($challenges === FALSE) {
      return $this->_error('API error');
    } else {
      $challenges = array_map(function($challenge){
        $challenge['_id'] = '' . $challenge['_id'];
        return $challenge;
      }, $challenges);
      return $this->_success($challenges);
    }
  }

  /**
  * Requires token
  */

  /**
   * Check user token
   * @method GET
   * @params user_id, token
   */
  function check_token_get() {
    $user_id = $this->get('user_id');
    $token = $this->get('token');

    if($this->_check_token($user_id, $token)) {
      return $this->_success(array());
    }

    return $this->_error('Token invalid');
  }

  function _check_token($user_id = NULL, $token = NULL) {
    if(!$user_id || !$token) {
      return FALSE;
    }

    $this->load->model('user_mongo_model');
    if(!$user = $this->user_mongo_model->getOne(array(
      'user_id' => (int) $user_id
    ))) {
      return FALSE;
    }

    if(!in_array($token, $user['tokens'])) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Do challenge action
   * @method POST
   * @params user_id, token, action_id, challenge_id[, timestamp]
   */

  function do_action_post() {
    $user_id = (int) $this->post('user_id');
    $token = $this->post('token');
    $action_id = (int) $this->post('action_id');
    $challenge_id = $this->post('challenge_id');
    if(!$time = $this->post('timestamp')) {
      $time = time();
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    if(!$user = $this->user_mongo_model->get_user($user_id)) {
      return $this->error('User invalid');
    }

    //Check if challenge is valid
    $this->load->library('challenge_lib');

    if(!$challenge = $this->challenge_lib->get_by_id($challenge_id)) {
      return $this->error('Challenge invalid');
    }

    //Check challenge quota
    if(isset($challenge['done_count_max']) && ($challenge['done_count_max'] > 0)) {
      $done_count = isset($challenge['done_count']) ? $challenge['done_count'] : 0;
      if($done_count >= $challenge['done_count_max']) {
        return $this->error('Reward out of stock');
      }
    }

    $company_id = (int) $challenge['company_id'];

    //Add audit & stat
    $user_data = array(
      'timestamp' => time()
      //@TODO - add more data
    );

    //find action data
    $this->load->model('action_data_model');
    $action_data_id = $challenge['criteria'][0]['action_data_id'];
    if(!$action_data = $this->action_data_model->getOne(array('_id' => new MongoId($action_data_id)))) { //@TODO - get action data for all criterias
      return $this->error(print_r($challenge, true));
    }

    //Challenge check
    $match_all_criteria = FALSE;
    $match_all_criteria_today = FALSE;
    $is_in_progress = FALSE;

    //3.1 if repeat challenge : check audit in date range
    if($is_daily_challenge = (isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && ($days > 0))) {

      //Check if user completed already or not
      if(isset($user['daily_challenge_completed']) && isset($user['daily_challenge_completed'][$challenge_id])) {
        foreach($user['daily_challenge_completed'][$challenge_id] as $key => $daily_challenge) {
          if($daily_challenge['start_date'] <= date('Ymd', $time) && $daily_challenge['end_date'] >= date('Ymd', $time)) {
            return $this->error('Challenge done already (daily)');
          }
        }
      }

      //add stat after checking challenge done
      $this->load->library('audit_lib');
      $this->load->library('action_user_data_lib');
      if(!$action_user_data_id = $this->action_user_data_lib->add_action_user_data(
        $company_id,
        $action_id,
        $action_data_id,
        $challenge_id,
        $user_id,
        $user_data
        )){
        show_error('Invalid Data');
      } else {
        //Add audit & stat
        $audit_data = array(
          'timestamp' => $time,
          'user_id' => $user_id,
          'action_id' => $action_id,
          'app_id' => 0,
          'app_install_id' => 0,
          'page_id' => 0,
          'company_id' => $company_id,
          'subject' => NULL,
          'object' => NULL,
          'objecti' => $challenge['hash'],
          'image' => $challenge['detail']['image']
        );

        if(!$audit_id = $this->audit_lib->audit_add($audit_data)) {
          return $this->error('Audit add failed'. var_export($audit_data, true));
        }

        //Update action user data with audit id
        if(!$update_result = $this->action_user_data_lib->update_action_user_data($action_user_data_id, array('audit_id' => $audit_id))) {
          return $this->error('Update action user data failed');
        }

        $this->load->library('achievement_lib');
        $info = array(
                'action_id'=> $action_id,
                'app_install_id'=> 0,
                'page_id' => 0
              );
        if(!$achievement_result = $this->achievement_lib->
          increment_achievement_stat($company_id, 0, $user_id, $info, 1)) {
          return $this->error('Increment achievement stat failed');
        }
      }
      //Finish adding stat

      $match_all_criteria_today = TRUE;
      foreach($challenge['criteria'] as $criteria){
        $count_required = $criteria['count'];
        $query = $criteria['query'];
        $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
        $action_id = $query['action_id'];
        $audit_criteria = compact('company_id', 'user_id');
        $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
        $end_date = date('Ymd', $time);

        $audit_count = $this->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

        if($audit_count < $count_required) {
          $match_all_criteria_today = FALSE;
        }
      }

      if($match_all_criteria_today) {
        // $result['completed_today'][] = $challenge_id;
      }
    } else {
      //Check if user completed already or not
      if(isset($user['challenge_completed']) && in_array($challenge_id, $user['challenge_completed'])) {
        return $this->error('Challenge done already');
      }

      //add stat after checking challenge done
      $this->load->library('audit_lib');
      $this->load->library('action_user_data_lib');
      if(!$action_user_data_id = $this->action_user_data_lib->add_action_user_data(
        $company_id,
        $action_id,
        $action_data_id,
        $challenge_id,
        $user_id,
        $user_data
        )){
        show_error('Invalid Data');
      } else {
        //Add audit & stat
        $audit_data = array(
          'timestamp' => $time,
          'user_id' => $user_id,
          'action_id' => $action_id,
          'app_id' => 0,
          'app_install_id' => 0,
          'page_id' => 0,
          'company_id' => $company_id,
          'subject' => NULL,
          'object' => NULL,
          'objecti' => $challenge['hash'],
          'image' => $challenge['detail']['image']
        );

        if(!$audit_id = $this->audit_lib->audit_add($audit_data)) {
          return $this->error('Audit add failed'. var_export($audit_data, true));
        }

        //Update action user data with audit id
        if(!$update_result = $this->action_user_data_lib->update_action_user_data($action_user_data_id, array('audit_id' => $audit_id))) {
          return $this->error('Update action user data failed');
        }

        $this->load->library('achievement_lib');
        $info = array(
                'action_id'=> $action_id,
                'app_install_id'=> 0,
                'page_id' => 0
              );
        if(!$achievement_result = $this->achievement_lib->
          increment_achievement_stat($company_id, 0, $user_id, $info, 1)) {
          return $this->error('Increment achievement stat failed');
        }
      }
      //Finish adding stat

      //3.2 if non-repeat challenge : check achievement stat and action data
      $match_all_criteria = TRUE;
      foreach($challenge['criteria'] as $criteria){
        $query = $criteria['query'];
        $count = $criteria['count'];

        // make stat criteria to query in progress challenges
        $action_query = 'action.'.$query['action_id'].'.company.'.$company_id.'.count';
        if(!isset($query['app_id']) || (isset($criteria['is_platform_action']) && $criteria['is_platform_action'])) {
          //Query in progress challenge
          $stat_criteria = array(
            'app_id' => 0,
            'user_id' => $user_id,
            $action_query => array('$gte' => $count)
          );
        } else {
          //Query in progress challenge
          $stat_criteria = array(
            'app_id' => $query['app_id'],
            'user_id' => $user_id,
            $action_query => array('$gte' => $count)
          );
        }

        /**
         * @TODO: we can reduce one step here // Book
         */

        //if it is in progress, check again with action count
        $is_in_progress = TRUE;
        $stat_criteria[$action_query] = array('$gte' => $count);
        $matched_achievement_stat =
          $this->achievement_stat->list_stat($stat_criteria);
        if(!$matched_achievement_stat) {
          $match_all_criteria = FALSE;
        }else if(isset($criteria['action_data_id'])){

          /**
           * check with action_user_data that user have done it or not
           */
          $this->load->library('action_user_data_lib');
          $action_user_data = $this->action_user_data_lib->
            get_action_user_data_by_action_data($criteria['action_data_id']);

          if(!$action_user_data){
            $match_all_criteria = FALSE;
          }
        }
      }
    }

    $data = array(
      'challenge_completed' => FALSE,
      'reward_item' => NULL
    );

    //3.3 if match all ...
    // 1 add into 'completed'
    // 2 add into user
    // 3 remove from user : challenge/daily_challenge
    // 4 add achievement
    // 5 notificationchallenge_redeeming/challenge_completed/daily_challenge_completed
    // 6 add 'user completed challenge' audit
    // 7 maybe add company score
    // 8 give coupon
    // 9 accept coupon if is point reward
    $this->load->model('reward_item_model');
    if($match_all_criteria || $match_all_criteria_today) {
      //1
      $data['challenge_completed'] = TRUE;
      //@TODO - give every reward item, not just the first one
      $reward_item_id = get_mongo_id($challenge['reward_items'][0]);
      $data['reward_item'] = $this->reward_item_model->get_by_reward_item_id($reward_item_id);

      $achieved_info = array(
        'company_id' => $company_id
      );

      if(isset($info['campaign_id'])){
        $achieved_info['campaign_id'] = $info['campaign_id'];
      }

      //2
      //Update user model
      $this->load->model('user_mongo_model');
      $update_record = array(
        '$addToSet' => array(
          'challenge_redeeming' => $challenge_id,
        ),
        '$pull' => array(
        )
      );

      //3
      //if repeating challenge : add to 'daily_challenge_completed' and remove from 'daily_challenge'
      if(isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && ($days > 0)
        && $match_all_criteria_today) {
        $start_date = date('Ymd', $time);
        $end_date = date('Ymd', $time + (($days-1) * 60 * 60 * 24));
        $achieved_info['daily_challenge'] = array(
          'start_date' => $start_date,
          'end_date' => $end_date
        );
        $update_record['$addToSet']['daily_challenge_completed.'.$challenge_id] = $achieved_info['daily_challenge'];
        $update_record['$pull']['daily_challenge.'.$challenge_id] = $achieved_info['daily_challenge'];
      } else {
        //if not repeating challenge : Add completed challenge into user mongo model and remove from in progress
        $update_record['$addToSet']['challenge_completed'] = $challenge_id;
        $update_record['$pull']['challenge'] = $challenge_id;
      }

      if(!$update_user = $this->user_mongo_model->update(array('user_id' => $user_id), $update_record)) {
        return $this->error('Update user failed');
      }

      //4
      //Add achievement, if duplicated it should not be added
      $ref = 'challenge';
      if(!$this->achievement_user->add($user_id, $challenge_id,
        $query['app_id'] = 0, $info['app_install_id'] = 0, $achieved_info, $ref)){
        // $result['success'] = FALSE;
        // $result['data'] = 'add achievement failed';
      }

      //5
      //Add notification
      $this->load->library('notification_lib');
      $message = 'You have completed a challenge : ' . $challenge['detail']['name'] . '.';
      $link = '#';
      $image = $challenge['detail']['image'];
      if(!$this->notification_lib->add($user_id, $message, $link, $image)) {
        return $this->error('Add notification failed');
      }

      //6
      //Add audit
      $this->load->library('audit_lib');
      $action_id = $this->socialhappen->get_k('audit_action', 'User Complete Challenge');
      $audit = array(
        'app_id' => 0,
        'subject' => '',
        'action_id' => $action_id,
        'company_id' => $company_id,
        'objecti' => $challenge['hash'],
        'user_id' => $user_id,
        'image' => $image
      );
      if(!$audit_add_result = $this->audit_lib->audit_add($audit)) {
        return $this->error('add audit failed');
      }

      //7
      //Add company score
      $this->load->library('audit_lib');
      $action = $this->audit_lib->get_audit_action(0, $action_id);
      // $company_score = $action['score'];
      $company_score = 0; //Now don't give company score from audit
      $increment_info = array(
        'company_score' => $company_score,
        'action_id' => $action_id,
        'app_install_id' => 0
      );
      $this->load->library('achievement_lib');
      if(!$increment_page_score_result = $this->achievement_lib->
        increment_achievement_stat($company_id, 0, $user_id, $increment_info, 1)) {
        return $this->error('increment stat failed');
      }

      //8
      //Give reward coupon
      if(issetor($challenge['reward_items'])) {
        $this->load->library('coupon_lib');
        $this->load->library('reward_lib');
        foreach($challenge['reward_items'] as $reward_item) {
          $reward_item_id = get_mongo_id($reward_item);
          $coupon = array(
            'reward_item' => $reward_item,
            'reward_item_id' => $reward_item_id,
            'user_id' => $user_id,
            'company_id' => $company_id,
            'challenge_id' => $challenge_id
          );
          if(!$coupon_id = $this->coupon_lib->create_coupon($coupon)) {
            return $this->error('add coupon failed');
          }

          //If the reward is_points_reward : approve it immediately
          if(issetor($reward_item['is_points_reward'])) {
            if(!$coupon_confirm_result = $this->coupon_lib->confirm_coupon($coupon_id, 0)) {
              return $this->error('confirm point coupon failed');
            }
          }
        }
      }

      //9 add done count
      $this->load->model('challenge_model');
      $challenge_update_result = $this->challenge_model->update(array('_id' => new MongoId($challenge_id)), array(
        '$inc' => array('done_count' => 1)
      ));
      if(!$challenge_update_result) {
        return $this->error('increment done count failed');
      }
    }

    return $this->_success($data);
  }

  /**
   * Get coupons
   * @method GET
   * @params [user_id, token]
   */
  function coupons_get() {
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    $this->load->model('coupon_model');
    if($user_id && $token) {
      if(!$this->_check_token($user_id, $token)) {
        return $this->error('Token invalid');
      }

      $coupons = $this->coupon_model->get_by_user($user_id);
    } else {
      $coupons = $this->coupon_model->get();
    }

    $coupons = array_map(function($coupon){
      $coupon['_id'] = '' . $coupon['_id'];
      return $coupon;
    }, $coupons);

    return $this->_success($coupons);
  }
}
