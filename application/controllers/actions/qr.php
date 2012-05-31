<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}
	
  function index_deprecated(){
    $action = $this->action_data_lib->get_action_data_from_code();
    $code = $action['hash'];
    
    if($action){
      $user = $this->socialhappen->get_user();
      $this->load->library('challenge_lib');
      $challenge = $this->challenge_lib->get_one(
        array( 
          'criteria.action_data_id' => get_mongo_id($action)
        )
      );

      $challenge_id = get_mongo_id($challenge);
      
      if($user){
        $challenge_progress = $this->challenge_lib->get_challenge_progress($user['user_id'], $challenge_id);
        
        if(!$challenge){
          show_error('Invalid Challenge');
          return;
        }
        $player_challenging = isset($user['challenge']) && in_array($challenge['hash'], $user['challenge']);
        
        
        /**
         * check if user joined challenge
         */
        if($challenge_progress){
          /**
           * @todo : render challenge with proceed button
           *         go to actions/qr/go/{code} when click proceed
           */
          $this->load->vars(
            array(
              'challenge_hash' => $challenge['hash'],
              'challenge' => $challenge,
              'player_logged_in' => $this->socialhappen->is_logged_in(),
              'player_challenging' => $player_challenging,
              'challenge_progress' => $challenge_progress,
              'challenge_done' => FALSE,
              'redeem_pending' => isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming']),
            )
          );
          $data = array(
            'header' => $this -> socialhappen -> get_header_bootstrap( 
              array(
                'title' => $challenge['detail']['name'],
                'script' => array(
                  'common/bar',
                ),
                'style' => array(
                  'common/player',
                )
              )
            ),
            'challenge' => $challenge,
            'proceed_url' => $this->action_data_lib->get_proceed_qr_url($code)
          );

          $this->parser->parse('actions/qr/qr_challenge_proceed_view', $data);
        }else{
          /**
           * @todo : render challenge with join challenge button
           */
          $this->load->vars(
            array(
              'challenge_hash' => $challenge['hash'],
              'challenge' => $challenge,
              'player_logged_in' => $this->socialhappen->is_logged_in(),
              'player_challenging' => $player_challenging,
              // 'challenge_progress' => $challenge_progress,
              'challenge_done' => FALSE,
              'redeem_pending' => isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming']),
            )
          );
          $data = array(
            'header' => $this -> socialhappen -> get_header_bootstrap( 
              array(
                'title' => $challenge['detail']['name'],
                'script' => array(
                  'common/bar',
                ),
                'style' => array(
                  'common/player',
                )
              )
            ),
            'challenge' => $challenge,
            'join_url' => site_url('/player/join_challenge/' . $challenge['hash'] . '/?next='. site_url($this->uri->uri_string()))
          );

          $this->parser->parse('actions/qr/qr_challenge_join_view', $data);
        }
        
      }else{
        /**
         * @todo : render challenge with login or register button
         */
        $data = array(
          'challenge' => $challenge,
          'login_url' => site_url('/login/?next='. site_url($this->uri->uri_string()).'?code='.$code)
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
    //Get challenge and action data
    $action_data = $code ? $this->action_data_lib->get_action_data_by_code($code) : NULL;
    $this->load->library('challenge_lib');
    $challenge = $this->challenge_lib->get_one(
      array( 
        'criteria.action_data_id' => get_mongo_id($action_data)
      )
    );

    //Check if challenge is valid
    if(!$challenge || !isset($challenge['hash'])) {
      return show_error('Invalid QR Code');
    }

    //Check if login
    if(!$user_id = $this->socialhappen->get_user_id()){
      return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
    }

    //Check if challenge accepted
    $this->load->library('user_lib');
    $user = $this->user_lib->get_user($user_id);
    $player_challenging = isset($user['challenge']) && in_array($challenge['hash'], $user['challenge']);
    if(!$player_challenging) {
      return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
    }

    //Perform the QR action

    //Add some action-specific user_data
    $user_data = array(
      'timestamp' => time()
      //TODO: add more
    );

    $this->load->library('action_user_data_lib');
    if(!$result = $this->action_user_data_lib->add_action_user_data(
      $challenge['company_id'],
      $action_data['action_id'],
      get_mongo_id($action_data), 
      get_mongo_id($challenge),
      $user['user_id'],
      $user_data
      )){
      show_error('Invalid Data');
    } else {
      //Add audit & stat
      $this->load->library('audit_lib');
      $audit_data = array(
        'user_id' => $user['user_id'],
        'action_id' => $action_data['action_id'],
        'app_id' => 0,
        'app_install_id' => 0,
        'page_id' => 0,
        'company_id' => $challenge['company_id'],
        'subject' => NULL,
        'object' => NULL,
        'objecti' => NULL,
        //'additional_data' => $additional_data
      );

      $audit_result = $this->audit_lib->audit_add($audit_data);

      $this->load->library('achievement_lib');
      $info = array(
              'action_id'=> $action_data['action_id'],
              'app_install_id'=> 0, 
              'page_id' => 0
            );
      $achievement_result = $this->achievement_lib->
        increment_achievement_stat($challenge['company_id'], 0, $user['user_id'], $info, 1);
      
      if(!$audit_result || !$achievement_result) {
        log_message('error', 'Audit | Achievement error');
      }

      //Add challenge url to redirect back
      $action_data['challenge_url'] = base_url('player/challenge/'.$challenge['hash']);

      // we may render mobile web here
      $template = array(
        'title' => 'Welcome to SocialHappen',
        'styles' => array(
          'common/bootstrap',
          'common/bootstrap-responsive'
        ),
        'body_views' => array(
          'actions/qr/qr_challenge_finish_view' => $action_data
        ),
        'scripts' => array(
          'common/bootstrap.min',
        )
      );
      $this->load->view('common/template', $template);
    }
    
	}
}