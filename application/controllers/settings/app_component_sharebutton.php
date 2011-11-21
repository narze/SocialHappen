<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_sharebutton extends CI_Controller {

	function __construct(){
		parent::__construct();

		if($this->input->get('tab') == TRUE) {
			$this->load->view('tab/header', 
				array(
					'facebook_app_id' => $this->config->item('facebook_app_id'),
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
		$this->form_validation->set_rules('share_on_facebook', 'Share on Facebook', 'trim|xss_clean');			
		$this->form_validation->set_rules('share_on_twitter', 'Share on Twitter', 'trim|xss_clean');			
		$this->form_validation->set_rules('share_title', 'Share Title', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('share_caption', 'Share Caption', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('share_image', 'Share Image', 'required|trim|xss_clean');			
		$this->form_validation->set_rules('share_description', 'Share Description', 'required|trim|xss_clean');			
		$this->form_validation->set_rules('share_score', 'Share Score', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('share_maximum', 'Share Maximum', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('share_cooldown', 'Share Cooldown', 'required|trim|xss_clean|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$this->load->model('app_component_model','app_component');
		if ($this->form_validation->run() == FALSE)
		{
			$sharebutton = $this->app_component->get_sharebutton_by_campaign_id($campaign_id);
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
					'text' => set_value('share_description'),
					'image' => set_value('share_image')
				)
			);
		
			if ($this->app_component->update_sharebutton_by_campaign_id($campaign_id, $form_data) == TRUE)
			{
				redirect('settings/campaign/'.$app_install_id.'?sharebutton_settings_success=1');
			}
			else
			{
				log_message('error','Error in update_sharebutton_by_campaign_id '.print_r($form_data, TRUE));
				redirect('settings/campaign/'.$app_install_id.'?error=1');
			}
		}
	}
}
/* End of file app_component_sharebutton.php */
/* Location: ./application/controllers/settings/app_component_sharebutton.php */