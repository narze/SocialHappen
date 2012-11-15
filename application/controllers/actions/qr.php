<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('action_data_lib');
	}

  function index() {
    //redirect to app store
    show_error('Redirecting to app store');
    return;

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

    $challenge_id = get_mongo_id($challenge);

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
    if($is_daily_challenge = (isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && $days > 0)) {
      //Check if daily challenge accepted
      $this->load->library('user_lib');
      $user = $this->user_lib->get_user($user_id);
      $player_challenging = FALSE;
      $now = date('Ymd');
      if(isset($user['daily_challenge'][$challenge_id])) {
        foreach($user['daily_challenge'][$challenge_id] as $challenge_range) {
          if($challenge_range['start_date'] <= $now && $now <= $challenge_range['end_date']) {
            $player_challenging = TRUE;
            break;
          }
        }
      }
      if(!$player_challenging) {
        return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
      }

      $player_completed_daily = FALSE;
      if(isset($user['daily_challenge_completed'][$challenge_id])) {
        foreach($user['daily_challenge_completed'][$challenge_id] as $challenge_range) {
          if($challenge_range['start_date'] <= $now && $now <= $challenge_range['end_date']) {
            $player_completed_daily = TRUE;
            return redirect('player/challenge/'.$challenge['hash'].'?completed_daily=1');
          }
        }
      }
    } else {
      //Check if challenge accepted
      $this->load->library('user_lib');
      $user = $this->user_lib->get_user($user_id);
      $player_challenging = isset($user['challenge']) && in_array($challenge_id, $user['challenge']);
      if(!$player_challenging) {
        return redirect('player/challenge/'.$challenge['hash'].'?next=actions/qr/go/'.$code);
      }

      if($player_completed = isset($user['challenge_completed']) && in_array($challenge_id, $user['challenge_completed'])) {
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
    if(!$action_user_data_id = $this->action_user_data_lib->add_action_user_data(
      $challenge['company_id'],
      $action_data['action_id'],
      get_mongo_id($action_data),
      get_mongo_id($challenge),
      $user['user_id'],
      $user_data
      )){
      show_error('Invalid Data');
    } else {
      $user = $this->socialhappen->get_user();
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
        'image' => $challenge['detail']['image']
      );

      $audit_id = $this->audit_lib->audit_add($audit_data);

      //Update action user data with audit id
      $update_result = $this->action_user_data_lib->update_action_user_data($action_user_data_id, array('audit_id' => $audit_id));

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

      if(!$audit_id || !$achievement_result) {
        log_message('error', 'Audit | Achievement error');
      }

      // //Add challenge url to redirect back
      // $action_data['challenge_url'] = base_url('player/challenge/'.$challenge['hash'].'?action_done=1');

      // // we may render mobile web here
      // $template = array(
      //   'title' => 'Welcome to SocialHappen',
      //   'styles' => array(
      //     'common/bootstrap.min',
      //     'common/bootstrap-responsive.min'
      //   ),
      //   'body_views' => array(
      //     'actions/qr/qr_challenge_finish_view' => $action_data
      //   ),
      //   'scripts' => array(
      //     'common/bootstrap.min',
      //   )
      // );
      // $this->load->view('common/template', $template);

      redirect('player/challenge/'.$challenge['hash'].'?action_done=1');
    }
	}
}