<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}

  function index() {
    $this->go($this->input->get('code'));
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

    //Check if challenge is valid and active
    if(!$challenge || !isset($challenge['hash']) || !$challenge['active']) {
      return $this->socialhappen->error_page(
      'Challenge not found.',
      '<p>
        <a href="'.base_url('assets/world').'" class="btn btn-primary btn-large">
          Back 
        </a>
      </p>');
    }
    
    //Check if challenge is playable
    if($challenge['end_date'] < time()) {
      return redirect('player/challenge/'.$challenge['hash'].'?error=ended');
    } else if($challenge['start_date'] > time()) {
      return redirect('player/challenge/'.$challenge['hash'].'?error=not_started');
    }

    //Check if login
    if(!$user_id = $this->socialhappen->get_user_id()){
      return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
    }

    //Check daily challenge 
    if(isset($challenge['repeat']) && ($challenge['repeat'] === 'daily')) {
      //Check if daily challenge accepted
      $this->load->library('user_lib');
      $user = $this->user_lib->get_user($user_id);
      $player_challenging = isset($user['daily_challenge'][date('Ymd')]) && in_array(get_mongo_id($challenge), $user['daily_challenge'][date('Ymd')]);
      if(!$player_challenging) {
        return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
      }

      if($player_completed_daily = (isset($user['daily_challenge_completed'][date('Ymd')])
        && in_array(get_mongo_id($challenge), $user['daily_challenge_completed'][date('Ymd')]))) {
        // echo '<pre>';
        // var_dump($user['daily_challenge_completed']);
        // echo '</pre>';
        return redirect('player/challenge/'.$challenge['hash'].'?completed_daily=1');
      }
    } else {
      //Check if challenge accepted
      $this->load->library('user_lib');
      $user = $this->user_lib->get_user($user_id);
      $player_challenging = isset($user['challenge']) && in_array(get_mongo_id($challenge), $user['challenge']);
      if(!$player_challenging) {
        return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
      }

      if($player_completed = isset($user['challenge_completed']) && in_array(get_mongo_id($challenge), $user['challenge_completed'])) {
        //Check if challeng already completed
        return redirect('player/challenge/'.$challenge['hash'].'?already_completed=1');
      }
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
        'objecti' => $challenge['hash'],
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

      //Check challenge after stat increment
      $this->load->library('challenge_lib');
      $check_challenge_result = $this->challenge_lib->check_challenge($challenge['company_id'], $user_id, $info);
      
      if(!$audit_result || !$achievement_result) {
        log_message('error', 'Audit | Achievement error');
      }

      //Add challenge url to redirect back
      $action_data['challenge_url'] = base_url('player/challenge/'.$challenge['hash'].'?action_done=1');

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