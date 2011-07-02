<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('pagination');
	}

	function index($campaign_id = NULL){
		$this->socialhappen->check_logged_in('home');
		$this -> load -> model('campaign_model', 'campaigns');
		$campaign = $this -> campaigns -> get_campaign_profile_by_campaign_id($campaign_id);
		if($campaign) {
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_campaign_id($campaign_id);
			$this->load->model('page_model','pages');
			$page = $this->pages->get_page_profile_by_campaign_id($campaign_id);
			
			$this -> load ->model('user_model','users');
			$user_count = $this->users->count_users_by_campaign_id($campaign_id);
			$this->config->load('pagination', TRUE);
			$per_page = $this->config->item('per_page','pagination');
			
			$data = array(
				'campaign_id' => $campaign_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => $campaign['campaign_name'],
						'vars' => array('campaign_id'=>$campaign_id,
							'user_count' => $user_count,
							'per_page' => $per_page
						),
						'script' => array(
							'common/functions',
							'common/bar',
							'common/jquery.pagination',
							'campaign/campaign_stat',
							'campaign/campaign_users',
							'campaign/campaign_tabs',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array(
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							$campaign['campaign_name'] => base_url() . "campaign/{$campaign['campaign_id']}"
							)
						)
					,
				TRUE),
				'campaign_profile' => $this -> load -> view('campaign/campaign_profile', 
					array('campaign_profile' => $campaign),
				TRUE),
				'campaign_tabs' => $this -> load -> view('campaign/campaign_tabs', 
					array(
						'user_count' => $user_count
						),
				TRUE), 
				'campaign_stat' => $this -> load -> view('campaign/campaign_stat', 
					array(),
				TRUE),
				'campaign_users' => $this -> load -> view('campaign/campaign_users', 
					array(),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer());
			$this -> parser -> parse('campaign/campaign_view', $data);
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
	function json_get_users($campaign_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_campaigns_model','user_campaigns');
		$profile = $this->user_campaigns->get_campaign_users_by_campaign_id($campaign_id, $limit, $offset);
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
			$result['status'] = 'OK';
			$result['campaign_id'] = $campaign_id;
		} else {
			$result['status'] = 'ERROR';
		}
		echo json_encode($result);
	}
}


/* End of file campaign.php */
/* Location: ./application/controllers/campaign.php */