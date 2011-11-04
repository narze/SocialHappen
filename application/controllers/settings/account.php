<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($user_id = NULL){
		$this->load->library('settings');
		$setting_name = 'account';
		$this->settings->view_settings($setting_name, $user_id, NULL);
	}
	
	function view($user_id = NULL){
		//$this->socialhappen->ajax_check();
		$this->load->library('form_validation');
		if($user_id && $user_id == $this->socialhappen->get_user_id()){
			$user = $this->socialhappen->get_user();
			$user_facebook = $this->facebook->getUser($user['user_facebook_id']);
		
			$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('about', 'About', 'trim|xss_clean');
			$this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/account', array('user'=>$user,'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id'])));
			}
			else // passed validation proceed to post success logic
			{
				if(set_value('use_facebook_picture')){
					$user_image = issetor($this->facebook->get_profile_picture($user['user_facebook_id']));
				} else if (!$user_image = $this->socialhappen->replace_image('user_image', $user['user_image'])){
					$user_image = $user['user_image'];
				}
			
				// build array for the model
				$user_update_data = array(
								'user_first_name' => set_value('first_name'),
								'user_last_name' => set_value('last_name'),
								'user_about' => set_value('about'),
								'user_image' => $user_image
							);
				$this->load->model('user_model','users');
				if ($this->users->update_user($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/account', array('user'=>array_merge($user,$user_update_data), 'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id']),'success' => TRUE));
				}
				else
				{
					log_message('error','update user failed');
					echo 'error occured';
				}
			}
		}
	}
}
/* End of file account.php */
/* Location: ./application/controllers/settings/account.php */