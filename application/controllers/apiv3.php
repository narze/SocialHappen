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
    $logged_in = $this -> socialhappen -> is_logged_in();
    
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
      if($user){
        echo json_encode($user);
      }else{
        echo '{}';
      }
      
    }else{
      echo '{}';
    }
  }
}


/* End of file apiv3.php */
/* Location: ./application/controllers/apiv3.php */