<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('pagination');
		$this->socialhappen->check_logged_in();
	}

	function index($campaign_id = NULL){
		
		if(!$this->socialhappen->check_admin(array('campaign_id' => $campaign_id),array())){
			//no access
		} else {
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
				
				$this->load->library('audit_lib');
				$campaign_daily_active = $this->audit_lib->count_audit('subject', NULL, 102, array('campaign_id' => (int)$campaign_id), $this->audit_lib->_date());
				
				$this -> load -> model('user_model', 'user');
				$campaign_total_users = $this->user->count_users_by_campaign_id($campaign_id);
				
				
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
						array('campaign_profile' => $campaign,
							  'campaign_daily_active' => $campaign_daily_active,
							  'campaign_total_users' => $campaign_total_users),
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
	}
	
	/** 
	 * JSON : Gets campaign profile by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_profile($campaign_id = NULL){
		$this->socialhappen->ajax_check();
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
		$this->socialhappen->ajax_check();
		$this->load->model('user_campaigns_model','user_campaigns');
		$profile = $this->user_campaigns->get_campaign_users_by_campaign_id($campaign_id, $limit, $offset);
		echo json_encode($profile);
	}

	
	/**
	 * JSON : Add campaign
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->socialhappen->ajax_check();
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
			log_message('error','campaign add failed');
			$result['status'] = 'ERROR';
		}
		echo json_encode($result);
	}
	
	function get_stat_graph($campaign_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->load->library('audit_lib');
		
		if(empty($campaign_id)){
			return FALSE;
		}
		
		if(isset($start_date) && isset($end_date)){
			if($start_date > $end_date){
				$temp = $start_date;
				$start_date = $end_date;
				$end_date = $temp;
			}
		}else{
			date_default_timezone_set('Asia/Bangkok');
			$end_date = $this->audit_lib->_date();
			$start_date = (int)date('Ymd', time() - 2592000);
		}
		
		$dateRange = $this->audit_lib->get_date_range($start_date, $end_date);
		
		//print_r($dateRange);
		$action_id = 102;
		$res = $this->audit_lib->list_stat_campaign((int)$campaign_id, $action_id, (int)$start_date, $end_date);
		$stat_campaign_visit_db = array();
		foreach($res as $item){
			$stat_campaign_register_db[$item['date']] = $item['count'];
		}
		$action_id = 103;
		$res = $this->audit_lib->list_stat_campaign((int)$campaign_id, $action_id, (int)$start_date, $end_date);
		$stat_campaign_visit_db = array();
		foreach($res as $item){
			$stat_campaign_visit_db[$item['date']] = $item['count'];
		}
		
		$stat_campaign_register = array();
		$stat_campaign_visit = array();
		foreach ($dateRange as $date) {
			$stat_campaign_register[$date] = isset($stat_campaign_register_db[$date]) ? $stat_campaign_register_db[$date] : 0;
			$stat_campaign_visit[$date] = isset($stat_campaign_visit_db[$date]) ? $stat_campaign_visit_db[$date] : 0;
		}
		
		$data = array($stat_campaign_register, $stat_campaign_visit);
		$data_label = array('user register to campaign', 'user visit campaign');
		$title = 'Users Participation in Campaign';
		$div = array('id' => 'chart1',
					'width' => 900,
					'height' => 480,
					'class' => 'chart',
					'xlabel' => 'Dates',
					'ylabel' => 'Users');
		//echo json_encode($data);
		echo $this->audit_lib->render_stat_graph($data_label, $data, $title, $div);
	}
}


/* End of file campaign.php */
/* Location: ./application/controllers/campaign.php */