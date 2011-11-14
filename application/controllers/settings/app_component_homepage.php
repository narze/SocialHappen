<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_homepage extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index($app_install_id = NULL, $campaign_id = NULL){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('homepage_for_non_fans', 'Homepage for non-fans', 'trim|xss_clean');			
		$this->form_validation->set_rules('graphic', 'Graphic', 'required|max_length[255]');			
		$this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$this->load->model('app_component_model','app_component');
		if ($this->form_validation->run() == FALSE)
		{
			$homepage = $this->app_component->get_homepage_by_campaign_id($campaign_id);
			$vars = array(
				'campaign_id' => $campaign_id,
				'app_install_id' => $app_install_id,
				'homepage' => $homepage
			);
			$this->load->vars($vars);
			$this->load->view('settings/app_component/homepage');
		}
		else 
		{
			$form_data = array(
				'enable' => set_value('homepage_for_non_fans') == 1,
				'image' => set_value('graphic'),
				'message' => set_value('message')
			);
		
			if ($this->app_component->update_homepage_by_campaign_id($campaign_id, $form_data) == TRUE)
			{
				redirect('settings/campaign/'.$app_install_id.'?homepage_settings_success=1');
			}
			else
			{
				log_message('error','Error in update_homepage_by_campaign_id '.print_r($form_data, TRUE));
				redirect('settings/campaign/'.$app_install_id.'?error=1');
			}
		}
	}
}
/* End of file app_component_homepage.php */
/* Location: ./application/controllers/settings/app_component_homepage.php */