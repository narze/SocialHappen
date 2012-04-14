<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API Library
 * 
 * @author Wachiraphan C.
 */
class Apiv2_Lib {

  private $skip_auth = FALSE;

  function __construct($skip_auth = FALSE) {
    $this->CI =& get_instance();
    $this->skip_auth = $skip_auth;
  }
  
  
 /**
   * user signup application, thia method will add user information to database
   * and add audit_id = 101 - User Register SocialHappen 
   *
   * @param {Array} input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   *
   * @param input.user_email
   * @param input.user_password
   * @param input.user_facebook_id
   */
  function signup($input = array()) {
    if (!isset($input['user_email'])
     || !isset($input['user_password'])
     || !isset($input['user_facebook_id'])) {
      return FALSE;
    }
    
    if(!$this->_check_app($input)){
      return FALSE;
    }
    
    $app_id = $input['app_id'];
    $app_install_id = $input['app_install_id'];
    
    unset($input['app_id']);
    unset($input['app_install_id']);
    unset($input['app_install_secret_key']);
    
    $user = $input;
    
    $this->CI->load->model('user_model');
    
    $result = $this->CI->user_model->add_user($user);
    
    if($result){
      $this->CI->load->library('audit_lib');
      $this->CI->load->library('achievement_lib');
      
      $this->CI->load->model('user_model');
      $user = $this->CI->user_model->get_user_profile_by_user_facebook_id($input['user_facebook_id']);
      
      if(!$user || !isset($user['user_id'])){
        return FASLE;
      }
      
      $this->CI->audit_lib->add_audit($app_id, NULL, 101, NULL, NULL, array(
        'app_install_id' => $app_install_id,
        'user_id' => $user['user_id']
      ));
      
      $this->CI->achievement_lib->increment_achievement_stat(0, $app_id, $user['user_id'],
       array(
        'action_id' => 101,
        'app_install_id' => $app_install_id,
        'user_id' => $user['user_id']
       ), $amount = 1);
    }
    
    return $result;
  }
  
  /**
   * user play application, add audit_id = 103 - User Visit
   * 
   * @POST
   * 
   * @param {Array} input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   * 
   * @param input.user_facebook_id
   */
  function play_app($input = array()){
    if (!isset($input['user_facebook_id'])) {
      return FALSE;
    }
    
    if(!$this->_check_app($input)){
      return FALSE;
    }
    
    $this->CI->load->library('audit_lib');
    $this->CI->load->library('achievement_lib');
    
    $this->CI->load->model('user_model');
    $user = $this->CI->user_model->get_user_profile_by_user_facebook_id($input['user_facebook_id']);
    
    if(!$user || !isset($user['user_id'])){
      return FASLE;
    }
    
    $this->CI->audit_lib->add_audit($input['app_id'], NULL, 103, NULL, NULL, array(
      'app_install_id' => $input['app_install_id'],
      'user_id' => $user['user_id']
    ));
    
    $this->CI->achievement_lib->increment_achievement_stat(0, $input['app_id'], $user['user_id'],
     array(
      'action_id' => 103,
      'app_install_id' => $input['app_install_id'],
      'user_id' => $user['user_id']
     ), $amount = 1);
    
    return TRUE;
  }
  
  /**
   * get platform user, use this to check if current user is a platform user
   * 
   * 
   * @param {Array} input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   * 
   * @param input.user_facebook_id
   */
  function get_user($input = array()){
    if (!isset($input['user_facebook_id'])) {
      return NULL;
    }
    
    if(!$this->_check_app($input)){
      return FALSE;
    }
    
    $this->CI->load->model('user_model');
    
    $result = $this->CI->user_model->get_user_profile_by_user_facebook_id($input['user_facebook_id']);
    
    return $result;
  }
  
  /**
   * check app_id, app_install_id, app_install_secret_key
   * @param input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   */
  function _check_app($input = array()){
    if (!isset($input['app_id'])
     || !isset($input['app_install_id'])
     || !isset($input['app_install_secret_key'])) {
      return FALSE;
    }
    
    return TRUE;
  }
}

/* End of file apiv2_lib.php */
/* Location: ./application/libraries/apiv2_lib.php */