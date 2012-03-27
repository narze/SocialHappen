<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->presalt = 'tH!s!$Pr3Za|t';
		$this->postsalt = 'di#!zp0s+s4LT';
	}
	
	function index() {

		if(!$this->socialhappen->is_logged_in_as_player()) { redirect('player/login'); }

		$data = array(
			'header' => $this -> socialhappen -> get_header_bootstrap( 
				array(
					'title' => 'Applications',
					'script' => array(
						//'common/functions',
						//'common/jquery.form',
						'common/bar',
						//'common/fancybox/jquery.fancybox-1.3.4.pack',
						//'home/lightbox',
						//'payment/payment'
					),
					'style' => array(
						'common/player',
						//'common/platform',
						//'common/main',
						//'common/fancybox/jquery.fancybox-1.3.4'
					)
				)
			),
			'user' => $user
		);

		if(isset($user['user_facebook_id']) && isset($user['user_facebook_access_token'])) {
			$facebook_connected = TRUE;
		} else {
			$facebook_connected = FALSE;
		}
		$this->load->vars(
			array(
				'player_logged_in' => $this->socialhappen->is_logged_in_as_player(),
				'facebook_connected' => $facebook_connected
			)
		);
		
		$this->parser->parse('player/index_view', $data);
	}

	function signup() {

		$data = array(
			'header' => $this -> socialhappen -> get_header_bootstrap( 
				array(
					'title' => 'Applications',
					'script' => array(
						//'common/functions',
						//'common/jquery.form',
						'common/bar',
						//'common/fancybox/jquery.fancybox-1.3.4.pack',
						//'home/lightbox',
						//'payment/payment'
					),
					'style' => array(
						'common/player',
						//'common/platform',
						//'common/main',
						//'common/fancybox/jquery.fancybox-1.3.4'
					)
				)
			)
		);

		$this->load->vars(array(
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
			$data['facebook_user'] = $this->facebook->getUser();
			$this->parser->parse('player/signup_view', $data);
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			$password = set_value('password');
			$password_again = set_value('password_again');
			if($password !== $password_again) {
				$this->load->vars('password_not_match', TRUE);
				$this->parser->parse('player/signup_view', $data);
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
					$this->parser->parse('player/signup_view', $data);
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

		if($this->socialhappen->is_logged_in_as_player()) { redirect('player'); }

		$data = array(
			'header' => $this -> socialhappen -> get_header_bootstrap( 
				array(
					'title' => 'Applications',
					'script' => array(
						//'common/functions',
						//'common/jquery.form',
						'common/bar',
						//'common/fancybox/jquery.fancybox-1.3.4.pack',
						//'home/lightbox',
						//'payment/payment'
					),
					'style' => array(
						'common/player',
						//'common/platform',
						//'common/main',
						//'common/fancybox/jquery.fancybox-1.3.4'
					)
				)
			)
		);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|valid_email|max_length[100]');			
		$this->form_validation->set_rules('mobile_phone_number', 'Mobile Phone Number', 'trim|xss_clean|is_numeric|max_length[20]');			
		$this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|max_length[50]');
			
		$this->form_validation->set_error_delimiters('<span class="help-block">', '</span>');
	
		$next = $this->input->get('next');
		$this->load->vars('next', $next ? '?next='.urlencode($next) : '/');
		if ($this->form_validation->run() == FALSE)
		{
			$this->parser->parse('player/login_view', $data);
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
				$this->parser->parse('player/login_view', $data);
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

	function challenging_list() {
		$user_id = $this->socialhappen->get_user_id();
		$this->load->library('user_lib');
		$user = $this->user_lib->get_user($user_id);
		if(isset($user['challenge'])) {
			// echo '<pre>';
			// var_dump($user['challenge']);
			// echo '</pre>';
			foreach($user['challenge'] as &$challenge) {
				$challenge = new MongoId($challenge);
			} unset($challenge);
			$this->load->model('challenge_model');
			$challenges = $this->challenge_model->get(array('_id' => array('$in' => $user['challenge'])));
			$this->load->vars('challenges', $challenges);
			$this->load->view('player/challenge_list_view');
		} else {
			echo 'You did not join any challenge';
		}
	}

	function challenge($challenge_id) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('_id' => new MongoId($challenge_id)))) {
			$this->load->library('user_lib');
			$user_id = $this->socialhappen->get_user_id();
			$user = $this->user_lib->get_user($user_id);
			$player_challenging = isset($user['challenge']) && in_array($challenge_id, $user['challenge']);
			
			$this->load->library('challenge_lib');
			$challenge_progress = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);

			$this->load->vars(
				array(
					'challenge_id' => $challenge_id,
					'challenge' => $challenge,
					'player_logged_in' => $this->socialhappen->is_logged_in_as_player(),
					'player_challenging' => $player_challenging,
					'challenge_progress' => $challenge_progress
				)
			);
			$this->load->view('player/challenge_view');
		} else {
			show_error('Challenge Invalid', 404);
		}
	}

	function settings() {
		if($this->socialhappen->is_logged_in_as_player()) {
			$user = $this->socialhappen->get_user();
			if(issetor($user['user_facebook_id']) && issetor($user['user_facebook_access_token'])) {
				$facebook_connected = TRUE;
			} else {
				$facebook_connected = FALSE;
			}
			$this->load->vars(array('facebook_connected' => $facebook_connected));
			$this->load->view('player/settings_view');
			
		} else {
			redirect('player');
		}
	}

	function connect_facebook() {
		if(($user_facebook_id = $this->FB->getUser()) && 
				($user_facebook_id == $this->input->get('user_facebook_id')) && 
				($token = $this->input->get('token'))){
			$connecting_facebook = TRUE;
			$this->load->model('user_model');
			$this->user_model->update_user($this->socialhappen->get_user_id(), array(
				'user_facebook_id' => $user_facebook_id,
				'user_facebook_access_token' => $token
			));
		} else {
			$connecting_facebook = FALSE;
		}

		$user = $this->socialhappen->get_user();
		if($connecting_facebook || (issetor($user['user_facebook_id']) && issetor($user['user_facebook_access_token']))) {
			redirect('player/settings');
		}

		$this->load->vars(array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_channel_url' => $this->facebook->channel_url,
			'facebook_default_scope' => $this->config->item('facebook_default_scope'),
			'facebook_connected' => $connecting_facebook
			)
		);
		$this->load->view('player/connect_facebook_view');

	}

	function disconnect_facebook() {
		if($this->socialhappen->is_logged_in_as_player()) {
			$user_id = $this->socialhappen->get_user_id();
			$this->load->model('user_model');
			$this->user_model->update_user($user_id, array('user_facebook_id' => NULL, 'user_facebook_access_token' => NULL));
			echo 'Disconnected from facebook ',anchor('player/settings', 'Back'); 
		} else {
			redirect('player');
		}
	}

	function join_challenge($challenge_id = NULL) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('_id' => new MongoId($challenge_id)))) {
			$user_id = $this->socialhappen->get_user_id();
			$this->load->library('user_lib');
			if($this->user_lib->join_challenge($user_id, $challenge_id)) {
				echo 'Challenge joined';
				echo anchor('player/challenge/'.$challenge_id, 'Back');
			} else {
				echo 'Challenge join error';
			}
		} else {
			show_error('Challenge Invalid', 404);
		}
	}
}  

/* End of file player.php */
/* Location: ./application/controllers/player.php */