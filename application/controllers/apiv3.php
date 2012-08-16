<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Metwara Narksook
 */
class Apiv3 extends CI_Controller {

  function __construct(){
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
  }

  function index(){
    return json_return(array('status' => 'OK'));
  }

  /**
   * get platform user
   *
   * @param user_id [required]
   */
  function user($user_id = NULL){
    /**
     * post
     */
    $logged_in = $this->socialhappen->is_logged_in();

    if (!$user_id && $logged_in){ // see current user's
      $user = $this->socialhappen->get_user();

    }else if($user_id){ // see specific user
      $this->load->model('user_model');
      $user = $this->user_model->get_user_profile_by_user_id($user_id);
    }

    if(isset($user)){
      $user_id = $user['user_id'];

      $this->load->model('achievement_stat_model');
      $user_stat = $this->achievement_stat_model->get($app_id = 0, $user_id);
      $user_score = issetor($user_stat['score'], 0);

      $this->load->model('user_companies_model');
      $company = $this->user_companies_model->get_user_companies_by_user_id($user_id, 0, 0);
      $user['user_score'] = $user_score;

      if($company){
        $user['companies'] = $company;
      }else{
        $user['companies'] = array();
      }

      return json_return($user);
    }else{
      echo '{}';
    }
  }

  function company($company_id = NULL){


    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $user_id = $user['user_id'];

    if($company_id){
      $this->load->model('company_model');
      $company = $this->company_model->get_company_profile_by_campaign_id($company_id);

      $this->load->library('achievement_lib');
      $company_stat = $this->achievement_lib->get_company_stat($company_id, $user_id);

      if($company){
        unset($company['company_username']);
        unset($company['company_password']);

        $company['company_score'] = $company_stat && $company_stat['company_score'] ? $company_stat['company_score'] : 0;

        return json_return($company);
      }else{
        echo '{}';
      }

    }else{
      echo '{}';
    }
  }

  /**
   * get user and play data (moved from player->static_get_user_data)
   *
   * @param user_id [required]
   */
  function user_play_data(){
    $this->load->model('app_model');
    if($user = $this->socialhappen->get_user()) {
      $this->load->library('apiv2_lib');
      $this->load->library('audit_lib');
      $this->load->model('achievement_stat_model');

      $user_id = $user['user_id'];
      $audits = $this->audit_lib->list_audit(array('user_id' => $user_id, 'action_id' => 103, 'app_id' => array('$gt' => 10000)));

      $unique_app_ids  = array();
      $played_apps = array();
      foreach($audits as $audit){
        if(!in_array($audit['app_id'], $unique_app_ids)){
          $unique_app_ids[] = $audit['app_id'];
          $played_apps[] = $this->app_model->get_app_by_app_id($audit['app_id']);
        }
      }

      $user_stat = $this->achievement_stat_model->get($app_id = 0, $user_id);
      $user_score = issetor($user_stat['score'], 0);
    }
    $available_apps = $this->app_model->get_apps_by_app_id_range(10001);
    $result = compact('user', 'available_apps', 'played_apps', 'user_score');
    return json_return($result);
  }

  /**
   * list activity of user
   */
  function activity($user_id = NULL){
    if(!$user_id){
      echo '[]';
    }else{
      $this->load->library('audit_lib');
      $activity = $this->audit_lib->list_audit(array('user_id' => (int)$user_id, 'app_id' => 0));
      return json_return($activity);
    }
  }

  /**
   * list activity of a company
   */
  function company_activities($company_id = NULL){
    if(!$company_id){
      echo '[]';
    }

    $this->load->library('audit_lib');
    $activity = $this->audit_lib->list_audit(array('company_id' => (int) $company_id, 'app_id' => 0));
    return json_return($activity);
  }

  /**
   * list activity of challenge or actions in a challenge
   * @param $challenge_hashes comma-separated values
   * Challenge hash -> objecti
   *
   */
  function challenge_activity(){

    header('Content-Type: application/json', TRUE);
    $challenge_hashes = $this->input->post('challenge_hashes');
    $activity_result = array();

    if($challenge_hashes){
      $challenge_hash_array = json_decode($challenge_hashes);
      $activity_search_result = array();

      $this->load->library('audit_lib');

      foreach($challenge_hash_array as $challenge_hash){
        $challenge_activity =  $this->audit_lib->list_audit(array('objecti' => trim($challenge_hash)));

        $activity_search_result = array_merge($activity_search_result, $challenge_activity);
      }

      //sort timestamp
      usort($activity_search_result, array("apiv3", '_timestamp_cmp'));
      $activity_search_result = array_reverse($activity_search_result);

      $activity_result = $activity_search_result;
    }

    return json_return($activity_result);
  }

