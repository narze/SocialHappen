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
    $device_id = $this->post('device_id');
    $device_token = $this->post('device_token');
    $device_name = $this->post('device_name');

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
      /**
       * FOR PRIVATE TESTING ONLY
       * CHANGE availagle TO TRUE WHEN LAUNCH IN PUBLIC
       */
      'available' => TRUE,
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
      'device_id' => $device_id,
      'device_token' => $device_token,
      'device_name' => $device_name
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

    $code = 1;

    /**
     * FOR PRIVATE TESTING ONLY
     * REMOVE THIS WHEN LAUNCH IN PUBLIC
     */
    // if(isset($user_mongo['available']) && $user_mongo['available'] === FALSE) {
    //   $code = 3;
    // }

    return $this->success(array('user_id' => $user_id, 'user' => $user, 'token' => $token), $code);
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
    $device_id = $this->post('device_id');
    $device_token = $this->post('device_token');
    $device_name = $this->post('device_name');

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
      'device_id' => $device_id,
      'device_token' => $device_token,
      'device_name' => $device_name
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

    $code = 1;

    //Some old users didn't have mongo model, so add it if not found
    $this->load->model('user_mongo_model');
    if(!$user_mongo = $this->user_mongo_model->get_user($user_id)) {
      //Generate token & add into user's mongo model
      $add_user_mongo = array(
        'user_id' => $user_id,
        'points' => 10 //starter
      );
      if(!$this->user_mongo_model->add($add_user_mongo)) {
        // return $this->error('Add user failed');
      }
    }

    /**
     * FOR PRIVATE TESTING ONLY
     * REMOVE THIS WHEN LAUNCH IN PUBLIC
     */
    // if(isset($user_mongo['available']) && $user_mongo['available'] === FALSE) {
    //   $code = 3;
    // }

    return $this->success(array('user_id' => $user_id, 'user' => $user, 'token' => $token), $code);
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
    $skip_system_company = $this->get('skip_system_company');

    $this->load->model('company_model');

    if($company_id) {
      if($company = $this->company_model->get_company_profile_by_company_id($company_id)) {
        return $this->success(array($company));
      }
      return $this->error('Invalid company');
    }
    if($skip_system_company && ($skip_system_company !== "false")) {
      return $this->success($this->company_model->get_all_except_system());
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
   * Get branches
   * @method GET
   * @params -
   */
  function branches_get(){
    $challenge_id = $this->get('challenge_id');
    $company_id = $this->get('company_id');
    $branch_id = $this->get('branch_id');

    $this->load->library('branch_lib');
    if($branch_id) {
      if(!$branch = $this->branch_lib->get_one(array(
        '_id' => new MongoId($branch_id)))) {
        return $this->error('Branch not found');
      }

      return $this->success(array($branch));

    } else if($challenge_id) {

      $this->load->library('challenge_lib');

      $challenges = $this->challenge_lib->get_with_branches_data(array('_id' => new MongoId($challenge_id)));
      if($challenges){
        $challenge = count($challenges) > 0 ? $challenges[0] : NULL;
        if($challenge && isset($challenge['branches_data'])){
          return $this->success($challenge['branches_data']);
        }else{
          return $this->error('Branch data not found');
        }
      }else{
        return $this->error('Challenge not found');
      }
    } else if($company_id) {
      $branches = $this->branch_lib->get(array(
        'company_id' => $company_id
      ), 10000000);

      if($branches){
        return $this->success($branches);
      }else{
        return $this->error('Branch not found');
      }
    } else {
      return $this->error('Criteria not set');
    }
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
    $and_without_location = $this->get('and_without_location');

    $doable_date = $this->get('doable_date'); //[YYYYMMDD] if set, challenge that is not doable in the date will have [next_date] = next date available (requires user_id & token)
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    //Skip company_id = 1
    $skip_system_company = $this->get('skip_system_company');

    if($challenge_id) {
      $challenges = $this->challenge_lib->get(array('_id' => new MongoId($challenge_id)));
    } else if($company_id) {
      $challenges = $this->challenge_lib->get(array('company_id' => $company_id, 'active' => array('$ne' => FALSE)));
    } else if(($lon !== FALSE) && ($lat !== FALSE)) {
      $challenges = $this->challenge_lib->get_nearest_challenges(
        array($lon, $lat), $max_distance, $limit, $and_without_location, array('active' => array('$ne' => FALSE)));
    } else {
      $challenges = $this->challenge_lib->get(array('active' => array('$ne' => FALSE)));
    }

    // if got only 1 challenge (use challenge_id) get action done time
    if($user_id && $challenge_id && (count($challenges) === 1)) {
      if(!$this->_check_token($user_id, $token)) {
        return $this->error('Token invalid');
      }
      $challenge = &$challenges[0];
      $company_id = (int) $challenge['company_id'];
      $criterias = $challenge['criteria'];
      $is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0);
      $time = time();

      $this->load->model('achievement_stat_model', 'achievement_stat');
      $this->load->library('audit_lib');
      $this->load->library('action_user_data_lib');

      foreach($challenge['criteria'] as &$criteria) {
        //get action done time from action_data ?

        // from challenge_lib->check_challenge
        if($is_daily_challenge) {
          $count_required = $criteria['count'];
          $query = $criteria['query'];
          $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
          $action_id = (int) $query['action_id'];
          $audit_criteria = compact('company_id', 'user_id');
          $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
          $end_date = date('Ymd', $time);

          $audit_count = $this->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

          if($audit_count > $count_required) {
            $audit_criteria = compact('app_id', 'action_id', 'company_id', 'user_id');
            $actions = $this->audit_lib->list_audit_range(NULL, $audit_criteria, $start_date, $end_date);
            $latest_action = $actions[count($actions) - 1];
            $criteria['completed'] = $latest_action['timestamp'];
          }
        }

        if(!isset($criteria['completed'])) {
        //3.2 check achievement stat and action data
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
          $matched_achievement_stat =
            $this->achievement_stat->list_stat($stat_criteria);
          if(!$matched_achievement_stat) {
            $match_all_criteria = FALSE;
          }else if(isset($criteria['action_data_id'])){

            /**
             * check with action_user_data that user have done it or not
             */
            $action_user_datas = $this->action_user_data_lib->
              get_action_user_data_by_action_data($criteria['action_data_id'], $user_id);

            if($action_user_datas){
              if($is_daily_challenge) {
                $latest_action_user_data = $action_user_datas[count($action_user_datas) - 1];

                $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
                $end_date = date('Ymd', $time);
                $action_date = date('Ymd', $latest_action_user_data['user_data']['timestamp']);

                // if lastest timestamp is in date range
                if($action_date >= $start_date && $action_date <= $end_date) {
                  $criteria['completed'] = $latest_action_user_data['user_data']['timestamp'];
                }
              } else {
                $latest_action_user_data = $action_user_datas[count($action_user_datas) - 1];
                $criteria['completed'] = $latest_action_user_data['user_data']['timestamp'];
              }
            }
          }
        }

        if(!isset($criteria['completed'])) {
          $criteria['completed'] = FALSE;
        }
      } unset($criteria);
    }

    //Filter challenge if user & doable_date is set
    if($challenges && $user_id && $token) {
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

        //check challenge progress (actions)
        foreach($challenge['criteria'] as &$criteria) {
          $action_data_id = issetor($criteria['action_data_id']);
          if(isset($user['challenge_progress'][$challenge_id]['action_data']) && in_array($action_data_id, $user['challenge_progress'][$challenge_id]['action_data'])) {
            $criteria['completed'] = TRUE; // TODO : change this to timestamp
          } else {
            // $criteria['completed'] = FALSE;
          }
        }

        if($doable_date) {
          //check challenge completed
          if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0)) {

            //Check if user completed already or not
            if(isset($user['daily_challenge_completed']) && isset($user['daily_challenge_completed'][$challenge_id])) {
              foreach($user['daily_challenge_completed'][$challenge_id] as $key => $daily_challenge) {
                if($daily_challenge['start_date'] <= $doable_date && $daily_challenge['end_date'] >= $doable_date) {
                  $challenge['next_date'] = date('Ymd', date_create_from_format('Ymd', $doable_date)->getTimestamp() + $days * 24*60*60);
                  //User coupons
                  $coupons = $this->coupon_model->get_by_user_and_challenge($user['user_id'], $challenge_id);
                  $latest_coupon = reset($coupons);
                  $challenge['coupon_status'] = $latest_coupon['confirmed'] ? 'confirmed' : 'pending';
                  $challenge['coupon_id'] = get_mongo_id($latest_coupon);

                  //get challenge complete timestamp
                  $challenge['completed_at'] = $latest_coupon['timestamp'];
                }
              }
            }
          } else {
            if(isset($user['challenge_completed']) && in_array($challenge_id, $user['challenge_completed'])) {
              $challenge['next_date'] = FALSE;
              //User coupons
              $coupons = $this->coupon_model->get_by_user_and_challenge($user['user_id'], $challenge_id);
              $latest_coupon = reset($coupons);
              $challenge['coupon_status'] = $latest_coupon['confirmed'] ? 'confirmed' : 'pending';
              $challenge['coupon_id'] = get_mongo_id($latest_coupon);

              $challenge['coupons'] = $coupons;

              //get challenge complete timestamp
              $challenge['completed_at'] = $latest_coupon['timestamp'];
            }
          }

          if(issetor($challenge['reward_items'][0]['is_points_reward'])) {
            unset($challenge['coupon_status']);
          }
        }
      }
    }

    $companies = array();

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
      if($company_id) {
        if(!isset($companies[$company_id])) {
          $companies[$company_id] = $this->company_model->get_company_profile_by_company_id($challenge['company_id']);
        }
        $challenge['company'] = $companies[$company_id];
      } else {
        if(!isset($companies[$challenge['company_id']])) {
          $companies[$challenge['company_id']] = $this->company_model->get_company_profile_by_company_id($challenge['company_id']);
        }
        $challenge['company'] = $companies[$challenge['company_id']];
      }

      //Challenge could not be done if credits <= 0
      if($challenge['company']['credits'] <= 0) {
        $challenge['is_out_of_stock'] = TRUE;
      }

      if(!isset($challenge['is_out_of_stock'])) {
        $challenge['is_out_of_stock'] = FALSE;
      }
    }

    if($challenges === FALSE) {
      return $this->error('API error');
    } else {
      $challenge_count = count($challenges);
      for($i = 0; $i < $challenge_count; $i++) {
        if($skip_system_company && ($challenges[$i]['company_id'] == 1)) {
          unset($challenges[$i]);
          continue;
        }
        $challenges[$i]['_id'] = '' . $challenges[$i]['_id'];
      }
      $challenges = array_values($challenges); //reindex

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
    $action_data_id = $this->post('action_data_id');
    $action_user_data = $this->post('action_user_data') ? : array();

    $data = array(); // output

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

    $company_id = (int) $challenge['company_id'];

    //Check challenge quota
    $this->load->library('audit_lib');
    if(isset($challenge['done_count_max']) && ($challenge['done_count_max'] > 0)) {
      $done_count = isset($challenge['done_count']) ? $challenge['done_count'] : 0;
      if($done_count >= $challenge['done_count_max']) {

        $audit_data = array(
          'timestamp' => $time,
          'user_id' => $user_id,
          'action_id' => $this->socialhappen->get_k('audit_action', 'Action Failure'),
          'app_id' => 0,
          'app_install_id' => 0,
          'page_id' => 0,
          'company_id' => $company_id,
          'subject' => $location ? $location : NULL,
          'object' => $action_id,
          'objecti' => 'Reward out of stock',
          'image' => '',
          'challenge_id' => get_mongo_id($challenge)
        );

        if(!$this->audit_lib->audit_add($audit_data)) {
          return $this->error('Audit add failed'. var_export($audit_data, true));
        }

        return $this->error('Reward out of stock');
      }
    }

    // Parse action user data

    if($action_id === $this->socialhappen->get_k('audit_action', 'Feedback')) {
      $action_user_data = $this->_parse_feedback_action_user_data($action_user_data);
    }

    //Add audit & stat
    $user_data = array_merge(array(
      'timestamp' => time()
    ), $action_user_data);

    //find action data
    $this->load->model('action_data_model');
    $default_action_data_id = $challenge['criteria'][0]['action_data_id'];
    $nth_action = 0;

    if($action_data_id) {
      foreach ($challenge['criteria'] as $nth => $action) {
        if($action['action_data_id'] === $action_data_id) {
          $default_action_data_id = $action_data_id;
          $nth_action = $nth;
          $action_id = $action['query']['action_id'];
        }
      }
    }

    if(!$action_data = $this->action_data_model->getOne(array('_id' => new MongoId($default_action_data_id)))) { //@TODO - get action data for all criterias
      return $this->error(print_r($challenge, true));
    }

    //Action data check if exists
    if($action_data_id) {
      if($this->user_mongo_model->getOne(array('user_id' => $user_id, 'challenge_progress.'.$challenge_id.'.action_data' => $action_data_id))) {
        return $this->error('Action done already', 2);
      }
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

            $audit_data = array(
              'timestamp' => $time,
              'user_id' => $user_id,
              'action_id' => $this->socialhappen->get_k('audit_action', 'Action Failure'),
              'app_id' => 0,
              'app_install_id' => 0,
              'page_id' => 0,
              'company_id' => $company_id,
              'subject' => $location ? $location : NULL,
              'object' => $action_id,
              'objecti' => 'Challenge done already (daily)',
              'image' => '',
              'challenge_id' => get_mongo_id($challenge)
            );

            if(!$this->audit_lib->audit_add($audit_data)) {
              return $this->error('Audit add failed'. var_export($audit_data, true));
            }

            return $this->error('Challenge done already (daily)', 1);
          }
        }
      }


      //Add audit & stat with action user data id
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
        'image' => $challenge['detail']['image'],
        'action_data_id' => $default_action_data_id
      );

      if(!$audit_id = $this->audit_lib->audit_add($audit_data)) {
        return $this->error('Audit add failed'. var_export($audit_data, true));
      }

      //add stat after checking challenge done status
      $this->load->library('action_user_data_lib');
      if(!$action_user_data_id = $this->action_user_data_lib->add_action_user_data(
        $company_id,
        $action_id,
        $default_action_data_id,
        $challenge_id,
        $user_id,
        $user_data,
        array('audit_id' => $audit_id)
        )){
        return $this->error('Invalid Data');
      }

      // //Update action user data with audit id
      // if(!$update_result = $this->action_user_data_lib->update_action_user_data($action_user_data_id, array('audit_id' => $audit_id))) {
      //   return $this->error('Update action user data failed');
      // }

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
      $data['action_completed'] = FALSE;

      $match_all_criteria_today = TRUE;
      foreach($challenge['criteria'] as $criteria){
        $count_required = $criteria['count'];
        $query = $criteria['query'];
        $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
        $action_id = $query['action_id'];
        $action_data_id = $criteria['action_data_id'];
        $audit_criteria = compact('company_id', 'user_id', 'action_data_id');
        $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
        $end_date = date('Ymd', $time);

        $audit_count = $this->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

        if($audit_count < $count_required) {
          $match_all_criteria_today = FALSE;
        } else if($criteria['action_data_id'] === $default_action_data_id) {
          $data['action_completed'] = TRUE;
          if(!$this->user_mongo_model->update(array('user_id' => $user_id),
            array('$addToSet' => array(
              'challenge_progress.'.$challenge_id.'.action_data' => $action_data_id
            ),'$set' => array(
              'challenge_progress.'.$challenge_id.'.timestamp' => time(),
            )))) {
            return $this->error('Update user failed');
          }
        }
      }

      if($match_all_criteria_today) {
        // $result['completed_today'][] = $challenge_id;
      }
    } else {
      //Check if user completed already or not
      if(isset($user['challenge_completed']) && in_array($challenge_id, $user['challenge_completed'])) {

        $audit_data = array(
          'timestamp' => $time,
          'user_id' => $user_id,
          'action_id' => $this->socialhappen->get_k('audit_action', 'Action Failure'),
          'app_id' => 0,
          'app_install_id' => 0,
          'page_id' => 0,
          'company_id' => $company_id,
          'subject' => $location ? $location : NULL,
          'object' => $action_id,
          'objecti' => 'Challenge done already',
          'image' => '',
          'challenge_id' => get_mongo_id($challenge)
        );

        if($this->audit_lib->audit_add($audit_data)) {
          return $this->error('Audit add failed'. var_export($audit_data, true));
        }

        return $this->error('Challenge done already', 1);
      }

      //add stat after checking challenge done
      $this->load->library('audit_lib');
      $this->load->library('action_user_data_lib');
      if(!$action_user_data_id = $this->action_user_data_lib->add_action_user_data(
        $company_id,
        $action_id,
        $default_action_data_id,
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
          } else if($action_data_id && ($criteria['action_data_id'] === $action_data_id)) {
            $data['action_completed'] = TRUE;
            if(!$this->user_mongo_model->update(array('user_id' => $user_id), array('$addToSet' => array('challenge_progress.'.$challenge_id.'.action_data' => $action_data_id)))) {
              return $this->error('Update user failed');
            }
          }
        }
      }
    }

    $data['challenge_completed'] = FALSE;
    $data['reward_items'] = NULL;
    $data['challenge'] = $challenge;

    //get company
    $this->load->model('company_model');
    $data['company'] = $this->company_model->get_company_profile_by_company_id($company_id);

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
        '$pull' => array(),
        '$unset' => array()
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
        $update_record['$unset']['challenge_progress.'.$challenge_id] = 1;
      } else {
        //if not repeating challenge : Add completed challenge into user mongo model and remove from in progress
        $update_record['$addToSet']['challenge_completed'] = $challenge_id;
        $update_record['$pull']['challenge'] = $challenge_id;
        $update_record['$unset']['challenge_progress.'.$challenge_id] = 1;
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

      $image = $challenge['detail']['image'];

      //5
      //Add notification
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

      if($reward_points !== 0) {
        //Add user platform points
        $user_update = array(
          '$inc' => array('points' => $reward_points)
        );

        if(!$this->user_mongo_model->update(array('user_id' => (int) $user_id), $user_update)) {
          return $this->error('Update user failed.');
        }

        //Decrement company credits
        if(!$company = $data['company']) {
          return $this->error('Invalid Company');
        }

        $company['credits'] = issetor($company['credits'], 0) - $reward_points;
        //decrement company credits
        $company_update = array(
          'credits' => $company['credits']
        );

        if(!$result = $this->company_model->update_company_profile_by_company_id($company_id, $company_update)) {
          return $this->error('Update company failed');
        }

        //add credit use audit
        $this->load->library('audit_lib');
        $audit_data = array(
          'user_id' => $user['user_id'],
          'action_id' => $this->socialhappen->get_k('audit_action', 'Credit Use From Challenge'),
          'app_id' => 0,
          'app_install_id' => 0,
          'page_id' => 0,
          'company_id' => $company_id,
          'subject' => (int) $reward_points,
          'object' => $challenge['hash'],
          'objecti' => (int) $company['credits'],
          'image' => $challenge['detail']['image'],
          'challenge_id' => get_mongo_id($challenge)
        );

        if(!$this->audit_lib->audit_add($audit_data)) {
          return $this->error('Audit add failed');
        }

        $this->load->model('challenge_model');
        $challenge_update_result = $this->challenge_model->update(array('_id' => new MongoId($challenge_id)), array(
          '$inc' => array('done_count' => $reward_points)
        ));
        if(!$challenge_update_result) {
          return $this->error('increment done count failed');
        }
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

  function latest_challenge_get() {
    $user_id = (int) $this->get('user_id');
    $token = $this->get('token');

    $this->load->model('user_mongo_model');
    if(!$user = $this->user_mongo_model->get_user($user_id)) {
      return $this->error('User Invalid');
    }

    if(!isset($user['challenge_progress'])) {
      return $this->success(NULL);
    }

    $latest_challenge_id = NULL;
    $latest_timestamp = 0;
    foreach ($user['challenge_progress'] as $challenge_id => $challenge_progress) {
      if(isset($challenge_progress['timestamp']) && $challenge_progress['timestamp'] >= $latest_timestamp) {
        $latest_timestamp = $challenge_progress['timestamp'];
        $latest_challenge_id = $challenge_id;
      }
    }

    if(!$latest_challenge_id) {
      return $this->success(NULL);
    }

    $this->load->library('challenge_lib');
    $this->load->model('company_model');

    $challenge = $this->challenge_lib->get_by_id($challenge_id);
    $challenge['_id'] = get_mongo_id($challenge);
    if($company = $this->company_model->get_company_profile_by_company_id($challenge['company_id'])) {
      $company_name = $company['company_name'];
      $company_id = $company['company_id'];
    }
    $data = compact('challenge', 'company_id', 'company_name', 'company');
    return $this->success($data);

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
    $locale = $model['locale'];

    $update = compact('user_first_name', 'user_last_name', 'user_email', 'user_phone', 'user_address');
    $update_mongo = compact('shipping', 'locale');

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
      'company_id' => $reward_item['company_id'],
      'image' => $reward_item['image']
    ))) {
      return $this->error('Unexpected error', 6);
    }

    // give coupon for the reward
    $coupon = array(
      'reward_item_id' => $reward_item_id,
      'reward_item' => $reward_item,
      'user_id' => $user_id,
      'company_id' => $reward_item['company_id'],
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

    $company_ids = array_map(function($coupon){
      return new MongoId($coupon['company_id']);
    }, $coupons);

    $companies = array();

    $this->load->model('challenge_model');
    $this->load->model('company_model');
    $challenges = $this->challenge_model->get(array('_id' => array('$in' => $challenge_ids)));

    foreach($coupons as $key => &$coupon) {
      foreach($challenges as $challenge) {
        $challenge_id = get_mongo_id($challenge);
        if($coupon['challenge_id'] === $challenge_id) {
          $challenge['_id'] = $challenge_id;
          $challenge['coupon_status'] = $coupon['confirmed'] ? 'confirmed' : 'pending';
          $challenge['coupon_id'] = get_mongo_id($coupon);
          $coupon['challenge'] = $challenge;
          break;
        }
      }
      if(!isset($coupon['challenge'])) {
        unset($coupons[$key]); // sometimes challenge does not exist even challenge_id is present
        continue;
      }

      # get company name
      $company_id = $coupon['company_id'];
      if(!isset($companies[$company_id])) {
        $companies[$company_id] = $this->company_model->get_company_profile_by_company_id($company_id);
      }
      $coupon['company_name'] = $companies[$company_id]['company_name'];
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
    // Version format : x.x.x
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

  function debug_reset_user_post() {
    // if ((ENVIRONMENT !== 'testing') && (ENVIRONMENT !== 'development')) { return $this->error(); }

    $this->load->model('user_mongo_model');
    $this->load->model('audit_model');
    $this->load->model('action_user_data_model');
    $challenge_action_ids = array(201,202,203,204,206,207);

    $unset = array(
      "challenge_completed" => TRUE,
      "challenge_redeeming" => TRUE,
      "daily_challenge" => TRUE,
      "daily_challenge_completed" => TRUE,
      "reward_items" => TRUE,
      "challenge_progress" => TRUE
    );

    $by_user_id = (int) $this->post('by');
    $token = $this->post('token');

    if(!$this->_check_token($by_user_id, $token)) {
      return $this->error('Token invalid');
    }

    $this->load->model('user_model');
    // Check if this user is developer
    if(!$user = $this->user_model->get_user_profile_by_user_id($by_user_id)) {
      return $this->error('No permission');
    }

    if(!issetor($user['user_is_developer'])) {
      return $this->error('No permission');
    }

    if(!$user_ids = explode(",", $this->post('user_ids'))) {
      return $this->error('No user id specified');
    }

    foreach($user_ids as $user_id) {
      $user_id = (int) $user_id;
      $this->user_mongo_model->update(array('user_id' => $user_id), array('$unset' => $unset));

      // remove actions & action_datas
      $criteria = array('user_id' => $user_id, 'action_id' => array('$in' => $challenge_action_ids));
      $this->audit_model->delete($criteria);
      $this->action_user_data_model->delete($criteria);
    }

    return $this->success('Reset success');
  }

  function mobile_config_get() {
    $this->load->config('mobile');
    $config = array();
    if($this->config->item('mobile_config')) {
      $config = $this->config->item('mobile_config');
    }
    return $this->success($config);
  }

  function claim_reward_post() {
    $user_id = (int) $this->post('user_id');
    $reward_item_id = $this->post('reward_item_id');

    if(!$user_id || !$reward_item_id) {
      return $this->error('Insufficient arguments');
    }

    $this->load->model('user_model');
    $this->load->library('reward_lib');
    $this->load->library('instant_reward_queue_lib');

    if(!$user = $this->user_model->get_user_profile_by_user_id($user_id)) {
      return $this->error('User not found');
    }

    if(!$reward_item = $this->reward_lib->get_reward_item($reward_item_id)) {
      return $this->error('Reward item not found');
    }

    // TODO - check that user really owns that reward

    // check if user already claimed this reward
    $latest_transaction = $this->instant_reward_queue_lib->get_one(compact('user_id', 'reward_item_id'));
    if($latest_transaction['status'] === 'waiting') {
      return $this->error('Reward claimed already');
    }

    // get reward machine id from reward
    if((!$reward_machine_id = issetor($reward_item['reward_machine_id'])) || !issetor($reward_item['is_instant_reward'])) {
      return $this->error('Invalid reward');
    }

    // add queue & return transaction id
    $time = time();
    $transaction = array(
      'user_id' => $user_id,
      'reward_item_id' => $reward_item_id,
      'reward_machine_id' => $reward_machine_id,
      'status' => 'waiting',
      'last_updated' => $time,
    );

    // confirm the coupon : latest coupon with user_id & reward_item_id
    $this->load->library('coupon_lib');
    $coupons = $this->coupon_lib->list_user_reward_coupon($user_id, $reward_item_id);
    $latest_coupon = reset($coupons);

    $this->coupon_lib->confirm_coupon(get_mongo_id($latest_coupon), 0);

    if($transaction_id = $this->instant_reward_queue_lib->add($transaction)) {
      return $this->success(array(
        'transaction_id' => $transaction_id,
        'reward_machine_id' => $reward_machine_id
      ));
    }

    return $this->error('Queue add failed');
  }

  function claim_simple_reward_post() {
    $user_id = (int) $this->post('user_id');
    $coupon_id = $this->post('coupon_id');

    if(!$user_id || !$coupon_id) {
      return $this->error('Insufficient arguments');
    }

    $this->load->model('user_model');
    if(!$user = $this->user_model->get_user_profile_by_user_id($user_id)) {
      return $this->error('User not found');
    }

    $this->load->library('coupon_lib');
    // approve
    if(!$coupon_confirm_result = $this->coupon_lib->confirm_coupon($coupon_id, 0)) {
      return $this->error('confirm point coupon failed');
    }

    return $this->success();
  }

  function reward_released_poll_get() {
    $user_id = $this->get('user_id');
    $reward_item_id = $this->get('reward_item_id');
    $transaction_id = $this->get('transaction_id');

    if(!$user_id || !$reward_item_id || !$transaction_id) {
      return $this->error('Insufficient arguments');
    }

    $this->load->model('user_model');
    $this->load->library('reward_lib');
    $this->load->library('instant_reward_queue_lib');

    if(!$user = $this->user_model->get_user_profile_by_user_id($user_id)) {
      return $this->error('User not found');
    }

    if(!$reward_item = $this->reward_lib->get_reward_item($reward_item_id)) {
      return $this->error('Reward item not found');
    }

    if(!$transaction = $this->instant_reward_queue_lib->get_by_id($transaction_id)) {
      return $this->error('Transaction not found');
    }

    // return success if it is released, otherwise not success
    if($transaction['status'] !== 'released') {
      return $this->error('Reward not released yet');
    }

    return $this->success('Reward released');
  }

  function instant_reward_machine_poll_get() {
    $reward_machine_id = $this->get('reward_machine_id');

    if(!$reward_machine_id) {
      return $this->error('Insufficient arguments');
    }

    $this->load->library('instant_reward_queue_lib');

    # get latest queue that have waiting status
    $query = array('reward_machine_id' => $reward_machine_id, 'status' => 'waiting');

    if(!$transaction = $this->instant_reward_queue_lib->get_one($query)) {
      return $this->success(array('release' => FALSE));
    }

    return $this->success(array(
      'release' => TRUE,
      'transaction_id' => get_mongo_id($transaction),
      'user_id' => $transaction['user_id']
    ));
  }

  function instant_reward_machine_status_post() {
    $reward_machine_id = $this->post('reward_machine_id');
    $transaction_id = $this->post('transaction_id');
    $status = $this->post('status');

    if(!$reward_machine_id || !$transaction_id || !$status) {
      return $this->error('Insufficient arguments');
    }

    $this->load->library('instant_reward_queue_lib');

    if(!$update_result = $this->instant_reward_queue_lib->update(array('_id' => new MongoId($transaction_id), 'reward_machine_id' => $reward_machine_id), array('status' => $status))) {
      return $this->error('Transaction update failed');
    }

    return $this->success(array('status' => $status));
  }

  function check_like_get(){
    $action_id = $this->get('action_id');
    $access_token = $this->get('access_token');

    if(!$action_id || !$access_token){
      $this->error('missing args');
    }else{


      $this->load->library('challenge_lib');

      $challenge = $this->challenge_lib->get_one(array(
        'criteria.action_data_id' => $action_id
      ));

      $action = NULL;

      foreach ($challenge['criteria'] as $key => $value) {
        if($value['action_data_id'] == $action_id){
          $action = $value;
          break;
        }
      }

      if($action && isset($action['facebook_id'])){
        $facebook_id = $action['facebook_id'];

        $fql = 'SELECT page_id, profile_section, type FROM page_fan WHERE uid = me() AND page_id = "' . $facebook_id . '"';

        $url = 'https://graph.facebook.com/fql?q=' . urlencode($fql) . '&access_token=' . $access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close ($ch);

        $response = json_decode($response, TRUE);
        if(isset($response) && isset($response['data']) && count($response['data']) > 0){
          $this->success(array(
            // 'url' => $url,
            // 'action_id' => $action_id,
            // 'access_token' => $access_token,
            // 'facebook_id' => $facebook_id,
            // 'response' => $response
            'liked' => true
          ));
        }else{
          $this->success(array(
            'liked' => false
          ));
        }
      }else{
        $this->error('action not found');
      }
    }
  }

  function parse_qr_get() {
    $url = $this->get('url');

    $this->load->library('challenge_lib');
    if($challenge = $this->challenge_lib->get_by_url($url)) {
      return $this->success(array(
        'challenge_id' => get_mongo_id($challenge),
        'type' => 'challenge', // not used at the moment
        'action' => 'view' // not used at the moment
      ));
    } else {
      return $this->error('This QR is not SocialHappen challenge');
    }
  }

  function _parse_feedback_action_user_data($action_user_data) {
    if(isset($action_user_data['user_score'])) {
      $action_user_data['user_score'] = (int) $action_user_data['user_score'];
    }
    return $action_user_data;
  }

  function share_post() {
    $user_id = (int) $this->post('user_id');
    $token = $this->post('token');
    $facebook_access_token = $this->post('facebook_access_token');
    $type = $this->post('type');
    $data = $this->post('data'); //array

    if(!$user_id || !$token || !$facebook_access_token || !$type || !$data) {
      return $this->error('Insufficient Arguments');
    }

    if(!$this->_check_token($user_id, $token)) {
      return $this->error('Token invalid');
    }

    if($type === 'challenge_done') {
      // TODO : share to facebook
      return $this->success();
    } else {
      return $this->error('Invalid type');
    }
  }
}
