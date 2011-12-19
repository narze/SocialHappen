<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    	/** 
	 * JSON : Gets campaign profile by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_profile($campaign_id = NULL){
		$this->CI->load->model('campaign_model','campaigns');
		$profile = $this->CI->campaigns->get_campaign_profile_by_campaign_id($campaign_id);
		return json_encode($profile);
	}

	/**
	 * JSON : Gets users by campaign id
	 * @param $campaign_id
	 * @param $limit
	 * @param $offset
	 * @author Manassarn M.
	 */
	function json_get_users($campaign_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_campaigns_model','user_campaigns');
		$profile = $this->CI->user_campaigns->get_campaign_users_by_campaign_id($campaign_id, $limit, $offset);
		return json_encode($profile);
	}
}

/* End of file campaign_ctrl.php */
/* Location: ./application/libraries/controller/campaign_ctrl.php */