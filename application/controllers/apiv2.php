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
   * user signup application
   * 
   * @POST
   * 
   * @param app_id
   * @param app_install_id
   * @param app_install_secret_key
   * 
   * @param email
   * @param password
   * @param facebook_user_id
   */
  function signup(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_install_id'] = $this->input->post('app_install_id');
    $input['app_install_secret_key'] = $this->input->post('app_install_secret_key');
    
    $input['email'] = $this->input->post('email');
    $input['password'] = $this->input->post('password');
    $input['facebook_user_id'] = $this->input->post('facebook_user_id');
    
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
   * @param app_id
   * @param app_install_id
   * @param app_install_secret_key
   * 
   * @param facebook_user_id
   */
  function play_app(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_install_id'] = $this->input->post('app_install_id');
    $input['app_install_secret_key'] = $this->input->post('app_install_secret_key');
    
    $input['facebook_user_id'] = $this->input->post('facebook_user_id');
    
    $result = $this->apiv2_lib->join_app($input);
    
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
   * @param app_id
   * @param app_install_id
   * @param app_install_secret_key
   * 
   * @param facebook_user_id
   */
  function get_user(){
    /**
     * post
     */
    $input['app_id'] = $this->input->post('app_id');
    $input['app_install_id'] = $this->input->post('app_install_id');
    $input['app_install_secret_key'] = $this->input->post('app_install_secret_key');
    
    $input['facebook_user_id'] = $this->input->post('facebook_user_id');
    
    $result = $this->apiv2_lib->get_user($input);
    
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