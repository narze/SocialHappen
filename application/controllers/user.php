<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
		$this->load->library('controller/user_ctrl');
	}
	
	function index(){
		$this -> socialhappen -> check_logged_in();
		$user_id = $this->input->get('uid');
		$page_id = $this->input->get('pid');
		$app_install_id = $this->input->get('aid');
		$campaign_id = $this->input->get('cid');
		$input = compact('user_id', 'page_id', 'app_install_id', 'campaign_id');
		if(!$this->socialhappen->is_developer_or_features_enabled($input){
			redirect_back();
		}
		$result = $this->user_ctrl->main($input);
		if($result['success']){
			$data = $result['data'];
			$this -> parser -> parse('user/user_view', $data);
		} else {
			echo $result['error'];
		}
		
	}

	function _get_user_activity_page($page_id, $user_id, $limit = 100, $offset = 0){
		date_default_timezone_set('UTC');
		$this->load->library('audit_lib');
		$activity_db = $this->audit_lib->list_audit(array('page_id' => (int)$page_id, 'user_id' => (int)$user_id), $limit, $offset);
		
		$this->load->model('app_model', 'apps');
		$this->load->model('page_model', 'pages');
		$activity_list = array();
		foreach ($activity_db as $activity) {
			$activity['app_id'] = $activity['app_id'] > 0 ? $activity['app_id'] : $activity['object'];
			$app = $this->apps->get_app_by_app_id($activity['app_id']);
			$page = $this->pages->get_page_profile_by_page_id($activity['page_id']);
			$audit_action = $this->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			$activity_list[] = array(
				'page_name' => $page['page_name'],
				'app_name' => $app['app_name'],
				'campaign_name' => '-',
				'activity_detail' => $audit_action['description'],
				'date' => date('d F Y', $activity['timestamp']),
				'time' => date('H:i:s', $activity['timestamp'])
			);
		}
		return $activity_list;
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function page($user_id, $page_id){
		redirect("user?uid={$user_id}&pid={$page_id}");
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function app($user_id, $app_install_id){
		redirect("user?uid={$user_id}&aid={$app_install_id}");
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function campaign($user_id, $campaign_id){
		redirect("user?uid={$user_id}&cid={$campaign_id}");
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_profile($user_id = NULL){
		$this->socialhappen->ajax_check();
		$profile = $this->user_ctrl->get_user_profile_by_user_id($user_id);
		echo $profile;
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$apps = $this->user_ctrl->json_get_apps($user_id, $limit, $offset);
		echo $apps;
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->users_campaigns->get_user_campaigns_by_user_id($user_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function json_get_facebook_pages_owned_by_user(){
		$this->socialhappen->ajax_check();
		echo json_encode($this->facebook->get_user_pages());
	}
	
	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->socialhappen->ajax_check();
		$result = $this->user_ctrl->json_add();
		echo $result;
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$companies = $this->user_ctrl->json_get_companies($user_id, $limit, $offset);
		echo $companies;
	}
	
	function get_stat_graph($mode = NULL, $id = NULL, $user_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->load->library('audit_lib');
		
		if(empty($mode) || empty($id) || empty($user_id)){
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
			$start_date = date('Ymd', time() - 2592000);
		}
		
		$dateRange = $this->audit_lib->get_date_range($start_date, $end_date);
		
		$action_id = 103;

		$stat_page_visit = array();
		
		switch ($mode) {
			case 'page':
				$criteria = array('page_id' => (int)$id, 'subject' => (int)$user_id);
				$data_label = array('user visit page');
				$title = 'Users Visit This Page';
			break;
			
			case 'app':
				$criteria = array('app_install_id' => (int)$id, 'subject' => (int)$user_id);
				$data_label = array('user visit app');
				$title = 'Users Visit This App';
			break;
			
			case 'campaign':
				$criteria = array('campaign_id' => (int)$id, 'subject' => (int)$user_id);
				$data_label = array('user visit campaign');
				$title = 'Users Visit This Campaign';
			break;
			
			default:
				return FALSE;
			break;
		}
		
		foreach ($dateRange as $date) {
			$action_id = 103;
			$stat_page_visit[$date] = $this->audit_lib->count_audit('_id', NULL, $action_id, $criteria, $date);
		}
		
		$data = array($stat_page_visit);
		
		$div = array('id' => 'chart1',
					'width' => 900,
					'height' => 480,
					'class' => 'chart',
					'xlabel' => 'Dates',
					'ylabel' => 'Pageviews');
		//echo json_encode($data);
		echo $this->audit_lib->render_stat_graph($data_label, $data, $title, $div);
	}
	
	/**
	 * JSON : Get user activities
	 * @param $user_id
	 * @param $page_id
	 * @param $limit
	 * @param $offset
	 * @author Weerapat P.
	 */
	function json_get_user_activities($page_id = NULL, $user_id = NULL, $limit = 10, $offset = 0){
		$this->socialhappen->ajax_check();
		$activities = $this->user_ctrl->_get_user_activity_page($page_id, $user_id, $limit, $offset);
		echo json_encode($activities);
	}
	
	/**
	 * JSON : Count user activities
	 * @param $user_id
	 * @param $page_id
	 * @author Weerapat P.
	 */
	function json_count_user_activities($page_id = NULL, $user_id = NULL){
		$this->socialhappen->ajax_check();
		$activities = $this->user_ctrl->_get_user_activity_page($page_id, $user_id);
		echo count($activities);
	}
}


/* End of file user.php */
/* Location: ./application/controllers/user.php */