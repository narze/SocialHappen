<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends CI_Controller {
  
  function __construct(){
    parent::__construct();
    $this->presalt = 'tH!s!$Pr3Za|t';
    $this->postsalt = 'di#!zp0s+s4LT';
  }

  /**
   * Signup page
   */
  function index() {
    if($user = $this->socialhappen->get_user()) {
      //Logged in already
      if($next = $this->input->get('next')) {
        redirect($next);
      }

      if($user['user_is_player']) {
        redirect('player');
      } else {
        redirect('');
      }
    } else if(($facebook_user = $this->facebook->getUser()) && $this->socialhappen->login()) {
      if($next = $this->input->get('next')) {
        redirect($next);
      }

      if($user['user_is_player']) {
        redirect('player');
      } else {
        redirect('');
      }
    } else {
      //signup with/without facebook
      if($this->socialhappen->is_logged_in()) { redirect('player'); }

      // $data = array(
      //   'header' => $this->socialhappen->get_header_bootstrap( 
      //     array(
      //       'title' => 'Signup',
      //       'script' => array(
      //         //'common/functions',
      //         //'common/jquery.form',
      //         'common/bar',
      //         //'common/fancybox/jquery.fancybox-1.3.4.pack',
      //         //'home/lightbox',
      //         //'payment/payment'
      //       ),
      //       'style' => array(
      //         'common/player',
      //         //'common/platform',
      //         //'common/main',
      //         //'common/fancybox/jquery.fancybox-1.3.4'
      //       )
      //     )
      //   )
      // );

      // $this->load->vars(array(
      //   'facebook_default_scope' => $this->config->item('facebook_default_scope')
      //   )
      // );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[100]');     
      $this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'required|trim|xss_clean|is_numeric|max_length[20]');     
      $this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');      
      $this->form_validation->set_rules('password_again', 'Password Again', 'required|trim|xss_clean|max_length[50]');
        
      $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
    
      $duplicated = FALSE;
      $user_email = set_value('email');
      $user_phone = set_value('mobile_phone_number');

      //Try to find if email or phone is already exists
      $this->load->model('user_model');
      if($this->user_model->findOne(array('user_email' => $user_email))){
        $this->load->vars('duplicated_email', TRUE);
        $duplicated = TRUE;
      }
      if($this->user_model->findOne(array('user_phone' => $user_phone))){
        $this->load->vars('duplicated_phone', TRUE);
        $duplicated = TRUE;
      }

      if ($duplicated || ($this->form_validation->run() == FALSE)) // validation hasn't been passed
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
          'signup/signup_view' => array(
            'facebook_user' => $facebook_user,
            'next' => ($next = $this->input->get('next'))
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

      // $data['facebook_user'] = $this->facebook->getUser();
      //   $this->parser->parse('player/signup_view', $data);
      }
      else // passed validation proceed to post success logic
      {
        // build array for the model
        $password = set_value('password');
        $password_again = set_value('password_again');
        if($password !== $password_again) {
          $this->load->vars('password_not_match', TRUE);
          $this->parser->parse('player/signup_view', $data);
        } else {
          $facebook_user_id = $this->FB->getUser();
          $encrypted_password = sha1($this->presalt.$password.$this->postsalt);
          $form_data = array(
            'user_email' => set_value('email'),
            'user_phone' => set_value('mobile_phone_number'),
            'user_password' => $encrypted_password,
            'user_is_player' => 1,
            'user_facebook_id' => $facebook_user_id,
            'user_facebook_access_token' => $this->FB->getAccessToken(),
            'user_image' => $facebook_user_id ? 
              $this->facebook->get_profile_picture($facebook_user_id) 
              : base_url().'assets/images/default/user.png',
            'user_first_name' => issetor($facebook_user['first_name'], NULL),
            'user_last_name' => issetor($facebook_user['last_name'], NULL),
          );
            

          if ($user_id = $this->user_model->add_user($form_data)) // the information has therefore been successfully saved in the db
          {
            $this->socialhappen->login($user_id);

            if($next = $this->input->get('next')) {
              redirect($next);
            } else {
              redirect('player');
            }
          }
          else
          {
            echo 'An error occurred saving your information. Please try again later';
          // Or whatever error handling is necessary
          }
        }
      }
    }
  }
}