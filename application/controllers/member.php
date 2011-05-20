<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/** 
	 * JSON : get user profile by user_id
	 * @param $user_id
	 * @author Prachya P.
	 * 
	 */
	function json_get_profile($user_id = NULL){
		$this->load->model('user_model','users');
		$profile = $this->users->get_user_profile_by_id($user_id);
		echo json_encode($profile);
	}
}


/* End of file member.php */
/* Location: ./application/controllers/member.php */