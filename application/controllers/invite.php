<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Invite_component_lib',NULL,'invite');
	}

	function index($app_install_id = NULL){
		$this->load->library('form_validation');
		$page_id = $this->input->get('page_id');
		$facebook_page_id = $this->input->get('facebook_page_id');

		if($app_install_id){
			$this->load->library('campaign_lib');
			$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			$campaign_id = FALSE;

			if(issetor($campaign['in_campaign'])){
				$campaign_id = $campaign['campaign_id'];
				$this->load->model('app_component_model','app_component');
				$invite = $this->app_component->get_invite_by_campaign_id($campaign_id);
				if($invite && isset($invite['message']['text'])){
					$invite_message = $invite['message']['text'];
					$user = $this->socialhappen->get_user();
					$this->load->vars(array(
						'user' => $user,
						'app_install_id' => $app_install_id,
						'invite_message' => $invite_message,
						'campaign_id' => $campaign_id,
						'facebook_page_id' => $facebook_page_id,
						'facebook_app_id' => $this->config->item('facebook_app_id'),
						'facebook_channel_url' => $this->facebook->channel_url
					));
					$this->load->view('invite/main');
				} else {
					echo 'cannot invite : no campaign invite message';
				}
			} else {
				echo 'cannot invite : not in campaign';
			}
		} else {
			echo 'cannot invite : not in app';
		}

		
	}

	function create_invite($app_install_id = NULL){
		$campaign_id = $this->input->post('campaign_id');
		$facebook_page_id = $this->input->post('facebook_page_id');
		$invite_type = $this->input->post('private_invite') == 1 ? 1 : 2;
		$target_facebook_id = $this->input->post('target_id');
		$invite_message = $this->input->post('invite_message');
		
		if($app_install_id){
			$user = $this->socialhappen->get_user();
			$user_facebook_id = $user['user_facebook_id'];
			$this->load->library('campaign_lib');
			$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			if(issetor($campaign['in_campaign'])){
				if($campaign['campaign_id'] == $campaign_id){
					if($invite_key = $this->invite->add_invite($campaign_id,$app_install_id,$facebook_page_id,$invite_type,$user_facebook_id,$target_facebook_id)){
						$this->load->helper('form');
						$this->load->vars(array(
							'invite_link' => base_url().'invite/accept?invite_key='.$invite_key,
							'invite_message' => $invite_message,
							'public_invite' => $invite_type === 1 ? FALSE : TRUE,
							'app_install_id' => $app_install_id
						));
						$this->load->view('invite/send');
					} else {
						echo 'failed creating invite';
					}
				} else {
					echo 'campaign just ended';
				}
			} else {
				echo 'not in campaign';
			}
		} else {
			echo 'not in app';
		}
	}

	function invite_share($app_install_id = NULL){
		$share_message = $this->input->post('invite_message');
		$invite_link = $this->input->post('invite_link');
		if($user = $this->socialhappen->get_user()){
			$this->load->vars(array(
				'user' => $user,
				'twitter_checked' => !empty($user['user_twitter_access_token']) && !empty($user['user_twitter_access_token_secret']),
				'facebook_checked' => TRUE,
				'share_message' => $share_message,
				'share_link' => $invite_link,
				'app_install_id' => $app_install_id 
			));
			$this->load->helper('form');
			$this->load->view('invite/share');
		} else {
			echo 'Please login socialhappen';
		}
	}

	function invite_share_submit($app_install_id = NULL){
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
			$share_link = $this->input->post('share_link');

			if($twitter_share){
				if($result = $this->sharebutton_lib->twitter_post($message.' '.$share_link)){
					echo '<div>Shared twitter</div>';
				}
			}

			if($facebook_share){
				$this->load->model('installed_apps_model','installed_apps');
				$app = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
				$app_name = $app['app_name'];
				$this->load->library('facebook');
				if($result = $this->facebook->post_profile($message, $share_link, $app_name)){
					echo '<div>Shared facebook</div>';
				}
			}

			if(!$twitter_share && !$facebook_share){
				echo 'Error occured, please share again';
				return;
			}
		}
	}

	// Sends private invite
	function invite_send(){
		
	}

	function invite_list($user_facebook_id = NULL){
		//show invites status of user
		
		$this->load->view('invite/invite_list');
		
	}
	
	function invite_list_fetch(){
	
		$campaign_id = $this->input->post('campaign_id');
		$app_install_id = $this->input->post('app_install_id');
		$facebook_page_id = $this->input->post('facebook_page_id');
		$user_facebook_id = $this->input->post('user_facebook_id');
		
		$criteria = array(
						'campaign_id' => (int)$campaign_id,
						'app_install_id' => (int)$app_install_id,
						'facebook_page_id' => $facebook_page_id,
						'user_facebook_id' => $user_facebook_id,
					);
		
		$result = $this->invite->list_invite($criteria);
		echo json_encode($result);
	}
	
	function invite_status($invite_key){
		$invite = $this->invite->get_invite_by_invite_key($invite_key);
		
		$this->load->view('invite/invite_status', array('invite' => $invite));
	
	}
	
	function accept(){
		$invite_key = $this->input->get('invite_key');
		if((!$facebook_user = $this->facebook->getUser()) || !isset($facebook_user['id'])){
			echo '<a href="'.base_url().'invite/accept_facebook_connect?invite_key='.$invite_key.'">Redirect</a>';
			return;
		}
		$reserve_invite = $this->invite->reserve_invite($invite_key, $facebook_user['id']);
		
		if(!isset($reserve_invite['error'])){
			$invite = $this->invite->get_invite_by_invite_key($invite_key);
			$this->load->model('installed_apps_model');
			if(($page = $this->installed_apps_model->get_app_profile_by_app_install_id($invite['app_install_id'])) && issetor($page['facebook_tab_url'])){
				redirect($page['facebook_tab_url']);
			} else {
				echo 'No facebook url to redirect';
			}
		} else {
			echo 'This invite key is invalid';
		}
	}
	
	function accept_facebook_connect(){
		$invite_key = $this->input->get('invite_key');
		$data = array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_default_scope' => $this->config->item('facebook_admin_scope'),
			'next' => base_url().'invite/accept?invite_key='.$invite_key
		);
		$this -> load -> view('home/facebook_connect', $data);
	}

	function accept_invite_fetch(){
		$invite_key = $this->input->post('invite_key');
		$target_facebook_id = $this->input->post('target_facebook_id');
		
		if(issetor($invite_key) && issetor($target_facebook_id)){
			$result = $this->invite->accept_invite($invite_key, $target_facebook_id);
			echo json_encode($result);
		}else{
			echo json_encode(array('error' => 'accept invite failed'));
		}
		
	}
}  

/* End of file invite.php */
/* Location: ./application/controllers/invite.php */