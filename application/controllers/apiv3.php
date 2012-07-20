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
    echo json_encode(array('status' => 'OK'));
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

      echo json_encode($user);
    }else{
      echo '{}';
    }
  }

  function company($company_id = NULL){


    if(!$user = $this->socialhappen->get_user()) {
      echo json_encode(array('success' => FALSE, 'data' => 'Not signed in'));
      return;
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

        echo json_encode($company);
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
    echo json_encode($result);
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
      echo json_encode($activity);
    }
  }

  /**
   * list activity of a company
   */
  function company_activities($company_id = NULL){
    if(!$company_id){
      echo '[]';
      return;
    }

    $this->load->library('audit_lib');
    $activity = $this->audit_lib->list_audit(array('company_id' => (int) $company_id, 'app_id' => 0));
    echo json_encode($activity);
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

    echo json_encode($activity_result);
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
      echo json_encode($achievement);
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
    echo json_encode($notifications);
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
    echo json_encode($challenges);
  }

  function challenge_action() {
    $action_data_id = $this->input->get('action_data_id');

    $this->load->library('action_data_lib');
    $action_data = $this->action_data_lib->get_action_data($action_data_id);

    $id = $action_data['_id'];
    $id = $id['$id'];
    $action_data['_id'] = $id;

    echo json_encode($action_data);
  }

  /**
   * create/update challenge
   */
  function saveChallenge($challenge_hash = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      echo json_encode(array('success' => FALSE, 'data' => 'Not signed in'));
      return;
    }

    $challenge = $this->input->post('model', TRUE);

    if(!isset($challenge) || $challenge == ''){
      $result = array('success' => FALSE, 'data' => 'no challenge data');
    }else{
      $this->load->library('challenge_lib');
      $this->load->library('action_data_lib');
      $this->load->library('bitly_lib');

      $challenge = json_decode($challenge, TRUE);
      $company_id = $challenge['company_id'];

      if(!is_array($challenge)){
        echo json_encode(array('success' => FALSE, 'data' =>'data error'));
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
              echo json_encode(array('success' => FALSE, 'data' =>'update action_data failed '. print_r($action_data_attr['data'], true)));
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
            echo json_encode(array('success' => FALSE, 'data' =>'add action_data failed : ' . print_r($action_data_attr['data'], true)));
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
        foreach($challenge['reward_items'] as $reward_item) {
          if(isset($reward_item['_id'])) {
            //Reward exists : update
            $reward_item['company_id'] = $company_id;
            $reward_item_id = $reward_item['_id'];
            if(!$reward_update_result = $this->reward_item_model->update($reward_item_id, $reward_item)) {
              echo json_encode(array('success' => FALSE, 'data' => 'Update reward failed'));
              return;
            }
          } else {
            //New reward : add new
            $reward_item['company_id'] = $company_id;
            if(!$reward_item_id = $this->reward_item_model->add_challenge_reward($reward_item)) {
              echo json_encode(array('success' => FALSE, 'data' => 'Add reward failed'));
              return;
            }
          }

          $challenge['reward_item_ids'][] = $reward_item_id;
        }
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

          try{
            $bitly_response = $this->bitly_lib->bitly_v3_shorten($challenge_url);
            $short_challenge_url = $bitly_response['url'];
          }catch(Exception $ex){
            $short_challenge_url = '';
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
        echo json_encode(array('success' => TRUE, 'data' => $challenge));
      }else{
        echo json_encode(array('success' => FALSE, 'data' =>'add/update challenge failed'));
      }
    }
  }

  /**
   * create/update reward
   */
  function saveReward($reward_id = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      echo json_encode(array('success' => FALSE, 'data' => 'Not signed in'));
      return;
    }

    $reward = $this->input->post('model', TRUE);

    if(!isset($reward) || $reward == ''){
      echo json_encode(array('success' => FALSE, 'data' => 'no reward data'));
      return;
    }

    $this->load->model('reward_item_model');
    $reward = json_decode($reward, TRUE);

    if(!is_array($reward)){
      echo json_encode(array('success' => FALSE, 'data' =>'data error'));
      return FALSE;
    }

    if(isset($reward['_id'])) {
      //Reward exists : update
      $reward_item_id = $reward['_id'];
      if(!$reward_update_result = $this->reward_item_model->update($reward_item_id, $reward)) {
        echo json_encode(array('success' => FALSE, 'data' => 'Update reward failed'));
        return;
      }
    } else {
      //New reward : add new
      if(!$reward_item_id = $this->reward_item_model->add_redeem_reward($reward)) {
        echo json_encode(array('success' => FALSE, 'data' => 'Add reward failed'));
        return;
      }
    }

    echo json_encode(array('success' => TRUE, 'data' => $reward));

  }

  /**
   * Get reward_item
   */
  function reward_item($reward_item_id = NULL) {
    if(!$reward_item_id) {
      echo json_encode(array('success' => FALSE, 'data' => 'id not specified'));
      return;
    }

    $this->load->model('reward_item_model');
    if(!$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id)) {
      // echo json_encode(array('success' => TRUE, 'data' => NULL));
      // return;
    }

    echo json_encode(array('success' => TRUE, 'data' => $reward_item));
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

    echo json_encode(array('data' => $rewards_processed));
  }

  /**
   * Get challenge's challenger
   */
  function get_challengers($challenge_hash, $limit = 5, $offset = 0) {
    $this->load->library('challenge_lib');
    $this->load->model('user_model');
    $challengers = $this->challenge_lib->get_challengers_by_challenge_hash($challenge_hash);

    $challengers['in_progress_count'] = count($challengers['in_progress']);
    $challengers['completed_count'] = count($challengers['completed']);
    foreach($challengers['in_progress'] as $key => &$challenger_in_progress){
      if($key >= $offset + $limit || $key < $offset) {
        unset($challengers['in_progress'][$key]);
      }
      $challenger_in_progress = $this->user_model->get_user_profile_by_user_id($challenger_in_progress['user_id']);
    }
    foreach($challengers['completed'] as $key => &$challenger_completed){
      if($key >= $offset + $limit || $key < $offset) {
        unset($challengers['completed'][$key]);
      }
      $challenger_completed = $this->user_model->get_user_profile_by_user_id($challenger_completed['user_id']);
    }
    echo json_encode($challengers);
  }

  /**
   * Get company list
   */
  function companies() {
    $this->load->model('company_model');
    if($company_id = $this->input->get('company_id')) {
      echo json_encode($this->company_model->get_company_profile_by_company_id($company_id));
      return;
    }

    echo json_encode($this->company_model->get_all());
  }

  function rewards(){

    if(!$company_id = $this->input->get('company_id')) {
      echo json_encode(array('success' => FALSE, 'data' => 'No company_id'));
      return;
    }

    $this->load->model('reward_item_model');
    $rewards = $this->reward_item_model->get(array('company_id' => (int)$company_id));
    $rewards = array_map(function($reward) {
      $reward['_id'] = get_mongo_id($reward);
      return $reward;
    }, $rewards);
    echo json_encode($rewards);
  }

  /**
   * list only published rewards
   */
  function viewRewards(){
    if(!$user = $this->socialhappen->get_user()) {
      echo json_encode(array('success' => FALSE, 'data' => 'Not signed in'));
      return;
    }

    if(!$company_id = $this->input->get('company_id')) {
      echo json_encode(array('success' => FALSE, 'data' => 'No company_id'));
      return;
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
    echo json_encode($rewards);
  }

  function coupon_detail(){
    $hash = $this->input->get('coupon_hash');
    $this->load->library('coupon_lib');
    $rerult = array();

    if(isset($hash) && $hash!='')
      $result = $this->coupon_lib->get_by_hash($hash);

    echo json_encode($result);

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

    echo json_encode($result);
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

    echo json_encode($return);
  }

  function rewardsRedeemed() {
    if(!$user_id = $this->socialhappen->get_user_id()) {
      echo json_encode(array('success' => FALSE));
      return;
    }

    //Get user rewards
    $this->load->model('user_mongo_model');
    $user = $this->user_mongo_model->get_user($user_id);
    $user_rewards = isset($user['reward_items']) && !empty($user['reward_items']) ? $user['reward_items'] : array();
    echo json_encode(array('success' => TRUE, 'data' => $user_rewards));
  }

  function purchaseReward() {
    if(!$user_id = $this->socialhappen->get_user_id()) { echo json_encode(array('success' => FALSE, 'data' => 'Not logged in')); return; }
    if(!$reward_item_id = $this->input->post('reward_item_id')) { echo json_encode(array('success' => FALSE, 'data' => 'Unspecified reward item')); return; }
    if(!$company_id = $this->input->post('company_id')) { echo json_encode(array('success' => FALSE, 'data' => 'Unspecified company')); return; }

    $this->load->library('reward_lib');
    $purchase_coupon_result = $this->reward_lib->purchase_coupon($user_id, $reward_item_id, $company_id);
    echo json_encode($purchase_coupon_result);
  }

  /**
   * get all users' action data
   */
  function userActionData() {
    if(!$user_id = $this->socialhappen->get_user_id()) { echo json_encode(array('success' => FALSE)); return; }
    $action_id = $this->input->get('action_id');

    $query = array_filter(array(
      'user_id' => $user_id,
      'action_id' => (int) $action_id
    ));

    $this->load->library('action_user_data_lib');
    $user_action_data = $this->action_user_data_lib->get_action_user_data_array($query);

    $user_action_data = array_map(function($data) {
      $data['_id'] = get_mongo_id($data);
      return $data;
    }, $user_action_data);

    //echo json_encode($user_action_data); return;

    //Get message
    $result = $user_action_data;
    $action_list = array();
    $this->load->library('audit_lib');
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

      if(isset($result_action['format_string'])){
        $result[$i]['message'] = $this->audit_lib->translate_format_string(
          $result_action['format_string'],
          $result[$i],
          ($action_id <= 100)
        );
      }else{
        $result[$i]['message'] = '[empty format audit]';
      }
    }

    echo json_encode($result);
  }

  function getMyCards() {
    if(!$user_id = $this->socialhappen->get_user_id()) {
      echo json_encode(array('success' => FALSE, 'data' => 'No user session')); return;
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

      //Get rewards got from this company & count
      // $this->load->model('user_mongo_model');
      // $user = $this->user_mongo_model->get_user($user_id);
      // $reward_items = $user['reward_items'];
      // $this->load->model('reward_item_model');
      // $reward_item_ids = array();
      // foreach($reward_items as $reward_item_id) {
      //   $reward_item_ids[] = new MongoId($reward_item_id);
      // }
      // $reward_item_criteria = array(
      //   '_id' => array( '$in' => $reward_item_ids )
      // );
      // $card['rewards'] = $this->reward_item_model->get($reward_item_criteria);
      // $card['rewards_count'] = count($card['rewards']);

      //@TODO - Get activities
      $card['activities'] = array();

      $cards[] = $card;
    }

    echo json_encode(array('success' => TRUE, 'data' => $cards));
    return;

  }
}

/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */