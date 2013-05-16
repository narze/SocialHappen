<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Metwara Narksook
 */
class Apiv3 extends CI_Controller {

  function __construct(){
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json', TRUE);
    parent::__construct();
    if($this->uri->segment(1) === 'testmode') {
      $this->load->library('db_sync');
      $this->db_sync->use_test_db(TRUE);
    }

    $method_name = $this->router->fetch_method();
    if ( ! method_exists($this, $method_name)) {
      json_return(array('status' => false, 'error' => 'Unknown method.'), 404);
      die();
    }
  }

  function index(){
    return json_return(array('status' => 'OK'));
  }

  function branch(){
    $company_id = $this->input->get('company_id');

    $this->load->library('branch_lib');

    $result = array();

    if(isset($company_id) && $company_id){
      $criteria = array('company_id' => $company_id);
      $result = $this->branch_lib->get($criteria, 10000);
    }

    return json_return($result);
  }

  function branches() {
    $filter = $this->input->get('filter');
    $_GET['company_id'] = issetor($filter['company_id']);
    return $this->branch();
  }
  /**
   * create/update branch
   */
  function saveBranch($branch_id = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $branch = rawurldecode($this->input->post('model', TRUE));

    if(!isset($branch) || $branch == ''){
      return json_return(array('success' => FALSE, 'data' => 'no branch data'));
    }

    $this->load->library('branch_lib');
    $branch = json_decode($branch, TRUE);

    if(!is_array($branch)){
      return json_return(array('success' => FALSE, 'data' =>'data error'));
    }

    $branch['updated_timestamp'] = time();

    if(isset($branch['_id'])) {
      //Branch exists : update
      $branch_item_id = $branch['_id'];
      $criteria = array('_id' => new MongoId($branch_item_id));

      if(!$branch = $this->branch_lib->update($criteria, $branch)) {
        return json_return(array('success' => FALSE, 'data' => 'Update branch failed'));
      }
    } else {
      //New branch : add new
      if(!$branch = $this->branch_lib->add($branch)) {
        return json_return(array('success' => FALSE, 'data' => 'Add branch failed'));
      }
    }

    return json_return(array('success' => TRUE, 'data' => $branch));

  }

  function deleteBranch($branch_id = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $branch = rawurldecode($this->input->post('model', TRUE));

    if(!isset($branch) || $branch == ''){
      return json_return(array('success' => FALSE, 'data' => 'no branch data'));
    }

    $this->load->library('branch_lib');
    $branch = json_decode($branch, TRUE);

    if(!is_array($branch)){
      return json_return(array('success' => FALSE, 'data' =>'data error'));
    }

    $branch['updated_timestamp'] = time();

    if(isset($branch['_id'])) {
      //Branch exists : update
      $branch_item_id = $branch['_id'];
      $criteria = array('_id' => new MongoId($branch_item_id));

      if(!$branch_update_result = $this->branch_lib->remove($criteria)) {
        return json_return(array('success' => FALSE, 'data' => 'Delete branch failed'));
      }
    }

    return json_return(array('success' => TRUE, 'data' => $branch));

  }

  /**
   * Helper functions
   */

