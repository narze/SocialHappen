<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Passport extends CI_Controller {
  
  function __construct(){
    parent::__construct();
    $this->presalt = 'tH!s!$Pr3Za|t';
    $this->postsalt = 'di#!zp0s+s4LT';
  }
  
  /**
   * Index page (for debugging purpose)
   */
  function index($user_id = NULL) {
    
    $logged_in = $this -> socialhappen -> is_logged_in();
    
    if (!$user_id && $logged_in){ // see current user's passport
      $user = $this->socialhappen->get_user();
      redirect('assets/passport#/profile/' . $user['user_id']);
    }else if($user_id){ // see specific passport
      redirect('assets/passport#/profile/' . $user_id);
    }else{
      redirect('player/login');
    }
    
    
  }
}

/* End of file Passport.php */
/* Location: ./application/controllers/Passport.php */