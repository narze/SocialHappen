<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index(){

	}
}
/* End of file campaign.php */
/* Location: ./application/controllers/settings/campaign.php */