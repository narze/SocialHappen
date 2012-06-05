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

    //$data = array_cast_int($data);

    $challenge_id = get_mongo_id($challenge);
    //$data['$set']['hash'] = strrev(sha1($challenge_id));

    unset($data['_id']);
    
    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

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
      'in_progress' => array()
    );
    
    $this->CI->load->model('achievement_user_model','achievement_user');
    
    $user_achieved = $this->CI->achievement_user->list_user(
      array('user_id' => $user_id, 'company_id' => $company_id));
    
    $user_achieved_id_list = array();
    foreach ($user_achieved as $achieved){
      $user_achieved_id_list[] = $achieved['achievement_id']['$id'];
      if($achieved['achievement_id']['$ref'] === 'challenge'){
        $result['completed'][] = ''.$achieved['achievement_id']['$id'];
      }
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
          'company_id' => $company_id
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
      if($matched_in_progress_achievement_stat = 
        $this->CI->achievement_stat->list_stat($stat_criteria)) {
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
}