<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sharebutton Library
 * 
 * @author Manassarn M.
 */
class Sharebutton_lib {

	function __construct() {
        $this->CI =& get_instance();
    }

	function facebook_post($message = NULL){
		$this->CI->load->library('fb_library/fb_library',
			array(
			  'appId'  => $this->CI->config->item('facebook_app_id'),
			  'secret' => $this->CI->config->item('facebook_api_secret'),
			  'cookie' => true,
			),
			'FB');
		if($facebook_user = $this->CI->FB->getUser() && $this->CI->socialhappen->get_user()){  log_message('error', print_r($facebook_user,TRUE));
			try {
            	$post = $this->CI->FB->api('/me/feed', 'POST',
                array(
                  	'link' => 'www.example.com',
                  	'message' => $message
            	));
	            
		        return $post;

	      	} catch(FacebookApiException $e) {
		        log_message('error', $e->getType());
		        log_message('error', $e->getMessage());
		        return FALSE;
	      	}   
      	} else {
      		//no fb || no sh user. please login again
      		return FALSE;
      	}
	}

	function twitter_post($message = NULL){
		$this->CI->load->library('twitter_lib');
		if($this->CI->twitter_lib->check_login_then_init()){
			$response = $this->CI->twitter->post('statuses/update', array('status'=> $message));
			
			if(isset($response->error)){
				log_message($response->error);
				return FALSE;
			} else {
				return $response;
			}
		}
	}

	function twitter_connect(){
		$this->CI->load->library('twitter_lib');
		$this->CI->twitter_lib->init();
		$request_token = $this->CI->twitter->getRequestToken($this->CI->config->item('twitter_callback_url'));
		$this->CI->session->unset_userdata('twitter_access_token'); //Erase old access_token
			
		$this->CI->twitter_lib->store_request_token($request_token);

		switch ($this->CI->twitter->http_code) {
		  	case 200:
			    $url = $this->CI->twitter->getAuthorizeURL($request_token);
			    redirect($url); 
			    break;
		  	default:
		    	echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
	}

	function twitter_callback($oauth_token = NULL, $oauth_verifier = NULL, $denied = NULL){
		if($denied){
			echo 'denied access to twitter';
			return;
		}
		if(!$user_id = $this->CI->socialhappen->get_user_id()){
			echo "Please login SocialHappen";
			return;
		}
		if($oauth_token && $oauth_verifier){
			$this->CI->load->library('twitter_lib');
			// if($this->CI->twitter_lib->check_login_then_init()){
			// 	redirect('share/twitter');
			// }
			$twitter_request_token = $this->CI->session->userdata('twitter');
			if($oauth_token !== $twitter_request_token['oauth_token']){
				//redirect('share/twitter_connect?error=token_mismatch');
				echo 'Twitter token mismatch, please try again';
			}

			$this->CI->twitter_lib->init($twitter_request_token);
			$access_token = $this->CI->twitter->getAccessToken($oauth_verifier);
			$this->CI->session->set_userdata('twitter_access_token', $access_token);

			if (200 == $this->CI->twitter->http_code) {
				$this->CI->load->model('user_model','user');
				$this->CI->user->update_user($user_id, array('user_twitter_access_token' => $access_token['oauth_token'], 'user_twitter_name' => $access_token['screen_name'],'user_twitter_access_token_secret' => $access_token['oauth_token_secret']));
				$this->CI->session->unset_userdata('twitter'); //Not use anymore
			  	// redirect('share/twitter?success=1');
			} else {
			  	log_message('error','twitter error code : '.$this->CI->twitter->http_code); //read : https://dev.twitter.com/docs/error-codes-responses
			}
		}
		$this->CI->load->view('share/twitter_callback');
	}

	function twitter_check_access_token($user_id = NULL){
		$response = array();
		$response['status'] = 0;
		$this->CI->load->model('user_model','user');
		if($user = $this->CI->user->get_user_profile_by_user_id($user_id)){
			if(!empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret'])){
				$response['status'] = 1;
			}
		}
		return $response;
	}
}