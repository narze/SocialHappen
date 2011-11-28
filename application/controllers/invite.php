<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Invite_component_lib',NULL,'invite');
	}

	function index($app_install_id = NULL){
		//create invite form
		$data['invite_url'] = base_url().'invite/accept_invite/?invite_key=';	//by default
		$this->load->view('invite/main', $data);
		
	}
	
	function create_invite(){
		$campaign_id = $this->input->post('campaign_id');
		$app_install_id = $this->input->post('app_install_id');
		$facebook_page_id = $this->input->post('facebook_page_id');
		$user_facebook_id = $this->input->post('user_facebook_id');
		$invite_type = $this->input->post('invite_type');
		$target_facebook_id = $this->input->post('target_facebook_id');
		$message = $this->input->post('message');
		
		$invite_key = $this->invite->add_invite($campaign_id,$app_install_id,$facebook_page_id,
												$invite_type,$user_facebook_id,$target_facebook_id);
		
		if($invite_key){
			//communicate : not implemented yet
			// if(isset($target_id) && $target_id!='')
				// $this->callSendInvite(array('message'=>$message, 'invite_key' => $invite_key, 'target_id' => $target_id));
			// else
				// $tweet_result = $this->callTweetInvite(array('message'=>$message, 'invite_key' => $invite_key));
			
			// if(isset($tweet_result['auth_url']))
				// echo json_encode(array('auth_url' => $tweet_result['auth_url']));
			// else
				echo json_encode(array('invite_key' => $invite_key));
			
		}else{
			echo json_encode(array('error' => 'create invite failed'));
		}
	
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
	
	function accept_invite(){
		$invite_key = $this->input->get('invite_key');
		$invite = $this->invite->get_invite_by_invite_key($invite_key);
		
		$this->load->view('invite/accept_invite', $invite);
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