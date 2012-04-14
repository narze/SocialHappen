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
      'user_email' => 'a@a.com',
      'user_password' => 'password',
      'user_facebook_id' => '354382143516841354',
      'user_first_name' => 'first name'
    );
    
    $result = $this->apiv2_lib->signup($input);
    
    // echo 'signup_test - <pre>';
    // var_dump($result);
    // echo '</pre>';
    
    $this->load->model('user_model');
    
    $user = $this->user_model->get_user_profile_by_user_facebook_id($input['user_facebook_id']);
    
    $this->unit->run(TRUE, $result, "\$result", $result);
    $this->unit->run($input['user_facebook_id'], $user['user_facebook_id'], "\$result", $result);
    $this->unit->run($input['user_email'], $user['user_email'], "\$result", $result);
    $this->unit->run($input['user_first_name'], $user['user_first_name'], "\$result", $result);
    
    $user_id = 7;
    $this->load->model('audit_model');
    $result = $this->audit->list_recent_audit($user_id);
    $this->unit->run(1, count($result), "\$result", $result);
    $this->unit->run($user_id, $result[0]['user_id'], "\$result", $result);
    $this->unit->run($input['app_id'], $result[0]['app_id'], "\$result", $result);
    $this->unit->run($input['app_install_id'], $result[0]['app_install_id'], "\$result", $result);
    $this->unit->run(101, $result[0]['action_id'], "\$result", $result);
    
    $this->load->model('achievement_stat_model');
    $res = $this->achievement_stat_model->get($input['app_id'], $user_id);
    $this->unit->run(1, $res['action']['101']['app_install']['1']['count'], "\$result", $result);
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
      'user_facebook_id' => '354382143516841354'
    );
    
    $result = $this->apiv2_lib->play_app($input);
    
    $this->unit->run(TRUE, $result, "\$result", $result);
    
    $user_id = 7;
    $this->load->model('audit_model');
    $result = $this->audit->list_recent_audit($user_id);
    
    // echo 'play_app_test - <pre>';
    // var_dump($result);
    // echo '</pre>';
    
    $this->unit->run(2, count($result), "\$result", $result);
    $this->unit->run($user_id, $result[0]['user_id'], "\$result", $result);
    $this->unit->run($input['app_id'], $result[0]['app_id'], "\$result", $result);
    $this->unit->run($input['app_install_id'], $result[0]['app_install_id'], "\$result", $result);
    $this->unit->run(103, $result[0]['action_id'], "\$result", $result);
    
    $this->load->model('achievement_stat_model');
    $res = $this->achievement_stat_model->get($input['app_id'], $user_id);
    $this->unit->run(1, $res['action']['103']['app_install']['1']['count'], "\$result", $result);
  }
  
  function play_app_invalid_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1
    );
    
    $result = $this->apiv2_lib->play_app($input);
    
    $this->unit->run(FALSE, $result, "\$result", $result);
    
    $user_id = 7;
    $this->load->model('audit_model');
    $result = $this->audit->list_recent_audit($user_id);
    $this->unit->run(2, count($result), "\$result", $result);
    $this->unit->run($user_id, $result[0]['user_id'], "\$result", $result);
    $this->unit->run($input['app_id'], $result[0]['app_id'], "\$result", $result);
    $this->unit->run($input['app_install_id'], $result[0]['app_install_id'], "\$result", $result);
    $this->unit->run(103, $result[0]['action_id'], "\$result", $result);
    
    $this->load->model('achievement_stat_model');
    $res = $this->achievement_stat_model->get($input['app_id'], $user_id);
    $this->unit->run(1, $res['action']['103']['app_install']['1']['count'], "\$result", $result);
  }
  
  function get_user_test(){
    
    $input = array(
      'app_id' => 1,
      'app_install_id' => 1,
      'app_install_secret_key' => 1,
      'user_facebook_id' => '354382143516841354'
    );
    
    $expect = array(
      'user_email' => 'a@a.com',
      'user_facebook_id' => '354382143516841354',
      'user_first_name' => 'first name'
    );
    
    $result = $this->apiv2_lib->get_user($input);
    
    // echo 'get_user_test - <pre>';
    // var_dump($result);
    // echo '</pre>';
    
    $this->unit->run(TRUE, $result, "\$result", $result);
    $this->unit->run($expect['user_facebook_id'], $result['user_facebook_id'], "\$result", $result);
    $this->unit->run($expect['user_email'], $result['user_email'], "\$result", $result);
    $this->unit->run($expect['user_first_name'], $result['user_first_name'], "\$result", $result);
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