  static function _timestamp_cmp($a, $b){
    return strcmp($a["timestamp"], $b["timestamp"]);
  }

  /**
   * list achievement of user
   */
  function achievement($user_id = NULL){
    if(!$user_id){
      echo '[]';
    }else{
      $this->load->library('achievement_lib');
      $achievement = $this->achievement_lib->list_user_achieved_by_user_id((int)$user_id);
      return json_return($achievement);
    }
  }

  /**
   * get notification count and list of user
   */
  function notifications() {
    $notifications = array(
      'count' => 0,
      'items' => array()
    );
    $user_id = $this->socialhappen->get_user_id();
    if($user_id) {
      $this->load->library('notification_lib');
      $notifications['items'] = $this->notification_lib->lists($user_id);
      $notifications['count'] = count($notifications['items']);
    }
    return json_return($notifications);
  }

  /**
   * list challenge
   */
  function challenges(){

    $active = $this->input->get('active', TRUE);

    $last_hash = $this->input->get('last_id', TRUE);

    $company_id = $this->input->get('company_id', TRUE);

    $this->load->library('challenge_lib');
    $this->load->library('action_data_lib');
    $limit = 30;

    if($last_hash){
      $challenge = $this->challenge_lib->get_one(array('hash' => $last_hash));
      if($challenge){

        $query = array(
          '_id' => array('$lt' => new MongoId($challenge['_id']['$id']))
        );

        if($company_id){
          $query['company_id'] = (int)$company_id;
        }

        if($active){
          $query['active'] = true;
        }

        $challenges = $this->challenge_lib->get($query, $limit);
      }else{
        $challenges = array();
      }
    }else{
      $query = array();

      if($company_id){
        $query['company_id'] = (int)$company_id;
      }

      if($active){
        $query['active'] = true;
      }

      $challenges = $this->challenge_lib->get($query, $limit);
    }

    // function convert_id($item){
    //   // $item['_id'] = '' . $item['_id'];
    //   unset($item['_id']);
    //   // unset($item['criteria']);
    //   return $item;
    // }

    // $challenges = array_map("convert_id", $challenges);
    return json_return($challenges);
  }

  function challenge_action() {
    $action_data_id = $this->input->get('action_data_id');

    $this->load->library('action_data_lib');
    $action_data = $this->action_data_lib->get_action_data($action_data_id);

    $id = $action_data['_id'];
    $id = $id['$id'];
    $action_data['_id'] = $id;

    return json_return($action_data);
  }

