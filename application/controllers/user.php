<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
	}
	
	function index(){
		$this -> socialhappen -> check_logged_in();
		$user_id = $this->input->get('uid');
		if($user_id){
			$page_id = $this->input->get('pid');
			$app_install_id = $this->input->get('aid');
			$campaign_id = $this->input->get('cid');
			if(!$page_id && !$app_install_id && !$campaign_id){
				//no id specified
			} else {
				$this->load->library('audit_lib');
				$this -> load -> model('company_model', 'companies');
				$this -> load -> model('user_model','users');
				$user = $this -> users -> get_user_profile_by_user_id($user_id);
				$breadcrumb = array();
				if($page_id){
					$this -> load -> model('page_model', 'pages');
					$company = $this -> companies -> get_company_profile_by_page_id($page_id);
					$page = $this -> pages -> get_page_profile_by_page_id($page_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							'Member' => NULL
						);
						
					$activity = $this->_get_user_activity_page($page_id, $user_id);
					$recent_apps = $this->_get_user_activity_recent_apps($page_id, $user_id);
					$recent_campaigns = $this->_get_user_activity_recent_campaigns($page_id, $user_id);
					
					$this->load->model('page_user_data_model', 'page_user_data');
					$user_with_signup_fields = $this->page_user_data->get_page_user_by_user_id_and_page_id($user_id, $page_id);
					
				} else if ($app_install_id){
					$this -> load -> model('installed_apps_model', 'installed_apps');
					$company = $this -> companies -> get_company_profile_by_app_install_id($app_install_id);
					$app = $this -> installed_apps -> get_app_profile_by_app_install_id($app_install_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$app['app_name'] => base_url() . "app/{$app['app_install_id']}",
							'Member' => NULL
						);
					$activity = $this->_get_user_activity_app($app_install_id, $user_id);
				} else if ($campaign_id){
					$this -> load -> model('campaign_model', 'campaigns');
					$company = $this -> companies -> get_company_profile_by_campaign_id($campaign_id);
					$campaign = $this -> campaigns -> get_campaign_profile_by_campaign_id($campaign_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$campaign['campaign_name'] => base_url() . "campaign/{$campaign['campaign_id']}",
							'Member' => NULL
						);
					$activity = $this->_get_user_activity_campaign($campaign_id, $user_id);
				}
				
				$data = array(
				'user_id' => $user_id,
				'page_id' => issetor($page_id),
				'app_install_id' => issetor($app_install_id),
				'campaign_id' => issetor($campaign_id),
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => $user['user_first_name'].' '.$user['user_last_name'],
						'vars' => array('user_id' => $user_id,
										'page_id' => issetor($page_id),
										'app_install_id' => issetor($app_install_id),
										'campaign_id' => issetor($campaign_id)),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'user/user_activities',
							'user/user_tabs',
							//stat
		    				'stat/excanvas.min',
		    				'stat/jquery.jqplot.min',
			 				'stat/jqplot.highlighter.min',
			 				'stat/jqplot.cursor.min',
			 				'stat/jqplot.dateAxisRenderer.min',
			 				'stat/jqplot.canvasTextRenderer.min',
			 				'stat/jqplot.canvasAxisTickRenderer.min',
			 				'stat/jqplot.pointLabels.min',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4',
							'stat/jquery.jqplot.min'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', array('breadcrumb' => $breadcrumb),	TRUE),
				'user_profile' => $this -> load -> view('user/user_profile', 
					array('user_profile' => $user, 'recent_apps' => $recent_apps, 'recent_campaigns' => $recent_campaigns),
				TRUE),
				'user_tabs' => $this -> load -> view('user/user_tabs', 
					array(),
				TRUE), 
				'user_activities' => $this -> load -> view('user/user_activities', 
					array('activity' => $activity),
				TRUE), 
				'user_info' => $this -> load -> view('user/user_info', 
					array('user' => $user, 'user_data' => $user_with_signup_fields['user_data']),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
				$this -> parser -> parse('user/user_view', $data);
			}
			
		}
	}

	function _get_user_activity_page($page_id, $user_id){
		date_default_timezone_set('Asia/Bangkok');
		$activity_db = $this->audit_lib->list_audit(array('page_id' => (int)$page_id, 'user_id' => (int)$user_id));
		$activity_list = array();
		foreach ($activity_db as $activity) {
			//$action = $this->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			//$activity_list[] = date(DATE_RFC822, $activity['timestamp']) . ' : ' . $action['description'];
			$activity_list[] = $activity['message'];
		}
		return $activity_list;
	}
	
	function _get_user_activity_recent_apps($page_id, $user_id){
		date_default_timezone_set('Asia/Bangkok');
		$activity_db = $this->audit_lib->list_audit(array('page_id' => (int)$page_id, 'user_id' => (int)$user_id));
		$app_list = array();
		$this->load->model('app_model', 'app');
		foreach ($activity_db as $activity) {
			$app_list[] = $this->app->get_app_by_app_id($activity['app_id']);
		}
		return $app_list;
	}
	
	function _get_user_activity_recent_campaigns($page_id, $user_id){
		return $campaign_list = array(); //Test view
	}
	
	function _get_user_activity_app($app_install_id, $user_id){
		date_default_timezone_set('Asia/Bangkok');
		$activity_db = $this->audit_lib->list_audit(array('app_install_id' => (int)$app_install_id, 'user_id' => (int)$user_id));
		$activity_list = array();
		foreach ($activity_db as $activity) {
			//$action = $this->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			//$activity_list[] = date(DATE_RFC822, $activity['timestamp']) . ' : ' . $action['description'];
			$activity_list[] = $activity['message'];
		}
		return $activity_list;
	}
	
	function _get_user_activity_campaign($campaign_id, $user_id){
		date_default_timezone_set('Asia/Bangkok');
		$activity_db = $this->audit_lib->list_audit(array('campaign_id' => (int)$campaign_id, 'user_id' => (int)$user_id));
		$activity_list = array();
		foreach ($activity_db as $activity) {
			//$action = $this->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			//$activity_list[] = date(DATE_RFC822, $activity['timestamp']) . ' : ' . $action['description'];
			$activity_list[] = $activity['message'];
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
		$this->load->model('user_model','users');
		$profile = $this->users->get_user_profile_by_user_id($user_id);
		echo json_encode($profile);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_apps_model','user_apps');
		$apps = $this->user_apps->get_user_apps_by_user_id($user_id, $limit, $offset);
		echo json_encode($apps);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->users_campaigns->get_user_campaigns_by_user_id($user_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function json_get_facebook_pages_owned_by_user(){
		echo json_encode($this->facebook->get_user_pages());
	}
	
	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('user_model','users');
		$post_data = array(
							'user_first_name' => $this->input->post('user_first_name'),
							'user_last_name' => $this->input->post('user_last_name'),
							'user_email' => $this->input->post('user_email'),
							'user_image' => $this->input->post('user_image'),
							'user_facebook_id' => $this->input->post('user_facebook_id')
						);
		if($user_id = $this->users->add_user($post_data)){
			$result->status = 'OK';
			$result->user_id = $user_id;
		} else {
			$result->status = 'ERROR';
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_companies_model','user_companies');
		$companies = $this->user_companies->get_user_companies_by_user_id($user_id, $limit, $offset);
		echo json_encode($companies);
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
			date_default_timezone_set('Asia/Bangkok');
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
}


/* End of file user.php */
/* Location: ./application/controllers/user.php */