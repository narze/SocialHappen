<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_sharebutton extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
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
		if(!$this->socialhappen->check_admin(array('app_install_id'=>$app_install_id),array('role_all_company_apps_edit','role_app_edit'))){
			exit('You are not admin');
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('share_on_facebook', 'Share on Facebook', 'trim|xss_clean');			
		$this->form_validation->set_rules('share_on_twitter', 'Share on Twitter', 'trim|xss_clean');			
		$this->form_validation->set_rules('share_title', 'Share Title', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('share_caption', 'Share Caption', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('share_description', 'Share Description', 'required|trim|xss_clean');			
		$this->form_validation->set_rules('share_score', 'Share Score', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('share_maximum', 'Share Maximum', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('share_cooldown', 'Share Cooldown', 'required|trim|xss_clean|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
		$do_update = FALSE;
		$this->load->model('app_component_model','app_component');
		$campaign = $this->app_component->get_by_campaign_id($campaign_id);
		$sharebutton = issetor($campaign['sharebutton'], NULL);
		if($campaign || $sharebutton){
			$do_update = TRUE;
		}
		if ($this->form_validation->run() == FALSE)
		{
			$vars = array(
				'campaign_id' => $campaign_id,
				'app_install_id' => $app_install_id,
				'sharebutton' => $sharebutton
			);
			$this->load->vars($vars);
			$this->load->view('settings/app_component/sharebutton');
		}
		else 
		{
			$tab = $this->input->get('tab') ? '&tab=true' : '';
			$form_data = array(
				'facebook_button' => set_value('share_on_facebook') == 1,
				'twitter_button' => set_value('share_on_twitter') == 1,
				'criteria' => array(
					'score' => set_value('share_score'),
					'maximum' => set_value('share_maximum'),
					'cooldown' => set_value('share_cooldown')
				),
				'message' => array(
					'title' => set_value('share_title'),
					'caption' => set_value('share_caption'),
					'text' => set_value('share_description')
				)
			);
			if($do_update){
				if(!$share_image = $this->socialhappen->replace_image('share_image', $sharebutton['message']['image'])){
					$share_image = $sharebutton['message']['image'];
				}
				$form_data['message']['image'] = $share_image ? $share_image : base_url().'assets/images/default/campaign.png';

				if ($this->app_component->update_sharebutton_by_campaign_id($campaign_id, $form_data) == TRUE)
				{
					redirect('settings/campaign/'.$app_install_id.'?sharebutton_settings_success=1'.$tab);
				}
			}
			else
			{
				$form_data['message']['image'] = $this->socialhappen->upload_image('share_image');

				if ($this->app_component->add(array('campaign_id'=>$campaign_id, 'sharebutton'=>$form_data)) == TRUE)
				{
					redirect('settings/campaign/'.$app_install_id.'?sharebutton_settings_success=1'.$tab);
				}
			}
			//error
			log_message('error','Error in update_sharebutton_by_campaign_id '.print_r($form_data, TRUE));
			redirect('settings/campaign/'.$app_install_id.'?error=1'.$tab);
		}
	}
}
/* End of file app_component_sharebutton.php */
/* Location: ./application/controllers/settings/app_component_sharebutton.php */