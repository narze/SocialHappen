<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index($app_install_id = NULL){
		$this->load->library('form_validation');
		$share_message = '//default//';
		if($app_install_id){
			$this->load->library('campaign_lib');
			$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
		
			if(issetor($campaign['in_campaign'])){
				$campaign_id = $campaign['campaign_id'];
				$this->load->model('app_component_model','app_component');
				$sharebutton = $this->app_component->get_sharebutton_by_campaign_id($campaign_id);
				$this->load->library('sharebutton_lib');
				if($sharebutton && isset($sharebutton['message']['text'])){
					$share_message = $sharebutton['message']['text'];
				}
			}
		}

		$user = $this->socialhappen->get_user();
		$this->load->vars(array(
			'user' => $user,
			'twitter_checked' => !empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret']),
			'facebook_checked' => TRUE,
			'share_message' => $share_message,
			'app_install_id' => $app_install_id
		));
		$this->load->view('share/main');
	}

	function twitter_connect(){
		$this->load->library('twitter_lib');
		$this->twitter_lib->init();
		$request_token = $this->twitter->getRequestToken($this->config->item('twitter_callback_url'));
		$this->session->unset_userdata('twitter_access_token'); //Erase old access_token
			
		$this->twitter_lib->store_request_token($request_token);

		switch ($this->twitter->http_code) {
		  	case 200:
			    $url = $this->twitter->getAuthorizeURL($request_token);
			    redirect($url); 
			    break;
		  	default:
		    	echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
	}

	function twitter_callback(){
		$oauth_token = $this->input->get('oauth_token');
		$oauth_verifier = $this->input->get('oauth_verifier');
		$user_id = $this->socialhappen->get_user_id();
		if(!$user_id){
			echo "Please login SocialHappen";
			return;
		}
		if($oauth_token && $oauth_verifier){
			$this->load->library('twitter_lib');
			// if($this->twitter_lib->check_login_then_init()){
			// 	redirect('share/twitter');
			// }
			$twitter_request_token = $this->session->userdata('twitter');
			if($oauth_token && $twitter_request_token['oauth_token'] !== $this->input->get('oauth_token')){
				//redirect('share/twitter_connect?error=token_mismatch');
				echo 'Twitter token mismatch, please try again';
			}

			$this->twitter_lib->init($twitter_request_token);
			$access_token = $this->twitter->getAccessToken($oauth_verifier);
			$this->session->set_userdata('twitter_access_token', $access_token);

			if (200 == $this->twitter->http_code) {
				$this->load->model('user_model','user');
				$this->user->update_user($user_id, array('user_twitter_access_token' => $access_token['oauth_token'], 'user_twitter_name' => $access_token['screen_name'],'user_twitter_access_token_secret' => $access_token['oauth_token_secret']));
				$this->session->unset_userdata('twitter'); //Not use anymore
			  	// redirect('share/twitter?success=1');
			} else {
			  	log_message('error','twitter error code : '.$this->twitter->http_code); //read : https://dev.twitter.com/docs/error-codes-responses
			}
		}
		$this->load->view('share/twitter_callback');
	}

/*
	function twitter_share($app_install_id = NULL){
		$this->load->library('campaign_lib');
		$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
		if($campaign === FALSE || $campaign['in_campaign'] === FALSE){ //No campaign, or campaign ended : just share, but don't know what to share :(
			//$post_id = $this->sharebutton_lib->facebook_share($campaign_id);
			echo 'no campaign, cannot post';
		} else if($campaign['in_campaign']){
			$campaign_id = $campaign['campaign_id'];
			$this->load->model('app_component_model','app_component');
			$sharebutton = $this->app_component->get_sharebutton_by_campaign_id($campaign_id);
			$this->load->library('sharebutton_lib');
			if(!$sharebutton || !issetor($sharebutton['twitter_button'])){
				//Share with no criteria, no score
				$post_id = $this->sharebutton_lib->twitter_share();
				echo 'Post w/o campaign';
			} else {
				// Share and give score if not exceed maximum
				$post_id = $this->sharebutton_lib->twitter_share($sharebutton);
				'Post with campaign';
			}
			echo 'Posted on twitter with id : '.$post_id;
		}
	}*/

	function twitter_check_access_token($user_id = NULL){
		$response = array();
		$response['status'] = 0;
		$this->load->model('user_model','user');
		if($user = $this->user->get_user_profile_by_user_id($user_id)){
			if(!empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret'])){
				$response['status'] = 1;
			}
		}
		echo json_encode($response);
	}

	function share_submit($app_install_id = NULL){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('twitter', 'Twitter', '');			
		$this->form_validation->set_rules('facebook', 'Facebook', '');			
		$this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->index($app_install_id);
			return;
		}
		else
		{
		 	$this->load->library('sharebutton_lib');
			$message = $this->input->post('message');
			$twitter_share = $this->input->post('twitter') == 1;
			$facebook_share = $this->input->post('facebook') == 1;

			if($twitter_share){
				if($result = $this->sharebutton_lib->twitter_post($message)){
					echo '<div>Shared twitter</div>';
				}
			}

			if($facebook_share){
				$this->load->library('facebook');
				if($result = $this->facebook->post_profile($message)){
					echo '<div>Shared facebook</div>';
				}
			}

			//TODO : Give score if available
		}
	}
}
/* End of file share.php */
/* Location: ./application/controllers/share.php */