<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiv2_lib_test extends CI_Controller {
  function __construct(){
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->library('apiv2_lib');
        
    $this->unit->reset_dbs();
  }
  
  function __destruct(){
    $this->unit->report_with_counter();
  }
  
  function index(){
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method) {
      if(preg_match("/(_test)$/",$method)){
        $this->$method();
      }
    }
  }
  
  function signup_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1,
      'email' => 'a@a.com',
      'password' => 'password',
      'facebook_user_id' => '354382143516841354'
    );
    
    $result = $this->apiv2_lib->signup($input);
    
    $this->unit->run(TRUE, $result, "\$result", $result);
  }
  
  function signup_invalid_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1
    );
    
    $result = $this->apiv2_lib->signup($input);
    
    $this->unit->run(FALSE, $result, "\$result", $result);
  }
  
  function play_app_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1,
      'facebook_user_id' => '354382143516841354'
    );
    
    $result = $this->apiv2_lib->play_app($input);
    
    $this->unit->run(TRUE, $result, "\$result", $result);
  }
  
  function play_app_invalid_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1
    );
    
    $result = $this->apiv2_lib->play_app($input);
    
    $this->unit->run(FALSE, $result, "\$result", $result);
  }
  
  function get_user_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1,
      'facebook_user_id' => '354382143516841354'
    );
    
    $result = $this->apiv2_lib->get_user($input);
    
    $this->unit->run(TRUE, $result, "\$result", $result);
  }
  
  function get_user_invalid_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1
    );
    
    $result = $this->apiv2_lib->get_user($input);
    
    $this->unit->run(NULL, $result, "\$result", $result);
  }
}
