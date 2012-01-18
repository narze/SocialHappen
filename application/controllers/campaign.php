<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	function __construct(){
		parent::__construct();
		// $this->socialhappen->check_logged_in();
		$this->load->library('pagination');
		$this->load->library('controller/campaign_ctrl');
	}

	function index($campaign_id = NULL){
		if(!$this->socialhappen->is_developer_or_features_enabled(array('campaign_id'=>$campaign_id))){
			redirect_back();
		}
		$result = $this->campaign_ctrl->main($campaign_id);
		if($result['success']){
			$data = $result['data'];
			$this -> parser -> parse('campaign/campaign_view', $data);
		} else {
			echo $result['error'];
		}
	}
	
	/** 
	 * JSON : Gets campaign profile by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_profile($campaign_id = NULL){
		$this->socialhappen->ajax_check();
		$profile = $this->campaign_ctrl->json_get_profile($campaign_id);
		echo $profile;
	}
	
	/**
	 * JSON : Gets users by campaign id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function json_get_users($campaign_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$profile = $this->campaign_ctrl->json_get_users($campaign_id, $limit, $offset);
		echo $profile;
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
			date_default_timezone_set('UTC');
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