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
      if($user){
        echo json_encode($user);
      }else{
        echo '{}';
      }
    }else if($user_id){ // see specific user
      $this->load->model('user_model');
      $user = $this->user_model->get_user_profile_by_user_id($user_id);
      $this->load->model('achievement_stat_model');
      $user_stat = $this->achievement_stat_model->get($app_id = 0, $user_id);
      $user_score = issetor($user_stat['score'], 0); 
      
      if($user){
        $user['user_score'] = $user_score;
        echo json_encode($user);
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
    
    $this->load->library('challenge_lib');
    $limit = 30;
    
    if($last_hash){
      $challenge = $this->challenge_lib->get_one(array('hash' => $last_hash));
      if($challenge){
        $challenges = $this->challenge_lib->get(array(
          '_id' => array('$lt' => new MongoId($challenge['_id']['$id']))
        ), $limit);
      }else{
        $challenges = array();
      }
    }else{
      $challenges = $this->challenge_lib->get(array(), $limit);
    }
    
    
    
    
    function convert_id($item){
      // $item['_id'] = '' . $item['_id'];
      unset($item['_id']);
      unset($item['criteria']);
      return $item;
    }
    
    $challenges = array_map("convert_id", $challenges);
    echo json_encode($challenges);
  }
}


/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */