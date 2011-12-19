<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_invite extends CI_Controller {

	function __construct(){
		parent::__construct();

		if($this->input->get('tab') == TRUE) {
			$this->load->view('tab/header', 
				array(
					'facebook_app_id' => $this->config->item('facebook_app_id'),
					'facebook_channel_url' => $this->facebook->channel_url,
					'script' => array(
						'common/functions',
						'common/onload',
						'common/jquery.form',
						'common/jquery-ui.min',
						'settings/main_page_app_settings_facebook'
					),
					'style' => array(
						'../../assets/css/common/api_app_bar',
						'../../assets/css/common/smoothness/jquery-ui-1.8.9.custom'
					)
				),FALSE // FALSE = echo
			);
		}
	}
	
	function index($app_install_id = NULL, $campaign_id = NULL){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('facebook', 'Facebook', '');			
		$this->form_validation->set_rules('email', 'Email', '');			
		$this->form_validation->set_rules('invite_score', 'Invite score', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('maximum_invite', 'Maximum invite', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('invite_cooldown', 'Invite cooldown', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('page_acceptance_score', 'Page acceptance score', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('app_acceptance_score', 'App acceptance score', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('invite_title', 'Invite title', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('invite_text', 'Invite text', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
		$do_update = FALSE;
		$this->load->model('app_component_model','app_component');
		$campaign = $this->app_component->get_by_campaign_id($campaign_id);
		$invite = issetor($campaign['invite'], NULL);
		if($campaign || $invite){
			$do_update = TRUE;
		}
		if ($this->form_validation->run() == FALSE)
		{
			
			$vars = array(
				'campaign_id' => $campaign_id,
				'app_install_id' => $app_install_id,
				'invite' => $invite
			);
			$this->load->vars($vars);
			$this->load->view('settings/app_component/invite');
		}
		else 
		{
			$tab = $this->input->get('tab') ? '&tab=true' : '';
			$form_data = array(
		       	'facebook_invite' => set_value('facebook') == 1,
		       	'email_invite' => set_value('email') == 1,
		       	'criteria' => array(
					'score' => set_value('invite_score'),
					'maximum' => set_value('maximum_invite'),
					'cooldown' => set_value('invite_cooldown'),
					'acceptance_score' => array(
						'page' => set_value('page_acceptance_score'),
						'campaign' => set_value('app_acceptance_score')
					)
				),
				'message' => array(
					'title' => set_value('invite_title'),
					'text' =>  set_value('invite_text')
				)
			);
			if($do_update){
				if(!$invite_image = $this->socialhappen->replace_image('invite_image', $invite['message']['image'])){
					$invite_image = $invite['message']['image'];
				}
				$form_data['message']['image'] = $invite_image ? $invite_image : base_url().'assets/images/default/campaign.png';

				if ($this->app_component->update_invite_by_campaign_id($campaign_id, $form_data) == TRUE)
				{
					redirect('settings/campaign/'.$app_install_id.'?invite_settings_success=1'.$tab);
				}
			}
			else
			{
				$form_data['message']['image'] = $this->socialhappen->upload_image('invite_image');

				if ($this->app_component->add(array('campaign_id'=>$campaign_id, 'invite'=>$form_data)) == TRUE)
				{
					redirect('settings/campaign/'.$app_install_id.'?invite_settings_success=1'.$tab);
				}
			}
			//error
			log_message('error','Error in update_invite_by_campaign_id '.print_r($form_data, TRUE));
			redirect('settings/campaign/'.$app_install_id.'?error=1'.$tab);
		}
	}
}
/* End of file app_component_invite.php */
/* Location: ./application/controllers/settings/app_component_invite.php */