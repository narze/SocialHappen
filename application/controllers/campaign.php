<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
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
}


/* End of file campaign.php */
/* Location: ./application/controllers/campaign.php */