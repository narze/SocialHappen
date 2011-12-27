<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Invite_component_lib',NULL,'invite');
		$this->load->library('controller/invite_ctrl');
	}

	function index($app_install_id = NULL){
		$this->load->library('form_validation');
		$page_id = $this->input->get('page_id');
		$facebook_page_id = $this->input->get('facebook_page_id');
		$input = compact('app_install_id', 'page_id', 'facebook_page_id');

		$data = $this->invite_ctrl->main($input);
		if($data['success'] === TRUE){
			unset($data['success']);
			$this->load->vars($data);
			$this->load->view('invite/main');
		} else {
			echo $data['error'];
		}
	}

	function create_invite($app_install_id = NULL){
		$campaign_id = $this->input->post('campaign_id');
		$facebook_page_id = $this->input->post('facebook_page_id');
		$invite_type = $this->input->post('private_invite') == 1 ? 1 : 2;
		$target_facebook_id = $this->input->post('target_id');
		$invite_message = $this->input->post('invite_message');
		
		$input = compact('app_install_id', 'campaign_id', 'facebook_page_id', 'invite_type',
			'target_facebook_id', 'invite_message');

		$data = $this->invite_ctrl->create_invite($input);

		if($data['success'] === TRUE){
			unset($data['success']);
			$this->load->vars($data);
			$this->load->view('invite/send');
		} else {
			echo $data['error'];
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
		$facebook_user_id = $facebook_user['id'];

		$input = compact('invite_key', 'facebook_user_id');

		$data = $this->invite_ctrl->accept($input);

		if($data['success'] === TRUE){
			redirect($data['facebook_tab_url']);
		} else {
			echo $data['error'];
		}
	}
	
	function accept_facebook_connect(){
		$invite_key = $this->input->get('invite_key');
		$data = array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_default_scope' => $this->config->item('facebook_admin_scope'),
			'facebook_channel_url' => $this->facebook->channel_url,
			'next' => base_url().'invite/accept?invite_key='.$invite_key
		);
		$this -> load -> view('common/fb-root', $data);
		$this -> load -> view('invite/facebook_connect', $data);
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