<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_user_class extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index(){

	}
}
/* End of file page_user_class.php */
/* Location: ./application/controllers/settings/page_user_class.php */