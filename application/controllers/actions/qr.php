<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}
	
  /**
   * qr code handler method ex. /actions/qr/go/3531sdavgbsd32436fd4363
   *
   * @param _id mongodb id of action object
   */
	function go($_id = NULL) {
    /**
     * @todo : insert session validation method here
     */
    $valid_session = TRUE;
    
    if($valid_session){
      $action_data = $_id ? $this->action_data_lib->get_action_data($_id) : NULL;
      
      if($action_data){
        /**
         * @todo : insert audit/achievement here
         */
        // add_audit();
        // increment_achievement();
        
        // we may render mobile web here
        echo $action_data['data']['qr_message'];
      }else{
        // we may render beautiful error page here
        show_error('Invalid Url');
      } 
    }else{
      
      // user have no sesion, redirect to authen page with current url as 
      // 'next' query string
      
      $current_url = site_url($this->uri->uri_string());
      redirect('/actions/qr/authen?next=' . $current_url);
    }
	}
  
  function authen(){
    // we do authen here, or use global mobile authentication page
    echo 'authen';
  }
}