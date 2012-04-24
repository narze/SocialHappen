<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API controller
 * @author Metwara Narksook
 */
class Apiv2 extends CI_Controller {

  function __construct(){
    header("Access-Control-Allow-Origin: *");
    parent::__construct();
    
    $this->load->library('apiv2_lib');
  }

  function index(){
    echo json_encode(array('status' => 'OK'));
  }
  
  /**
   * user sign up application
   * 
   * @POST
   * 
   * @param app_id [required]
   * @param app_secret_key [required]
   * 
   * @param user_email [required]
   * @param user_password [required]
   * @param user_facebook_id [required]
   * @param user_first_name
   * @param user_last_name
   * @param user_image
   * @param user_facebook_access_token
   * @param user_gender_id
   * @param user_birth_date
   * @param user_about
   * @param user_twitter_name
   * @param user_phone
   * @param user_gender
   */
  function signup(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_secret_key'] = $this->input->post('app_secret_key');
    
    $input['user_email'] = $this->input->post('user_email');
    // $input['user_password'] = $this->input->post('user_password');
    $input['user_facebook_id'] = $this->input->post('user_facebook_id');
    
    $result = $this->apiv2_lib->signup($input);
    
    if($result){
      echo json_encode(array(
        'success' => TRUE,
        'result' => $result
      ));
    }else{
      echo json_encode(array(
        'success' => FALSE,
        'result' => $result
      ));
    }
  }
  
  /**
   * user play application
   * 
   * @POST
   * 
   * @param app_id [required]
   * @param app_secret_key [required]
   * 
   * @param user_facebook_id [required]
   */
  function play_app(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_secret_key'] = $this->input->post('app_secret_key');
    
    $input['user_facebook_id'] = $this->input->post('user_facebook_id');
    
    $result = $this->apiv2_lib->play_app($input);
    
    if($result){
      echo json_encode(array(
        'success' => TRUE,
        'result' => $result
      ));
    }else{
      echo json_encode(array(
        'success' => FALSE,
        'result' => $result
      ));
    }
  }
  
  /**
   * get platform user, use this to check if current user is a platform user
   * 
   * @POST
   * 
   * @param app_id [required]
   * @param app_secret_key [required]
   * 
   * @param user_facebook_id [required]
   */
  function get_user(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_secret_key'] = $this->input->post('app_secret_key');
    
    $input['user_facebook_id'] = $this->input->post('user_facebook_id');
    
    $result = $this->apiv2_lib->get_user($input);
    log_message('error', $result);
    if($result){
      echo json_encode(array(
        'success' => TRUE,
        'result' => $result
      ));
    }else{
      echo json_encode(array(
        'success' => FALSE,
        'result' => $result
      ));
    }
    
  }
}


/* End of file apiv2.php */
/* Location: ./application/controllers/apiv2.php */