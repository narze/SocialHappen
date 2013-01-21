<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Challenge Class
 * @author Manassarn M.
 */
class Challenge_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('challenge_model');
  }

  function add($data) {
    if($id = $this->CI->challenge_model->add($data)) {
      if($result = $this->CI->challenge_model->update(array(
        '_id' => new MongoId($id)
        ), array(
          '$set' => array('hash' => strrev(sha1($id))
      )))) {
        $this->generate_locations($id);
        return $id;
      }
    }
    return FALSE;
  }

  function get($criteria, $limit = 100, $offset = 0, $sort = NULL) {
    $result = $this->CI->challenge_model->get($criteria, $limit, $offset, $sort);
    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->challenge_model->getOne($criteria);
    return $result;
  }

  function get_by_hash($hash) {
    return $this->CI->challenge_model->getOne(array('hash' => $hash));
  }

  function get_by_id($id) {
    return $this->CI->challenge_model->getOne(array('_id' => new MongoId($id)));
  }

  function update($criteria, $data) {
    if(!$challenge = $this->get_one($criteria)) {
      return FALSE;
    }

    $challenge_id = get_mongo_id($challenge);
    //$data['$set']['hash'] = strrev(sha1($challenge_id));

    unset($data['_id']);


    if(isset($data['branches_data'])) {
      unset($data['branches_data']);
    }

    //Cleanup unused reward_item_ids
    //@TODO - remove this after some time
    if(isset($data['reward_item_ids'])) {
      unset($data['reward_item_ids']);
    }

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    //Cleanup unused reward_item_ids
    //@TODO - remove this after some time
    $data['$unset'] = array('reward_item_ids' => TRUE);

    //Check time
    if(isset($data['$set']['end']) && $data['$set']['end'] < $challenge['start']) {
      return FALSE;
    }

    $result = $this->CI->challenge_model->update($criteria, $data);
    $this->generate_locations($challenge_id);
    return $result;
  }

  function remove($criteria) {
    return $this->CI->challenge_model->delete($criteria);
  }

  function check_challenge($company_id = NULL, $user_id = NULL,
    $info = array(), $time = NULL) {
    if(!$time) { $time = time(); }
    // Set input/output
    $company_id = (int) $company_id;
    $user_id = (int) $user_id;
    $result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );

    $this->CI->load->library('audit_lib');
    $this->CI->load->library('user_lib');
    $user = $this->CI->user_lib->get_user($user_id);

    //1. get incomplete challenge from both challenge and repeating challenge
    //1) normal challenge - incomplete
    $incomplete_challenges = isset($user['challenge']) ? $user['challenge'] : array();

    // normal challenge - completed
    $completed_challenges = isset($user['challenge_completed']) ? $user['challenge_completed'] : array();

    // 2) daily_challenge, must check the date range
    /* repeating challenge format :
      daily_challenge: {
        challengeId1 : [
          {
            start_date: startdate1,
            end_date: enddate1
          },
          {
            start_date: startdate2,
            end_date: enddate2
          }
        ],
        challengeId2 : ...
      }
    */
    // repeating challenge - incomplete (filter date)
    $user['daily_challenge'] = isset($user['daily_challenge']) ? $user['daily_challenge'] : array();
    foreach($user['daily_challenge'] as $challenge_id => $daily_challenges) {
      foreach($daily_challenges as $key => $daily_challenge) {
        if($daily_challenge['start_date'] > date('Ymd', $time) || $daily_challenge['end_date'] < date('Ymd', $time)) {
          unset($user['daily_challenge'][$challenge_id][$key]);
        }
      }
      //reset inner array
      $user['daily_challenge'][$challenge_id] = array_values($user['daily_challenge'][$challenge_id]);

      //remove challenge id if it's empty
      if(count($user['daily_challenge'][$challenge_id]) === 0) {
        unset($user['daily_challenge'][$challenge_id]);
      }
    }

    $incomplete_challenges = array_merge($incomplete_challenges, array_keys($user['daily_challenge']));

    // repeating challenge - completed
    $user['daily_challenge_completed'] = isset($user['daily_challenge_completed']) ? $user['daily_challenge_completed'] : array();
    $completed_challenges = array_merge($completed_challenges, array_keys($user['daily_challenge_completed']));

    // filter completed challenge id by company
    foreach($completed_challenges as $key => $completed_challenge_id) {
      $completed_challenge = $this->get_one(array('_id' => new MongoId($completed_challenge_id)));
      if($completed_challenge['company_id'] !== $company_id) {
        unset($completed_challenges[$key]);
      }
    }

    //array reset
    $result['completed'] = array_values($completed_challenges);

    //2. get challenge from incomplete challenge list, and filter by company
    foreach($incomplete_challenges as $key => $incomplete_challenge_id) {
      $incomplete_challenge = $this->get_one(array('_id' => new MongoId($incomplete_challenge_id)));

      if($incomplete_challenge['company_id'] !== $company_id) {
        unset($incomplete_challenges[$key]);
      } else {
        $incomplete_challenges[$key] = $incomplete_challenge;
      }

    }

    //array reset
    $incomplete_challenges = array_values($incomplete_challenges);

    foreach($incomplete_challenges as $challenge) {
      $challenge_id = get_mongo_id($challenge);
      $match_all_criteria = TRUE;
      $match_all_criteria_today = FALSE;
      $is_in_progress = FALSE;
      $company_id = $challenge['company_id'];

      //3.1 if non-repeat challenge : check audit in date range
      if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0)) {

        $match_all_criteria_today = TRUE;
        foreach($challenge['criteria'] as $criteria){
          $count_required = $criteria['count'];
          $query = $criteria['query'];
          $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
          $action_id = $query['action_id'];
          $audit_criteria = compact('company_id', 'user_id');
          $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
          $end_date = date('Ymd', $time);

          $audit_count = $this->CI->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

          if($audit_count < $count_required) {
            $match_all_criteria_today = FALSE;
          }
        }

        if($match_all_criteria_today) {
          $result['completed_today'][] = $challenge_id;
        }
      }

      //3.2 check achievement stat and action data
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
            $this->CI->achievement_stat->list_stat($stat_criteria);
          if(!$matched_achievement_stat) {
            $match_all_criteria = FALSE;
          }else if(isset($criteria['action_data_id'])){

            /**
             * check with action_user_data that user have done it or not
             */
            $this->CI->load->library('action_user_data_lib');
            $action_user_data = $this->CI->action_user_data_lib->
              get_action_user_data_by_action_data($criteria['action_data_id']);

            if(!$action_user_data){
              $match_all_criteria = FALSE;
            }
          }

      }

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
      if($match_all_criteria || $match_all_criteria_today) {
        //1
        $result['completed'][] = $challenge_id;

        $achieved_info = array(
          'company_id' => $company_id
        );

        if(isset($info['campaign_id'])){
          $achieved_info['campaign_id'] = $info['campaign_id'];
        }

        //2
        //Update user model
        $this->CI->load->model('user_mongo_model');
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

        if(!$update_user = $this->CI->user_mongo_model->update(array('user_id' => $user_id), $update_record)) {
          $result['success'] = FALSE;
          $result['data'] = 'add user failed';
        }

        //4
        //Add achievement, if duplicated it should not be added
        $ref = 'challenge';
        if(!$this->CI->achievement_user->add($user_id, $challenge_id,
          $query['app_id'] = 0, $info['app_install_id'] = 0, $achieved_info, $ref)){
          // $result['success'] = FALSE;
          // $result['data'] = 'add achievement failed';
        }

        //5
        //Add notification
        $this->CI->load->library('notification_lib');
        $message = 'You have completed a challenge : ' . $challenge['detail']['name'] . '.';
        $link = '#';
        $image = $challenge['detail']['image'];
        // if(!$this->CI->notification_lib->add($user_id, $message, $link, $image)) {
        //   $result['success'] = FALSE;
        //   $result['data'] = 'add notification failed';
        // }

        //6
        //Add audit
        $this->CI->load->library('audit_lib');
        $action_id = $this->CI->socialhappen->get_k('audit_action', 'User Complete Challenge');
        $audit = array(
          'app_id' => 0,
          'subject' => '',
          'action_id' => $action_id,
          'company_id' => $company_id,
          'objecti' => $challenge['hash'],
          'user_id' => $user_id,
          'image' => $image
        );
        if(!$audit_add_result = $this->CI->audit_lib->audit_add($audit)) {
          $result['success'] = FALSE;
          $result['data'] = 'add audit failed';
        }

        //7
        //Add company score
        $this->CI->load->library('audit_lib');
        $action = $this->CI->audit_lib->get_audit_action(0, $action_id);
        // $company_score = $action['score'];
        $company_score = 0; //Now don't give company score from audit
        $increment_info = array(
          'company_score' => $company_score,
          'action_id' => $action_id,
          'app_install_id' => 0
        );
        $this->CI->load->library('achievement_lib');
        if(!$increment_page_score_result = $this->CI->achievement_lib->
          increment_achievement_stat($company_id, 0, $user_id, $increment_info, 1)) {
          $result['success'] = FALSE;
          $result['data'] = 'increment stat failed';
        }

        //8
        //Give reward coupons
        if(issetor($challenge['reward_items'])) {
          $this->CI->load->library('coupon_lib');
          $this->CI->load->library('reward_lib');
          foreach($challenge['reward_items'] as $reward_item) {
            $reward_item_id = get_mongo_id($reward_item);
            $coupon = array(
              'reward_item' => $reward_item,
              'reward_item_id' => $reward_item_id,
              'user_id' => $user_id,
              'company_id' => $company_id,
              'challenge_id' => $challenge_id
            );
            if(!$coupon_id = $this->CI->coupon_lib->create_coupon($coupon)) {
              $result['success'] = FALSE;
              $result['data'] = 'add coupon failed';
            }

            //If the reward is_points_reward : approve it immediately
            if(issetor($reward_item['is_points_reward'])) {
              if(!$coupon_confirm_result = $this->CI->coupon_lib->confirm_coupon($coupon_id, 0)) {
                $result['success'] = FALSE;
                $result['data'] = 'confirm point coupon failed';
              }
            }
          }
        }
      } else {
        $result['in_progress'][] = $challenge_id;
      }
    }
    return $result;

    //OLD CODE

    // $this->CI->load->model('achievement_user_model','achievement_user');

    // // List achievement that user has
    // $user_achieved = $this->CI->achievement_user->list_user(
    //   array('user_id' => $user_id, 'company_id' => $company_id));

    // $user_achieved_id_list = array();
    // foreach ($user_achieved as $key => $achieved){

    //   // Include challenge type achievement in [completed]
    //   if($achieved['achievement_id']['$ref'] === 'challenge'){
    //     $result['completed'][] = ''.$achieved['achievement_id']['$id'];

    //     // if daily challenge and date is in range, also include in [daily_completed
    //     if(isset($achieved['daily_challenge']) && $achieved['daily_challenge']) {
    //       $start = $achieved['daily_challenge']['start_date'];
    //       $end = $achieved['daily_challenge']['end_date'];
    //       $now = date('Ymd', $time);
    //       if($now >= $start && $now <= $end) {
    //         $result['completed_today'][] = ''.$achieved['achievement_id']['$id'];
    //       } else {
    //         //Don't remove from candidate list, by skip adding into user_achieved_id_list
    //         continue;
    //       }

    //     }
    //   }

    //   $user_achieved_id_list[] = $achieved['achievement_id']['$id'];
    // }

    // $candidate_achievement_criteria = array('company_id' => $company_id);
    // //if user already achieved something, exclude them out with $nin
    // if(count($user_achieved_id_list) > 0 ){
    //   $candidate_achievement_criteriax['_id'] = array('$nin' => $user_achieved_id_list);
    // }

    // $challenge_list = $this->CI->challenge_model->get($candidate_achievement_criteria);

    // Check each unachieved challenge
    // $this->CI->load->model('achievement_stat_model', 'achievement_stat');
    // foreach ($challenge_list as $challenge) {
    //   $challenge_id = get_mongo_id($challenge);
    //   $match_all_criteria = TRUE;
    //   $is_in_progress = FALSE;
    //   $company_id = $challenge['company_id'];

    //   //If challenge is daily, we must check in audit as well (time-based criteria)
    //   if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0)) {
    //     $this->CI->load->library('audit_lib');
    //     $match_all_criteria_today = TRUE;
    //     foreach($challenge['criteria'] as $criteria){
    //       $count_required = $criteria['count'];
    //       $query = $criteria['query'];
    //       $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
    //       $action_id = $query['action_id'];
    //       $audit_criteria = compact('company_id', 'user_id');
    //       $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
    //       $end_date = date('Ymd', $time);

    //       $audit_count = $this->CI->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

    //       if($audit_count < $count_required) {
    //         $match_all_criteria_today = FALSE;
    //       }
    //     }

    //     if($match_all_criteria_today) {
    //       $result['completed_today'][] = $challenge_id;
    //     }
    //   }
    //   //Don't have to check stat if audit count reached count_required

    //     // check each criteria
    //     foreach($challenge['criteria'] as $criteria){
    //       $query = $criteria['query'];
    //       $count = $criteria['count'];

    //       // make stat criteria to query in progress challenges
    //       $action_query = 'action.'.$query['action_id'].'.company.'.$company_id.'.count';
    //       if(!isset($query['app_id']) || (isset($criteria['is_platform_action']) && $criteria['is_platform_action'])) {
    //         //Query in progress challenge
    //         $stat_criteria = array(
    //           'app_id' => 0,
    //           'user_id' => $user_id,
    //           $action_query => array('$gt' => 0)
    //         );
    //       } else {
    //         //Query in progress challenge
    //         $stat_criteria = array(
    //           'app_id' => $query['app_id'],
    //           'user_id' => $user_id,
    //           $action_query => array('$gt' => 0)
    //         );
    //       }

    //       /**
    //        * @TODO: we can reduce one step here // Book
    //        */

    //       $matched_in_progress_achievement_stat =
    //         $this->CI->achievement_stat->list_stat($stat_criteria);
    //       if(!$matched_in_progress_achievement_stat) {
    //         $match_all_criteria = FALSE;
    //       } else {

    //         //if it is in progress, check again with action count
    //         $is_in_progress = TRUE;
    //         $stat_criteria[$action_query] = array('$gte' => $count);
    //         $matched_achievement_stat =
    //           $this->CI->achievement_stat->list_stat($stat_criteria);
    //         if(!$matched_achievement_stat) {
    //           $match_all_criteria = FALSE;
    //         }else if(isset($criteria['action_data_id'])){

    //           /**
    //            * check with action_user_data that user have done it or not
    //            */
    //           $this->CI->load->library('action_user_data_lib');
    //           $action_user_data = $this->CI->action_user_data_lib->
    //             get_action_user_data_by_action_data($criteria['action_data_id']);

    //           if(!$action_user_data){
    //             $match_all_criteria = FALSE;
    //           }
    //         }
    //       }
    //     }


      // if($match_all_criteria) {

      //   $result['completed'][] = $challenge_id;
      //   //This user completed this challenge
      //   $achieved_info = array(
      //     'company_id' => $company_id
      //   );

      //   if(isset($info['campaign_id'])){
      //     $achieved_info['campaign_id'] = $info['campaign_id'];
      //   }

      //   //Daily challenge flag
      //   if(isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && ($days > 0)
      //     && isset($match_all_criteria_today) && $match_all_criteria_today) {
      //     $start_date = date('Ymd', $time);
      //     $end_date = date('Ymd', $time + (($days-1) * 60 * 60 * 24));
      //     $achieved_info['daily_challenge'] = array(
      //       'start_date' => $start_date,
      //       'end_date' => $end_date
      //     );
      //   }

      //   //Add achievement
      //   $ref = 'challenge';
      //   if(!$this->CI->achievement_user->add($user_id, $challenge_id,
      //     $query['app_id'] = 0, $info['app_install_id'] = 0, $achieved_info, $ref)){
      //     $result['success'] = FALSE;
      //     $result['data'] = 'add achievement failed';
      //   }

      //   //Add notification
      //   $this->CI->load->library('notification_lib');
      //   $message = 'You have completed a challenge : ' . $challenge['detail']['name'] . '.';
      //   $link = '#';
      //   $image = $challenge['detail']['image'];
      //   if(!$this->CI->notification_lib->add($user_id, $message, $link, $image)) {
      //     $result['success'] = FALSE;
      //     $result['data'] = 'add notification failed';
      //   }

      //   //Add completed challenge into user mongo model
      //   $this->CI->load->model('user_mongo_model');
      //   $update_record = array(
      //     '$addToSet' => array(
      //       'challenge_redeeming' => $challenge_id,
      //       'challenge_completed' => $challenge_id
      //     )
      //   );
      //   if(!$this->CI->user_mongo_model->update(array('user_id' => $user_id), $update_record)) {
      //     $result['success'] = FALSE;
      //     $result['data'] = 'add user failed';
      //   }

      //   //Add audit
      //   $this->CI->load->library('audit_lib');
      //   $action_id = $this->CI->socialhappen->get_k('audit_action', 'User Complete Challenge');
      //   $audit = array(
      //     'app_id' => 0,
      //     'subject' => '',
      //     'action_id' => $action_id,
      //     'company_id' => $company_id,
      //     'objecti' => $challenge['hash'],
      //     'user_id' => $user_id,
      //     'image' => $image
      //   );
      //   if(!$audit_add_result = $this->CI->audit_lib->audit_add($audit)) {
      //     $result['success'] = FALSE;
      //     $result['data'] = 'add audit failed';
      //   }

      //   //Add company score
      //   $this->CI->load->library('audit_lib');
      //   $action = $this->CI->audit_lib->get_audit_action(0, $action_id);
      //   // $company_score = $action['score'];
      //   $company_score = 0; //Now don't give company score from audit
      //   $increment_info = array(
      //     'company_score' => $company_score,
      //     'action_id' => $action_id,
      //     'app_install_id' => 0
      //   );
      //   $this->CI->load->library('achievement_lib');
      //   if(!$increment_page_score_result = $this->CI->achievement_lib->
      //     increment_achievement_stat($company_id, 0, $user_id, $increment_info, 1)) {
      //     $result['success'] = FALSE;
      //     $result['data'] = 'increment stat failed';
      //   }

      //   //Give reward coupons
      //   if(issetor($challenge['reward_items'])) {
      //     $this->CI->load->library('coupon_lib');
      //     $this->CI->load->library('reward_lib');
      //     foreach($challenge['reward_items'] as $reward_item) {
      //       $reward_item_id = get_mongo_id($reward_item);
      //       $coupon = array(
      //         'reward_item' => $reward_item,
      //         'reward_item_id' => $reward_item_id,
      //         'user_id' => $user_id,
      //         'company_id' => $company_id,
      //         'challenge_id' => $challenge_id
      //       );
      //       if(!$coupon_id = $this->CI->coupon_lib->create_coupon($coupon)) {
      //         $result['success'] = FALSE;
      //         $result['data'] = 'add coupon failed';
      //       }

      //       //If the reward is_points_reward : approve it immediately
      //       if(issetor($reward_item['is_points_reward'])) {
      //         if(!$coupon_confirm_result = $this->CI->coupon_lib->confirm_coupon($coupon_id, 0)) {
      //           $result['success'] = FALSE;
      //           $result['data'] = 'confirm point coupon failed';
      //         }
      //       }
      //     }
      //   }


      // } else if($is_in_progress) {
      //   $result['in_progress'][] = $challenge_id;
      // }
    // }
    // return $result;
  }

  function get_challenge_progress($user_id = NULL, $challenge_id = NULL, $time = NULL) {
    if(!$time) { $time = time(); }
    $this->CI->load->model('user_mongo_model');
    if((!$user = $this->CI->user_mongo_model->getOne(array('user_id' => $user_id))) ||
      (!$challenge = $this->get_one(array('_id' => new MongoId($challenge_id))))){
      return FALSE;
    }

    if(isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && ($days > 0)) {
      //If check daily challenge and user don't have today's temp record (user->challenge_daily_completed-> {[Ymd]: challenge_id})
        //Run check challenge and get completed today
      //else use temp record
      //TODO : and remove old temp records
      // if(!$daily_challenge_done = (isset($user['daily_challenge_completed'][date('Ymd', $time)]) && in_array($challenge_id, $user['daily_challenge_completed'][date('Ymd', $time)]))) {
        //Check daily challenge progress in audits
        $this->CI->load->library('audit_lib');
        $data = array();
        $all_done = TRUE;
        $company_id = $challenge['company_id'];
        $start_date = date('Ymd', $time - (($days-1) * 60 * 60 * 24));
        $end_date = date('Ymd', $time);
        foreach($challenge['criteria'] as $criteria){
          $count_required = $criteria['count'];
          $query = $criteria['query'];
          $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
          $action_id = $query['action_id'];
          $audit_criteria = compact('company_id', 'user_id');

          $audit_count = $this->CI->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

          $action['action_data'] = $criteria;
          $action['action_done'] = $audit_count >= $count_required;
          if(!$action['action_done']) {
            $all_done = FALSE;
          }
          $action['action_count'] = $audit_count;

          $data[] = $action;
        }
        if($all_done) {
          $start_date = date('Ymd', $time);
          $end_date = date('Ymd', $time + (($days-1) * 60 * 60 * 24));
          $update_result = $this->CI->user_mongo_model->update(array('user_id' => $user_id), array('$addToSet' => array('daily_challenge_completed.'.$challenge_id => array('start_date' => $start_date, 'end_date' => $end_date))));
        }
        return $data;
      // }


    } else {
      $criterias = $challenge['criteria'];
      $company_id = $challenge['company_id'];
      $data = array();
      foreach ($criterias as $criteria) {
        $query = $criteria['query'];
        $target_count = isset($criteria['count']) ? $criteria['count'] : 1;

        $action_query = 'action.'.$query['action_id'].'.company.'.$company_id.'.count';
        if(!isset($query['app_id']) || (isset($criteria['is_platform_action']) && $criteria['is_platform_action'])) {
          //Query in progress challenge
          $stat_criteria = array(
            'app_id' => 0,
            'user_id' => $user_id,
            $action_query => array('$gte' => 0)
          );
        } else {
          //Query in progress challenge
          $stat_criteria = array(
            'app_id' => $query['app_id'],
            'user_id' => $user_id,
            $action_query => array('$gte' => 0)
          );
        }
        $action = array();

        $this->CI->load->model('achievement_stat_model', 'achievement_stat');
        $matched_in_progress_achievement_stat =
          $this->CI->achievement_stat->list_stat($stat_criteria);

        if(isset($criteria['action_data_id'])){
          $this->CI->load->library('action_user_data_lib');
          $action_user_data = $this->CI->action_user_data_lib->
            get_action_user_data_by_action_data($criteria['action_data_id']);
        }else{
          $action_user_data = TRUE;
        }


        if($matched_in_progress_achievement_stat && $action_user_data) {
          $progress_count = $matched_in_progress_achievement_stat[0]['action'][$query['action_id']]['company'][$company_id]['count'];
          $action['action_data'] = $criteria;
          $action['action_done'] = $progress_count >= $target_count;
          $action['action_count'] = $action['action_done'] ? $target_count : $progress_count;
        } else {
          $action['action_data'] = $criteria;
          $action['action_done'] = FALSE;
          $action['action_count'] = 0;
        }
        $data[] = $action;
      }
      return $data;
    }
  }

  function redeem_challenge($user_id = NULL, $challenge_id = NULL) {
    $this->CI->load->model('user_mongo_model');
    if((!$user = $this->CI->user_mongo_model->getOne(array('user_id' => $user_id))) ||
      (!$challenge = $this->get_one(array('_id' => new MongoId($challenge_id))))){
      return FALSE;
    }

    if(isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming'])) {
      $update_record = array(
        '$pull' => array('challenge_redeeming' => $challenge_id)
      );
      return $this->CI->user_mongo_model->update(array('user_id' => $user_id), $update_record);
    } else {
      return FALSE;
    }
  }

  function get_distinct_company() {
    return $this->CI->challenge_model->get_distinct_company();
  }

  function get_challengers($challenge_id) {
    $challenge_criteria = array(
      'challenge' => $challenge_id
    );
    $challenge_complated_criteria = array(
      'challenge_completed' => $challenge_id
    );

    $this->CI->load->model('user_mongo_model');
    $all_challengers = $this->CI->user_mongo_model->get($challenge_criteria);
    $completed_challengers = $this->CI->user_mongo_model->get($challenge_complated_criteria);

    foreach($completed_challengers as $user) {
      unset($all_challengers[array_search($user['user_id'], $all_challengers)]);
    }

    $result = array(
      'in_progress' => array_values($all_challengers),
      'completed' => array_values($completed_challengers)
    );

    return $result;
  }

  function get_challengers_by_challenge_hash($challenge_hash) {
    if($challenge = $this->get_by_hash($challenge_hash)) {
      return $this->get_challengers(get_mongo_id($challenge));
    }

    return FALSE;
  }

  function get_nearest_challenges($location = array(), $max_dist = 0.000001, $limit = 20, $and_get_without_location_specified = FALSE) {
    if(!is_array($location) || count($location) !== 2) {
      return FALSE;
    }

    $query = array(
      'locations' => array(
        '$near' => array(floatval($location[0]), floatval($location[1]))
      )
    );


    if($max_dist > 0) {
      $query['locations']['$maxDistance'] = floatval($max_dist);
    }

    $challenges = $this->CI->challenge_model->get_sort($query, FALSE, $limit);

    if($and_get_without_location_specified) {
      $query = array(
        'verify_location' => FALSE
      );
      $challenge_at_0_0 = $this->CI->challenge_model->get_sort($query, FALSE, $limit);
      $challenges = array_merge($challenges, $challenge_at_0_0);

      $query = array(
        'locations' => array('$exists' => FALSE)
      );
      $challenges_without_location = $this->CI->challenge_model->get_sort($query, FALSE, $limit);
      $challenges = array_merge($challenges, $challenges_without_location);
    }
    $this->CI->load->model('company_model');

    $duplicatedHash = array();

    $uniqueChallenges = array();
    for ($i=0; $i < count($challenges); $i++) {
      if(!isset($duplicatedHash[$challenges[$i]['_id'] . ''])){
        $duplicatedHash[$challenges[$i]['_id'] . ''] = true;
        $uniqueChallenges[] = $challenges[$i];
      }
    }

    // get company profile to show in map, do we need company image ?
    for ($i=0; $i < count($uniqueChallenges); $i++) {
      $company = $this->CI->company_model->get_company_profile_by_company_id($uniqueChallenges[$i]['company_id']);
      // $challenges[$i]['company'] = $company;
    }

    return $uniqueChallenges;
  }

  function generate_locations($challenge_id){
    $challenge = $this->get_by_id($challenge_id);

    if(!$challenge){
      return FALSE;
    }

    // add location if custom_location flag is TRUE or not set
    if((!isset($challenge['custom_location']) && isset($challenge['location']))
      || isset($challenge['custom_location']) && $challenge['custom_location'] && isset($challenge['location'])
      ){
      $locations = array($challenge['location']);
    }else{
      $locations = array();
    }

    if(isset($challenge['custom_locations']) && count($challenge['custom_locations']) > 0){
      $locations = array_merge($locations, $challenge['custom_locations']);
    }

    $available_branches = array();

    if((!isset($challenge['all_branch']) || !$challenge['all_branch'])
      && isset($challenge['branches']) && count($challenge['branches']) > 0){
      $this->CI->load->library('branch_lib');

      foreach ($challenge['branches'] as $branch_id) {
        $branch = $this->CI->branch_lib->get_one(array('_id' => new MongoId($branch_id)));
        if($branch){
          $available_branches[] = $branch_id;
          $locations[] = $branch['location'];
        }
      }
    }else if(isset($challenge['all_branch']) && $challenge['all_branch']){
      $this->CI->load->library('branch_lib');
      $branches = $this->CI->branch_lib->get(array('company_id' => (int)$challenge['company_id']));
      if($branches && count($branches) > 0){
        foreach ($branches as $branch) {
          $locations[] = $branch['location'];
        }
      }
    }

    $data = array('$set' => array(
      'branches' => $available_branches
    ));

    if(count($locations) > 0){
      $data['$set']['locations'] = $locations;
    }else{
      $data['$unset'] = array(
        'locations' => TRUE
      );
    }

    // echo "<pre>";
    // var_dump($data);

    return $this->CI->challenge_model->update(array('_id' => new MongoId('' . $challenge_id)), $data, array('safe' => true));
  }

  function get_with_branches_data($criteria, $limit = 100) {
    $challenges = $this->CI->challenge_model->get($criteria, $limit);

    $challenges = $this->map_get_branches_data($challenges);

    return $challenges;
  }

  function map_get_branches_data($challenges){
    $this->CI->load->library('branch_lib');

    foreach ($challenges as $i => $challenge) {
      $branches_data = array();
      if(isset($challenge['branches']) && count($challenge['branches']) > 0){
        $ids = array_map(function($branch){
          return new MongoId($branch);
        }, $challenge['branches']);

        $criteria = array('_id' => array(
          '$in' => $ids
        ));

        $branches_data = $this->CI->branch_lib->get($criteria);
      }else if(isset($challenge['all_branch']) && $challenge['all_branch']){
        $branches_data = $this->CI->branch_lib->get(array('company_id' => (int)$challenge['company_id']));
      }

      $branches_data = array_map(function($branch){
        $branch['_id'] = '' . $branch['_id'];
        return $branch;
      }, $branches_data);

      $challenge['branches_data'] = $branches_data;

      $challenges[$i] = $challenge;
    }

    return $challenges;
  }

  function count($criteria = array()) {
    return $this->CI->challenge_model->count($criteria);
  }

  function get_challenge_name_like($name = NULL) {
    if(!$name) { return FALSE; }

    $criteria = array('detail.name' => array('$regex' => '\b'.$name, '$options' => 'i'));

    return $this->CI->challenge_model->get($criteria);
  }
}