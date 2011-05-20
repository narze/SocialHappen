<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/** 
	 * JSON : get campaign profile by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_campaign_profile_by_id($campaign_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$profile = $this->campaigns->get_campaign_profile_by_id($campaign_id);
		echo json_encode($profile);
	}
	
}


/* End of file campaign.php */
/* Location: ./application/controllers/campaign.php */