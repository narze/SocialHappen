<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userimage extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		redirect();
	}

	/**
	 * Routes with base_url('userimage/:id')
	 */
	function get_user_image($user_id = NULL) {
		$this->load->model('user_model');
		$user = $this->user_model->get_user_profile_by_user_id($user_id);
		$user_image = $user['user_image'];
		redirect($user_image);
	}
}

/* End of file userimage.php */
/* Location: ./application/controllers/userimage.php */