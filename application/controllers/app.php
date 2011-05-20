<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/** 
	 * JSON : get app profile by app_install_id
	 * @param $user_id
	 * @author Prachya P.
	 * 
	 */
	function json_get_app_profile_by_id($app_install_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$profile = $this->installed_apps->get_app_profile_by_id($app_install_id);
		echo json_encode($profile);
	}
	

}


/* End of file app.php */
/* Location: ./application/controllers/app.php */