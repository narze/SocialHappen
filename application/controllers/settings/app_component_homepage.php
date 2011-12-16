<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_homepage extends CI_Controller {

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
	
	function index($app_install_id = NULL){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('homepage_for_non_fans', 'Homepage for non-fans', 'trim|xss_clean');		
		$this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
		$do_update = FALSE;
		$this->load->model('homepage_model','homepage');
		if($homepage = $this->homepage->get_homepage_by_app_install_id($app_install_id)){
			$do_update = TRUE;
		}
		if ($this->form_validation->run() == FALSE)
		{
			$vars = array(
				'app_install_id' => $app_install_id,
				'homepage' => $homepage
			);
			$this->load->vars($vars);
			$this->load->view('settings/app_component/homepage');
		}
		else 
		{
			$tab = $this->input->get('tab') ? '&tab=true' : '';
			$form_data = array(
				'app_install_id' => $app_install_id,
				'enable' => set_value('homepage_for_non_fans') == 1,
				'message' => set_value('message')
			);
			if($do_update){
				if(!$homepage_image = $this->socialhappen->replace_image('graphic', $homepage['image'])){
					$homepage_image = $homepage['image'];
				}
				$form_data['image'] = $homepage_image;

				if($this->homepage->update_homepage_by_app_install_id($app_install_id, $form_data) == TRUE){
					redirect('settings/app_component/homepage/'.$app_install_id.'?homepage_settings_success=1'.$tab);
				}
			} else {
				$homepage_image = $this->socialhappen->upload_image('graphic');
				$form_data['image'] = $homepage_image;

				if ($this->homepage->add($form_data) == TRUE){
					redirect('settings/app_component/homepage/'.$app_install_id.'?homepage_settings_success=1'.$tab);
				}
			}
			//error
			log_message('error','Error in add '.print_r($form_data, TRUE));
			redirect('settings/app_component/homepage/'.$app_install_id.'?error=1'.$tab);
		}
	}
}
/* End of file app_component_homepage.php */
/* Location: ./application/controllers/settings/app_component_homepage.php */