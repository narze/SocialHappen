<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index(){

	}

	function facebook($app_install_id = NULL){
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
			if(!$sharebutton || !issetor($sharebutton['facebook_button'])){
				//Share with no criteria, no score
				$post_id = $this->sharebutton_lib->facebook_share();
				echo 'Post w/o campaign';
			} else {
				// Share and give score if not exceed maximum
				$post_id = $this->sharebutton_lib->facebook_share($sharebutton);
				'Post with campaign';
			}
			echo 'Posted on facebook with id : '.$post_id;
		}
	}

	function twitter(){
		$this->load->library('twitter_lib');
		if($this->twitter_lib->check_login_then_init()){
			if($this->input->get('success')){
				echo 'Login success | ';
			}
			echo anchor('share/twitter_logout','logout');
			echo anchor('share/twitter_test_share/123',' test share');
		} else {
			if($this->input->get('error')){
				echo 'Error';
			}
			echo 'please login '.anchor('share/twitter_connect','click here');
		}
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
		$this->load->library('twitter_lib');
		if($this->twitter_lib->check_login_then_init()){
			redirect('share/twitter');
		}
		$twitter_request_token = $this->session->userdata('twitter');
		if($this->input->get('oauth_token') && $twitter_request_token['oauth_token'] !== $this->input->get('oauth_token')){
			redirect('share/twitter_connect?error=token_mismatch');
		}

		$this->twitter_lib->init($twitter_request_token);
		$access_token = $this->twitter->getAccessToken($this->input->get('oauth_verifier'));
		$this->session->set_userdata('twitter_access_token', $access_token);

		if (200 == $this->twitter->http_code) {
		$this->session->unset_userdata('twitter'); //Not use anymore
		  	redirect('share/twitter?success=1');
		} else {
		  	redirect('share/twitter?error=code_'.$this->twitter->http_code); //read : https://dev.twitter.com/docs/error-codes-responses
		}
	}

	function twitter_logout(){
		$this->session->unset_userdata('twitter_access_token'); //Erase old access_token
		redirect('share/twitter');
	}

	function twitter_test_share($message = NULL){
		$this->load->library('twitter_lib');
		if($this->twitter_lib->check_login_then_init()){
			$response = $this->twitter->post('statuses/update',array('status'=>$message));
			echo '<pre>';
			var_dump($response);
			echo '</pre>';
			if(isset($response->error)){
				//Handle status posting error
			}
		}
	}

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
	}
}
/* End of file share.php */
/* Location: ./application/controllers/share.php */