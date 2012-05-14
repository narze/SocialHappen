<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends CI_Controller {
  
  function __construct(){
    parent::__construct();
    $this->presalt = 'tH!s!$Pr3Za|t';
    $this->postsalt = 'di#!zp0s+s4LT';
  }

  /**
   * Signup landing page
   */
  function index() {
    $next = $this->socialhappen->strip_next_from_url($this->input->get('next'));
    if($this->socialhappen->get_user()) {
      //Logged in already
      if($next) {
        redirect($next);
      }

      redirect('player/play');
    } else {
      $template = array(
        'title' => 'Signup',
        'styles' => array(
          'common/bootstrap',
          'common/bootstrap-responsive',
          'common/player'
        ),
        'body_views' => array(
          'common/fb_root' => array(
            'facebook_app_id' => $this->config->item('facebook_app_id'),
            'facebook_channel_url' => $this->facebook->channel_url,
            'facebook_app_scope' => $this->config->item('facebook_player_scope')
          ),
          'bar/plain_bar_view' => array(),
          'signup/signup_view' => array(
            'next' => $next ? "?next={$next}" : ''
          ),
          'common/vars' => array(
            'vars' => array(
              'base_url' => base_url()
            )
          )
        ),
        'scripts' => array(
          'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
          'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
          'common/jquery.timeago',
          'common/underscore-min',
          'common/bootstrap.min',
          'common/plain-bar',
          'signup/signup'
        )
      );
      $this->load->view('common/template', $template);
    }
  }

  /**
   * Signup (with email) page 
   */
  function form() {
    $next = $this->socialhappen->strip_next_from_url($this->input->get('next'));
    if($this->socialhappen->get_user()) {
      //Logged in already
      if($next) {
        redirect($next);
      }

      redirect('player/play');
    } else {
      $this->load->library('form_validation');
      $this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');     
      $this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');     
      $this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[100]');     
      // $this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'required|trim|xss_clean|is_numeric|max_length[20]');     
      $this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');      
      $this->form_validation->set_rules('password_again', 'Password Again', 'required|trim|xss_clean|max_length[50]');
      $this->form_validation->set_rules('timezone', 'Timezone', 'required|trim|xss_clean');
        
      $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
    
      $duplicated = FALSE;
      $user_email = set_value('email');
      // $user_phone = set_value('mobile_phone_number');

      //Try to find if email or phone is already exists
      $this->load->model('user_model');
      if($user_email && $this->user_model->findOne(array('user_email' => $user_email))){
        $this->load->vars('duplicated_email', TRUE);
        $duplicated = TRUE;
      }
      // if($user_phone && $this->user_model->findOne(array('user_phone' => $user_phone))){
      //   $this->load->vars('duplicated_phone', TRUE);
      //   $duplicated = TRUE;
      // }

      //Check password
      $password_mismatch = FALSE;
      $password = set_value('password');
      $password_again = set_value('password_again');
      if($password !== $password_again) {
        $this->load->vars('password_not_match', TRUE);
        $password_mismatch = TRUE;
      }

      if ($duplicated || $password_mismatch ||($this->form_validation->run() == FALSE)) // validation hasn't been passed
      {
        $template = array(
          'title' => 'Signup',
          'styles' => array(
            'common/bootstrap',
            'common/bootstrap-responsive',
            'common/player'
          ),
          'body_views' => array(
            'common/fb_root' => array(
              'facebook_app_id' => $this->config->item('facebook_app_id'),
              'facebook_channel_url' => $this->facebook->channel_url,
              'facebook_app_scope' => $this->config->item('facebook_player_scope')
            ),
            'bar/plain_bar_view' => array(),
            'signup/signup_form' => array(
              'next' => $next ? "?next={$next}" : ''
            ),
            'common/vars' => array(
              'vars' => array(
                'base_url' => base_url()
              )
            )
          ),
          'scripts' => array(
            'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
            'common/jquery.timeago',
            'common/underscore-min',
            'common/bootstrap.min',
            'common/jstz.min',
            'common/plain-bar',
            'signup/form'
          )
        );
        $this->load->view('common/template', $template);

      }
      else
      {   
          $timezone = set_value('timezone');
          $user_timezone = $timezone ? $timezone : 'UTC';
          $this->load->library('timezone_lib');
          if(!$minute_offset = $this->timezone_lib->get_minute_offset_from_timezone($user_timezone)){
            $minute_offset = 0;
          }

          $encrypted_password = sha1($this->presalt.$password.$this->postsalt);
          $form_data = array(
            'user_email' => set_value('email'),
            // 'user_phone' => $user_phone,
            'user_password' => $encrypted_password,
            'user_is_player' => 1,
            'user_image' => base_url().'assets/images/default/user.png',
            'user_first_name' => set_value('first_name'),
            'user_last_name' => set_value('last_name'),
            'user_timezone_offset' => $minute_offset
          );
            

          if ($user_id = $this->user_model->add_user($form_data))
          {
            $this->socialhappen->player_login($user_id);

            if($next) {
              redirect($next);
            } else {
              redirect('player/play');
            }
          }
          else
          {
            echo 'An error occurred saving your information. Please try again later';
          }
        
      }
    }
  }

  /**
   * Signup with facebook
   */
  function facebook() {
    $next = $this->socialhappen->strip_next_from_url($this->input->get('next'));
    if(($this->socialhappen->get_user()) || 
      (($facebook_user = $this->facebook->getUser()) && $this->socialhappen->login())) {
      //Logged in already
      if($next) {
        redirect($next);
      }

      redirect('player/play');
    } else if(isset($facebook_user['id'])) {
      $facebook_user_id = $facebook_user['id'];
      $form_data = array(
        'user_is_player' => 1,
        'user_facebook_id' => $facebook_user_id,
        'user_facebook_access_token' => $this->FB->getAccessToken(),
        'user_image' => $this->facebook->get_profile_picture($facebook_user_id),
        'user_first_name' => $facebook_user['first_name'],
        'user_last_name' => $facebook_user['last_name']
      );

      if(isset($facebook_user['email'])) {
        $form_data['user_email'] = $facebook_user['email'];
      }

      if(isset($facebook_user['location']['name'])) {
        $form_data['user_location'] = $facebook_user['location']['name'];
      }

      if(isset($facebook_user['locale'])) {
        $form_data['user_locale'] = $facebook_user['locale'];
      }
      
      $this->load->model('user_model');
      if ($user_id = $this->user_model->add_user($form_data))
      {
        $this->socialhappen->player_login($user_id);

        if($next) {
          redirect($next);
        } else {
          redirect('player/play');
        }
      }
      else
      {
        echo 'An error occurred saving your information. Please try again later';
      }
    } else {
      redirect('signup'.$next ? "?next={$next}" : '');
    }
  }
}