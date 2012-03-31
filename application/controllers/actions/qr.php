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
      $valid_session = TRUE;
      $user_id = 0;
      
      if($valid_session){
        
        $challenge_id = $action['challenge_id'];
        
        $this->load->library('challenge_lib');
        $challenge = $this->challenge_lib->get_by_hash($code);
        
        /**
         * check if user joined challenge
         */
        if($challenge){
          /**
           * @todo : render challenge with proceed button
           *         go to actions/qr/go/{code} when click proceed
           */
          
        }else{
          /**
           * @todo : render challenge with join challenge button
           */
        }
        
      }else{
        /**
         * @todo : render challenge with login or register button
         */
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
      redirect($code);
    }
	}
  
  function authen(){
    // we do authen here, or use global mobile authentication page
    echo 'authen';
  }
}