  function error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code, 'timestamp' => time()));
    return FALSE;
  }

  function success($data = array(), $code = 1, $options = array()) {
    echo json_encode(array_merge(array('success' => TRUE, 'data' => $data, 'code' => $code, 'timestamp' => time()), $options));
    return TRUE;
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
    if($this->socialhappen->detect_method() === 'post') {
      return $this->_updateCompany($company_id);
    }

    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $user_id = $user['user_id'];

    if($company_id){
      $this->load->model('company_model');
      $company = $this->company_model->get_company_profile_by_company_id($company_id);

      $this->load->library('achievement_lib');
      $company_stat = $this->achievement_lib->get_company_stat($company_id, $user_id);

      if($company){
        unset($company['company_username']);
        unset($company['company_password']);

        $company['company_score'] = $company_stat && $company_stat['company_score'] ? $company_stat['company_score'] : 0;
        // return json_return($company_id);
        return json_return($company);
      }else{
        echo '{}';
      }

    }else{
      echo '{}';
    }
  }

  function _updateCompany($company_id = NULL) {
    header('Content-Type: application/json', TRUE);
    $result = array('success' => FALSE);
    $company = json_decode($this->input->post('model'), TRUE);
    unset($company['company_score']);

    if($company_id != $company['company_id']) {
      $result['data'] = 'Bad input';
    }

    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }
    $user_id = $user['user_id'];

    //Check company owner
    $this->load->model('user_companies_model');
    if(!$this->user_companies_model->is_company_admin($user_id, $company_id)) {
      $result['data'] = 'No permission';
      return json_return($result);
    }

    //Update company
    $this->load->model('company_model');
    if(!$update_result = $this->company_model->update_company_profile_by_company_id($company_id, $company)) {
      $result['data'] = 'Update faied';
    }

    $result['success'] = TRUE;
    $result['data'] = $company;

    return json_return($result);
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

    $this->load->library('branch_lib');
    $this->load->library('challenge_lib');
    $this->load->library('action_data_lib');
    $limit = 30;

    if($last_hash){
      $challenge = $this->challenge_lib->get_one(array('hash' => $last_hash));
      if($challenge){

        $query = array(
          '_id' => array('$lt' => new MongoId(get_mongo_id($challenge)))
        );

        if($company_id){
          $query['company_id'] = (int)$company_id;
        }

        if($active){
          $query['active'] = true;
        }

        $challenges = $this->challenge_lib->get_with_branches_data($query, $limit);
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

      $challenges = $this->challenge_lib->get_with_branches_data($query, $limit);
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

  function challenge_list() {
    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('branch_lib');
    $this->load->library('challenge_lib');
    $this->load->library('action_data_lib');
    $this->load->model('sonar_box_model');
    $this->load->model('company_model');

    $query_options = array();
    if(isset($filter['name']) && strlen($filter['name'])) {
      $query_options['detail.name'] = array('$regex' => '\b'.$filter['name'], '$options' => 'i');
    }

    # find company_id
    if(isset($filter['company']) && strlen($filter['company'])) {
      $companies = $this->company_model->get_all(NULL, NULL, array('like' => array('company_name' => $filter['company'])));
      $company_ids = array_map(function($company) { return (int) $company['company_id']; }, $companies);
      $query_options['company_id'] = array('$in' => $company_ids);
    }

    if(isset($filter['sonar_data']) && strlen($filter['sonar_data'])) {
      $sonars = $this->sonar_box_model->get(array('data' => array('$regex' => $filter['sonar_data'])));
      $sonars = array_map(function($sonar) { return $sonar['data']; }, $sonars);
      $query_options['sonar_frequency'] = array('$in' => $sonars);
    }

    if(isset($filter['start_date_from']) && strlen($filter['start_date_from'])) {
      # find where start_date > start_date_from
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['start_date'])) {
        $query_options['start_date'] = array();
      }

      $query_options['start_date']['$gte'] = strtotime($filter['start_date_from']) + 0;
    }
    if(isset($filter['start_date_to']) && strlen($filter['start_date_to'])) {
      # find where start_date < start_date_to
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['start_date'])) {
        $query_options['start_date'] = array();
      }

      $query_options['start_date']['$lte'] = strtotime($filter['start_date_to']) + 60*60*24 - 1;
    }

    if(isset($filter['end_date_from']) && strlen($filter['end_date_from'])) {
      # find where end_date > end_date_from
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['end_date'])) {
        $query_options['end_date'] = array();
      }

      $query_options['end_date']['$gte'] = strtotime($filter['end_date_from']) + 0;
    }
    if(isset($filter['end_date_to']) && strlen($filter['end_date_to'])) {
      # find where end_date < end_date_to
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['end_date'])) {
        $query_options['end_date'] = array();
      }

      $query_options['end_date']['$lte'] = strtotime($filter['end_date_to']) + 60*60*24 - 1;
    }

    # sort & order
    if(in_array($sort, array('name', 'start_date', 'end_date', 'sonar_data'))) {
      if($sort === 'name') { $sort = 'detail.name'; }
      if($sort === 'sonar_data') { $sort = 'sonar_frequency'; }
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    $challenges = $this->challenge_lib->get($query_options, $limit, $offset, $sort);
    $challenges_all_count = $this->challenge_lib->count($query_options);

    foreach($challenges as &$challenge) {
      // Get company
      $challenge['company'] = $this->company_model->get_company_profile_by_company_id($challenge['company_id']);
    } unset($challenge);

    $options = array(
      'total' => $challenges_all_count,
      'total_pages' => ceil($challenges_all_count / $limit),
      'count' => count($challenges),
      'filter' => $query_options,
      'sort' => $sort
    );

    return $this->success($challenges, 1, $options);
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

      //Challenge location
      $challenge['location'][0] = floatval($challenge['location'][0]);
      $challenge['location'][1] = floatval($challenge['location'][1]);

      //Create challenge data without reward (to store in challenge's model)
      $challenge_data = $challenge;
      unset($challenge_data['reward_item']);

      //Try to update challenge
      if($challenge_hash){
        try {
          $asis_challenge = $this->challenge_lib->get_one(array('hash' => $challenge_hash));
          $challenge_id = get_mongo_id($asis_challenge);

          $challenge_update = $this->challenge_lib->update(array('_id' => new MongoId($challenge_id)), $challenge_data);

          // //sonar box data manipulation
          // if($challenge_data['sonar_frequency']) {
          //   $this->load->model('sonar_box_model');
          //   $this->sonar_box_model->upsert(array('challenge_id' => $challenge_id), array(
          //     'name' => $challenge_data['detail']['name'],
          //     'info' => array(),
          //     'challenge_id' => $challenge_id,
          //     'data' => $challenge_data['sonar_frequency']
          //   ));
          // } else if($asis_challenge['sonar_frequency'] && !$challenge_data['sonar_frequency']) {
          //   //remove sonar box data if removed
          //   $this->load->model('sonar_box_model');
          //   $this->sonar_box_model->delete(array(
          //     'challenge_id' => $challenge_id
          //   ));
          // }
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

          //create sonar data if defined
          if(issetor($challenge_data['sonar_frequency'])){
            $this->load->model('sonar_box_model');
            $this->sonar_box_model->add_sonar_box(array(
              'name' => $challenge_data['detail']['name'],
              'info' => array(),
              'challenge_id' => $challenge_id,
              'data' => $challenge_data['sonar_frequency']
            ));
          }

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

    $reward['updated_timestamp'] = time();

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
   * create/update offer
   */
  function saveOffer($reward_id = NULL){
    header('Content-Type: application/json', TRUE);
    if(!$user = $this->socialhappen->get_user()) {
      return json_return(array('success' => FALSE, 'data' => 'Not signed in'));
    }

    $offer = rawurldecode($this->input->post('model', TRUE));

    if(!isset($offer) || $offer == ''){
      return json_return(array('success' => FALSE, 'data' => 'no offer data'));
    }

    $this->load->model('reward_item_model');
    $offer = json_decode($offer, TRUE);

    if(!is_array($offer)){
      return json_return(array('success' => FALSE, 'data' =>'data error'));
      return FALSE;
    }

    $offer['updated_timestamp'] = time();

    if(isset($offer['_id'])) {
      //Offer exists : update
      $reward_item_id = $offer['_id'];
      $offer_update = array(
        '$set' => filter_array($offer, array('company_id', 'description', 'image', 'name', 'status', 'type', 'start_timestamp', 'end_timestamp', 'user_list', 'value', 'redeem_method', 'updated_timestamp', 'address', 'source'), TRUE)
      );
      if(!$offer_update_result = $this->reward_item_model->updateOne(
          array('_id' => new MongoId($reward_item_id))
        , $offer_update)) {
        return json_return(array('success' => FALSE, 'data' => 'Update reward failed'));
      }
    } else {
      //New reward : add new
      if(!$reward_item_id = $this->reward_item_model->add_offer_reward($offer)) {
        return json_return(array('success' => FALSE, 'data' => 'Add offer failed'));
      }
    }

    return json_return(array('success' => TRUE, 'data' => $offer));

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
    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->model('company_model');
    if($company_id = $this->input->get('company_id')) {
      return json_return($this->company_model->get_company_profile_by_company_id($company_id));
    }

    $query_options = array();

    $where = array();
    $like = array();

    if(isset($filter['name']) && strlen($filter['name'])) {
      # find in name
      $like['company_name'] = trim($filter['name']);
    }
    if(isset($filter['created_at_from']) && strlen($filter['created_at_from'])) {
      # find where company_register_date > created_at_from
      # TODO : Re-specify HH:MM:SS
      $where['company_register_date >='] = $filter['created_at_from'] . " 00:00:00";
    }
    if(isset($filter['created_at_to']) && strlen($filter['created_at_to'])) {
      # find where company_register_date < created_at_to
      # TODO : Re-specify HH:MM:SS
      $where['company_register_date <='] = $filter['created_at_to'] . " 23:59:59";
    }
    if(isset($filter['credits']) && strlen($filter['credits'])) {
      $where['credits'] = $filter['credits'];
    }

    if(count($where)) {
      $query_options['where'] = $where;
    }

    if(count($like)) {
      $query_options['like'] = $like;
    }

    # sort & order
    if(in_array($sort, array('company_name', 'credits', 'company_register_date'))) {
      $sort = array($sort => ($order === '-' ? 'desc' : 'asc'));
    } else {
      $sort = FALSE;
    }

    $companies = $this->company_model->get_all($limit, $offset, $query_options, $sort);
    $companies_all_count = $this->company_model->count_company_profile($query_options);

    $options = array(
      'total' => $companies_all_count,
      'total_pages' => ceil($companies_all_count / $limit),
      'count' => count($companies),
      'filter' => $filter,
      'sort' => $sort
    );
    return $this->success($companies, 1, $options);
  }

  function reward_list() {
    if ($model = json_decode($this->input->post('model'), TRUE)) {
      return $this->_reward_add($model);
    }

    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('reward_lib');
    $this->load->model('reward_item_model');

    $query_options = array();
    if(isset($filter['name']) && strlen($filter['name'])) {
      $query_options['name'] = array('$regex' => '\b'.$filter['name'], '$options' => 'i');
    }

    if(isset($filter['point_from']) && strlen($filter['point_from'])) {
      # find where point > point_from
      if(!isset($query_options['redeem.point'])) {
        $query_options['redeem.point'] = array();
      }

      $query_options['redeem.point']['$gte'] = (int) $filter['point_from'];
    }
    if(isset($filter['point_to']) && strlen($filter['point_to'])) {
      # find where point < point_to
      if(!isset($query_options['redeem.point'])) {
        $query_options['redeem.point'] = array();
      }

      $query_options['redeem.point']['$lte'] = (int) $filter['point_to'];
    }

    if(isset($filter['amount_from']) && strlen($filter['amount_from'])) {
      # find where amount > amount_from
      if(!isset($query_options['redeem.amount'])) {
        $query_options['redeem.amount'] = array();
      }

      $query_options['redeem.amount']['$gte'] = (int) $filter['amount_from'];
    }
    if(isset($filter['amount_to']) && strlen($filter['amount_to'])) {
      # find where amount < amount_to
      if(!isset($query_options['redeem.amount'])) {
        $query_options['redeem.amount'] = array();
      }

      $query_options['redeem.amount']['$lte'] = (int) $filter['amount_to'];
    }

    if(isset($filter['amount_redeemed_from']) && strlen($filter['amount_redeemed_from'])) {
      # find where amount_redeemed > amount_redeemed_from
      if(!isset($query_options['redeem.amount_redeemed'])) {
        $query_options['redeem.amount_redeemed'] = array();
      }

      $query_options['redeem.amount_redeemed']['$gte'] = (int) $filter['amount_redeemed_from'];
    }
    if(isset($filter['amount_redeemed_to']) && strlen($filter['amount_redeemed_to'])) {
      # find where amount_redeemed < amount_redeemed_to
      if(!isset($query_options['redeem.amount_redeemed'])) {
        $query_options['redeem.amount_redeemed'] = array();
      }

      $query_options['redeem.amount_redeemed']['$lte'] = (int) $filter['amount_redeemed_to'];
    }

    if(isset($filter['once']) && strlen($filter['once'])) {
      $query_options['redeem.once'] = !!((int) $filter['once']);
    }

    # sort & order
    if(in_array($sort, array('name', 'point', 'amount', 'amount_redeemed'))) { # TODO : sort once
      if($sort === 'point') { $sort = 'redeem.point'; }
      if($sort === 'amount') { $sort = 'redeem.amount'; }
      if($sort === 'amount_redeemed') { $sort = 'redeem.amount_redeemed'; }
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    //system rewards
    $query_options['company_id'] = 1;
    $query_options['type'] = 'redeem';

    $rewards = $this->reward_item_model->get_full($query_options, $limit, $offset, $sort);
    $rewards_all_count = $this->reward_item_model->count($query_options);

    foreach($rewards as &$reward) {

    } unset($reward);

    $options = array(
      'total' => $rewards_all_count,
      'total_pages' => ceil($rewards_all_count / $limit),
      'count' => count($rewards),
      'filter' => $query_options,
      'sort' => $sort
    );

    return $this->success($rewards, 1, $options);
  }

  function _reward_add($reward) {
    $this->load->model('reward_item_model');

    if(!strlen($reward['redeem']['point'])
    || !strlen($reward['redeem']['amount'])
    || !strlen($reward['name'])
      ) {
      return $this->error('Reward data invalid');
    }

    $reward['updated_timestamp'] = time();
    $reward['value'] = 0;
    $reward['company_id'] = 1;
    $reward['type'] = 'redeem';
    $reward['is_points_reward'] = TRUE;

    if(!$reward_item_id = $this->reward_item_model->add_redeem_reward($reward)) {
      return $this->error('Add reward failed');
    }

    return $this->success($reward);
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

  function devices() {
    if ($model = json_decode($this->input->post('model'), TRUE)) {
      return $this->_device_add($model);
    }

    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('challenge_lib');
    $this->load->library('branch_lib');
    $this->load->model('sonar_box_model');
    $this->load->model('company_model');

    $query_options = array();
    if(isset($filter['name']) && strlen($filter['name'])) {
      $query_options['name'] = array('$regex' => '\b'.$filter['name'], '$options' => 'i');
    }

    if(isset($filter['data']) && strlen($filter['data'])) {
      $query_options['data'] = array('$regex' => $filter['data']);
    }

    if(isset($filter['challenge']) && strlen($filter['challenge'])) {
      $challenges = $this->challenge_lib->get_challenge_name_like($filter['challenge']);
      $challenge_ids = array_map(function($challenge) { return get_mongo_id($challenge); }, $challenges);
      $query_options['challenge_id'] = array('$in' => $challenge_ids);
    }

    # sort & order
    if(in_array($sort, array('name', 'data'))) {
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    $devices = $this->sonar_box_model->get_all($query_options, $limit, $offset, $sort);
    $devices_all_count = $this->sonar_box_model->count($query_options);

    foreach($devices as &$device) {
      // Get company
      if(isset($device['company_id'])) {
        $device['company'] = $this->company_model->get_company_profile_by_company_id($device['company_id']);
      }

      // Get challenge
      if(isset($device['challenge_id'])) {
        $device['challenge'] = $this->challenge_lib->get_one(array('_id' => new MongoId($device['challenge_id'])));
      }

      // Get branch
      if(isset($device['branch_id'])) {
        $device['branch'] = $this->branch_lib->get_one(array('_id' => new MongoId($device['branch_id'])));
      }
    } unset($device);

    $options = array(
      'total' => $devices_all_count,
      'total_pages' => ceil($devices_all_count / $limit),
      'count' => count($devices),
      'filter' => $query_options,
      'sort' => $sort
    );

    return $this->success($devices, 1, $options);
  }

  function _device_add($device) {
    $this->load->library('sonar_box_lib');

    if(!strlen($device['id'])
    || !strlen($device['title'])
    || !strlen($device['data'])
    // || !strlen($device['company'])
    // || !strlen($device['branch'])
      ) {
      return $this->error('Device data invalid');
    }

    // $device['company_id'] = (int) $device['company'];
    // unset($device['company']);
    // $device['branch_id'] = $device['branch'];
    // unset($device['branch']);

    $device['status'] = 'pending';

    if(!$device_item_id = $this->sonar_box_lib->add($device)) {
      return $this->error('Add device failed');
    }

    // $this->load->library('branch_lib');
    // $this->load->model('company_model');
    // $device['company'] = $this->company_model->get_company_profile_by_company_id($device['company_id']);
    // $device['branch'] = $this->branch_lib->get_one(array('_id' => new MongoId($device['branch_id'])));

    return $this->success($device);
  }

  function sonar_codes() {
    if ($model = json_decode($this->input->post('model'), TRUE)) {
      return $this->_sonar_code_add($model);
    }

    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('challenge_lib');
    $this->load->library('branch_lib');
    $this->load->model('sonar_box_model');
    $this->load->model('company_model');

    $query_options = array();
    if(isset($filter['name']) && strlen($filter['name'])) {
      $query_options['name'] = array('$regex' => '\b'.$filter['name'], '$options' => 'i');
    }

    if(isset($filter['data']) && strlen($filter['data'])) {
      $query_options['data'] = array('$regex' => $filter['data']);
    }

    if(isset($filter['challenge']) && strlen($filter['challenge'])) {
      $challenges = $this->challenge_lib->get_challenge_name_like($filter['challenge']);
      $challenge_ids = array_map(function($challenge) { return get_mongo_id($challenge); }, $challenges);
      $query_options['challenge_id'] = array('$in' => $challenge_ids);
    }

    # sort & order
    if(in_array($sort, array('name', 'data'))) {
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    $sonar_codes = $this->sonar_box_model->get_all($query_options, $limit, $offset, $sort);
    $sonar_codes_all_count = $this->sonar_box_model->count($query_options);

    foreach($sonar_codes as &$sonar_code) {
      // Get company
      if(isset($sonar_code['company_id'])) {
        $sonar_code['company'] = $this->company_model->get_company_profile_by_company_id($sonar_code['company_id']);
      }

      // Get challenge
      if(isset($sonar_code['challenge_id'])) {
        $sonar_code['challenge'] = $this->challenge_lib->get_one(array('_id' => new MongoId($sonar_code['challenge_id'])));
      }

      // Get branch
      if(isset($sonar_code['branch_id'])) {
        $sonar_code['branch'] = $this->branch_lib->get_one(array('_id' => new MongoId($sonar_code['branch_id'])));
      }
    } unset($sonar_code);

    $options = array(
      'total' => $sonar_codes_all_count,
      'total_pages' => ceil($sonar_codes_all_count / $limit),
      'count' => count($sonar_codes),
      'filter' => $query_options,
      'sort' => $sort
    );

    return $this->success($sonar_codes, 1, $options);
  }

  function _sonar_code_add($sonar_code) {
    $this->load->library('sonar_box_lib');

    if(!strlen($device['id'])
    || !strlen($device['title'])
    || !strlen($device['data'])
    // || !strlen($device['company'])
    // || !strlen($device['branch'])
      ) {
      return $this->error('Sonar code data invalid');
    }

    // $device['company_id'] = (int) $device['company'];
    // unset($device['company']);
    // $device['branch_id'] = $device['branch'];
    // unset($device['branch']);

    $device['status'] = 'pending';

    if(!$device_item_id = $this->sonar_box_lib->add($device)) {
      return $this->error('Add device failed');
    }

    // $this->load->library('branch_lib');
    // $this->load->model('company_model');
    // $device['company'] = $this->company_model->get_company_profile_by_company_id($device['company_id']);
    // $device['branch'] = $this->branch_lib->get_one(array('_id' => new MongoId($device['branch_id'])));

    return $this->success($device);
  }

  /**
   * Get idle sonar boxes, and action's sonar box (if challenge_id, action_data_id is specified)
   */
  function idle_sonar_boxes() {
    $this->load->model('sonar_box_model');

    $query = array(
      '$or' => array(
        array('challenge_id' => NULL),
        array('action_data_id' => NULL)
      )
    );

    if(($challenge_id = $this->input->get('challenge_id')) && ($action_data_id = $this->input->get('action_data_id'))) {
      $query = array(
        '$or' => array(
          $query,
          array(
            '$and' => array(
              'challenge_id' => $challenge_id,
              'action_data_id' => $action_data_id
            )
          )
        )
      );
    }

    $idle_sonar_boxes = $this->sonar_box_model->get($query);

    $idle_sonar_boxes = array_map(function($idle_sonar_box) {
      $idle_sonar_box['_id'] = get_mongo_id($idle_sonar_box);
      return $idle_sonar_box;
    }, $idle_sonar_boxes);

    return $this->success($idle_sonar_boxes);
  }

  /**
   * list offer type rewards
   */
  function offers() {
    if(!$company_id = $this->input->get('company_id')) {
      return json_return(array('success' => FALSE, 'data' => 'No company_id'));
    }

    $this->load->model('reward_item_model');
    $offers = $this->reward_item_model->get(array(
      'company_id' => (int) $company_id,
      'type' => 'offer'
    ), array('_id' => -1));
    $offers = array_map(function($offer) {
      $offer['_id'] = get_mongo_id($offer);
      return $offer;
    }, $offers);
    return json_return($offers);
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

  function deliver_reward(){
    $coupon_id = $this->input->post('coupon_id');
    $this->load->library('coupon_lib');
    //TO-DO : get current user's id (admin_id) -> call coupon_lib->confirm_coupon
    $admin_id = $this->socialhappen->get_user_id();
    $return = array(
      'success' => FALSE
    );
    if($result = $this->coupon_lib->deliver_reward($coupon_id, $admin_id)) {
      $return = array(
        'success' => TRUE
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
   * Show company balance (credits related action)
   */
  function company_balance($company_id = NULL) {
    $this->load->model('company_model');
    if(!$company = $this->company_model->get_company_profile_by_company_id($company_id)){
      return $this->error('Company not found');
    }

    $this->load->library('audit_lib');
    $credit_use = $this->socialhappen->get_k('audit_action', 'Credit Use From Challenge');
    $credit_add = $this->socialhappen->get_k('audit_action', 'Add Credits');

    $credits_related_action_ids = array(
      $credit_use, $credit_add
    );

    $audits = $this->audit_lib->list_audit(array('company_id' => $company['company_id'], 'action_id' => array('$in' => $credits_related_action_ids)), 0);

    //Process actions
    foreach($audits as $key => &$audit) {
      if(($audit['action_id'] === $credit_use) && is_numeric($audit['subject']) && is_numeric($audit['objecti'])) {
        $audit['credit_added'] = - $audit['subject'];
        $audit['credit_total'] = $audit['objecti'];
      } else if(($audit['action_id'] === $credit_add) && is_numeric($audit['object']) && is_numeric($audit['objecti'])) {
        $audit['credit_added'] = $audit['object'];
        $audit['credit_total'] = $audit['objecti'];
      } else {
        unset($audits[$key]);
      }
    } unset($audit);

    $audits = array_values($audits);

    return json_return($audits);
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

  /**
   * Add push notification to every devices
   */
  function add_push_notification() {
    $result = array('success' => FALSE);

    $message = $this->input->post('message');
    $this->load->model('user_token_model');

    if(!$this->user_token_model->add_push_message(array(), $message)) {
      $result['data'] = 'Push notification failed';
      return json_return($result);
    }

    $result['success'] = TRUE;
    $result['data'] = array('Add push notification for all devices successfully');
    return json_return($result);
  }

  function push_notification_to_device() {
    $result = array('success' => FALSE);

    $this->load->model('user_token_model');
    $array = $this->user_token_model->pull_active_user_message();
    if(!isset($array['user']) || !isset($array['message'])) {
      $result['data'] = array('No queued messages');
      return json_return($result);
    }

    $device_token = $array['user']['device_token'];
    $device_name = $array['user']['device_name'];
    $device = $array['user']['device'];
    $message = $array['message'];

    $this->load->model('user_model');
    $user = $this->user_model->get_user_profile_by_user_id($array['user']['user_id']);
    if($device === 'ios') {

      $this->load->library('apn');
      $this->apn->payloadMethod = 'enhance'; // you can turn on this method for debuggin purpose
      $this->apn->connectToPush();

      // adding custom variables to the notification
      // $this->apn->setData(array( 'someKey' => true ));

      $send_result = $this->apn->sendMessage($device_token, $message, /*badge*/ 0, /*sound*/ 'default'  );

      if($send_result) {
        $result['success'] = TRUE;
        $result['data'] = 'Pushed "'.$message.'" to "'. "{$user['user_first_name']} {$user['user_last_name']} ({$device_name} [{$device_token}])";
      }
      else {
        log_message('error',$this->apn->error);
        $result['data'] = 'Fail pushing "'.$message.'" to user "'. "{$user['user_first_name']} {$user['user_last_name']}\" ({$device_token})";
      }

      $this->apn->disconnectPush();


    } else {
      //not supported for now
      $result['data'] = 'Device not supported';

    }

    return json_return($result);

  }

  /**
   * Get safe (unused) data for sonar box
   */
  function get_sonar_box_data() {
    $this->load->model('sonar_box_model');
    $sonar_box_data = $this->sonar_box_model->generate_safe_sonar_data();
    return json_return(array(
      'success' => TRUE,
      'data' => $sonar_box_data
    ));
  }

  /**
   * APIs below are for new backend
   */

  function users() {
    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->model('user_model');
    $this->load->model('user_mongo_model');
    $this->load->model('user_token_model');

    $query_options = array();

    $where = array();
    $like = array();

    if(isset($filter['first_name']) && strlen($filter['first_name'])) {
      # find in first_name
      $like['user_first_name'] = trim($filter['first_name']);
    }
    if(isset($filter['last_name']) && strlen($filter['last_name'])) {
      # find in last_name
      $like['user_last_name'] = trim($filter['last_name']);
    }
    if(isset($filter['signup_date_from']) && strlen($filter['signup_date_from'])) {
      # find where signup_date > signup_date_from
      # TODO : Re-specify HH:MM:SS
      $where['user_register_date >='] = $filter['signup_date_from'] . " 00:00:00";
    }
    if(isset($filter['signup_date_to']) && strlen($filter['signup_date_to'])) {
      # find where signup_date < signup_date_to
      # TODO : Re-specify HH:MM:SS
      $where['user_register_date <='] = $filter['signup_date_to'] . " 23:59:59";
    }
    if(isset($filter['last_seen_from']) && strlen($filter['last_seen_from'])) {
      # find where last_seen > last_seen_from
      # TODO : Re-specify HH:MM:SS
      $where['user_last_seen >='] = $filter['last_seen_from'] . " 00:00:00";
    }
    if(isset($filter['last_seen_to']) && strlen($filter['last_seen_to'])) {
      # find where last_seen < last_seen_to
      # TODO : Re-specify HH:MM:SS
      $where['user_last_seen <='] = $filter['last_seen_to'] . " 23:59:59";
    }

    if(count($where)) {
      $query_options['where'] = $where;
    }

    if(count($like)) {
      $query_options['like'] = $like;
    }

    # find in mongo and get ids
    $find_in_mongo = FALSE;
    if(isset($filter['platforms']) && strlen($filter['platforms'])) {
      # find each platform
      $find_in_mongo = TRUE;
      $users_found = $this->user_token_model->get(array('device' => array('$in' => $filter['platforms'])));
      $user_ids_from_platforms = array_map(function($user) { return (int) $user['user_id']; }, $users_found);
    }
    if(isset($filter['points']) && strlen($filter['points'])) {
      # find if points match
      $find_in_mongo = TRUE;
      $users_found = $this->user_mongo_model->get(array('points' => (int) $filter['points']));
      $user_ids_from_points = array_map(function($user) { return (int) $user['user_id']; }, $users_found);
    }

    // Intersect results from mongo (if isset)
    if(isset($user_ids_from_platforms)) {
      $user_ids = $user_ids_from_platforms;
    }
    if(isset($user_ids_from_points)) {
      if(isset($user_ids)) {
        $user_ids = array_intersect($user_ids, $user_ids_from_points);
      } else {
        $user_ids = $user_ids_from_points;
      }
    }

    if(isset($user_ids)) {
      $user_ids = array_unique($user_ids);
      $query_options['where_in'] = array('user_id' => $user_ids);
    }

    # sort & order
    if(in_array($sort, array('user_first_name', 'user_register_date', 'user_last_seen'))) {
      $sort = array($sort => ($order === '-' ? 'desc' : 'asc'));
    } else if(in_array($sort, array('points'))) {
      $sort = FALSE;
      $sort_by_points = TRUE;
    } else {
      $sort = FALSE;
    }

    if ($find_in_mongo && empty($user_ids)) {
      // not found in mongo & intersect results with user_model = empty
      $users = array();
      $users_all_count = 0;
    } else {
      $users = $this->user_model->get_all_user_profile($limit, $offset, $query_options, $sort);
      $users_all_count = $this->user_model->count_users($query_options);
    }

    foreach($users as &$user) {
      // Get points
      $user_mongo = $this->user_mongo_model->get_user($user['user_id']);
      $user['user_points'] = issetor($user_mongo['points'], 0);

      // Get platforms
      $tokens = $this->user_token_model->get(array('user_id' => $user['user_id']));
      $user['user_platforms'] = array();
      foreach($tokens as $token) {
        $user['user_platforms'][] = $token['device'];
      }

    } unset($user);

    if(isset($sort_by_points)) {
      usort($users, function($a, $b) { return $a['user_points'] - $b['user_points']; });
      if($order === '-') {
        $users = array_reverse($users);
      }
    }

    $options = array(
      'total' => $users_all_count,
      'total_pages' => ceil($users_all_count / $limit),
      'count' => count($users),
      'filter' => $filter,
      'sort' => $sort
    );

    return $this->success($users, 1, $options);
  }

  function activities() {
    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('audit_lib');
    $this->load->library('branch_lib');
    $this->load->library('challenge_lib');
    $this->load->model('user_model');
    $this->load->model('company_model');
    $this->load->model('challenge_model');
    $this->load->model('audit_model');
    $this->load->model('branch_model');

    $query_options = array();

    # find user_id
    $user_like = array();
    if(isset($filter['first_name']) && strlen($filter['first_name'])) {
      # find in first_name
      $user_like['user_first_name'] = $filter['first_name'];
    }
    if(isset($filter['last_name']) && strlen($filter['last_name'])) {
      # find in last_name
      $user_like['user_last_name'] = $filter['last_name'];
    }
    if($user_like) {
      $users = $this->user_model->get_all_user_profile(NULL, NULL, array('like' => $user_like));
      $user_ids = array_map(function($user) { return (int) $user['user_id']; }, $users);
      $query_options['user_id'] = array('$in' => $user_ids);
    }

    # find company_id
    if(isset($filter['company']) && strlen($filter['company'])) {
      $companies = $this->company_model->get_all(NULL, NULL, array('like' => array('company_name' => $filter['company'])));
      $company_ids = array_map(function($company) { return (int) $company['company_id']; }, $companies);
      $query_options['company_id'] = array('$in' => $company_ids);
    }

    # find in mongo and get ids
    if(isset($filter['action']) && $filter['action']) {
      $action_ids = array();
      foreach ($filter['action'] as $action) {
        $action_id = $this->socialhappen->get_k('audit_action', $action);
        $action_ids[] = $action_id;
      }

      $query_options['action_id'] = array('$in' => $action_ids);
    }
    if(isset($filter['date_from']) && strlen($filter['date_from'])) {
      # find where date > date_from
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['timestamp'])) {
        $query_options['timestamp'] = array();
      }

      $query_options['timestamp']['$gte'] = strtotime($filter['date_from']) + 0;
    }
    if(isset($filter['date_to']) && strlen($filter['date_to'])) {
      # find where date < date_to
      # TODO : Re-specify HH:MM:SS
      if(!isset($query_options['timestamp'])) {
        $query_options['timestamp'] = array();
      }

      $query_options['timestamp']['$lte'] = strtotime($filter['date_to']) + 60*60*24 - 1;
    }

    if(isset($filter['branch']) && strlen($filter['branch'])) {
      $branches = $this->branch_lib->get_branch_title_like($filter['branch']);
      $branch_ids = array_map(function($branch) { return get_mongo_id($branch); }, $branches);
      $query_options['branch_id'] = array('$in' => $branch_ids);
    }

    if(isset($filter['challenge']) && strlen($filter['challenge'])) {
      $challenges = $this->challenge_lib->get_challenge_name_like($filter['challenge']);
      $challenge_ids = array_map(function($challenge) { return get_mongo_id($challenge); }, $challenges);
      $query_options['challenge_id'] = array('$in' => $challenge_ids);
    }

    # sort & order
    if(in_array($sort, array('timestamp'))) { # TODO : add sort for date/company/branch/challenge/name
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    $activities = $this->audit_lib->list_audit($query_options, $limit, $offset, $sort);
    $activities_all_count = $this->audit_lib->count($query_options);

    $users = array();

    foreach($activities as &$activity) {
      // Get user
      if(isset($activity['user_id']) && $activity['user_id']) {
        $user_id = $activity['user_id'];

        if(!isset($users[$user_id])) {
          $users[$user_id] = $this->user_model->get_user_profile_by_user_id($user_id);
        }

        $activity['user'] = $users[$user_id];
      } else {
        $activity['user'] = array();
      }

      // Get challenge if it is challenge
      // If found, update the audit's challenge_id
      if(isset($activity['challenge_id'])) {
        $activity['challenge'] = $this->challenge_model->getOne(
          array('_id' => new MongoId($activity['challenge_id']))
        );
      } else {
        $activity['challenge'] = $this->challenge_model->getOne(
          array('hash' => $activity['objecti'])
        );
      }

      if($activity['challenge'] && !isset($activity['challenge_id'])) {
        $challenge_id = get_mongo_id($activity['challenge']);
        $activity['challenge_id'] = $challenge_id;
        if(!$this->audit_lib->update_challenge_id_by_audit_id(get_mongo_id($activity), $challenge_id)) {
          $this->error('Update audit failed', $challenge_id);
        }
      }


      // Get action message & description
      $app_id = (int) 0;
      $action_id = $activity['action_id'];
      $action_list = array();

      if(!isset($action_list[$app_id.'_'.$action_id])){
        $audit_action = $this->audit_lib->get_audit_action($app_id, $action_id);
        if(isset($audit_action)){
          $action_list[$app_id.'_'.$action_id] = $audit_action;
        }
      }else{
        $audit_action = $action_list[$app_id.'_'.$action_id];
      }

      if(isset($audit_action['format_string']) && isset($action_id)){
        $activity['audit_message'] = $this->audit_lib->translate_format_string(
          $audit_action['format_string'],
          $this->audit_model->getOne(array('_id' => $activity['_id'])),
          ($action_id <= 100)
        );
      } else {
        $activity['audit_message'] = NULL;
      }

      $activity['audit_description'] = issetor($audit_action['description'], NULL);

      // Get company
      if(isset($activity['company_id'])) {
        $activity['company'] = $this->company_model->get_company_profile_by_company_id($activity['company_id']);
      }

      // Get branch
      if(isset($activity['branch_id'])) {
        $activity['branch'] = $this->branch_lib->get_one(array('_id' => new MongoId($activity['branch_id'])));
      }
    } unset($activity);

    $options = array(
      'total' => $activities_all_count,
      'total_pages' => ceil($activities_all_count / $limit),
      'count' => count($activities),
      'filter' => $query_options,
      'sort' => $sort
    );
    return $this->success($activities, 1, $options);
  }

  function credit_add() {
    $company_id = $this->input->post('company_id');
    $credit = $this->input->post('credit') | 0;
    if(!$company_id) {
      return $this->error('Invalid Input');
    }

    $this->load->model('company_model');

    if(!$company = $this->company_model->get_company_profile_by_company_id($company_id)) {
      return $this->error('Invalid Company');
    }

    $company['credits'] = issetor($company['credits'], 0) + (int) $credit;

    $update = array(
      'credits' => $company['credits']
    );

    if(!$result = $this->company_model->update_company_profile_by_company_id($company_id, $update)) {
      return $this->error('Update Failed');
    }else{
      $user = $this->socialhappen->get_user();
      $this->load->library('audit_lib');
      $audit_data = array(
        'user_id' => $user['user_id'],
        'action_id' => 205, // Add Credits
        'app_id' => 0,
        'app_install_id' => 0,
        'page_id' => 0,
        'company_id' => $company_id,
        'subject' => NULL,
        'object' => (int)$credit,
        'objecti' => (int)$company['credits'],
        'image' => NULL
      );

      $audit_id = $this->audit_lib->audit_add($audit_data);
    }

    return $this->success($company);
  }

  function audit_actions() {
    $this->load->model('audit_action_model');

    $audit_actions = $this->audit_action_model->get();

    return $this->success($audit_actions);
  }

  function check_admin_session() {
    if(!$user_id = $this->socialhappen->get_user_id()) {
      return $this->error('Session timed out');
    }

    $this->load->model('user_model');
    if(!$user = $this->user_model->get_user_profile_by_user_id($user_id)) {
      return $this->error('Invalid user', 403);
    }

    if(!$user['user_is_developer']) {
      return $this->error('Invalid user', 403);
    }

    return $this->success($user_id);
}

  function reward_machines() {
    if ($model = json_decode($this->input->post('model'), TRUE)) {
      return $this->_reward_machine_add($model);
    }
    $limit = $this->input->get('limit') ? : 10;
    $offset = $this->input->get('offset') ? : 0;
    $filter = $this->input->get('filter');
    $sort = $this->input->get('sort');
    $order = $this->input->get('order') ? : 1;

    $this->load->library('reward_machine_lib');

    $query_options = array();
    if(isset($filter['name']) && strlen($filter['name'])) {
      $query_options['name'] = array('$regex' => '\b'.$filter['name'], '$options' => 'i');
    }

    if(isset($filter['description']) && strlen($filter['description'])) {
      $query_options['description'] = array('$regex' => '\b'.$filter['description'], '$options' => 'i');
    }

    # sort & order
    if(in_array($sort, array('name', '_id'))) {
      $sort = array($sort => ($order === '-' ? -1 : 1));
    } else {
      $sort = FALSE;
    }

    $reward_machines = $this->reward_machine_lib->get($query_options, $limit, $offset, $sort);
    $reward_machines_all_count = $this->reward_machine_lib->count($query_options);

    $options = array(
      'total' => $reward_machines_all_count,
      'total_pages' => ceil($reward_machines_all_count / $limit),
      'count' => count($reward_machines),
      'filter' => $query_options,
      'sort' => $sort
    );

    return $this->success($reward_machines, 1, $options);
  }

  function _reward_machine_add($machine) {
      $this->load->library('reward_machine_lib');

      if(!strlen($machine['name'])) {
        return $this->error('Reward machine data invalid');
      }

      if(!$machine_item_id = $this->reward_machine_lib->add($machine)) {
        return $this->error('Add reward machinemachine failed');
      }

      return $this->success($machine);
    }
}

/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */