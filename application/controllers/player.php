<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {

  function __construct(){
    parent::__construct();
    $this->presalt = 'tH!s!$Pr3Za|t';
    $this->postsalt = 'di#!zp0s+s4LT';

    if($next = $this->input->get('next')) {
      $this->socialhappen->set_next_url($next);
    }
  }

  /**
   * Index page (for debugging purpose)
   */
  function index() {

    if(!$this->socialhappen->is_logged_in()) { redirect('login'); }

    $user = $this->socialhappen->get_user();

    $data = array(
      'header' => $this->socialhappen->get_header_bootstrap(
        array(
          'title' => 'Player',
          'script' => array(
            //'common/functions',
            //'common/jquery.form',
            'common/bar',
            //'common/fancybox/jquery.fancybox-1.3.4.pack',
            //'home/lightbox',
            //'payment/payment'
          ),
          'style' => array(
            'common/player',
            //'common/platform',
            //'common/main',
            //'common/fancybox/jquery.fancybox-1.3.4'
          )
        )
      )
    );

    //get company
    $this->load->model('challenge_model');
    $companies = $this->challenge_model->get_distinct_company();
    $this->load->model('company_model');
    foreach ($companies as &$company_id) {
      $company_id = $this->company_model->get_company_profile_by_company_id($company_id);
    }
    $this->load->vars('companies', $companies);


    if(isset($user['user_facebook_id']) && isset($user['user_facebook_access_token'])) {
      $facebook_connected = TRUE;
    } else {
      $facebook_connected = FALSE;
    }
    $this->load->library('user_lib');
    $this->load->vars(
      array(
        'player_logged_in' => $this->socialhappen->is_logged_in(),
        'facebook_connected' => $facebook_connected,
        'user' => $this->user_lib->get_user($user['user_id'])
      )
    );

    $this->parser->parse('player/index_view', $data);
  }

  /**
   * View all challenges
   */
  function challenge_list($company_id) {
    if($this->socialhappen->is_logged_in() && $company_id) {



      $this->load->model('company_model');
      $this->load->model('challenge_model');
      $company = $this->company_model->get_company_profile_by_company_id($company_id);
      //TODO : List player's challenges, not all challenges
      $this->load->vars(
        array(
          'company' => $company,
          'challenges' => $this->challenge_model->get(array('company_id' => (int) $company_id))
        )
      );

      $data = array(
        'header' => $this->socialhappen->get_header_bootstrap(
          array(
            'title' => $company['company_name'],
            'script' => array(
              'common/bar',
            ),
            'style' => array(
              'common/player',
            )
          )
        )
      );

      $this->parser->parse('player/challenge_list_view', $data);
    } else {
      redirect('player');
    }
  }

  /**
   * View challenges that you are challenging
   */
  function challenging_list() {
    $user_id = $this->socialhappen->get_user_id();
    $this->load->library('user_lib');
    $user = $this->user_lib->get_user($user_id);
    if(isset($user['challenge'])) {
      foreach($user['challenge'] as &$challenge) {
        $challenge = new MongoId($challenge);
      } unset($challenge);
      $this->load->model('challenge_model');
      $challenges = $this->challenge_model->get(array('_id' => array('$in' => $user['challenge'])));
      $this->load->vars('challenges', $challenges);
      $this->load->view('player/challenge_list_view');
    } else {
      echo 'You did not join any challenge';
    }
  }

  /**
   * Challenge landing
   */
  function challenge($challenge_hash) {
    $this->load->model('challenge_model');
    $this->load->library('challenge_lib');
    if((!$challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) || !$challenge['active']) {
      show_error('Challenge invalid. <a href="'.base_url('assets/world').'">Back</a>');
    } else {
      $challenge_id = get_mongo_id($challenge);
      $this->load->library('user_lib');
      $user_id = $this->socialhappen->get_user_id();
      $user = $this->user_lib->get_user($user_id);

      $player_challenging = isset($user['challenge']) && in_array($challenge_id, $user['challenge']);

      //Check daily challenge
      if($is_daily_challenge = isset($challenge['repeat']) && ($challenge['repeat'] === 'daily')) {
        $player_challenging = isset($user['daily_challenge'][date('Ymd')]) && in_array($challenge_id, $user['daily_challenge'][date('Ymd')]);
      }


      $action_done = $this->input->get('action_done') && $user_id;
      //challenge_progress

      if($player_challenging) {
        $challenge_progress = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
        $challenge_done = TRUE;
        if($challenge_progress) {
          foreach($challenge_progress as $action) {
            if(!$action['action_done']) {
              $challenge_done = FALSE;
            }
          }
        } else {
          $challenge_done = FALSE;
        }
      } else {
        $challenge_done = FALSE;
        $challenge_progress = FALSE;
      }
      
      //Challenge reward
      $challenge_reward = NULL;
      $this->load->model('reward_item_model');
      $challenge_reward = $this->reward_item_model->get_one(array('_id' => new MongoId($challenge['reward_item_id'])));

      //Challenge score
      $this->load->library('audit_lib');
      $user_complete_challenge_action = $this->audit_lib->get_audit_action(0, $this->socialhappen->get_k('audit_action', 'User Complete Challenge'));
      $challenge_score = $user_complete_challenge_action['score'];

      //User's company score
      $this->load->library('achievement_lib');
      $user_company_stat = $this->achievement_lib->get_company_stat($challenge['company_id'], $user_id);
      $company_score = issetor($user_company_stat['company_score'], 0);

      //Challengers
      $challengers = $this->challenge_lib->get_challengers($challenge_id);
      //Get users' profile
      $this->load->model('user_model');
      $limit = 1;
      $challengers['in_progress_count'] = count($challengers['in_progress']);
      $challengers['completed_count'] = count($challengers['completed']);
      foreach($challengers['in_progress'] as $key => &$challenger_in_progress){
        if($key >= $limit) {
          unset($challengers['in_progress'][$key]);
        }
        $challenger_in_progress = $this->user_model->get_user_profile_by_user_id($challenger_in_progress['user_id']);
      }
      foreach($challengers['completed'] as $key => &$challenger_completed){
        if($key >= $limit) {
          unset($challengers['completed'][$key]);
        }
        $challenger_completed = $this->user_model->get_user_profile_by_user_id($challenger_completed['user_id']);
      }

      //Challenge expiration
      $challenge_not_started = $challenge_ended = FALSE;
      date_default_timezone_set('UTC');
      if($challenge['start_date'] > time()) {
        $challenge_not_started = TRUE;
      } else if($challenge['end_date'] < time()) {
        $challenge_ended = TRUE;
      }

      $this->load->vars(
        array(
          'challenge_hash' => $challenge_hash,
          'challenge' => $challenge,
          'player_logged_in' => $this->socialhappen->is_logged_in(),
          'player_challenging' => $player_challenging,
          'challenge_done' => $challenge_done,
          'challenge_progress' => $challenge_progress,
          'redeem_pending' => isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming']),
          'challenge_reward' => $challenge_reward,
          'challenge_score' => $challenge_score,
          'company_score' => $company_score,
          'challengers' => $challengers,
          'challenge_not_started' => $challenge_not_started,
          'challenge_ended' => $challenge_ended,
          'is_daily_challenge' => $is_daily_challenge
        )
      );

      $template = array(
        'title' => $challenge['detail']['name'],
        'styles' => array(
          'common/bootstrap',
          'common/bootstrap-responsive',
          'common/bar',
          'common/player',
          'player/challenge'
        ),
        'body_views' => array(
          'common/fb_root' => array(
            'facebook_app_id' => $this->config->item('facebook_app_id'),
            'facebook_channel_url' => $this->facebook->channel_url,
            'facebook_app_scope' => $this->config->item('facebook_player_scope')
          ),
          'bar/plain_bar_view' => array(),
          'player/challenge_view' => array(),
          'common/vars' => array(
            'vars' => array(
              'base_url' => base_url(),
              'challenge_done' => $challenge_done ? 1 : 0,
              'action_done' => $action_done ? 1 : 0,
              'challenge_start_date' => $challenge['start_date'] | 0,
              'challenge_end_date' => $challenge['end_date'] | 0,
              'challengeError' => $this->input->get('error'),
              'challengeInProgressIndex' => $limit,
              'challengeCompletedIndex' => $limit,
              'getMoreLimit' => $limit,
              'challengeHash' => $challenge['hash']
            )
          )
        ),
        'scripts' => array(),
        'requirejs' => array('js/plain-bar', 'js/player-challenge-common')
      );

      //If challenge is not done, player can do challenges
      if(!$challenge_done) {
        $template['requirejs'][] = 'js/player-challenge';
      } else if($action_done) {
        //If last action is done
        $template['requirejs'][] = 'js/player-challenge-complete';
      }

      $this->load->view('common/template', $template);
    }
  }

  /**
   * View player's setting
   */
  function settings() {
    if(!$user = $this->socialhappen->get_user()) {
      redirect('login?next=player/settings');
    }
    $user_id = $user['user_id'];
    $user_facebook = $this->facebook->getUser();

    $this->load->library('form_validation');
    $this->load->helper('date');

    $this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');     
    $this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');     
    $this->form_validation->set_rules('about', 'About', 'trim|xss_clean');
    $this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
    $this->form_validation->set_rules('timezones', 'Timezone', 'trim|xss|clean');
      
    $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
  
    $timezones = timezones();
    $user['user_timezone'] = array_search($user['user_timezone_offset'] / 60, $timezones);

    if ($this->form_validation->run() == FALSE) // validation hasn't been passed
    {
      //Do nothing
    }
    else
    {
      if(set_value('use_facebook_picture')){
        $user_image = issetor($this->facebook->get_profile_picture($user['user_facebook_id']));
      } else if (!$user_image = $this->socialhappen->replace_image('user_image', $user['user_image'])){
        $user_image = $user['user_image'];
      }
    
      $minute_offset = $timezones[set_value('timezones')] * 60;

      $user_update_data = array(
        'user_first_name' => set_value('first_name'),
        'user_last_name' => set_value('last_name'),
        'user_about' => set_value('about'),
        'user_image' => $user_image,
        'user_timezone_offset' => $minute_offset
      );
      $this->load->model('user_model','users');
      if ($this->users->update_user($user_id, $user_update_data))
      {
        $updated_user = array_merge($user,$user_update_data);
        $updated_user['user_timezone'] = array_search($updated_user['user_timezone_offset'] / 60, $timezones);
        $user = $updated_user;
        $this->load->vars('success', TRUE);
      }
      else
      {
        log_message('error','update user failed');
        echo 'error occured';
      }
    }

    $data = compact('user', 'user_facebook');

    $template = array(
      'title' => 'Account settings',
      'styles' => array(
        'common/bootstrap',
        'common/bootstrap-responsive',
        'common/bar',
        'common/player',
        // 'play/play'
      ),
      'body_views' => array(
        'common/fb_root' => array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_channel_url' => $this->facebook->channel_url,
          'facebook_app_scope' => $this->config->item('facebook_player_scope')
        ),
        // '../../assets/passport/templates/header/navigation.html' => NULL,
        'bar/plain_bar_view' => array(),
        'player/settings_view' => $data,
        'common/vars' => array(
          'vars' => array(
            'base_url' => base_url()
          )
        )
      ),
      'scripts' => array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
        'common/jquery.masonry.min',
        'common/jquery.timeago',
        'common/underscore-min',
        'common/bootstrap.min',
        'common/plain-bar',
        'player/settings.js'
      )
    );
    $this->load->view('common/template', $template);
  }

  /**
   * Connect to facebook
   */
  function connect_facebook() {
    if(($user_facebook_id = $this->FB->getUser()) &&
        ($user_facebook_id == $this->input->get('user_facebook_id')) &&
        ($token = $this->input->get('token'))){
      $connecting_facebook = TRUE;
      $this->load->model('user_model');
      $this->user_model->update_user($this->socialhappen->get_user_id(), array(
        'user_facebook_id' => $user_facebook_id,
        'user_facebook_access_token' => $token
      ));
    } else {
      $connecting_facebook = FALSE;
    }

    $user = $this->socialhappen->get_user();
    if($connecting_facebook || (issetor($user['user_facebook_id']) && issetor($user['user_facebook_access_token']))) {
      redirect('player/settings');
    }

    $this->load->vars(array(
      'facebook_app_id' => $this->config->item('facebook_app_id'),
      'facebook_channel_url' => $this->facebook->channel_url,
      'facebook_default_scope' => $this->config->item('facebook_default_scope'),
      'facebook_connected' => $connecting_facebook
      )
    );
    $this->load->view('player/connect_facebook_view');

  }

  /**
   * Disconnect from facebook
   */
  function disconnect_facebook() {
    if($this->socialhappen->is_logged_in()) {
      $user_id = $this->socialhappen->get_user_id();
      $this->load->model('user_model');
      $this->user_model->update_user($user_id, array('user_facebook_id' => NULL, 'user_facebook_access_token' => NULL));
      echo 'Disconnected from facebook ',anchor('player/settings', 'Back');
    } else {
      redirect('player');
    }
  }

  /**
   * Make the current user joins the challenge
   */
  function join_challenge($challenge_hash = NULL) {
    if(!$this->socialhappen->is_logged_in()) {
      redirect('login?next=player/join_challenge/' . $challenge_hash);
    }

    $this->load->library('challenge_lib');
    if(!$challenge = $this->challenge_lib->get_by_hash($challenge_hash)) {
      return show_error('Challenge Invalid', 404);
    }

    if(!$user_id = $this->socialhappen->get_user_id()) {
      return redirect('player/challenge/'.$challenge_hash);
    }

    $this->load->library('user_lib');
    if(!$this->user_lib->join_challenge($user_id, $challenge_hash)) {
      return show_error('Challenge Error', 404);
    }

    if($next = $this->socialhappen->get_next_url()) {
      return redirect($next);
    }

    redirect('player/challenge/'.$challenge_hash);   
  }

  /**
   * Logout and redirect to index
   */
  function logout() {
    $this->socialhappen->logout();
    redirect('player');
  }

  /**
   * View redeem pending list (for merchant only)
   */
  function merchant_redeem_pending_list() {
    $company_id = NULL; //TODO
    if($user = $this->socialhappen->get_user()) {
      if($user['user_is_player']) {
        echo 'You are not merchant';
      } else {
        $this->load->library('challenge_lib');
        $this->load->model('user_mongo_model');
        $challenges = $this->challenge_lib->get(array()); //TODO search using company id
        $challenge_ids = array();
        foreach($challenges as $challenge) {
          $challenge_ids[] = get_mongo_id($challenge);
        }
        $redeeming_users = $this->user_mongo_model->get(array(
          'challenge_redeeming' => array(
            '$in' => $challenge_ids
            )
          )
        );
        $this->load->vars(array(
          'redeeming_users' => $redeeming_users
        ));
        $this->load->view('player/merchant_redeem_pending_list');
      }
    } else {
      redirect('player');
    }
  }

  /**
   * Confirm user's redeem (for merchant only)
   */
  function merchant_redeem_pending($user_id, $challenge_id) {
    $company_id = NULL; //TODO
    if($user = $this->socialhappen->get_user()) {
      if($user['user_is_player']) {
        echo 'You are not merchant';
      } else {
        $this->load->library('challenge_lib');
        if($result = $this->challenge_lib->redeem_challenge($user_id, $challenge_id)){
          echo 'Redeemed';
        } else {
          echo 'Cannot redeem';
        }
      }
    } else {
      redirect('player');
    }
  }

  /**
   * Redirect to action's url with ?code=[hash] data
   */
  function challenge_action($challenge_hash, $action) {
    $this->load->model('challenge_model');
    if($challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) {
      if(isset($challenge['criteria'][$action])) {
        if($challenge['criteria'][$action]['is_platform_action']) { //If platform's action : handle it by using library
          $this->load->library('action_data_lib');
          $action_url = $this->action_data_lib->get_action_url($challenge['criteria'][$action]['action_data_id']);
          redirect($action_url, 'refresh');
        } else { //TODO if not, redirect to app?
          echo 'this is not platform\'s action';
        }
      } else {
        show_error('Action Invalid');
      }
    } else {
      show_error('Challenge Invalid', 404);
    }
  }

  /**
   * Get action's form data
   */
  function get_challenge_action_form($challenge_hash, $action) {
    $this->load->helper('form');
    $this->load->model('challenge_model');
    if($challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) {
      if(isset($challenge['criteria'][$action])) {
        if($challenge['criteria'][$action]['is_platform_action']) { //If platform's action : handle it by using library
          $this->load->library('action_data_lib');
          // $action_url = $this->action_data_lib->get_action_url($challenge['criteria'][$action]['action_data_id']);
          $this->action_data_lib->get_form($challenge['criteria'][$action]['action_data_id']);
        } else { //TODO if not, redirect to app?
          echo 'this is not platform\'s action';
          return;
        }
      } else {
        show_error('Action Invalid');
        return;
      }
    } else {
      show_error('Challenge Invalid', 404);
      return;
    }
  }

  public function static_signup(){
    $this->load->library('apiv2_lib');
    $app_data = $this->input->get('app_data', TRUE);

    $this->load->vars(array(
      'header' => $this->socialhappen->get_header_bootstrap(
        array(
          'title' => 'Welcome to SocialHappen',
          'script' => array(
            'common/bar',
          ),
          'style' => array(
            'common/player',
          ),
          'use_static_fb_root' => TRUE
        )
      )
    ));

    if(!$app_data){

      $app_data_array = array(
              'app_id' => 0,
              'app_secret_key' => 0,
            );

      $app_data = base64_encode(json_encode($app_data_array));

    } else {

      /*
      print_r(base64_encode(json_encode(
                        array(
                            'app_id' => 10004,
                            'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
                            'user_facebook_id' => '631885465',
                            'data' => array('message' => 'message', 'link' => 'link')
                          ))));
      */

      $app_data_array = json_decode(base64_decode($app_data), TRUE);
    }

    $data = compact('app_data','app_data_array');
    $this->load->view('player/static_signup_view', $data);
  }

  /**
   * Static signup : AJAX
   */
  public function static_signup_trigger(){
    $this->load->library('apiv2_lib');

    //mandatory parameters
    $app_data = $this->input->post('app_data', TRUE);
    $user_email = $this->input->post('email', TRUE);
    $user_first_name = $this->input->post('firstname', TRUE);
    $user_last_name = $this->input->post('lastname', TRUE);
    $user_facebook_id = $this->input->post('user_facebook_id', TRUE);

    $app_data = json_decode(base64_decode($app_data), TRUE);

    $app_id = $app_data['app_id'];
    $app_secret_key = $app_data['app_secret_key'];
    $user_image = "https://graph.facebook.com/{$user_facebook_id}/picture";
    $user_is_player = 1;

    //check args
    if(isset($app_id) && isset($app_secret_key) && $user_facebook_id && $user_email){
      $args = compact('app_id', 'app_secret_key', 'user_facebook_id', 'user_email', 'user_first_name', 'user_last_name', 'user_image', 'user_is_player');
      $signup_result = $this->apiv2_lib->signup($args);

      //show result
      if($signup_result){
        echo json_encode(array('result' => 'ok', 'message' => 'sucessfully sign-up', 'data' => $signup_result));

      } else {
        echo json_encode(array('result' => 'error', 'message' => 'signup error', 'data' => $signup_result));
      }
    }
  }

  /**
   * redirect after sign-up
   */
  public function static_play_app_trigger(){
    $this->load->library('apiv2_lib');

    $facebook_data = array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_app_scope' => $this->config->item('facebook_default_scope'),
          'facebook_channel_url' => $this->facebook->channel_url
    );

    $this->load->vars(array(
      'static_fb_root' => $this->load->view('player/static_fb_root', $facebook_data, TRUE)
    ));

    //view-redirect after signup
    $app_data = $this->input->get('app_data', TRUE);
    $app_data_array = json_decode(base64_decode($app_data), TRUE);

    //print_r($app_data);

    if(!$app_data_array['app_id'])
      $data['app_id'] = 0;
    else
      $data['app_id'] = $app_data_array['app_id'];

    if($user_facebook_data = $this->facebook->getUser()){
      $data['user_data'] = $user_facebook_data;

      $this->load->model('user_model');
      if($user = $this->user_model->get_user_profile_by_user_facebook_id($user_facebook_data['id'])){
        $data['user_data']['sh_user_data'] = $user;
        //login after trigger play_app
        $this->socialhappen->player_login($user['user_id']);
      }
      $app_data_array['user_facebook_id'] = $user_facebook_data['id'];
      $play_app_result = $this->apiv2_lib->play_app($app_data_array);
    }
    redirect('play?app_data='.$app_data.'&play_app_result='.$play_app_result);
  }

  /**
   * Static page
   */
  function static_page() {
    $template = array(
      'title' => 'Welcome to SocialHappen',
      'styles' => array(
        'common/bootstrap',
        'common/bootstrap-responsive',
        'common/bar',
        'common/player'
      ),
      'body_views' => array(
        'common/fb_root' => array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_channel_url' => $this->facebook->channel_url,
          'facebook_app_scope' => $this->config->item('facebook_player_scope')
        ),
        // '../../assets/passport/templates/header/navigation.html' => NULL,
        'bar/plain_bar_view' => array(),
        'player/static_signup_view' => array(),
        'common/vars' => array(
          'vars' => array(
            'base_url' => base_url()
          )
        )
      ),
      'scripts' => array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
        'common/jquery.masonry.min',
        'common/jquery.timeago',
        'common/underscore-min',
        'common/bootstrap.min',
        'common/plain-bar'
      )
    );
    $this->load->view('common/template', $template);
  }
}

/* End of file player.php */
/* Location: ./application/controllers/player.php */