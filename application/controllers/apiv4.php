<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller for mobile native app
 * Coding style : snake_case
 */

header("Access-Control-Allow-Origin: *");
require_once(APPPATH . 'libraries/REST_Controller.php');
class Apiv4 extends REST_Controller {

  function __construct(){
    parent::__construct();
    if($this->uri->segment(1) === 'testmode') {
      $this->load->library('db_sync');
      $this->db_sync->use_test_db(TRUE);
    }
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
    $email = $this->post('email');
    $password = $this->post('password');
    $facebook_user_id = $this->post('facebook_user_id');
    $facebook_user_first_name = $this->post('facebook_user_first_name');
    $facebook_user_last_name = $this->post('facebook_user_last_name');
    $facebook_user_image = $this->post('facebook_user_image');

    if(!$email || !$password) {
      return $this->_error('No email and/or password');
    }

    if(!$facebook_user_id) {
      return $this->_error('Please connect facebook before signing up');
    }

    $this->load->model('user_model');

    if($this->user_model->findOne(array('user_email' => $email))) {
      return $this->_error('Email already used');
    }

    if($this->user_model->findOne(array('user_facebook_id' => $facebook_user_id))) {
      return $this->_error('Facebook account already used');
    }

    $presalt = 'tH!s!$Pr3Za|t';
    $postsalt = 'di#!zp0s+s4LT';
    $encrypted_password = sha1($presalt.$password.$postsalt);

    $user = array(
      'user_first_name' => $facebook_user_first_name,
      'user_last_name' => $facebook_user_last_name,
      'user_image' => $facebook_user_image,
      'user_email' => $email,
      'user_password' => $encrypted_password,
      'user_facebook_id' => $facebook_user_id
    );

    if(!$user_id = $this->user_model->add_user($user)) {
      return $this->_error('Add user failed');
    }

    return $this->_success($user_id);
  }

  /**
  * Requires token
  */

}
