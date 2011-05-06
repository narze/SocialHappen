<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Landing extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
		//$this->facebook->authentication($this->uri->uri_string());
	}

	function index(){
		
		$data['authenticate'] = $this->facebook->is_authentication();
		
		if($data['authenticate']){
			
		}else{		
			$data['facebook_app_id'] = $this->config->item('facebook_app_id');
			$data['facebook_default_scope'] = $this->config->item('facebook_default_scope');
		}
		
		$this->load->view('landing_views/landing_view',$data);
	}
	 
}  

/* End of file landing.php */
/* Location: ./application/controllers/landing.php */