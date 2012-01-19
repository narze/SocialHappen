<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($campaign_id = NULL){
    	$result = array('success' => FALSE);

		if(!$this->CI->socialhappen->check_admin(array('campaign_id' => $campaign_id),array())){
			$result['error'] = 'User is not admin';
		} else {
			$this->CI-> load -> model('campaign_model', 'campaigns');
			$campaign = $this->CI-> campaigns -> get_campaign_profile_by_campaign_id($campaign_id);
			if(!$campaign) {
				$result['error'] = 'Campaign not found';
			} else {
				$this->CI-> load -> model('company_model', 'companies');
				$company = $this->CI-> companies -> get_company_profile_by_campaign_id($campaign_id);
				$this->CI->load->model('page_model','pages');
				$page = $this->CI->pages->get_page_profile_by_campaign_id($campaign_id);
				
				$this->CI-> load ->model('user_model','users');
				$user_count = $this->CI->users->count_users_by_campaign_id($campaign_id);
				$this->CI->config->load('pagination', TRUE);
				$per_page = $this->CI->config->item('per_page','pagination');
				
				$this->CI->load->library('audit_lib');
				$campaign_daily_active = $this->CI->audit_lib->count_audit('subject', NULL, 102, array('campaign_id' => (int)$campaign_id), $this->CI->audit_lib->_date());
				
				$this->CI-> load -> model('user_model', 'user');
				$campaign_total_users = $this->CI->user->count_users_by_campaign_id($campaign_id);

				$input = array('campaign_id' => $campaign_id);
				$common = array(
					'user_count' => $user_count,
					'user_exceed_limit' => !$this->CI->socialhappen->is_developer_or_member_under_limit($input)
				);
				$this->CI->load->vars($common);
				
				$result['data'] = array(
					'campaign_id' => $campaign_id,
					'header' => $this->CI-> socialhappen -> get_header( 
						array(
							'title' => $campaign['campaign_name'],
							'vars' => array('campaign_id'=>$campaign_id,
								'user_count' => $user_count,
								'per_page' => $per_page
							),
							'script' => array(
								'common/functions',
								'common/jquery.form',
								'common/bar',
								'common/jquery.pagination',
								'campaign/campaign_stat',
								'campaign/campaign_users',
								'campaign/campaign_tabs',
								'common/fancybox/jquery.fancybox-1.3.4.pack',
								
								//stat
								'stat/excanvas.min',
								'stat/jquery.jqplot.min',
								'stat/jqplot.highlighter.min',
								'stat/jqplot.cursor.min',
								'stat/jqplot.dateAxisRenderer.min',
								'stat/jqplot.canvasTextRenderer.min',
								'stat/jqplot.canvasAxisTickRenderer.min',
								'stat/jqplot.pointLabels.min'
							),
							'style' => array(
								'common/main',
								'common/platform',
								'common/fancybox/jquery.fancybox-1.3.4',
								//stat
								'stat/jquery.jqplot.min'
							)
						)
					),
					'company_image_and_name' => $this->CI-> load -> view('company/company_image_and_name', 
						array(
							'company' => $company
						),
					TRUE),
					'breadcrumb' => $this->CI-> load -> view('common/breadcrumb', 
						array('breadcrumb' => 
							array(
								$company['company_name'] => base_url() . "company/{$company['company_id']}",
								$page['page_name'] => base_url() . "page/{$page['page_id']}",
								$campaign['campaign_name'] => base_url() . "campaign/{$campaign['campaign_id']}"
								)
							)
						,
					TRUE),
					'campaign_profile' => $this->CI-> load -> view('campaign/campaign_profile', 
						array('campaign_profile' => $campaign,
							  'campaign_daily_active' => $campaign_daily_active,
							  'campaign_total_users' => $campaign_total_users),
					TRUE),
					'campaign_tabs' => $this->CI-> load -> view('campaign/campaign_tabs', 
						array(
							'user_count' => $user_count
							),
					TRUE), 
					'campaign_stat' => $this->CI-> load -> view('campaign/campaign_stat', 
						array(),
					TRUE),
					'campaign_users' => $this->CI-> load -> view('campaign/campaign_users', 
						array(),
					TRUE),
					'footer' => $this->CI-> socialhappen -> get_footer());
				$result['success'] = TRUE;
			}
		}
		return $result;
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