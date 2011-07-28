<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Logout page
	 * @author Manassarn M.
	 */
	function index(){
		$this->socialhappen->logout();
		$this->load->view('logout_view',array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'redirect_url' => $this->input->get('redirect')
		));
	}
}  

/* End of file logout.php */
/* Location: ./application/controllers/logout.php */