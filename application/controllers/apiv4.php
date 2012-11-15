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

  function error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code, 'timestamp' => time()));
    return FALSE;
  }

  function success($data = array(), $code = 1) {
    echo json_encode(array('success' => TRUE, 'data' => $data, 'code' => $code, 'timestamp' => time()));
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
      return $this->error('undefined facebook_user_id');
    }

    //check facebook_user_id in user model
    $this->load->model('user_model');
    $user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user_id);

    if(!$user) {
      return $this->error('user not found');
    }

    return $this->success($user);
  }

  /**
   * Signup SocialHappen
   * @method POST
   * @params email
   *       , phone
   *       , password
   *       , facebook_user_id
   *       , facebook_user_first_name
   *       , facebook_user_last_name
   *       , facebook_user_image
   */
  function signup_post() {
    $email = $this->post('email');
    $phone = $this->post('phone');
    $password = $this->post('password');
    $facebook_user_id = $this->post('facebook_user_id');
    $facebook_user_first_name = $this->post('facebook_user_first_name');
    $facebook_user_last_name = $this->post('facebook_user_last_name');
    $facebook_user_image = $this->post('facebook_user_image');
    $device = $this->post('device');
    $device_token = $this->post('device_token');

    if(!$email || !$phone || !$password) {
      return $this->error('No email, phone, password');
    }

    // if(!$facebook_user_id) {
    //   return $this->error('Please connect facebook before signing up');
    // }

    $this->load->model('user_model');

    if($this->user_model->findOne(array('user_email' => $email))) {
      return $this->error('Email already used');
    }

    if($this->user_model->findOne(array('user_facebook_id' => $facebook_user_id))) {
      return $this->error('Facebook account already used');
    }

    $presalt = 'tH!s!$Pr3Za|t';
    $postsalt = 'di#!zp0s+s4LT';
    $encrypted_password = sha1($presalt.$password.$postsalt);

    $user = array(
      'user_first_name' => $facebook_user_first_name ? $facebook_user_first_name : "",
      'user_last_name' => $facebook_user_last_name ? $facebook_user_last_name : "",
      'user_image' => $facebook_user_image ? $facebook_user_image : "",
      'user_email' => $email,
      'user_password' => $encrypted_password,
      'user_phone' => $phone,
      'user_facebook_id' => $facebook_user_id ? $facebook_user_id : NULL
    );

    if(!$user_id = $this->user_model->add_user($user)) {
      return $this->error('Add user failed');
    }

    //Generate token & add into user's mongo model
    $user_mongo = array(
      'user_id' => $user_id,
      'points' => 10
    );
    $this->load->model('user_mongo_model');
    if(!$this->user_mongo_model->add($user_mongo)) {
      return $this->error('Add user failed');
    }

    //add user token
    $this->load->model('user_token_model');
    $user_token_data = array(
      'user_id' => $user_id,
      'device' => $device,
      'device_token' => $device_token
    );
    if(!$user_token = $this->user_token_model->add_user_token($user_token_data)) {
      return $this->error('Add user token failed');
    }
    $token = $user_token['login_token'];

    //Give user a badge, by add signup audit
    $this->load->library('audit_lib');
    $action_id = $this->socialhappen->get_k('audit_action','User Register SocialHappen');

    if(!$this->audit_lib->audit_add(array(
      'user_id' => $user_id,
      'action_id' => $action_id,
      'app_id' => 0,
      'app_install_id' => 0,
      'company_id' => 0,
      'subject' => $user_id,
      'object' => NULL,
      'objecti' => NULL,
      'image' => $user['user_image']
    ))) {
      return $this->error('Add audit failed');
    }

    $this->load->library('achievement_lib');
    $info = array('action_id' => $action_id, 'app_install_id' => 0);
    if(!$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, 0, $user_id, $info, 1)) {
      return $this->error('increment stat failed');
    }

    unset($user['user_password']);

    return $this->success(array('user_id' => $user_id, 'user' => $user, 'token' => $token));
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
    $device = $this->post('device');
    $device_token = $this->post('device_token');

    $signinsuccess = FALSE;

    if($type === 'facebook') {

      if($user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user_id)) {
        $signinsuccess = TRUE;
      } else {
        return $this->error('Your facebook id are not a SocialHappen user');
      }

    } else if($type === 'email') {

      $presalt = 'tH!s!$Pr3Za|t';
      $postsalt = 'di#!zp0s+s4LT';
      $encrypted_password = sha1($presalt.$password.$postsalt);

      if($user = $this->user_model->passwordMatch(array('user_email' => $email), $encrypted_password)) {
        $signinsuccess = TRUE;
      } else {
        return $this->error('Wrong email and password combination');
      }

    } else {
      return $this->error('Wrong type', 2);
    }

    if(!$signinsuccess) {
      return $this->error('Sign in failed', 2);
    }

    //add user token
    $user_id = $user['user_id'];
    $this->load->model('user_token_model');
    $user_token_data = array(
      'user_id' => $user_id,
      'device' => $device,
      'device_token' => $device_token
    );
    if(!$user_token = $this->user_token_model->add_user_token($user_token_data)) {
      return $this->error('Add user token failed', 2);
    }
    $token = $user_token['login_token'];

    //add audit
    $this->load->library('audit_lib');
    $action_id = $this->socialhappen->get_k('audit_action','User Login');

    if(!$this->audit_lib->audit_add(array(
      'user_id' => $user_id,
      'action_id' => $action_id,
      'app_id' => 0,
      'app_install_id' => 0,
      'company_id' => 0,
      'subject' => $user_id,
      'object' => NULL,
      'objecti' => NULL,
      'image' => $user['user_image']
    ))) {
      return $this->error('Add audit failed', 2);
    }

    $this->load->library('achievement_lib');
    $info = array('action_id' => $action_id, 'app_install_id' => 0);
    if(!$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, 0, $user_id, $info, 1)) {
      return $this->error('increment stat failed', 2);
    }

    return $this->success(array('user_id' => $user_id, 'user' => $user, 'token' => $token));
  }

  /**
   * Signout SocialHappen
   * @method POST
   * @params user_id, token
   */
  function signout_post() {
    $user_id = $this->post('user_id');
    $token = $this->post('token');
    // $device = $this->post('device');
    // $device_token = $this->post('device_token');

    $this->load->model('user_token_model');
    $criteria = array(
      'user_id' => (int) $user_id,
      'login_token' => $token,
      // 'device' => $device,
      // 'device_token' => $device_token
    );

    $this->user_token_model->remove_user_token($criteria);

    return $this->success('Signout successful');
  }

  /**
   * Get companies
   * @method GET
   * @params -
   */
  function companies_get() {
    $company_id = $this->get('company_id');

    $this->load->model('company_model');

    if($company_id) {
      if($company = $this->company_model->get_company_profile_by_company_id($company_id)) {
        return $this->success(array($company));
      }
      return $this->error('Invalid company');
    }

    return $this->success($this->company_model->get_all());
  }

  /**
   * Get rewards
   * @method GET
   * @params [reward_item_id]
   */
  function rewards_get() {
    $reward_item_id = $this->get('reward_item_id');

    $query = array(
      'status' => 'published',
      'type' => 'redeem'
    );

    if($reward_item_id) {
      $query = array(
        '_id' => new MongoId($reward_item_id)
      );
    }

    $this->load->model('reward_item_model');

    $rewards = $this->reward_item_model->get($query);

    $rewards = array_map(function($reward){
      $reward['_id'] = '' . $reward['_id'];
      return $reward;
    }, $rewards);

    return $this->success($rewards);
  }

  /**
   * Get offers (a type of reward item)
   * @method GET
   * @params [reward_item_id]
   */
  function offers_get() {
    $reward_item_id = $this->get('reward_item_id');

    $query = array(
      'status' => 'published',
      'type' => 'offer'
    );

    if($reward_item_id) {
      $query = array(
        '_id' => new MongoId($reward_item_id)
      );
    }

    $this->load->model('reward_item_model');

    $offers = $this->reward_item_model->get($query);

    $offers = array_map(function($offer){
      $offer['_id'] = '' . $offer['_id'];
      return $offer;
    }, $offers);

    return $this->success($offers);
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

      $this->load->model('user_mongo_model');
      if(!$user = $this->user_mongo_model->get_user($user_id)) {
        return $this->error('User invalid');
      }

      $this->load->model('coupon_model');

      foreach($challenges as &$challenge) {
        $challenge_id = get_mongo_id($challenge);

        //check completed
        if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0)) {

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
            $challenge['next_date'] = FALSE;
            //User coupons
            $challenge['coupons'] = $this->coupon_model->get_by_user_and_challenge($user['user_id'], $challenge_id);
          }
        }
      }
    }

    $this->load->model('company_model');
    foreach($challenges as &$challenge) {
      //Check challenge quota
      if(isset($challenge['done_count_max']) && ($challenge['done_count_max'] > 0)) {
        $done_count = isset($challenge['done_count']) ? $challenge['done_count'] : 0;
        if($done_count >= $challenge['done_count_max']) {
          $challenge['is_out_of_stock'] = TRUE;
        }
      }

      //embed company data if not finding with company_id
      if(!$company_id) {
        $challenge['company'] = $this->company_model->get_company_profile_by_company_id($challenge['company_id']);
      } else {
        $challenge['company'] = $this->company_model->get_company_profile_by_company_id($company_id);
      }
    }

    if($challenges === FALSE) {
      return $this->error('API error');
    } else {
      $challenges = array_map(function($challenge){
        $challenge['_id'] = '' . $challenge['_id'];
        return $challenge;
      }, $challenges);
      return $this->success($challenges);
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
      return $this->success(array());
    }

    return $this->error('Token invalid');
  }

  function _check_token($user_id = NULL, $token = NULL) {
    if(!$user_id || !$token) {
      return FALSE;
    }

    $this->load->model('user_token_model');
    if(!$user = $this->user_token_model->getOne(array(
      'user_id' => (int) $user_id,
      'login_token' => $token
    ))) {
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
    $location = $this->post('location');

    if(!$time = $this->post('timestamp')) {
      $time = time();
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    $this->load->model('user_mongo_model');
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
    if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && ($days > 0))) {

      //Check if user completed already or not
      if(isset($user['daily_challenge_completed']) && isset($user['daily_challenge_completed'][$challenge_id])) {
        foreach($user['daily_challenge_completed'][$challenge_id] as $key => $daily_challenge) {
          if($daily_challenge['start_date'] <= date('Ymd', $time) && $daily_challenge['end_date'] >= date('Ymd', $time)) {
            return $this->error('Challenge done already (daily)', 1);
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
        return $this->error('Invalid Data');
      }

      //Add audit & stat
      $audit_data = array(
        'timestamp' => $time,
        'user_id' => $user_id,
        'action_id' => $action_id,
        'app_id' => 0,
        'app_install_id' => 0,
        'page_id' => 0,
        'company_id' => $company_id,
        'subject' => $location ? $location : NULL,
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
        return $this->error('Challenge done already', 1);
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
        showerror('Invalid Data');
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
          'subject' => $location ? $location : NULL,
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
      'reward_items' => NULL
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

      $reward_item_ids = array_map(function($reward_item) {
        return new MongoId(get_mongo_id($reward_item));
      }, $challenge['reward_items']);

      $data['reward_items'] = $this->reward_item_model->get(array('_id' => array( '$in' => $reward_item_ids )));

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
      if(isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && ($days > 0)
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
//notification did not work now
      //if(!$this->notification_lib->add($user_id, $message, $link, $image)) {
      //  return $this->error('Add notification failed');
      //}

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

          //Attach coupon_id to $data['reward_items']
          foreach ($data['reward_items'] as &$data_reward_item) {
            $data_reward_item_id = get_mongo_id($data_reward_item);
            if($data_reward_item_id == $reward_item_id) {
              $data_reward_item['coupon_id'] = $coupon_id;
            }
          }
        }
      }

      //9 add done count
      //Get all challlenge points
      if(!isset($reward_points)) {
        $reward_points = 0;

        $reward_item_ids = array_map(function($reward_item) {
          return new MongoId(get_mongo_id($reward_item));
        }, $challenge['reward_items']);

        $this->load->model('reward_item_model');
        $reward_items = $this->reward_item_model->get(array('_id' => array( '$in' => $reward_item_ids )));

        foreach($reward_items as $reward_item) {
          if(issetor($reward_item['is_points_reward'])) {
            $reward_points += $reward_item['value'];
          }
        }

        //get company
        $this->load->model('company_model');
        $data['company'] = $this->company_model->get_company_profile_by_company_id($company_id);
      }

      //Add user platform points
      $user_update = array(
        '$inc' => array('points' => $reward_points)
      );

      if(!$this->user_mongo_model->update(array('user_id' => (int) $user_id), $user_update)) {
        return $this->error('Update user failed.');
      }

      $this->load->model('challenge_model');
      $challenge_update_result = $this->challenge_model->update(array('_id' => new MongoId($challenge_id)), array(
        '$inc' => array('done_count' => $reward_points)
      ));
      if(!$challenge_update_result) {
        return $this->error('increment done count failed');
      }
    }

    return $this->success($data);
  }

  /**
   * Get coupons
   * @method GET
   * @params [coupon_id, user_id, token]
   */
  function coupons_get() {
    $coupon_id = $this->get('coupon_id');
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    if($coupon_id) {
      $query = array('_id' => new MongoId($coupon_id));
    } else if($user_id && $token) {
      if(!$this->_check_token($user_id, $token)) {
        return $this->error('Token invalid');
      }
      $query = array('user_id' => $user_id);
    } else {
      return $this->error('Invalid parameters');
    }

    $this->load->model('coupon_model');
    $coupons = $this->coupon_model->get($query);

    $this->load->model('company_model');
    $this->load->model('challenge_model');
    $companies = $challenges = array();
    $coupons = array_map(function($coupon){
      $coupon['_id'] = '' . $coupon['_id'];
      return $coupon;
    }, $coupons);

    foreach($coupons as &$coupon) {
      //Get companies
      if($company_id = $coupon['company_id']) {
        if(isset($companies[$company_id])) {
          $company = $companies[$company_id];
        } else {
          $company = $this->company_model->get_company_profile_by_company_id($company_id);
          $companies[$company_id] = $company;
        }
        $coupon['company'] = $company;
      } else {
        $coupon['company'] = NULL;
      }

      //Get challenges
      if($challenge_id = $coupon['challenge_id']) {
        if(isset($challenges[$challenge_id])) {
          $challenge = $challenges[$challenge_id];
        } else {
          $challenge = $this->challenge_model->getOne(array('_id' => new MongoId($challenge_id)));
          $challenges[$challenge_id] = $challenge;
        }
        $coupon['challenge'] = $challenge;
      } else {
        $coupon['challenge'] = NULL;
      }
    }
    return $this->success($coupons);
  }

  /**
   * Get badges (achievements)
   * @method GET
   * @params [user_id, token]
   */
  function badges_get() {
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    $this->load->model('coupon_model');
    if($user_id && $token) {
      if(!$this->_check_token($user_id, $token)) {
        return $this->error('Token invalid');
      }

      $coupons = $this->coupon_model->get_by_user($user_id);
    } else {
      return $this->error('User invalid');
    }

    $this->load->library('achievement_lib');
    $achievements = $this->achievement_lib->list_user_achieved_by_user_id($user_id);

    $achievements = array_map(function($achievement){
      $achievement['_id'] = '' . $achievement['_id'];
      return $achievement;
    }, $achievements);

    return $this->success($achievements);
  }

  /**
   * Get user's profile
   * @method GET
   * @params [user_id, token]
   */
  function profile_get() {
    //@TODO
    if($this->get('model')) {
      return $this->profile_post();
    }

    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    $this->load->model('user_model');
    if(!$user_id || !$token) {
      return $this->error('User invalid');
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    if(!$user = $this->user_model->get_user_profile_by_user_id($user_id)) {
      return $this->error('User invalid');
    }

    //update user's last_active
    $this->load->model('user_token_model');
    $this->user_token_model->update_last_active(array('user_id' => $user_id, 'login_token' => $token));

    //get user points
    $this->load->model('user_mongo_model');
    $user_mongo = $this->user_mongo_model->get_user($user_id);
    $user['points'] = issetor($user_mongo['points'], 0);
    $user['challenge_completed'] = issetor($user_mongo['challenge_completed'], array());
    $user['daily_challenge_completed'] = issetor($user_mongo['daily_challenge_completed'], array());
    $user['shipping'] = issetor($user_mongo['shipping'], array());

    return $this->success($user);
  }

  /**
   * Update user's profile
   * @method POST
   * @params [model]
   */
  function profile_post() {
    $model = json_decode($this->post('model'), TRUE);

    $user_id = (int) $model['user_id'];
    $token = $model['token'];

    $user_first_name = $model['user_first_name'];
    $user_last_name = $model['user_last_name'];
    $user_email = $model['user_email'];
    $user_phone = $model['user_phone'];
    $user_address = $model['user_address'];
    $shipping = $model['shipping'];

    $update = compact('user_first_name', 'user_last_name', 'user_email', 'user_phone', 'user_address');
    $update_mongo = compact('shipping');

    $this->load->model('user_model');
    if(!$user_id || !$token) {
      return $this->error('User invalid');
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    $this->load->model('user_model');
    if(!$this->user_model->update_user($user_id, $update)) {
      return $this->error('Update failed');
    }

    $this->load->model('user_mongo_model');
    if(!$this->user_mongo_model->update(array('user_id' => $user_id), array('$set' => $update_mongo))) {
      return $this->error('Update failed');
    }

    return $this->success(array_merge($update, $update_mongo));
  }

  /**
   * Redeem reward
   * @method POST
   * @params [user_id, token, reward_item_id]
   */
  function redeem_reward_post() {
    $user_id = (int) $this->post('user_id');
    $token = $this->post('token');
    $reward_item_id = $this->post('reward_item_id');
    $shipping = json_decode($this->post('shipping'), TRUE);

    $this->load->model('user_mongo_model');
    if(!$user_id || !$token) {
      return $this->error('User invalid');
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    if(!$user = $this->user_mongo_model->get_user($user_id)) {
      return $this->error('User invalid');
    }

    $this->load->model('reward_item_model');
    if(!$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id)) {
      return $this->error('Invalid reward');
    }

    if(!isset($reward_item['redeem'])) {
      return $this->error('Invalid reward');
    }

    // published reward only
    if($reward_item['status'] !== 'published') {
      return $this->error('Invalid reward', 0);
    }

    // redeemable once and redeemed already
    $this->load->library('coupon_lib');
    if($reward_item['redeem']['once']) {
      if($this->coupon_lib->get_one(array('reward_item_id' => $reward_item_id, 'user_id' => $user_id))) {
        return $this->error('You have already redeemed this reward', 1);
      }
    }

    if(isset($reward_item['redeem']['amount_redeemed']) && ($reward_item['redeem']['amount_redeemed'] === $reward_item['redeem']['amount'])) {
      return $this->error('Reward out of stock', 2);
    }

    // remaining points
    if($user['points'] < $reward_item['redeem']['point']) {
      return $this->error('Insufficient point', 3);
    }

    // decrement point
    $decrement_point = array(
      '$inc' => array('points' => - abs($reward_item['redeem']['point']))
    );
    if(!$this->user_mongo_model->update(array('user_id' => $user_id), $decrement_point)) {
      return $this->error('Unexpected error', 4);
    }

    // increment amount_redeemed
    $reward_update = array(
      'type' => 'redeem',
      'redeem' => array(
        'point' => $reward_item['redeem']['point'],
        'amount' => $reward_item['redeem']['amount'],
        'amount_redeemed' => isset($reward_item['redeem']['amount_redeemed']) ? $reward_item['redeem']['amount_redeemed'] + 1 : 1,
        'once' => $reward_item['redeem']['once']
      )
    );
    if(!$this->reward_item_model->update($reward_item_id, $reward_update)) {
      return $this->error('Unexpected error', 5);
    }

    // add action
    $this->load->library('audit_lib');
    if(!$audit_add_result = $this->audit_lib->audit_add(array(
      'app_id' => 0,
      'action_id' =>
        $this->socialhappen->get_k('audit_action', 'User Receive Coupon'),
      'object' => $reward_item['name'],
      'objecti' => $reward_item_id,
      'user_id' => $user_id,
      'company_id' => 0,
      'image' => $reward_item['image']
    ))) {
      return $this->error('Unexpected error', 6);
    }

    // give coupon for the reward
    $coupon = array(
      'reward_item_id' => $reward_item_id,
      'reward_item' => $reward_item,
      'user_id' => $user_id,
      'company_id' => 0,
      'shipping' => $shipping
    );
    if(!$coupon_id = $this->coupon_lib->create_coupon($coupon)) {
      return $this->error('Unexpected error', 7);
    }

    $result = array(
      'coupon_id' => $coupon_id,
      'coupon' => $coupon,
      'points_remain' => $user['points'] - $reward_item['redeem']['point']
    );
    return $this->success($result);
  }

  /**
   * Get cards (coupon's challenge, sort by company)
   * @method GET
   * @params [user_id, token]
   */
  function cards_get() {
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    $this->load->model('user_mongo_model');
    if(!$user_id || !$token) {
      return $this->error('User invalid');
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    if(!$user = $this->user_mongo_model->get_user($user_id)) {
      return $this->error('User invalid');
    }

    $this->load->model('coupon_model');
    $coupons = $this->coupon_model->get(array('user_id' => $user_id, 'company_id' => array('$ne' => 0) ));
    $coupons = array_map(function($coupon){
      if(!$coupon['challenge_id']) {
        return FALSE; // coupons without challenge will be filtered
      }
      $coupon['_id'] = get_mongo_id($coupon);
      return $coupon;
    }, $coupons);

    $coupons = array_filter($coupons);

    $challenge_ids = array_map(function($coupon){
      return new MongoId($coupon['challenge_id']);
    }, $coupons);

    $this->load->model('challenge_model');
    $challenges = $this->challenge_model->get(array('_id' => array('$in' => $challenge_ids)));

    foreach($coupons as $key => &$coupon) {
      foreach($challenges as $challenge) {
        $challenge_id = get_mongo_id($challenge);
        if($coupon['challenge_id'] === $challenge_id) {
          $challenge['_id'] = $challenge_id;
          $coupon['challenge'] = $challenge;
          break;
        }
      }
      if(!isset($coupon['challenge'])) {
        unset($coupons[$key]); // sometimes challenge does not exist even challenge_id is present
      }
    }

    $coupons = array_values($coupons); //reindex

    return $this->success($coupons);
  }

  /**
   * Test Push Notification
   */
  function test_push_notification_get()
  {
      //@TODO get everyone's device token
      //narze's
      $device_token = 'e13b8f33d312bcb227cd3260e46e207b7a01d0f331b58f39cc2e91cc35cd6978';

      $this->load->library('apn');
      $this->apn->payloadMethod = 'enhance'; // you can turn on this method for debuggin purpose
      $this->apn->connectToPush();

      // adding custom variables to the notification
      $this->apn->setData(array( 'someKey' => true ));

      $send_result = $this->apn->sendMessage($device_token, 'Test notif #1 (TIME:'.date('H:i:s').')', /*badge*/ 2, /*sound*/ 'default'  );

      if($send_result) {
        echo 'sended';
          // log_message('debug','Sending successful');

      }
      else {
          log_message('error',$this->apn->error);
      }

      $this->apn->disconnectPush();
  }

  // designed for retreiving devices, on which app not installed anymore
  public function apn_feedback()
  {
      $this->load->library('apn');

      $unactive = $this->apn->getFeedbackTokens();

      if (!count($unactive))
      {
          log_message('info','Feedback: No devices found. Stopping.');
          return false;
      }

      foreach($unactive as $u)
      {
          $devices_tokens[] = $u['devtoken'];
      }

      /*
      print_r($unactive) -> Array ( [0] => Array ( [timestamp] => 1340270617 [length] => 32 [devtoken] => 002bdf9985984f0b774e78f256eb6e6c6e5c576d3a0c8f1fd8ef9eb2c4499cb4 ) )
      */
  }

  function notice_get() {
    $version = $this->get('version');

    if($version == 0) {
      $data = array(
        'title' => 'Notice',
        'message' => 'This is a test message',
        'close' => TRUE
      );
    } else if($version > 1) {
      //...
    }

    if(isset($data)) {
      return $this->success($data);
    }

    return $this->error('No notice');
  }
}
