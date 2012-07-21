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
      $result = $this->CI->challenge_model->update(array(
        '_id' => new MongoId($id)
        ), array(
          '$set' => array('hash' => strrev(sha1($id))
      )));
      if($result['updatedExisting']) {
        return $id;
      }
    }
    return FALSE;
  }

  function get($criteria, $limit = 100) {
    $result = $this->CI->challenge_model->get($criteria, $limit);
    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->challenge_model->getOne($criteria);
    return $result;
  }

  function get_by_hash($hash) {
    return $this->CI->challenge_model->getOne(array('hash' => $hash));
  }

  function update($criteria, $data) {
    if(!$challenge = $this->get_one($criteria)) {
      return FALSE;
    }

    $challenge_id = get_mongo_id($challenge);
    //$data['$set']['hash'] = strrev(sha1($challenge_id));

    unset($data['_id']);


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

    return $this->CI->challenge_model->update($criteria, $data);
  }

  function remove($criteria) {
    return $this->CI->challenge_model->delete($criteria);
  }

  function check_challenge($company_id = NULL, $user_id = NULL,
    $info = array()) {

    $company_id = (int) $company_id;
    $user_id = (int) $user_id;
    $result = array(
      'success' => TRUE,
      'completed' => array(),
      'in_progress' => array(),
      'completed_today' => array()
    );

    $this->CI->load->model('achievement_user_model','achievement_user');

    $user_achieved = $this->CI->achievement_user->list_user(
      array('user_id' => $user_id, 'company_id' => $company_id));

    $user_achieved_id_list = array();
    foreach ($user_achieved as $key => $achieved){
      if($achieved['achievement_id']['$ref'] === 'challenge'){
        $result['completed'][] = ''.$achieved['achievement_id']['$id'];
        //Mark completed today if is daily challenge
        if(isset($achieved['daily_challenge']) && $achieved['daily_challenge']) {
          $start = $achieved['daily_challenge']['start_date'];
          $end = $achieved['daily_challenge']['end_date'];
          $now = date('Ymd');
          if($now >= $start && $now <= $end) {
            $result['completed_today'][] = ''.$achieved['achievement_id']['$id'];
          } else {
            //Don't remove from candidate list, by skip adding into user_achieved_id_list
            continue;
          }

        }
      }

      $user_achieved_id_list[] = $achieved['achievement_id']['$id'];
    }

    //if user achieved something, exclude them out with $nin
    if(count($user_achieved_id_list) > 0 ){
      $candidate_achievement_criteria =
        array('_id' => array('$nin' => $user_achieved_id_list),
              'company_id' => $company_id);
    } else {
      $candidate_achievement_criteria = array('company_id' => $company_id);
                                              // 'info.enable' => TRUE);
    }

    $challenge_list =
      $this->CI->challenge_model->get($candidate_achievement_criteria);

    $this->CI->load->model('achievement_stat_model', 'achievement_stat');
    foreach ($challenge_list as $challenge) {
      $challenge_id = get_mongo_id($challenge);
      $match_all_criteria = TRUE;
      $is_in_progress = FALSE;
      $company_id = $challenge['company_id'];

      //If challenge is daily, we must check in audit as well (time-based criteria)
      if($is_daily_challenge = (isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && $days > 0)) {
        $this->CI->load->library('audit_lib');
        $match_all_criteria_today = TRUE;
        foreach($challenge['criteria'] as $criteria){
          $count_required = $criteria['count'];
          $query = $criteria['query'];
          $app_id = isset($query['app_id']) ? $query['app_id'] : 0;
          $action_id = $query['action_id'];
          $audit_criteria = compact('company_id', 'user_id');
          $start_date = date('Ymd', time() - (($days-1) * 60 * 60 * 24));
          $end_date = date('Ymd');

          $audit_count = $this->CI->audit_lib->count_audit_range(NULL, $app_id, $action_id, $audit_criteria, $start_date, $end_date);

          if($audit_count < $count_required) {
            $match_all_criteria_today = FALSE;
          }
        }

        if($match_all_criteria_today) {
          $result['completed_today'][] = $challenge_id;
        }
      }
      //Don't have to check stat if audit count reached count_required

        foreach($challenge['criteria'] as $criteria){
          $query = $criteria['query'];
          $count = $criteria['count'];

          $action_query = 'action.'.$query['action_id'].'.company.'.$company_id.'.count';
          if(!isset($query['app_id']) || (isset($criteria['is_platform_action']) && $criteria['is_platform_action'])) {
            //Query in progress challenge
            $stat_criteria = array(
              'app_id' => 0,
              'user_id' => $user_id,
              $action_query => array('$gt' => 0)
            );
          } else {
            //Query in progress challenge
            $stat_criteria = array(
              'app_id' => $query['app_id'],
              'user_id' => $user_id,
              $action_query => array('$gt' => 0)
            );
          }

          /**
           * @TODO: we can reduce one step here // Book
           */

          $matched_in_progress_achievement_stat =
            $this->CI->achievement_stat->list_stat($stat_criteria);
          if(!$matched_in_progress_achievement_stat) {
            $match_all_criteria = FALSE;
          } else {
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
        }


      if($match_all_criteria) {

        $result['completed'][] = $challenge_id;
        //This user completed this challenge
        $achieved_info = array(
          'company_id' => $company_id
        );

        if(isset($info['campaign_id'])){
          $achieved_info['campaign_id'] = $info['campaign_id'];
        }

        //Daily challenge flag
        if(isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && ($days > 0)
          && isset($match_all_criteria_today) && $match_all_criteria_today) {
          $start_date = date('Ymd');
          $end_date = date('Ymd', time() + (($days-1) * 60 * 60 * 24));
          $achieved_info['daily_challenge'] = array(
            'start_date' => $start_date,
            'end_date' => $end_date
          );
        }

        //Add achievement
        $ref = 'challenge';
        if(!$this->CI->achievement_user->add($user_id, $challenge_id,
          $query['app_id'] = 0, $info['app_install_id'] = 0, $achieved_info, $ref)){
          $result['success'] = FALSE;
        }

        //Add notification
        $this->CI->load->library('notification_lib');
        $message = 'You have completed a challenge : ' . $challenge['detail']['name'] . '.';
        $link = '#';
        $image = $challenge['detail']['image'];
        $this->CI->notification_lib->add($user_id, $message, $link, $image);

        //Add completed challenge into user mongo model
        $this->CI->load->model('user_mongo_model');
        $update_record = array(
          '$addToSet' => array(
            'challenge_redeeming' => $challenge_id,
            'challenge_completed' => $challenge_id
          )
        );
        $this->CI->user_mongo_model->update(array('user_id' => $user_id), $update_record);

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
        $audit_add_result = $this->CI->audit_lib->audit_add($audit);

        //Add company score
        $this->CI->load->library('audit_lib');
        $action = $this->CI->audit_lib->get_audit_action(0, $action_id);
        $company_score = $action['score'];
        $increment_info = array(
          'company_score' => $company_score,
          'action_id' => $action_id,
          'app_install_id' => 0
        );

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
            $coupon_id = $this->CI->coupon_lib->create_coupon($coupon);

            //If the reward is_points_reward : approve it immediately
            if(issetor($reward_item['is_points_reward'])) {
              $coupon_confirm_result = $this->CI->coupon_lib->confirm_coupon($coupon_id, 0);
            }
          }
        }

        //Increment company stat
        $this->CI->load->library('achievement_lib');
        $increment_page_score_result = $this->CI->achievement_lib->
          increment_achievement_stat($company_id, 0, $user_id, $increment_info, 1);

      } else if($is_in_progress) {
        $result['in_progress'][] = $challenge_id;
      }
    }
    return $result;
  }

  function get_challenge_progress($user_id = NULL, $challenge_id = NULL) {
    $this->CI->load->model('user_mongo_model');
    if((!$user = $this->CI->user_mongo_model->getOne(array('user_id' => $user_id))) ||
      (!$challenge = $this->get_one(array('_id' => new MongoId($challenge_id))))){
      return FALSE;
    }

    if(isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && ($days > 0)) {
      //If check daily challenge and user don't have today's temp record (user->challenge_daily_completed-> {[Ymd]: challenge_id})
        //Run check challenge and get completed today
      //else use temp record
      //TODO : and remove old temp records
      // if(!$daily_challenge_done = (isset($user['daily_challenge_completed'][date('Ymd')]) && in_array($challenge_id, $user['daily_challenge_completed'][date('Ymd')]))) {
        //Check daily challenge progress in audits
        $this->CI->load->library('audit_lib');
        $data = array();
        $all_done = TRUE;
        $company_id = $challenge['company_id'];
        $start_date = date('Ymd', time() - (($days-1) * 60 * 60 * 24));
        $end_date = date('Ymd');
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
          $start_date = date('Ymd');
          $end_date = date('Ymd', time() + (($days-1) * 60 * 60 * 24));
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
      $result = $this->CI->user_mongo_model->update(array('user_id' => $user_id), $update_record);
      return $result['updatedExisting'];
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
      'in_progress' => $all_challengers,
      'completed' => $completed_challengers
    );

    return $result;
  }

  function get_challengers_by_challenge_hash($challenge_hash) {
    if($challenge = $this->get_by_hash($challenge_hash)) {
      return $this->get_challengers(get_mongo_id($challenge));
    }

    return FALSE;
  }
}