<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index($campaign_id = NULL){
		if($campaign_id){
			$data['campaign_id'] = $campaign_id;
			$this->load->view('campaign_view', $data);
			return $data;
		}
	}
	
	/** 
	 * JSON : Gets campaign profile by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_profile($campaign_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$profile = $this->campaigns->get_campaign_profile_by_campaign_id($campaign_id);
		echo json_encode($profile);
	}
	
	/**
	 * JSON : Gets users by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_users($campaign_id = NULL){
		$this->load->model('user_campaigns_model','user_campaigns');
		$profile = $this->user_campaigns->get_campaign_users_by_campaign_id($campaign_id);
		echo json_encode($profile);
	}

	
	/**
	 * JSON : Add campaign
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('campaign_model','campaigns');
		$post_data = array(
							'app_install_id' => $this->input->post('app_install_id'),
							'campaign_name' => $this->input->post('campaign_name'),
							'campaign_detail' => $this->input->post('campaign_detail'),
							'campaign_status_id' => $this->input->post('campaign_status_id'),
							'campaign_active_member' => $this->input->post('campaign_active_member'),
							'campaign_all_member' => $this->input->post('campaign_all_member'),
							'campaign_end_timestamp' => $this->input->post('campaign_end_timestamp')
							);
		if($campaign_id = $this->campaigns->add_campaign($post_data)){
			$result->status = 'OK';
			$result->campaign_id = $campaign_id;
		} else {
			$result->status = 'ERROR';
		}
		echo json_encode($result);
	}
}


/* End of file campaign.php */
/* Location: ./application/controllers/campaign.php */