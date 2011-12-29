<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

	function main($input = array()){
		$user_id = issetor($input['user_id']);
		$result = array(
	    	'success' => FALSE
	    );
		if(!$user_id){
			$result['error'] = 'No user_id specified';
		} else {
			$page_id = issetor($input['page_id']);
			$app_install_id = issetor($input['app_install_id']);
			$campaign_id = issetor($input['campaign_id']);
			if(!$page_id && !$app_install_id && !$campaign_id){
				$result['error'] = 'No id specified';
			} else {
				$this->CI->load->library('audit_lib');
				$this->CI -> load -> model('company_model', 'companies');
				$this->CI -> load -> model('user_model','users');
				$user = $this->CI -> users -> get_user_profile_by_user_id($user_id);
				$breadcrumb = array();
				if($page_id){
					$this->CI -> load -> model('page_model', 'pages');
					$company = $this->CI -> companies -> get_company_profile_by_page_id($page_id);
					$page = $this->CI -> pages -> get_page_profile_by_page_id($page_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							'Member' => NULL
						);
						
					//$activity = $this->_get_user_activity_page($page_id, $user_id);
					$recent_apps = $this->_get_user_activity_recent_apps($page_id, $user_id);
					$recent_campaigns = $this->_get_user_activity_recent_campaigns($page_id, $user_id);
					
					$this->CI->load->model('page_user_data_model', 'page_user_data');
					$user_with_signup_fields = $this->CI->page_user_data->get_page_user_by_user_id_and_page_id($user_id, $page_id);
					
				} else if ($app_install_id){
					$this->CI -> load -> model('installed_apps_model', 'installed_apps');
					$company = $this->CI -> companies -> get_company_profile_by_app_install_id($app_install_id);
					$app = $this->CI -> installed_apps -> get_app_profile_by_app_install_id($app_install_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$app['app_name'] => base_url() . "app/{$app['app_install_id']}",
							'Member' => NULL
						);
					$activity = $this->_get_user_activity_app($app_install_id, $user_id);
				} else if ($campaign_id){
					$this->CI -> load -> model('campaign_model', 'campaigns');
					$company = $this->CI -> companies -> get_company_profile_by_campaign_id($campaign_id);
					$campaign = $this->CI -> campaigns -> get_campaign_profile_by_campaign_id($campaign_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$campaign['campaign_name'] => base_url() . "campaign/{$campaign['campaign_id']}",
							'Member' => NULL
						);
					$activity = $this->_get_user_activity_campaign($campaign_id, $user_id);
				}
				
				$result['data'] = array(
					'user_id' => $user_id,
					'page_id' => issetor($page_id),
					'app_install_id' => issetor($app_install_id),
					'campaign_id' => issetor($campaign_id),
					'header' => $this->CI -> socialhappen -> get_header( 
						array(
							'title' => $user['user_first_name'].' '.$user['user_last_name'],
							'vars' => array('user_id' => $user_id,
											'page_id' => issetor($page_id),
											'activities_per_page' => 10,
											'app_install_id' => issetor($app_install_id),
											'campaign_id' => issetor($campaign_id)),
							'script' => array(
								'common/functions',
								'common/jquery.form',
								'common/bar',
								'common/jquery.pagination',
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
					'company_image_and_name' => $this->CI -> load -> view('company/company_image_and_name', 
						array(
							'company' => $company
						),
					TRUE),
					'breadcrumb' => $this->CI -> load -> view('common/breadcrumb', array('breadcrumb' => $breadcrumb),	TRUE),
					'user_profile' => $this->CI -> load -> view('user/user_profile', 
						array('user_profile' => $user, 'recent_apps' => issetor($recent_apps), 'recent_campaigns' => issetor($recent_campaigns)),
					TRUE),
					'user_tabs' => $this->CI -> load -> view('user/user_tabs', 
						array(),
					TRUE), 
					'user_activities' => $this->CI -> load -> view('user/user_activities', 
						array('activities' => isset($activity) ? $activity : NULL ),
					TRUE), 
					'user_info' => $this->CI -> load -> view('user/user_info', 
						array('user' => $user, 'user_data' => issetor($user_with_signup_fields['user_data'])),
					TRUE),
					'footer' => $this->CI -> socialhappen -> get_footer()
				);
				$result['success'] = TRUE;
			}
		}
		return $result;
	}

	
	function _get_user_activity_page($page_id, $user_id, $limit = 100, $offset = 0){
		date_default_timezone_set('UTC');
		$this->CI->load->library('audit_lib');
		$activity_db = $this->CI->audit_lib->list_audit(array('page_id' => (int)$page_id, 'user_id' => (int)$user_id), $limit, $offset);
		
		$this->CI->load->model('app_model', 'apps');
		$this->CI->load->model('page_model', 'pages');
		$activity_list = array();
		foreach ($activity_db as $activity) {
			$activity['app_id'] = $activity['app_id'] > 0 ? $activity['app_id'] : $activity['object'];
			$app = $this->CI->apps->get_app_by_app_id($activity['app_id']);
			$page = $this->CI->pages->get_page_profile_by_page_id($activity['page_id']);
			$audit_action = $this->CI->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
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
	
	function _get_user_activity_recent_apps($page_id, $user_id){
		date_default_timezone_set('UTC');		
		$app_ids = $this->CI->audit_lib->list_audit_range('app_id', array('page_id'=>(int)$page_id, 'user_id'=>(int)$user_id));
		$app_ids = array_filter($app_ids); //Remove zero value
		$app_list = array();
		$this->CI->load->model('app_model', 'app');
		foreach($app_ids as $app_id)
		{
			$app_list[] = $this->CI->app->get_app_by_app_id($app_id);
			if(isset($app_list[5])) break; //limit 6 apps
		}
		return $app_list;
	}
	
	function _get_user_activity_recent_campaigns($page_id, $user_id){
		return $campaign_list = array(); //Test view
	}
	
	function _get_user_activity_app($app_install_id, $user_id){
		date_default_timezone_set('UTC');
		$activity_db = $this->CI->audit_lib->list_audit(array('app_install_id' => (int)$app_install_id, 'user_id' => (int)$user_id));
		$activity_list = array();
		foreach ($activity_db as $activity) {
			//$action = $this->CI->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			//$activity_list[] = date(DATE_RFC822, $activity['timestamp']) . ' : ' . $action['description'];
			$activity_list[] = $activity['message'];
		}
		return $activity_list;
	}
	
	function _get_user_activity_campaign($campaign_id, $user_id){
		date_default_timezone_set('UTC');
		$activity_db = $this->CI->audit_lib->list_audit(array('campaign_id' => (int)$campaign_id, 'user_id' => (int)$user_id));
		$activity_list = array();
		foreach ($activity_db as $activity) {
			//$action = $this->CI->audit_lib->get_audit_action($activity['app_id'], $activity['action_id']);
			//$activity_list[] = date(DATE_RFC822, $activity['timestamp']) . ' : ' . $action['description'];
			$activity_list[] = $activity['message'];
		}
		return $activity_list;
	}

	function page(){
		
	}

	function app(){
		
	}

	function campaign(){
		
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_profile($user_id = NULL){
		$this->CI->load->model('user_model','users');
		$profile = $this->CI->users->get_user_profile_by_user_id($user_id);
		return json_encode($profile);
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_apps_model','user_apps');
		$apps = $this->CI->user_apps->get_user_apps_by_user_id($user_id, $limit, $offset);
		return json_encode($apps);
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->CI->users_campaigns->get_user_campaigns_by_user_id($user_id, $limit, $offset);
		return json_encode($campaigns);
	}

	function json_get_facebook_pages_owned_by_user(){
		
	}

	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->CI->load->model('user_model','users');
		$post_data = array(
							'user_first_name' => $this->CI->input->post('user_first_name'),
							'user_last_name' => $this->CI->input->post('user_last_name'),
							'user_email' => $this->CI->input->post('user_email'),
							'user_image' => $this->CI->input->post('user_image'),
							'user_facebook_id' => $this->CI->input->post('user_facebook_id')
						);
		if($user_id = $this->CI->users->add_user($post_data)){
			$result->status = 'OK';
			$result->user_id = $user_id;
			
			$this->CI->load->library('audit_lib');
			$action_id = $this->CI->socialhappen->get_k('audit_action','User Register SocialHappen');
			$this->CI->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_install_id' => 0,
					'user_id' => $user_id
				)
			);
			
			$this->CI->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>0);
			$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		} else {
			log_message('error','add user failed');
			$result->status = 'ERROR';
		}
		return json_encode($result);
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_companies_model','user_companies');
		$companies = $this->CI->user_companies->get_user_companies_by_user_id($user_id, $limit, $offset);
		return json_encode($companies);
	}

	function get_stat_graph(){
		
	}

	function json_get_user_activities(){
		
	}

	function json_count_user_activities(){
		
	}


}

/* End of file company_ctrl.php */
/* Location: ./application/libraries/controller/company_ctrl.php */