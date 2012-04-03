<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}
	
  function index(){
    $action = $this->action_data_lib->get_action_data_from_code();
    $code = $action['hash'];
    
    if($action){
      /**
       * @todo : insert session validation method here
       */
      $valid_session = FALSE;
      
      if($this->socialhappen->is_logged_in()) { 
        $valid_session = TRUE;
        $user = $this->socialhappen->get_user();
        $user_id = $user['user_id'];
      } else if($facebook_user = $this->facebook->getUser()) {
        $this->load->model('user_model');
        if($user = $this->user_model->get_user_profile_by_user_facebook_id($facebook_user['id'])) {
          $this->socialhappen->player_login($user['user_id']);
          $user_id = $user['user_id'];
          $valid_session = TRUE;
        }
      }
      
      $challenge_id = $action['data']['challenge_id'];
      $this->load->library('challenge_lib');
      $challenge = $this->challenge_lib->get_one(array(
        '_id' => new MongoId($challenge_id)
      ));
      
      if($valid_session){
        $challenge_progress = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
        
        if(!$challenge){
          show_error('Invalid Challenge');
          return;
        }
        
        /**
         * check if user joined challenge
         */
        if($challenge_progress){
          /**
           * @todo : render challenge with proceed button
           *         go to actions/qr/go/{code} when click proceed
           */
          $data = array(
            'challenge' => $challenge,
            'proceed_url' => $this->action_data_lib->get_proceed_qr_url($code)
          );

          $this->load->view('actions/qr/qr_challenge_proceed_view', $data);
        }else{
          /**
           * @todo : render challenge with join challenge button
           */
          $data = array(
            'challenge' => $challenge,
            'join_url' => site_url('/player/join_challenge/' . $challenge['hash'] . '/?next='. site_url($this->uri->uri_string()))
          );

          $this->load->view('actions/qr/qr_challenge_join_view', $data);
        }
        
      }else{
        /**
         * @todo : render challenge with login or register button
         */
        $data = array(
          'challenge' => $challenge,
          'login_url' => site_url('/player/login/?next='. site_url($this->uri->uri_string()).'?code='.$code)
        );

        $this->load->view('actions/qr/qr_challenge_login_view', $data);
      }
    }else{
      show_error('Invalid Url');
    }
  }
  
  /**
   * qr code handler method ex. /actions/qr/go/3531sdavgbsd32436fd4363
   *
   * @param code hash of action object
   */
	function go($code = NULL) {
    /**
     * @todo : insert session validation method here
     */
    $valid_session = TRUE;
    
    if($valid_session){
      $action_data = $code ? $this->action_data_lib->get_action_data_by_code($code) : NULL;
      
      if($action_data){
        /**
         * @todo : insert audit/achievement here
         */
        // add_audit();
        // increment_achievement();
        
        // we may render mobile web here
        echo $action_data['data']['done_message'];
      }else{
        // we may render beautiful error page here
        show_error('Invalid Url');
      } 
    }else{
      
      // user have no sesion, redirect to challenge action page
      
      // $current_url = site_url($this->uri->uri_string());
      $url = $this->action_data_lib->get_qr_url($code);
      redirect($url);
    }
	}
}