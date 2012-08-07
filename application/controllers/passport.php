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
    if($user_id || ($user_id = $this->socialhappen->get_user_id())) {
      return redirect('assets/passport/#/profile/'.$user_id);
    }

    redirect('login?next=passport');
  }
}

/* End of file Passport.php */
/* Location: ./application/controllers/Passport.php */