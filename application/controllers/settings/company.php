<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
		// $this -> socialhappen -> check_logged_in();
	}
	
	function index(){
		echo true;
	}
}
/* End of file settings.php */
/* Location: ./application/controllers/settings.php */