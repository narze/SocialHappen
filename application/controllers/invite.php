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
						'facebook_app_id' => $this->config->item('facebook_app_id')
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
		
		if($app_install_id){
			$user = $this->socialhappen->get_user();
			$user_facebook_id = $user['user_facebook_id'];
			$this->load->library('campaign_lib');
			$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			if(issetor($campaign['in_campaign'])){
				if($campaign['campaign_id'] == $campaign_id){
					if($invite_key = $this->invite->add_invite($campaign_id,$app_install_id,$facebook_page_id,$invite_type,$user_facebook_id,$target_facebook_id)){
						$this->load->vars(array(
							'invite_link' => base_url().'invite/accept?invite_key='.$invite_key
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

	function invite_share(){
		
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
		$user_facebook_id = $facebook_user['id'];
		$invite = $this->invite->get_invite_by_invite_key($invite_key);
		if(!$invite){
			echo 'invite key invalid';
		} else if($invite['invite_type'] == 2 || ($invite['invite_type'] == 1 && in_array($user_facebook_id, $invite['target_facebook_id_list']))){ // if key is public invite OR private and user is in the invitee list
			$campaign_id = $invite['campaign_id']; //TODO : Check if this is current campaign_id
			$this->load->model('invite_pending_model','invite_pending');
			if($invite_key === $this->invite_pending->get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id, $campaign_id)){
				redirect($invite['redirect_url']);
			} else if($add_result = $this->invite_pending->add($user_facebook_id, $campaign_id, $invite_key)){
				redirect($invite['redirect_url']);
			} else {
				echo 'exception, please try again';
			}
		} else {
			echo 'You are not invited by this key';
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