<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_signup_form extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index(){

	}
}
/* End of file page_signup_form.php */
/* Location: ./application/controllers/settings/page_signup_form.php */