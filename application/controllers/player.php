<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->presalt = 'tH!s!$Pr3Za|t';
		$this->postsalt = 'di#!zp0s+s4LT';
	}
	
	/**
	 * Index page (for debugging purpose)
	 */
	function index() {

		if(!$this->socialhappen->is_logged_in()) { redirect('login'); }

		$user = $this->socialhappen->get_user();

		$data = array(
			'header' => $this->socialhappen->get_header_bootstrap( 
				array(
					'title' => 'Player',
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

		//get company
		$this->load->model('challenge_model');
		$companies = $this->challenge_model->get_distinct_company();
		$this->load->model('company_model');
		foreach ($companies as &$company_id) {
			$company_id = $this->company_model->get_company_profile_by_company_id($company_id);
		}
		$this->load->vars('companies', $companies);


		if(isset($user['user_facebook_id']) && isset($user['user_facebook_access_token'])) {
			$facebook_connected = TRUE;
		} else {
			$facebook_connected = FALSE;
		}
		$this->load->library('user_lib');
		$this->load->vars(
			array(
				'player_logged_in' => $this->socialhappen->is_logged_in(),
				'facebook_connected' => $facebook_connected,
				'user' => $this->user_lib->get_user($user['user_id'])
			)
		);

		$this->parser->parse('player/index_view', $data);
	}
	
	/**
	 * View all challenges
	 */
	function challenge_list($company_id) {
		if($this->socialhappen->is_logged_in() && $company_id) {
			
			

			$this->load->model('company_model');
			$this->load->model('challenge_model');
			$company = $this->company_model->get_company_profile_by_company_id($company_id);
			//TODO : List player's challenges, not all challenges
			$this->load->vars(
				array(
					'company' => $company,
					'challenges' => $this->challenge_model->get(array('company_id' => (int) $company_id))
				)
			);

			$data = array(
				'header' => $this->socialhappen->get_header_bootstrap( 
					array(
						'title' => $company['company_name'],
						'script' => array(
							'common/bar',
						),
						'style' => array(
							'common/player',
						)
					)
				)
			);

			$this->parser->parse('player/challenge_list_view', $data);
		} else {
			redirect('player');
		}
	}

	/**
	 * View challenges that you are challenging
	 */
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

	/**
	 * Challenge landing
	 */
	function challenge($challenge_hash) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) {
			//echo '<pre>'; var_dump($challenge); echo '</pre>';
			$challenge_id = get_mongo_id($challenge);
			$this->load->library('user_lib');
			$user_id = $this->socialhappen->get_user_id();
			$user = $this->user_lib->get_user($user_id);
			$player_challenging = isset($user['challenge']) && in_array($challenge_hash, $user['challenge']);
			
			
			//challenge_progress
			if($user_id) {
				$this->load->library('challenge_lib');
				$challenge_progress = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
				$challenge_done = TRUE;
				if($challenge_progress) {
					foreach($challenge_progress as $action) {
						if(!$action['action_done']) {
							$challenge_done = FALSE;
						}
					}
				} else {
					$challenge_done = FALSE;
				}
			} else {
				$challenge_done = FALSE;
			}
			
			//Challenge Duration
			if($current_user = $this->socialhappen->get_user($user_id)) {
				$this->load->library('timezone_lib');
				$challenge['start_time'] = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['start']), $current_user['user_timezone_offset']);
				$challenge['end_time'] = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['end']), $current_user['user_timezone_offset']);

			} else {
				$challenge['start_time'] = date('Y-m-d H:i:s', $challenge['start']);
				$challenge['end_time'] = date('Y-m-d H:i:s', $challenge['end']);
			}

			$this->load->vars(
				array(
					'challenge_hash' => $challenge_hash,
					'challenge' => $challenge,
					'player_logged_in' => $this->socialhappen->is_logged_in(),
					'player_challenging' => $player_challenging,
					'challenge_done' => $challenge_done,
					'redeem_pending' => isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming']),
				)
			);

			$data = array(
				'header' => $this->socialhappen->get_header_bootstrap( 
					array(
						'title' => $challenge['detail']['name'],
						'script' => array(
							'common/bar',
						),
						'style' => array(
							'common/player',
						)
					)
				)
			);

			$this->parser->parse('player/challenge_view', $data);
		} else {
			show_error('Challenge Invalid', 404);
		}
	}

	/**
	 * Challenge action
	 */
	function challenge_actions($challenge_hash) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) {
			
			$challenge_id = get_mongo_id($challenge);
			$this->load->library('user_lib');
			$user_id = $this->socialhappen->get_user_id();
			$user = $this->user_lib->get_user($user_id);
			$player_challenging = isset($user['challenge']) && in_array($challenge_hash, $user['challenge']);
			
			//challenge_progress
			if($user_id) {
				$this->load->library('challenge_lib');
				$challenge_progress = $this->challenge_lib->get_challenge_progress($user_id, $challenge_id);
				$challenge_done = TRUE;
				foreach($challenge_progress as $action) {
					if(!$action['action_done']) {
						$challenge_done = FALSE;
					}
				}
			} else {
				$challenge_progress = FALSE;
				$challenge_done = FALSE;
			}

			$this->load->vars(
				array(
					'challenge_hash' => $challenge_hash,
					'challenge' => $challenge,
					'player_logged_in' => $this->socialhappen->is_logged_in(),
					'player_challenging' => $player_challenging,
					'challenge_progress' => $challenge_progress,
					'challenge_done' => $challenge_done,
					'redeem_pending' => isset($user['challenge_redeeming']) && in_array($challenge_id, $user['challenge_redeeming']),
				)
			);

			$data = array(
				'header' => $this->socialhappen->get_header_bootstrap( 
					array(
						'title' => $challenge['detail']['name'],
						'script' => array(
							'common/bar',
						),
						'style' => array(
							'common/player',
						)
					)
				)
			);

			$this->parser->parse('player/challenge_actions_view', $data);
		} else {
			show_error('Challenge Invalid', 404);
		}
	}

	/**
	 * View player's setting
	 */
	function settings() {
		if($this->socialhappen->is_logged_in()) {
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

	/**
	 * Connect to facebook
	 */
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

	/** 
	 * Disconnect from facebook
	 */
	function disconnect_facebook() {
		if($this->socialhappen->is_logged_in()) {
			$user_id = $this->socialhappen->get_user_id();
			$this->load->model('user_model');
			$this->user_model->update_user($user_id, array('user_facebook_id' => NULL, 'user_facebook_access_token' => NULL));
			echo 'Disconnected from facebook ',anchor('player/settings', 'Back'); 
		} else {
			redirect('player');
		}
	}

	/**
	 * Make the current user joins the challenge
	 */
	function join_challenge($challenge_hash = NULL) {
		$this->load->library('challenge_lib');
		if($challenge = $this->challenge_lib->get_by_hash($challenge_hash)) {
			$user_id = $this->socialhappen->get_user_id();
			$this->load->library('user_lib');
			if($this->user_lib->join_challenge($user_id, $challenge_hash)) {
				//echo 'Challenge joined';
				//echo anchor('player/challenge/'.$challenge_hash, 'Back');
				redirect('player/challenge_actions/'.$challenge_hash);
			} else {
				echo 'Challenge join error';
			}
		} else {
			show_error('Challenge Invalid', 404);
		}
	}

	/**
	 * Logout and redirect to index
	 */
	function logout() {
		$this->socialhappen->logout();
		redirect('player');
	}

	/**
	 * View redeem pending list (for merchant only)
	 */
	function merchant_redeem_pending_list() {
		$company_id = NULL; //TODO
		if($user = $this->socialhappen->get_user()) {
			if($user['user_is_player']) {
				echo 'You are not merchant';
			} else {
				$this->load->library('challenge_lib');
				$this->load->model('user_mongo_model');
				$challenges = $this->challenge_lib->get(array()); //TODO search using company id
				$challenge_ids = array();
				foreach($challenges as $challenge) {
					$challenge_ids[] = get_mongo_id($challenge);
				}
				$redeeming_users = $this->user_mongo_model->get(array(
					'challenge_redeeming' => array(
						'$in' => $challenge_ids
						)
					)
				);
				$this->load->vars(array(
					'redeeming_users' => $redeeming_users
				));
				$this->load->view('player/merchant_redeem_pending_list');
			}
		} else {
			redirect('player');
		}
	}

	/**
	 * Confirm user's redeem (for merchant only)
	 */
	function merchant_redeem_pending($user_id, $challenge_id) {
		$company_id = NULL; //TODO
		if($user = $this->socialhappen->get_user()) {
			if($user['user_is_player']) {
				echo 'You are not merchant';
			} else {
				$this->load->library('challenge_lib');
				if($result = $this->challenge_lib->redeem_challenge($user_id, $challenge_id)){
					echo 'Redeemed';
				} else {
					echo 'Cannot redeem';
				}
			}
		} else {
			redirect('player');
		}
	}

	/**
	 * Redirect to action's url with ?code=[hash] data
	 */
	function challenge_action($challenge_hash, $action) {
		$this->load->model('challenge_model');
		if($challenge = $this->challenge_model->getOne(array('hash' => $challenge_hash))) {
			if(isset($challenge['criteria'][$action])) {
				// echo '<pre>';
				// var_dump($challenge['criteria'][$action]);
				// echo '</pre>';
				if($challenge['criteria'][$action]['is_platform_action']) { //If platform's action : handle it by using library
					$this->load->library('action_data_lib');
					$action_url = $this->action_data_lib->get_action_url($challenge['criteria'][$action]['action_data_id']);
					redirect($action_url, 'refresh');
				} else { //TODO if not, redirect to app?
					echo 'this is not platform\'s action';
				}
			} else {
				show_error('Action Invalid');
			}
		} else {
			show_error('Challenge Invalid', 404);
		}
	}

	/**
	 * Play page
	 */
	function play(){
	 	$this->load->library('apiv2_lib');
		$app_data = $this->input->get('app_data', TRUE);

		$facebook_data = array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_app_scope' => $this->config->item('facebook_player_scope'),
			'facebook_channel_url' => $this->facebook->channel_url
		);
		
	 	$this->load->vars(array(
  		'static_fb_root' => $this->load->view('player/static_fb_root', $facebook_data, TRUE)
		));

	 	if(!$app_data){
			$app_data_array = array(
				'app_id' => 0, 
				'app_secret_key' => 0,
			);
			$app_data = base64_encode(json_encode($app_data_array));
			$data['true_app_data'] = false;

		} else {
			$data['true_app_data'] = true;
			$app_data_array = json_decode(base64_decode($app_data), TRUE);
		}
		
		$data['app_data'] = $app_data;
		$data['app_data_array'] = $app_data_array;

 		$template = array(
      'title' => 'Welcome to SocialHappen',
      'styles' => array(
        'common/bootstrap',
        'common/bootstrap-responsive',
        'common/bar',
        'common/player',
        'player/play'
      ),
      'body_views' => array(
        'common/fb_root' => array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_channel_url' => $this->facebook->channel_url,
          'facebook_app_scope' => $this->config->item('facebook_player_scope')
        ),
        // '../../assets/passport/templates/header/navigation.html' => NULL,
        'bar/plain_bar_view' => array(),
        'player/play_view' => $data,
        'common/vars' => array(
        	'vars' => array(
        		'base_url' => base_url()
        	)
        )
      ),
      'scripts' => array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
        'common/jquery.masonry.min',
        'common/jquery.timeago',
        'common/underscore-min',
        'common/bootstrap.min',
        'common/plain-bar',
        'player/play'
      )
    );
    $this->load->view('common/template', $template);
	}

	public function static_signup(){
		$this->load->library('apiv2_lib');
		$app_data = $this->input->get('app_data', TRUE);

	 	$this->load->vars(array(
    	'header' => $this->socialhappen->get_header_bootstrap( 
				array(
					'title' => 'Welcome to SocialHappen',
					'script' => array(
						'common/bar',
					),
					'style' => array(
						'common/player',
					),
					'use_static_fb_root' => TRUE
				)
			)
		));

		if(!$app_data){

			$app_data_array = array(
							'app_id' => 0, 
							'app_secret_key' => 0,
						);
					
			$app_data = base64_encode(json_encode($app_data_array));

		} else {

			/*
			print_r(base64_encode(json_encode(
												array(
														'app_id' => 10004, 
														'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
														'user_facebook_id' => '631885465',
														'data' => array('message' => 'message', 'link' => 'link')
													))));
			*/
		
			$app_data_array = json_decode(base64_decode($app_data), TRUE);
		}

		$data = compact('app_data','app_data_array');
		$this->load->view('player/static_signup_view', $data);
	}

	/**
	 * Static signup : AJAX
	 */
	public function static_signup_trigger(){
	 	$this->load->library('apiv2_lib');

		//mandatory parameters
		$app_data = $this->input->post('app_data', TRUE);
		$user_email = $this->input->post('email', TRUE);
		$user_first_name = $this->input->post('firstname', TRUE);
		$user_last_name = $this->input->post('lastname', TRUE);
		$user_facebook_id = $this->input->post('user_facebook_id', TRUE);

		$app_data = json_decode(base64_decode($app_data), TRUE);
		
		$app_id = $app_data['app_id'];
		$app_secret_key = $app_data['app_secret_key'];
		$user_image = "https://graph.facebook.com/{$user_facebook_id}/picture";
		$user_is_player = 1;

		//check args
		if(isset($app_id) && isset($app_secret_key) && $user_facebook_id && $user_email){
			$args = compact('app_id', 'app_secret_key', 'user_facebook_id', 'user_email', 'user_first_name', 'user_last_name', 'user_image', 'user_is_player');
			$signup_result = $this->apiv2_lib->signup($args);

			//show result
			if($signup_result){
				echo json_encode(array('result' => 'ok', 'message' => 'sucessfully sign-up', 'data' => $signup_result));
				
			} else {
				echo json_encode(array('result' => 'error', 'message' => 'signup error', 'data' => $signup_result));
			}
		}
	}

	/**
	 * redirect after sign-up
	 */
	public function static_play_app_trigger(){
		$this->load->library('apiv2_lib');

		$facebook_data = array(
					'facebook_app_id' => $this->config->item('facebook_app_id'),
					'facebook_app_scope' => $this->config->item('facebook_default_scope'),
					'facebook_channel_url' => $this->facebook->channel_url
		);
		
	 	$this->load->vars( array(
					        	'static_fb_root' => $this->load->view('player/static_fb_root', $facebook_data, TRUE)
					        	)
     	);

		//view-redirect after signup
		$app_data = $this->input->get('app_data', TRUE);
		$app_data_array = json_decode(base64_decode($app_data), TRUE);

		//print_r($app_data);

		if(!$app_data_array['app_id'])
			$data['app_id'] = 0;
		else
			$data['app_id'] = $app_data_array['app_id'];

		if($user_facebook_data = $this->facebook->getUser()){
			$data['user_data'] = $user_facebook_data;

			$this->load->model('user_model');
			if($user = $this->user_model->get_user_profile_by_user_facebook_id($user_facebook_data['id'])){
				$data['user_data']['sh_user_data'] = $user;
				//login after trigger play_app
				$this->socialhappen->player_login($user['user_id']);
			}
			$app_data_array['user_facebook_id'] = $user_facebook_data['id'];
			$play_app_result = $this->apiv2_lib->play_app($app_data_array);
		}

	redirect('player/play?app_data='.$app_data.'&dashboard=1&play_app_result='.$play_app_result);

	}
}  

/* End of file player.php */
/* Location: ./application/controllers/player.php */