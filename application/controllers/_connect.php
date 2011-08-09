<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Connect extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
 	
	/**
	 * redirect to specific URL
	 */
	function redirect($redirect, $type = NULL)
	{	
				
		$data['facebook_app_id'] = $this->config->item('facebook_app_id');
		$data['facebook_default_scope'] = $this->config->item('facebook_default_scope');
		$data['redirect'] = $redirect;
		
		$this->load->view('connect_view',$data);
		
	}
	
	/**
	 * 
	 */
	function collect_data($redirect = ''){
		$redirect = implode("/", explode("+", $redirect));
		
		$this->load->library('facebook');
		$user_facebook_id = $this->facebook->getUser();
		$user_facebook_id = $user_facebook_id['id'];
		
		$this->load->model('user_model', 'user');
		$this->user->add_by_facebook_id($user_facebook_id);
		$this->user->update_user_last_seen($user_facebook_id);
		
		redirect(site_url($redirect));
	}

}

/* End of file connect.php */
/* Location: ./application/controllers/connect.php */   