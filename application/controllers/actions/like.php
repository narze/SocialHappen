<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json', TRUE);
require_once(APPPATH . 'libraries/REST_Controller.php');

class Like extends REST_Controller {

  function __construct(){
    parent::__construct();
    $this->load->library('action_data_lib');
    $this->load->library('action_user_data_lib');
  }

  /**
   * Helper functions
   */

  function error($error_message = NULL, $code = 0) {
    echo json_encode(array('success' => FALSE, 'data' => $error_message, 'code' => $code, 'timestamp' => time()));
    return FALSE;
  }

  function success($data = array(), $code = 1) {
    echo json_encode(array('success' => TRUE, 'data' => $data, 'code' => $code, 'timestamp' => time()));
    return TRUE;
  }

  function index() {

  }

  function check_like_get(){
    $action_id = $this->get('action_id');
    $access_token = $this->get('access_token');

    if(!$action_id || !$access_token){
      $this->error('missing args');
    }else{


      $this->load->library('challenge_lib');

      $challenge = $this->challenge_lib->get_one(array(
        'criteria.action_data_id' => $action_id
      ));

      $action = NULL;

      foreach ($challenge['criteria'] as $key => $value) {
        if($value['action_data_id'] == $action_id){
          $action = $value;
          break;
        }
      }

      if($action && isset($action['facebook_id'])){
        $facebook_id = $action['facebook_id'];

        $fql = 'SELECT page_id, profile_section, type FROM page_fan WHERE uid = me() AND page_id = "' . $facebook_id . '"';

        $url = 'https://graph.facebook.com/fql?q=' . urlencode($fql) . '&access_token=' . $access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close ($ch);

        $response = json_decode($response, TRUE);
        if(isset($response) && isset($response['data']) && count($response['data']) > 0){
          $this->success(array(
            // 'url' => $url,
            // 'action_id' => $action_id,
            // 'access_token' => $access_token,
            // 'facebook_id' => $facebook_id,
            // 'response' => $response
            'liked' => true
          ));
        }else{
          $this->success(array(
            'liked' => false
          ));
        }
      }else{
        $this->error('action not found');
      }
    }
  }
}