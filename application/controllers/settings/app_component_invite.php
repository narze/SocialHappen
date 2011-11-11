<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_invite extends CI_Controller {

	function __construct(){
		parent::__construct();
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
		$this->form_validation->set_rules('invite_image', 'Invite image', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('invite_title', 'Invite title', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('invite_text', 'Invite text', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$this->load->model('app_component_model','app_component');
		if ($this->form_validation->run() == FALSE)
		{
			$invite = $this->app_component->get_invite_by_campaign_id($campaign_id);
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
					'text' =>  set_value('invite_text'),
					'image' => set_value('invite_image')
				)
			);

			if ($this->app_component->update_invite_by_campaign_id($campaign_id, $form_data) == TRUE)
			{
				redirect('settings/campaign/'.$app_install_id.'?invite_settings_success=1');
			}
			else
			{
				log_message('error','An error occurred saving your information. Please try again later');
			}
		}
	}
}
/* End of file app_component_invite.php */
/* Location: ./application/controllers/settings/app_component_invite.php */