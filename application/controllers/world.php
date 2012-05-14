<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class World extends CI_Controller {
  
  function __construct(){
    parent::__construct();
  }
  
  /**
   * Index page (for debugging purpose)
   */
  function index() {
    
    $logged_in = $this -> socialhappen -> is_logged_in();
    
    if ($logged_in){ // see current user's world
      redirect('assets/world');
    }else{
      redirect('login?next=world');
    }
    
    
  }
}

/* End of file World.php */
/* Location: ./application/controllers/World.php */