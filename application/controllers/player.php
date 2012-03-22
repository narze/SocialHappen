<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->presalt = 'tH!s!$Pr3Za|t';
		$this->postsalt = 'di#!zp0s+s4LT';
	}
	
	function index() {
		echo anchor('player/signup', 'Signup Socialhappen').'<br/>';
		echo anchor('player/login', 'Login').'<br/>';
		echo anchor('player/challenge_list', 'View Challenges');
	}

	function signup() {

		$this->load->vars(array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_channel_url' => $this->facebook->channel_url,
			'facebook_default_scope' => $this->config->item('facebook_default_scope')
			)
		);

		if($this->input->get('user_facebook_id') && $this->input->get('token'))
		{
			$this->session->set_userdata(array(
				'user_facebook_id' => $this->input->get('user_facebook_id'),
				'token'=>$this->input->get('token')
			));
		}

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
					'user_is_player' => 1,
					'user_facebook_id' => $this->input->post('user_facebook_id'),
					'user_facebook_access_token' => $this->input->post('token')
				);
					
				$do_not_add = FALSE;
				// run insert model to write data to db
				$this->load->model('user_model');
				if($this->user_model->findOne(array('user_email' => $form_data['user_email']))){
					$this->load->vars('duplicated_email', TRUE);
					$do_not_add = TRUE;
				}
				if($this->user_model->findOne(array('user_phone' => $form_data['user_phone']))){
					$this->load->vars('duplicated_phone', TRUE);
					$do_not_add = TRUE;
				}

				if ($do_not_add) {
					$this->load->view('player/signup_view');
				}
				else if ($user_id = $this->user_model->add_user($form_data)) // the information has therefore been successfully saved in the db
				{
					echo 'Player added';
					$this->socialhappen->player_login($user_id);
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
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email|max_length[100]');			
		$this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'trim|xss_clean|is_numeric|max_length[20]');			
		$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$next = $this->input->get('next');
		$this->load->vars('next', $next ? '?next='.urlencode($next) : '/');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('player/login_view');
		}
		else
		{
     	$email = set_value('email');
     	$mobile_phone_number = set_value('mobile_phone_number');
     	$password = set_value('password');
     	$encrypted_password = sha1($this->presalt.$password.$this->postsalt);
			
			$this->load->model('user_model');
			if($email) {
				$user = $this->user_model->findOne(array(
					'user_email' => $email,
					'user_password' => $encrypted_password
				));
			} else if($mobile_phone_number) {
				$user = $this->user_model->findOne(array(
					'user_phone' => $mobile_phone_number,
					'user_password' => $encrypted_password
				));
			} else {
				$user = FALSE;
				$this->load->vars('email_and_phone_not_entered', TRUE);
			}
					
			// run insert model to write data to db
		
			if ($user) // the information has therefore been successfully saved in the db
			{
				//login process (session)
				$this->socialhappen->player_login($user['user_id']);
				//end login process

				if($next) {
					redirect($next);
				} else {
					redirect('player');
				}
			}
			else
			{
				$this->load->vars('login_failed', TRUE);
				$this->load->view('player/login_view');
			}
		}
	}

	function challenge_list() {
		if($this->socialhappen->is_logged_in_as_player()) {
			$this->load->model('challenge_model');
			//TODO : List player's challenges, not all challenges
			$challenges = $this->challenge_model->get(array());
			$this->load->vars('challenges', $challenges);
			$this->load->view('player/challenge_list_view');
		} else {
			redirect('player');
		}
	}

	function challenge($challenge_id) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('_id' => new MongoId($challenge_id)))) {
			echo '<pre>';
			var_export($challenge);
			echo '</pre>';
			$this->load->vars(
				array(
					'challenge_id' => $challenge_id,
					'challenge' => $challenge,
					'player_logged_in' => $this->socialhappen->is_logged_in_as_player()
				)
			);
			$this->load->view('player/challenge_view');
		} else {
			show_error('Challenge Invalid', 404);
		}
	}
}  

/* End of file player.php */
/* Location: ./application/controllers/player.php */