  /**
   * create/update challenge
   */
  function saveChallenge($challenge_hash = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $challenge = rawurldecode($this->input->post('model', TRUE));

    if(!isset($challenge) || $challenge == ''){
      $result = array('success' => FALSE, 'data' => 'no challenge data');
    }else{
      $this->load->library('challenge_lib');
      $this->load->library('action_data_lib');
      $this->load->library('bitly_lib');

      $challenge = json_decode($challenge, TRUE);
      $company_id = $challenge['company_id'];

      if(!is_array($challenge)){
        return json_return(array('success' => FALSE, 'data' =>'data error'));
        return FALSE;
      }

      //add/update action_data
      foreach($challenge['criteria'] as &$action_data_object){
        $action_data_create_flag = true;
        $action_data_attr = $action_data_object['action_data'];

        //check exist action_data
        if(isset($action_data_object['action_data']['_id'])){
          $mgid = $action_data_object['action_data']['_id'];
          $action_data = $this->action_data_lib->get_action_data($mgid['$id']);

          if($action_data){
            //update if exist
            $action_data_create_flag = FALSE;
            unset($action_data_attr['_id']);
            $update_action_data_result = $this->action_data_lib->update($mgid['$id'], $action_data_attr);

            if(!$update_action_data_result){
              return json_return(array('success' => FALSE, 'data' =>'update action_data failed '. print_r($action_data_attr['data'], true)));
              return FALSE;
            }
          }
        }

        if($action_data_create_flag){
          //update mongoID to challenge criteria for new added action data
          if($action_data_id = $this->action_data_lib->add_action_data($action_data_object['action_data']['action_id'], $action_data_attr['data'])){

            if($action_data_object['action_data']['action_id'] == 201){
              //short url for qr

              $qr_action_url = $this->action_data_lib->get_action_url($action_data_id);

              try{
                $bitly_response = $this->bitly_lib->bitly_v3_shorten($qr_action_url);
                $short_qr_action_url = $bitly_response['url'];
              }catch(Exception $ex){
                $short_qr_action_url = '';
              }

              $action_data_object['action_data']['short_url'] = $short_qr_action_url;

            }

            //re update challenge object
            $action_data_object['action_data_id'] = $action_data_id;
            $action_data_object['action_data']['_id'] = new MongoId($action_data_id);
            $action_data_object['action_data']['hash'] = strrev(sha1($action_data_id));

          }else{
            return json_return(array('success' => FALSE, 'data' =>'add action_data failed : ' . print_r($action_data_attr['data'], true)));
            return FALSE;
          }
        }
      }

      //create
      $challenge_create_flag = true;
      $challenge_update = null;
      $challenge_create = null;

      //Add-update rewards
      if(issetor($challenge['reward_items'])) {
        $this->load->model('reward_item_model');
        foreach($challenge['reward_items'] as &$reward_item) {
          if(isset($reward_item['_id'])) {
            //Reward exists : update
            $reward_item['company_id'] = $company_id;
            $reward_item_id = get_mongo_id($reward_item);
            if(!$reward_update_result = $this->reward_item_model->update($reward_item_id, $reward_item)) {
              return json_return(array('success' => FALSE, 'data' => 'Update reward failed'));
            }
          } else {
            //New reward : add new
            $reward_item['company_id'] = $company_id;
            if(!$reward_item_id = $this->reward_item_model->add_challenge_reward($reward_item)) {
              return json_return(array('success' => FALSE, 'data' => 'Add reward failed'));
            }

            //Set reward id
            $reward_item['_id'] = new MongoId($reward_item_id);
          }
        } unset($reward_item);
      }


      //Create challenge data without reward (to store in challenge's model)
      $challenge_data = $challenge;
      unset($challenge_data['reward_item']);

      //Try to update challenge
      if($challenge_hash){
        try {
          $asis_challenge = $this->challenge_lib->get_one(array('hash' => $challenge_hash));
          $challenge_id = get_mongo_id($asis_challenge);

          $challenge_update = $this->challenge_lib->update(array('_id' => new MongoId($challenge_id)), $challenge_data);
        } catch (Exception $ex){
          //update exception
          $challenge_create_flag = FALSE;
        }

        if($challenge_update)
            $challenge_create_flag = FALSE;
      }

      //Create challenge if cannot update
      if($challenge_create_flag){
        $challenge_create = $this->challenge_lib->add($challenge_data);

        if($challenge_create){
          $challenge_id = $challenge_create;

          //https://socialhappen.dyndns.org/socialhappen/player/challenge/6c700063f3a8188a57446fd910eeecc46ad4fc5e
          //create hash for short url
          $challenge_hash = strrev(sha1($challenge_id));
          $challenge_url = base_url().'player/challenge/'.$challenge_hash;

          $short_challenge_url = $challenge_url;
          try {
            $bitly_response = $this->bitly_lib->bitly_v3_shorten($challenge_url);
            if(isset($bitly_response['url'])) {
              $short_challenge_url = $bitly_response['url'];
            }
          } catch(Exception $ex) {

          }

          //add ".qrcode?s=<size>" follows the bitly's short_url for qr
          $update_challenge_data = array();
          $update_challenge_data['short_url'] = $short_challenge_url;

          $this->challenge_lib->update(array('_id' => new MongoId($challenge_id)), $update_challenge_data);

        }


      }

      if($challenge_create || $challenge_update){
        // $challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($challenge_id)));
        // $challenge['_id'] = $challenge['_id']['$id'];
        return json_return(array('success' => TRUE, 'data' => $challenge));
      }else{
        return json_return(array('success' => FALSE, 'data' =>'add/update challenge failed'));
      }
    }
  }

