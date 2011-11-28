<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
		$this->load->model('campaign_model','campaign');

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
	
	function index($app_install_id = NULL){
		$campaigns = $this->campaign->get_app_campaigns_by_app_install_id_ordered($app_install_id, 'campaign_start_timestamp desc');
		$vars = array('campaigns' => $campaigns,'app_install_id'=>$app_install_id);

		$this->load->vars($vars);
		$this->load->view('settings/campaign/list');
	}

	function add($app_install_id = NULL){
		//todo : check permission
		$this->load->library('campaign_lib');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('campaign_name', 'Campaign Name', 'required|trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('campaign_start_date', 'Campaign Start Date', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_end_date', 'Campaign End Date', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_start_hour', 'Campaign Start Hour', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_end_hour', 'Campaign End Hour', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_start_min', 'Campaign Start Minute', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_end_min', 'Campaign End Minute', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_end_message', 'Campaign End Message', 'trim|xss_clean');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		if ($this->form_validation->run() == FALSE) 
		{
			$vars = array('app_install_id'=>$app_install_id);
			$this->load->vars($vars);
			$this->load->view('settings/campaign/add');
		}
		else 
		{
			$start_date = set_value('campaign_start_date');
			$end_date = set_value('campaign_end_date');
			$start_hour = set_value('campaign_start_hour');
			$end_hour = set_value('campaign_end_hour');
			$start_min = set_value('campaign_start_min');
			$end_min = set_value('campaign_end_min');

			$campaign_start_timestamp = $start_date.' '.$start_hour.':'.$start_min.':00';
			$campaign_end_timestamp = $end_date.' '.$end_hour.':'.$end_min.':00';

			$campaigns = $this->campaign->get_app_campaigns_by_app_install_id($app_install_id);
			if(!$this->campaign_lib->validate_date_range_with_campaigns($campaign_start_timestamp, $campaign_end_timestamp, $campaigns)){
				$vars = array('app_install_id'=>$app_install_id, 'date_range_validation_error' => TRUE);
				$this->load->vars($vars);
				$this->load->view('settings/campaign/add');
			}
			else
			{
				$tab = $this->input->get('tab') ? '&tab=true' : '';
				$form_data = array(
					'app_install_id' => $app_install_id,
			       	'campaign_name' => set_value('campaign_name'),
			       	'campaign_start_timestamp' => $campaign_start_timestamp,
			       	'campaign_end_timestamp' => $campaign_end_timestamp,
			       	'campaign_end_message' => set_value('campaign_end_message')
				);

				if ($campaign_id = $this->campaign->add_campaign($form_data)) 
				{
					$this->load->model('app_component_model','app_component');
					$this->app_component->add(array('campaign_id'=>$campaign_id));

					redirect('settings/campaign/'.$app_install_id.'?success=1'.$tab); 
				}
				else
				{
					log_message('error','Error in add_campaign '.print_r($form_data, TRUE));
					redirect('settings/campaign/'.$app_install_id.'?error=1'.$tab);
				}
			}
		}
	}

	function update($app_install_id = NULL, $campaign_id = NULL){
		//todo : check permission
		$this->load->library('campaign_lib');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('campaign_name', 'Campaign Name', 'required|trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('campaign_start_date', 'Campaign Start Date', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_end_date', 'Campaign End Date', 'required|trim|xss_clean|max_length[10]');
		$this->form_validation->set_rules('campaign_start_hour', 'Campaign Start Hour', 'required|trim|xss_clean|max_length[2]');
		$this->form_validation->set_rules('campaign_end_hour', 'Campaign End Hour', 'required|trim|xss_clean|max_length[2]');
		$this->form_validation->set_rules('campaign_start_min', 'Campaign Start Minute', 'required|trim|xss_clean|max_length[2]');
		$this->form_validation->set_rules('campaign_end_min', 'Campaign End Minute', 'required|trim|xss_clean|max_length[2]');
		$this->form_validation->set_rules('campaign_end_message', 'Campaign End Message', 'trim|xss_clean');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

		$campaign = $this->campaign->get_campaign_profile_by_campaign_id($campaign_id);
		$campaign_datetime = $this->parse_campaign_datetime($campaign);
		$vars = array('app_install_id' => $app_install_id , 'campaign_id'=>$campaign_id,'campaign' => $campaign, 'campaign_datetime'=>$campaign_datetime);
			$this->load->vars($vars);
		if ($this->form_validation->run() == FALSE) 
		{
			$this->load->view('settings/campaign/update');
		}
		else 
		{
			$campaigns = $this->campaign->get_app_campaigns_by_app_install_id($app_install_id);
			if(is_array($campaigns)){ //Remove self from validating date range list
				foreach($campaigns as $key => $campaign){
					if($campaign['campaign_id'] == $campaign_id){
						unset($campaigns[$key]);
						break;
					}
				}
			}

			$start_date = set_value('campaign_start_date');
			$end_date = set_value('campaign_end_date');
			$start_hour = set_value('campaign_start_hour');
			$end_hour = set_value('campaign_end_hour');
			$start_min = set_value('campaign_start_min');
			$end_min = set_value('campaign_end_min');
			$campaign_start_timestamp = $start_date.' '.$start_hour.':'.$start_min.':00';
			$campaign_end_timestamp = $end_date.' '.$end_hour.':'.$end_min.':00';

			if(!$this->campaign_lib->validate_date_range_with_campaigns($campaign_start_timestamp, $campaign_end_timestamp, $campaigns)){
				$this->load->vars(array('date_range_validation_error' => TRUE));
				$this->load->view('settings/campaign/update');
			} 
			else
			{
				$tab = $this->input->get('tab') ? '&tab=true' : '';
				$form_data = array(
			       	'campaign_name' => set_value('campaign_name'),
			       	'campaign_start_timestamp' => $campaign_start_timestamp,
			       	'campaign_end_timestamp' => $campaign_end_timestamp,
			       	'campaign_end_message' => set_value('campaign_end_message')
				);

				if(	$form_data['campaign_name'] == $campaign['campaign_name'] &&
					$form_data['campaign_start_timestamp'] == $campaign['campaign_start_timestamp'] &&
					$form_data['campaign_end_timestamp'] == $campaign['campaign_end_timestamp'] &&
					$form_data['campaign_end_message'] == $campaign['campaign_end_message']	) 
				{
					redirect('settings/campaign/'.$app_install_id.'?update_success=1'.$tab);
				}

				if ($this->campaign->update_campaign_by_id($campaign_id, $form_data)) 
				{
					redirect('settings/campaign/'.$app_install_id.'?update_success=1'.$tab); 
				}
				else
				{
					log_message('error','Error in update_campaign_by_id '.print_r($form_data, TRUE));
					redirect('settings/campaign/'.$app_install_id.'?error=1'.$tab);
				}
			}
		}
	}

	function parse_campaign_datetime($campaign = NULL){
		if(!$campaign){
			return FALSE;
		}
		return array(
				'campaign_start_date' => substr($campaign['campaign_start_timestamp'],0,10),
				'campaign_end_date' => substr($campaign['campaign_end_timestamp'],0,10),
				'campaign_start_hour' => substr($campaign['campaign_start_timestamp'],11,2),
				'campaign_end_hour' => substr($campaign['campaign_end_timestamp'],11,2),
				'campaign_start_min' => substr($campaign['campaign_start_timestamp'],14,2),
				'campaign_end_min' => substr($campaign['campaign_end_timestamp'],14,2)
			);
	}
}
/* End of file campaign.php */
/* Location: ./application/controllers/settings/campaign.php */