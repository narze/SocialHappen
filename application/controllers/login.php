<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

  function __construct(){
    parent::__construct();
    $this->presalt = 'tH!s!$Pr3Za|t';
    $this->postsalt = 'di#!zp0s+s4LT';
  }

  /**
   * Login page
   */
  function index() {
    $next = $this->socialhappen->get_next_url();
    if($user = $this->socialhappen->get_user()) {
      //Logged in already
      if($next) {
        redirect($next);
      }

      redirect('play');
    } else if(($facebook_user = $this->facebook->getUser()) && $this->socialhappen->login()) {
      if($next) {
        redirect($next);
      }

      redirect('play');
    } else {
      //Login form

      $this->load->library('form_validation');
      $this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[100]');
      // $this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'trim|xss_clean|is_numeric|max_length[20]');
      $this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');

      $this->form_validation->set_error_delimiters('<span class="help-block">', '</span>');

      $this->load->vars('next', $next ? '?next='.urlencode($next) : '/');
      if ($this->form_validation->run() == FALSE) {

        $template = array(
          'title' => 'Login',
          'vars' => array(),
          'scripts' => array(
            'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
            'common/jquery.timeago',
            'common/bootstrap.min',
            'common/underscore-min',
            'common/plain-bar'
          ),
          'styles' => array(
            'common/bootstrap.min',
            'common/bootstrap-responsive.min',
            'common/bar',
            'common/player'
          ),
          'body_views' => array(
            'common/fb_root' => array(
              'facebook_app_id' => $this->config->item('facebook_app_id'),
              'facebook_channel_url' => $this->facebook->channel_url,
              'facebook_app_scope' => $this->config->item('facebook_player_scope')
            ),
            'bar/plain_bar_view' => array(),
            'login/login_view' => array(
              'next' => $next ? "?next={$next}" : ''
            ),
            'common/vars' => array(
              'vars' => array(
                'base_url' => base_url()
              )
            )
          )
        );
        $this->load->view('common/template', $template);
      } else {
        $email = set_value('email');
        // $mobile_phone_number = set_value('mobile_phone_number');

        $password = set_value('password');
        $encrypted_password = sha1($this->presalt.$password.$this->postsalt);

        $this->load->model('user_model');
        if($email) {
          $user = $this->user_model->findOne(array(
            'user_email' => $email,
            'user_password' => $encrypted_password
          ));
        // } else if($mobile_phone_number) {
        //   $user = $this->user_model->findOne(array(
        //     'user_phone' => $mobile_phone_number,
        //     'user_password' => $encrypted_password
        //   ));
        } else {
          $user = FALSE;
          $this->load->vars('email_and_phone_not_entered', TRUE);
        }

        // run insert model to write data to db

        if ($user) // the information has therefore been successfully saved in the db
        {
          //login process (session)
          $this->socialhappen->player_login($user['user_id']);
          //end login process

          if($next) {
            redirect($next);
          } else {
            redirect('play');
          }
        }
        else
        {
          $this->load->vars('login_failed', TRUE);

          $template = array(
            'title' => 'Login',
            'vars' => array(),
            'scripts' => array(
              'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
              'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
              'common/jquery.timeago',
              'common/bootstrap.min'
            ),
            'styles' => array(
              'common/bootstrap.min',
              'common/bootstrap-responsive.min',
              'common/bar',
              'common/player'
            ),
            'body_views' => array(
              'common/fb_root' => array(
                'facebook_app_id' => $this->config->item('facebook_app_id'),
                'facebook_channel_url' => $this->facebook->channel_url,
                'facebook_app_scope' => $this->config->item('facebook_player_scope')
              ),
              'bar/plain_bar_view' => array(),
              'login/login_view' => array(
                'next' => $next ? "?next={$next}" : ''
              )
            )
          );
          $this->load->view('common/template', $template);
        }
      }
    }
  }

  function success() {
    $redirect = $this->input->get('redirect');
    redirect($redirect);
  }
}