  /**
   * create/update reward
   */
  function saveReward($reward_id = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $reward = rawurldecode($this->input->post('model', TRUE));

    if(!isset($reward) || $reward == ''){
      return json_return(array('success' => FALSE, 'data' => 'no reward data'));
    }

    $this->load->model('reward_item_model');
    $reward = json_decode($reward, TRUE);

    if(!is_array($reward)){
      return json_return(array('success' => FALSE, 'data' =>'data error'));
      return FALSE;
    }

    if(isset($reward['_id'])) {
      //Reward exists : update
      $reward_item_id = $reward['_id'];
      if(!$reward_update_result = $this->reward_item_model->update($reward_item_id, $reward)) {
        return json_return(array('success' => FALSE, 'data' => 'Update reward failed'));
      }
    } else {
      //New reward : add new
      if(!$reward_item_id = $this->reward_item_model->add_redeem_reward($reward)) {
        return json_return(array('success' => FALSE, 'data' => 'Add reward failed'));
      }
    }

    return json_return(array('success' => TRUE, 'data' => $reward));

  }

  /**
   * Get reward_item
   */
  function reward_item($reward_item_id = NULL) {
    if(!$reward_item_id) {
      return json_return(array('success' => FALSE, 'data' => 'id not specified'));
    }

    $this->load->model('reward_item_model');
    if(!$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id)) {
      // return json_return(array('success' => TRUE, 'data' => NULL));
    }

