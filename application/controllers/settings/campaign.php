<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
		$this->load->model('campaign_model','campaign');
	}
	
	function index($app_install_id = NULL){
		$campaigns = $this->campaign->get_app_campaigns_by_app_install_id_ordered($app_install_id, 'campaign_start_date desc');
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
			$campaigns = $this->campaign->get_app_campaigns_by_app_install_id($app_install_id);
			if(!$this->campaign_lib->validate_date_range_with_campaigns(set_value('campaign_start_date'), set_value('campaign_end_date'),$campaigns)){
				$vars = array('app_install_id'=>$app_install_id, 'date_range_validation_error' => TRUE);
				$this->load->vars($vars);
				$this->load->view('settings/campaign/add');
			}
			else
			{
				$form_data = array(
					'app_install_id' => $app_install_id,
			       	'campaign_name' => set_value('campaign_name'),
			       	'campaign_start_date' => set_value('campaign_start_date'),
			       	'campaign_end_date' => set_value('campaign_end_date'),
			       	'campaign_end_message' => set_value('campaign_end_message')
				);

				if ($campaign_id = $this->campaign->add_campaign($form_data)) 
				{
					$this->load->model('app_component_model','app_component');
					$this->app_component->add(array('campaign_id'=>$campaign_id));

					redirect('settings/campaign/'.$app_install_id.'?success=1'); 
				}
				else
				{
					log_message('error','Error in add_campaign '.print_r($form_data, TRUE));
					redirect('settings/campaign/'.$app_install_id.'?error=1');
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
		$this->form_validation->set_rules('campaign_end_message', 'Campaign End Message', 'trim|xss_clean');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		if ($this->form_validation->run() == FALSE) 
		{
			$campaign = $this->campaign->get_campaign_profile_by_campaign_id($campaign_id);
			$vars = array('app_install_id' => $app_install_id , 'campaign_id'=>$campaign_id,'campaign' => $campaign);
			$this->load->vars($vars);
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
			if(!$this->campaign_lib->validate_date_range_with_campaigns(set_value('campaign_start_date'), set_value('campaign_end_date'), $campaigns)){
				$campaign = $this->campaign->get_campaign_profile_by_campaign_id($campaign_id);
				$vars = array('app_install_id'=>$app_install_id, 'campaign_id'=>$campaign_id,'campaign' => $campaign, 'date_range_validation_error' => TRUE);
				$this->load->vars($vars);
				$this->load->view('settings/campaign/update');
			} 
			else
			{
				$form_data = array(
			       	'campaign_name' => set_value('campaign_name'),
			       	'campaign_start_date' => set_value('campaign_start_date'),
			       	'campaign_end_date' => set_value('campaign_end_date'),
			       	'campaign_end_message' => set_value('campaign_end_message')
				);

				if ($this->campaign->update_campaign_by_id($campaign_id, $form_data)) 
				{
					redirect('settings/campaign/'.$app_install_id.'?update_success=1'); 
				}
				else
				{
					log_message('error','Error in update_campaign_by_id '.print_r($form_data, TRUE));
					redirect('settings/campaign/'.$app_install_id.'?error=1');
				}
			}
		}
	}
}
/* End of file campaign.php */
/* Location: ./application/controllers/settings/campaign.php */