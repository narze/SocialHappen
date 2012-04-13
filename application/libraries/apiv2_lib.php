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
   * user signup application
   *
   * @param {Array} input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   *
   * @param input.email
   * @param input.password
   * @param input.facebook_user_id
   */
  function signup($input = array()) {
    if (!isset($input['email'])
     || !isset($input['password'])
     || !isset($input['facebook_user_id'])) {
      return FALSE;
    }
    
    return TRUE;
  }
  
  /**
   * user play application
   * 
   * @POST
   * 
   * @param {Array} input
   * @param input.app_id
   * @param input.app_install_id
   * @param input.app_install_secret_key
   * 
   * @param input.facebook_user_id
   */
  function play_app($input = array()){
    if (!isset($input['facebook_user_id'])) {
      return FALSE;
    }
    
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
   * @param input.facebook_user_id
   */
  function get_user($input = array()){
    if (!isset($input['facebook_user_id'])) {
      return NULL;
    }
    
    return TRUE;
  }
}

/* End of file apiv2_lib.php */
/* Location: ./application/libraries/apiv2_lib.php */