    return json_return(array('success' => TRUE, 'data' => $reward_item));
  }

  /**
   * Get rewards (type challenge)
   */
  function get_rewards_for_challenge() {
    $this->load->model('reward_item_model');
    $criteria = array(
      'type' => 'challenge'
    );
    $rewards = $this->reward_item_model->get($criteria);

    //Use key as index
    $rewards_processed = array();
    foreach ($rewards as $value) {
      $rewards_processed[get_mongo_id($value)] = $value;
    }

    return json_return(array('data' => $rewards_processed));
  }

  /**
   * Get challenge's challenger
   */
  function get_challengers($challenge_hash, $limit = 5, $offset = 0) {
    $this->load->library('challenge_lib');
    $this->load->model('user_model');
    $challengers = $this->challenge_lib->get_challengers_by_challenge_hash($challenge_hash);

    $in_progress_count = count($challengers['in_progress']);
    $completed_count = count($challengers['completed']);
    foreach($challengers['in_progress'] as $key => &$challenger_in_progress){
      if($limit && ($key >= $offset + $limit) || ($key < $offset)) {
        unset($challengers['in_progress'][$key]);
      }
      $challenger_in_progress = $this->user_model->get_user_profile_by_user_id($challenger_in_progress['user_id']);
    }
    foreach($challengers['completed'] as $key => &$challenger_completed){
      if($limit && ($key >= $offset + $limit) || ($key < $offset)) {
        unset($challengers['completed'][$key]);
      }
      $challenger_completed = $this->user_model->get_user_profile_by_user_id($challenger_completed['user_id']);
    }
    $challengers['in_progress'] = array_values($challengers['in_progress']);
    $challengers['more_in_progress'] = $limit + $offset >= $in_progress_count;
    $challengers['completed'] = array_values($challengers['completed']);
    $challengers['more_completed'] = $limit + $offset >= $completed_count;
    return json_return($challengers);
  }

  /**
   * Get company list
   */
  function companies() {
    $this->load->model('company_model');
    if($company_id = $this->input->get('company_id')) {
      return json_return($this->company_model->get_company_profile_by_company_id($company_id));
    }

    return json_return($this->company_model->get_all());
  }

  /**
   * list redeem type rewards
   */
  function rewards() {
    if(!$company_id = $this->input->get('company_id')) {
      return json_return(array('success' => FALSE, 'data' => 'No company_id'));
    }

    $this->load->model('reward_item_model');
    $rewards = $this->reward_item_model->get(array(
      'company_id' => (int) $company_id,
      'type' => 'redeem'
    ), array('_id' => -1));
    $rewards = array_map(function($reward) {
      $reward['_id'] = get_mongo_id($reward);
      return $reward;
    }, $rewards);
    return json_return($rewards);
  }

  /**
   * list only published rewards
   */
  function viewRewards(){
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    if(!$company_id = $this->input->get('company_id')) {
      return json_return(array('success' => FALSE, 'data' => 'No company_id'));
    }

    $this->load->model('reward_item_model');

    $rewards = $this->reward_item_model->get(array(
      'status'=>'published',
      'type' => 'redeem',
      'company_id' => (int)$company_id
    ));

    $rewards = array_map(function($reward) {
      $reward['_id'] = get_mongo_id($reward);
      return $reward;
    }, $rewards);
    return json_return($rewards);
  }

  function coupon_detail(){
    $hash = $this->input->get('coupon_hash');
    $this->load->library('coupon_lib');
    $rerult = array();

    if(isset($hash) && $hash!='')
      $result = $this->coupon_lib->get_by_hash($hash);

    return json_return($result);

  }

  function coupons(){
    $company_id = $this->input->get('company_id');
    $challenge_id = $this->input->get('challenge_id');
    $user_id = $this->input->get('user_id');
    $filter = $this->input->get('filter');
    if($filter === 'confirmed') {
      $filter = TRUE;
    } else if($filter === 'not_confirmed') {
      $filter = FALSE;
    } else {
      $filter = NULL;
    }

    $this->load->library('coupon_lib');

    $result = array();

    if(isset($company_id) && $company_id){
      $result = $this->coupon_lib->list_company_coupon($company_id, $filter);
    } else if(isset($challenge_id) && $challenge_id){
      $result = $this->coupon_lib->list_challenge_coupon($challenge_id, $filter);
    } else if(isset($user_id) && $user_id){
      $result = $this->coupon_lib->list_user_coupon($user_id, $filter);
    }

    //Get user from results
    $this->load->model('user_model');
    foreach ($result as $key => &$coupon) {
      if(($filter !== NULL) && ($filter !== $coupon['confirmed'])) {
        unset($result[$key]);
        continue;
      }

      $coupon['user'] = $this->user_model->get_user_profile_by_user_id($coupon['user_id']);
    } unset($coupon);
    $result = array_values($result); //Sort again

    return json_return($result);
  }

  function confirm_coupon(){
    $coupon_id = $this->input->post('coupon_id');
    $this->load->library('coupon_lib');
    //TO-DO : get current user's id (admin_id) -> call coupon_lib->confirm_coupon
    $admin_id = $this->socialhappen->get_user_id();
    $return = array(
      'success' => FALSE,
      'coupon' => NULL
    );
    if($result = $this->coupon_lib->confirm_coupon($coupon_id, $admin_id)) {
      $return = array(
        'success' => TRUE,
        'coupon' => $result
      );
    }

    return json_return($return);
  }

  function rewardsRedeemed() {
    if(!$user_id = $this->socialhappen->get_user_id()) {
      return json_return(array('success' => FALSE));
    }

    //Get user rewards
    $this->load->model('user_mongo_model');
    $user = $this->user_mongo_model->get_user($user_id);
    $user_rewards = isset($user['reward_items']) && !empty($user['reward_items']) ? $user['reward_items'] : array();

    $this->load->library('coupon_lib');
    $company_id = $this->input->get('company_id');

    $user_coupon = array();
    if($user_id && $company_id){
      $user_coupon = $this->coupon_lib->list_user_company_coupon($user_id, $company_id);
    }else if($user_id){
      $user_coupon = $this->coupon_lib->list_user_coupon($user_id);
    }

    $user_coupon = array_map(function($coupon){
      if(!$coupon['confirmed']){
        return $coupon['reward_item_id'];
      }else{
        return NULL;
      }
    }, $user_coupon);

    $user_coupon = array_filter($user_coupon, function($coupon){
      return $coupon != NULL;
    });


    $user_rewards = array_merge($user_rewards, $user_coupon);
    return json_return(array('success' => TRUE, 'data' => $user_rewards));
  }

  function purchaseReward() {
    if(!$user_id = $this->socialhappen->get_user_id()) { return json_return(array('success' => FALSE, 'data' => 'Not logged in')); }
    if(!$reward_item_id = $this->input->post('reward_item_id')) { return json_return(array('success' => FALSE, 'data' => 'Unspecified reward item')); }
    if(!$company_id = $this->input->post('company_id')) { return json_return(array('success' => FALSE, 'data' => 'Unspecified company')); }

    $this->load->library('reward_lib');
    $purchase_coupon_result = $this->reward_lib->purchase_coupon($user_id, $reward_item_id, $company_id);
    return json_return($purchase_coupon_result);
  }

  /**
   * get all users' action data
   */
  function userActionData($user_id = NULL) {
    if(!$user_id){
      if(!$user_id = $this->socialhappen->get_user_id()) { return json_return(array('success' => FALSE)); }
    }

    $query = array_filter(array(
      'user_id' => $user_id
    ));

    $this->load->library('action_user_data_lib');
    $user_action_data = $this->action_user_data_lib->get_action_user_data_array($query);

    $user_action_data = array_map(function($data) {
      $data['_id'] = get_mongo_id($data);
      return $data;
    }, $user_action_data);

    //return json_return($user_action_data);

    //Get message
    $result = $user_action_data;
    $action_list = array();
    $this->load->library('audit_lib');
    $this->load->model('audit_model');
    for($i = 0;$i < count($result); $i++){
      $app_id = (int) 0;
      $action_id = $result[$i]['action_id'];
      if(empty($action_list[$app_id.'_'.$action_id])){
        $result_action = $this->audit_lib->get_audit_action($app_id, $action_id);
        if(isset($result_action)){
          $action_list[$app_id.'_'.$action_id] = $result_action;
        }
      }else{
        $result_action = $action_list[$app_id.'_'.$action_id];
      }

      if(isset($result_action['format_string']) && isset($result[$i]['audit_id'])){
        $result[$i]['message'] = $this->audit_lib->translate_format_string(
          $result_action['format_string'],
          $this->audit_model->getOne(array('_id' => new MongoId($result[$i]['audit_id']))),
          ($action_id <= 100)
        );
      }else{
        // $result[$i]['me$ssage'] = '[unknown audit]';
        // bad audit : should hide it
        log_message('error', 'audit message error '. print_r($result[$i], TRUE));
        unset($result[$i]);
      }
    }

    return json_return(array_reverse($result));
  }

  function getMyCards() {
    if(!$user_id = $this->socialhappen->get_user_id()) {
      return json_return(array('success' => FALSE, 'data' => 'No user session'));
    }

    $cards = array();

    // Get all companies that user played
    $this->load->model('achievement_stat_company_model');
    $criteria = array(
      'user_id' => (int) $user_id,
      'company_id' => array( '$gt' => 0 )
    );
    $companies_stat = $this->achievement_stat_company_model->list_stat($criteria);
    foreach($companies_stat as $company_stat) {
      $card = array();

      $company_id = $company_stat['company_id'];
      $card['company_id'] = $company_id;

      //Get company profile
      $this->load->model('company_model');
      $card['company'] = $this->company_model->get_company_profile_by_company_id($company_id);

      //Get company points
      $card['company_score'] = $company_stat['company_score'];

      //Get coupons (not rewards)
      $this->load->model('coupon_model');
      $card['coupons'] = $this->coupon_model->get(array('user_id' => (int) $user_id, 'company_id' => $company_id ));
      $card['coupons_count'] = count($card['coupons']);

      //@TODO - Get activities
      $card['activities'] = array();

      $cards[] = $card;
    }

    return json_return(array('success' => TRUE, 'data' => $cards));
  }

  function upload_image() {
    $file_input_name = $this->input->post('file-input-name');
    $old_image = $this->input->post('old-image');

    if(!$file_input_name) {
      if(!isset($_FILES) || !$_FILES) {
        return json_return(array('success' => FALSE, 'data' => 'Please select file to upload'));
      }
      // Get first key in $_FILES
      reset($_FILES);
      $file_input_name = key($_FILES);
    } else if(!isset($_FILES[$file_input_name])) {
      return json_return(array('success' => FALSE, 'data' => 'Please select file to upload'));
    }

    if(!$file_url = $this->socialhappen->replace_image($file_input_name, $old_image)) {
      return json_return(array('success' => FALSE, 'data' => 'Upload failed'));
    }

    return json_return(array('success' => TRUE, 'data' => $file_url));
  }

  /**
   * Get company users
   */
  function company_users($company_id = NULL) {
    $result = array('success' => FALSE);
    $this->load->model('company_model');
    if(!$company = $this->company_model->get_company_profile_by_company_id($company_id)){
      $result['data'] = 'Company not found';
    } else {
      $this->load->model('achievement_stat_company_model');
      $this->load->library('audit_lib');

      $stats = $this->achievement_stat_company_model->list_stat(
        array('company_id' => (int) $company_id)
      );
      $user_company_scores = array();
      $this->load->model('user_model');
      $join_company_action_id = $this->socialhappen->get_k('audit_action', 'User Join Challenge');
      foreach($stats as $user_stat){
        $user_id = $user_stat['user_id'];
        $company_score = $user_stat['company_score'];
        $audit = $this->audit_lib->get_first_audit(array(
          'user_id' => (int) $user_id,
          'company_id' => (int) $company_id,
          'action_id' => $join_company_action_id
        ));
        $joined_company_timestamp = $audit['timestamp'];

        $user_company_scores[] = array(
          'user_id' => $user_id,
          'company_score' => $company_score,
          'user_profile' => $this->user_model->get_user_profile_by_user_id($user_id),
          'user_stat' => $user_stat,
          'joined_company_timestamp' => $joined_company_timestamp
        );
      }
      //sorting
      $result['data'] = $user_company_scores;
      $result['count'] = count($stats);
      $result['success'] = TRUE;
    }
    return json_return($result);
  }

  /**
   * Get company users and sort by company score
   */
  function company_leaderboard($company_id = NULL) {
    $result = array('success' => FALSE);
    $this->load->model('company_model');
    if(!$company = $this->company_model->get_company_profile_by_company_id($company_id)){
      $result['data'] = 'Company not found';
    } else {
      $this->load->model('achievement_stat_company_model');
      $this->load->library('audit_lib');

      $stats = $this->achievement_stat_company_model->list_stat(
        array('company_id' => (int) $company_id)
      );
      $user_company_scores = $company_score_for_sorting = array();
      $this->load->model('user_model');
      $join_company_action_id = $this->socialhappen->get_k('audit_action', 'User Join Challenge');
      foreach($stats as $user_stat){
        $user_id = $user_stat['user_id'];
        $company_score = $user_stat['company_score'];
        $company_score_for_sorting[] = $company_score;
        $audit = $this->audit_lib->get_first_audit(array(
          'user_id' => (int) $user_id,
          'company_id' => (int) $company_id,
          'action_id' => $join_company_action_id
        ));
        $joined_company_timestamp = $audit['timestamp'];

        $user_company_scores[] = array(
          'user_id' => $user_id,
          'company_score' => $company_score,
          'user_profile' => $this->user_model->get_user_profile_by_user_id($user_id),
          'user_stat' => $user_stat,
          'joined_company_timestamp' => $joined_company_timestamp
        );
      }
      //sorting
      array_multisort($company_score_for_sorting, SORT_DESC, SORT_NUMERIC, $user_company_scores);
      $result['data'] = $user_company_scores;
      $result['count'] = count($stats);
      $result['success'] = TRUE;
    }
    return json_return($result);
  }

  /**
   * create company
   */
  function createCompany() {
    $result = array('success' => FALSE);

    // check user
    if(!$user = $this->socialhappen->get_user()) {
      $result['data'] = 'User not found';
      return json_return($result);
    }

    $user_id = $user['user_id'];

    // check if user already have company
    $this->load->model('user_companies_model');
    if(($companies = $this->user_companies_model->get_user_companies_by_user_id($user_id)) && (count($companies) > 0)) {
      $result['data'] = 'You already have a company';
      return json_return($result);
    }

    // add new company
    $this->load->model('company_model');
    $company = $this->input->post('company');
    $company['company_image'] = !isset($company['company_image']) ? base_url().'assets/images/default/company.png' : $company['company_image'];
    $company['creator_user_id'] = $user_id;
    if(!$company_id = $this->company_model->add_company($company)) {
      $result['data'] = 'Cannot add company, please contact administrator';
      return json_return($result);
    }

    // add company admin
    $user_company = array(
      'user_id' => $user_id,
      'company_id' => $company_id,
      'user_role' => 1
    );
    if(!$this->user_companies_model->add_user_company($user_company)) {
      $result['data'] = 'Cannot add company admin, please contact administrator';
      return json_return($result);
    }

    $result['success'] = TRUE;
    $result['data'] = array('company_id' => $company_id);
    return json_return($result);
  }
}

/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */