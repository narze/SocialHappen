<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller for mobile native app
 * Coding style : snake_case
 */

require_once(APPPATH . 'libraries/REST_Controller.php');
class Apiv4 extends REST_Controller {

  function __construct(){
    parent::__construct();
  }
  /**
   * Helper functions
   */
  function _error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code));
    return FALSE;
  }

  function _success($data = array(), $code = 1) {
    echo json_encode(array('success' => TRUE, 'data' => $data, 'code' => $code));
    return TRUE;
  }

  function index_get() {
    $this->response(array('success' => $this->get('test')));
  }

  /**
  * Not requires token
  */

  function check_user_get() {
    $facebook_user_id = $this->get('facebook_user_id');

    if(!$facebook_user_id) {
      return $this->_error('undefined facebook_user_id');
    }

    //check facebook_user_id in user model
    $this->load->model('user_model');
    $user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user_id);

    if(!$user) {
      return $this->_error('user not found');
    }

    return $this->_success($user);
  }

  function signup_post() {

  }

  /**
  * Requires token
  */

}
