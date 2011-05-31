<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
		//$this->facebook->authentication($this->uri->uri_string());
	}

	/**
	 * Home page
	 * @author Manassarn M.
	 */
	function index(){
		
		$data['authenticate'] = $this->facebook->is_authentication();
		
		if($data['authenticate']){
			$facebook_user = $this->facebook->getUser();
			$this->load->model('user_model','users');
			if($profile = $this->users->get_user_profile_by_user_facebook_id($facebook_user['id'])){
				var_dump($profile);
			} else {
				redirect('signup');
			}
		}else{		
			$data['facebook_app_id'] = $this->config->item('facebook_app_id');
			$data['facebook_default_scope'] = $this->config->item('facebook_default_scope');
		}
		
		$this->load->view('home_view',$data);
	}
	 
}  

/* End of file home.php */
/* Location: ./application/controllers/home.php */