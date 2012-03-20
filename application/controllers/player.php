<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->presalt = 'tH!s!$Pr3Za|t';
		$this->postsalt = 'di#!zp0s+s4LT';
	}
	
	function index() {
		echo anchor('player/signup', 'Signup Socialhappen');
	}

	function signup() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[100]');			
		$this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'required|trim|xss_clean|is_numeric|max_length[20]');			
		$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');			
		$this->form_validation->set_rules('password_again', 'Password Again', 'required|trim|xss_clean|max_length[50]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('player/signup_view');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			$password = set_value('password');
			$password_again = set_value('password_again');
			if($password !== $password_again) {
				$this->load->vars('password_not_match', TRUE);
				$this->load->view('player/signup_view');
			} else {
				$encrypted_password = sha1($this->presalt.$password.$this->postsalt);
				$form_data = array(
	       	'user_email' => set_value('email'),
	       	'user_phone' => set_value('mobile_phone_number'),
	       	'user_password' => $encrypted_password,
	       	'user_is_player' => 1
				);
					
				$do_not_add = FALSE;
				// run insert model to write data to db
				$this->load->model('user_model');
				if($this->user_model->find(array('user_email' => $form_data['user_email']))){
					$this->load->vars('duplicated_email', TRUE);
					$do_not_add = TRUE;
				}
				if($this->user_model->find(array('user_phone' => $form_data['user_phone']))){
					$this->load->vars('duplicated_phone', TRUE);
					$do_not_add = TRUE;
				}

				if ($do_not_add) {
					$this->load->view('player/signup_view');
				}
				else if ($this->user_model->add_user($form_data)) // the information has therefore been successfully saved in the db
				{
					echo 'Player added';
				}
				else
				{
					echo 'An error occurred saving your information. Please try again later';
				// Or whatever error handling is necessary
				}
			}
		}
	}

	function login() {

	}
}  

/* End of file player.php */
/* Location: ./application/controllers/player.php */