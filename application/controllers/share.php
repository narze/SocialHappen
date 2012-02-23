<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($app_install_id = NULL, $share_link = NULL){
		$this->load->library('form_validation');
		$share_link = $share_link ? $share_link : $this->input->get('link');
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
					$user = $this->socialhappen->get_user();
					$this->load->vars(array(
						'user' => $user,
						'twitter_checked' => !empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret']),
						'facebook_checked' => TRUE,
						'sharebutton' => $sharebutton,
						'share_message' => $share_message,
						'share_link' => $share_link,
						'app_install_id' => $app_install_id,
						'campaign_id' => $campaign_id
					));
					$this->load->view('share/main');
				} else {
					echo 'cannot share, no share message';
				}
			} else {
				echo 'cannot share, no campaign';
			}
		} else {
			echo 'cannot share, not in app';
		}
	}

	function twitter_connect(){
		$this->load->library('sharebutton_lib');
		$this->sharebutton_lib->twitter_connect();
	}

	function twitter_callback(){
		$oauth_token = $this->input->get('oauth_token');
		$oauth_verifier = $this->input->get('oauth_verifier');
		$denied = $this->input->get('denied');
		
		$this->load->library('sharebutton_lib');
		$this->sharebutton_lib->twitter_callback($oauth_token, $oauth_verifier, $denied);
	}

	function twitter_check_access_token($user_id = NULL){
		$this->load->library('sharebutton_lib');
		$response = $this->sharebutton_lib->twitter_check_access_token($user_id);
		echo json_encode($response);
	}

	function share_submit($app_install_id = NULL, $campaign_id = NULL){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('twitter', 'Twitter', '');			
		$this->form_validation->set_rules('facebook', 'Facebook', '');			
		$this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<div class="notice error">', '</div>');

		$share_link = $this->input->post('share_link');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->index($app_install_id, $share_link);
			return;
		}
		else
		{
		 	$this->load->library('sharebutton_lib');
			$message = $this->input->post('message');
			$twitter_share = $this->input->post('twitter') == 1;
			$facebook_share = $this->input->post('facebook') == 1;

			$result = array();

			$this->load->model('installed_apps_model','installed_apps');
			$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
				
			if($twitter_share){
				if($result = $this->sharebutton_lib->twitter_post($message.' '.$share_link)){
					$share_result['twitter'] = TRUE;
				}
			}

			if($facebook_share){
				$app_name = $app['app_name'];
				$this->load->library('facebook');
				if($result = $this->facebook->post_profile($message, $share_link, $app_name)){
					$share_result['facebook'] = TRUE;
				}
			}

			if(!$twitter_share && !$facebook_share){
				$share_result['error'] = 'Error occured, please share again';
			}

			$user = $this->socialhappen->get_user();
			$this->load->vars(array(
				'user' => $user,
				'twitter_checked' => !empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret']),
				'app_install_id' => $app_install_id,
				'share_result' => $share_result,
				'campaign_id' => $campaign_id
			));
			$this->load->view('share/main');

			//Add share score
			$share_action = $this->socialhappen->get_k('audit_action','User Share');

			$this->load->library('audit_lib');
			$user_id = $this->socialhappen->get_user_id();
			$audit_data = array(
				'app_id' => issetor($app['app_id'],0),
				'subject' => $user_id,
				'action_id' => $share_action,
				'user_id' => $user_id,
				'app_install_id' => $app_install_id,
				'page_id' => $app['page_id'],
				'campaign_id' => $campaign_id
			);
			$audit_result = $this->audit_lib->audit_add($audit_data);

			if($audit_result){
				$this->load->library('achievement_lib');
				$achievement_info = array(
					'action_id'=> $share_action,
					'app_install_id'=> $app_install_id,
					'page_id' => $app['page_id'],
					'campaign_id' => $campaign_id
				);

				$inc_result = $this->achievement_lib->increment_achievement_stat(
					issetor($app['app_id'],0),
					$user_id,
					$achievement_info,
					1);
				if($inc_result){
					//echo ' increment_achievement_stat complete';
				} else {	
					log_message('error','increment_achievement_stat failed');
				}
			} else {
				log_message('error','add_audit failed');
				return;
			}
		}
	}
}
/* End of file share.php */
/* Location: ./application/controllers/share.php */