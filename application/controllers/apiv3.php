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
   * list activity of challenge or actions in a challenge
   * Challenge hash -> objecti
   * 
   */
  function challenge_activity(){

    header('Content-Type: application/json', TRUE);
    $challenge_hashes = $this->input->post('challenge_hashes', TRUE);
    $activity_result = array();

    if(isset($challenge_hashes) && $challenge_hashes != ''){
        $challenge_hash_array = json_decode($challenge_hashes);
        $activity_search_result = array();

        $this->load->library('audit_lib');

        foreach($challenge_hash_array as $challenge_hash){
          $challenge_activity =  $this->audit_lib->list_audit(array('objecti' => $challenge_hash));
          
          $activity_search_result = array_merge($activity_search_result, $challenge_activity);
        }

        //sort timestamp
        usort($activity_search_result, array("apiv3", '_timestamp_cmp'));
        $activity_search_result = array_reverse($activity_search_result);

        $activity_result = $activity_search_result;
    }
        
    echo json_encode($activity_result);

    /*
    if(!$user_id){
      echo '[]';
    }else{
      $this->load->library('audit_lib');
      $activity = $this->audit_lib->list_audit(array('user_id' => (int)$user_id, 'app_id' => 0));
      echo json_encode($activity);
    }
    */
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
    
    $last_hash = $this->input->get('last_id', TRUE); 
    
    $company_id = $this->input->get('company_id', TRUE); 
    
    $this->load->library('challenge_lib');
    $this->load->library('action_data_lib');
    $limit = 30;
    
    if($last_hash){
      $challenge = $this->challenge_lib->get_one(array('hash' => $last_hash));
      if($challenge){
        
        if($company_id){
          $challenges = $this->challenge_lib->get(array(
            '_id' => array('$lt' => new MongoId($challenge['_id']['$id']), 'company_id' => (int)$company_id)
          ), $limit);
        }else{
          $challenges = $this->challenge_lib->get(array(
            '_id' => array('$lt' => new MongoId($challenge['_id']['$id']))
          ), $limit);
        }
      }else{
        $challenges = array();
      }
    }else{
      if($company_id){
        $challenges = $this->challenge_lib->get(array('company_id' => (int)$company_id), $limit);
        
      }else{
        $challenges = $this->challenge_lib->get(array(), $limit);
      }
    }
    
    function convert_id($item){
      // $item['_id'] = '' . $item['_id'];
      unset($item['_id']);
      // unset($item['criteria']);
      return $item;
    }
    
    $challenges = array_map("convert_id", $challenges);
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
    
    $challenge = $this->input->post('model', TRUE); 
    
    if(!isset($challenge) || $challenge == ''){
      $result = array('success' => false, 'data' => 'no challenge data');
    }else{
      $this->load->library('challenge_lib');
      $this->load->library('action_data_lib');

      $challenge = json_decode($challenge, TRUE);
      
      if(!is_array($challenge)){
        echo json_encode(array('success' => false, 'data' =>'data error'));
        return false;
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
            $action_data_create_flag = false;
            unset($action_data_attr['_id']);
            $update_action_data_result = $this->action_data_lib->update($mgid['$id'], $action_data_attr);

            if(!$update_action_data_result){
              echo json_encode(array('success' => false, 'data' =>'update action_data failed '. print_r($action_data_attr['data'], true)));
              return false;
            }
          }
        }

        if($action_data_create_flag){
          //update mongoID to challenge criteria for new added action data
          if($action_data_id = $this->action_data_lib->add_action_data($action_data_object['action_data']['action_id'], $action_data_attr['data'])){

            //re update challenge object
            $action_data_object['action_data_id'] = $action_data_id;
            $action_data_object['action_data']['_id'] = new MongoId($action_data_id);
            $action_data_object['action_data']['hash'] = strrev(sha1($action_data_id));

          }else{
            echo json_encode(array('success' => false, 'data' =>'add action_data failed : ' . print_r($action_data_attr['data'], true)));
            return false;
          }
        }
      }

      //create 

      $challenge_create_flag = true;
      $challenge_update = null;
      $challenge_create = null;

      //Add-update reward
      if(issetor($challenge['reward'])) {
        $this->load->model('reward_item_model');
        $reward = $challenge['reward'];

        if(isset($challenge['reward']['_id'])) {
          //Reward exists : update
          $reward_item_id = get_mongo_id($challenge['reward']);
          if(!$reward_update_result = $this->reward_item_model->update($reward_item_id, $reward)) {
            echo json_encode(array('success' => FALSE, 'data' => 'Update reward failed'));
            return;
          }
        } else {
          //New reward : add new
          if(!$reward_item_id = $this->reward_item_model->add_challenge_reward($reward)) {
            echo json_encode(array('success' => FALSE, 'data' => 'Add reward failed'));
            return;
          }
        }

        $challenge['reward_item_id'] = $reward_item_id;
      }


      //Create challenge data without reward (to store in challenge's model)
      $challenge_data = $challenge;
      unset($challenge_data['reward']);

      //Try to update challenge
      if($challenge_hash){
        try {
          $asis_challenge = $this->challenge_lib->get_one(array('hash' => $challenge_hash));
          $challenge_id = get_mongo_id($asis_challenge);

          $challenge_update = $this->challenge_lib->update(array('_id' => new MongoId($challenge_id)), $challenge_data);
        } catch (Exception $ex){
          //update exception
          $challenge_create_flag = false;
        }

        if($challenge_update)
            $challenge_create_flag = false;
      }

      //Create challenge if cannot update
      if($challenge_create_flag){
        $challenge_create = $this->challenge_lib->add($challenge_data);

        if($challenge_create)
            $challenge_id = $challenge_create;

      }

      if($challenge_create || $challenge_update){
        // $challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($challenge_id)));
        // $challenge['_id'] = $challenge['_id']['$id'];
        echo json_encode(array('success' => TRUE, 'data' => $challenge));
      }else{
        echo json_encode(array('success' => false, 'data' =>'add/update challenge failed'));
      }
    }
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
}


/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */