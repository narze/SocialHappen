<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Page_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($page_id = NULL){
    	$result = array(
	    	'success' => FALSE
	    );
	    if(!$this->CI->socialhappen->check_admin(array('page_id' => $page_id),array())){
			$result['error'] = 'User is not admin';
		} else {
			
			$this -> CI->load -> model('page_model', 'pages');
			$page = $this -> CI->pages -> get_page_profile_by_page_id($page_id);
			
			if(!$page) {
				$result['error'] = 'Page not found';
			} else {
				$this -> CI->load -> model('company_model', 'companies');
				$company = $this -> CI->companies -> get_company_profile_by_page_id($page_id);
				
				$facebook_page_graph = $this->CI->facebook->get_page_info($page['facebook_page_id']);

				$this -> CI->load ->model('installed_apps_model','installed_apps');
				$app_count = $this->CI->installed_apps->count_installed_apps_by_page_id($page_id);
				
				$this -> CI->load ->model('campaign_model','campaigns');
				$campaign_count = $this->CI->campaigns->count_campaigns_by_page_id($page_id);
				$this -> CI->load ->model('page_user_data_model','page_users');
				$user_count = $this->CI->page_users->count_page_users_by_page_id($page_id);
				$this->CI->config->load('pagination', TRUE);
				$per_page = $this->CI->config->item('per_page','pagination');
				
				// $key = 'subject';
				// $app_id = ???;
				// $action_id = ???;
				// $criteria = array('page_id' => $page_id);
				// $date = date("Ymd");
				// $new_user_count = $this->CI->audit_lib->count_audit($key, $app_id, $action_id, $criteria, $date));
				
				$this->CI->load->library('audit_lib');
				//var_dump($page_id);
				$action_id = $this->socialhappen->get_k('audit_action', 'User Register App');
				$list_stat_page = $this->CI->audit_lib->list_stat_page((int)$page_id, $action_id, $this->CI->audit_lib->_date());
				//var_dump($list_stat_page);
				if(count($list_stat_page) == 0){
					$new_user_count = 0;
				} else {
					$new_user_count = 0;
					foreach ($list_stat_page as $stat) {
						if(isset($stat['count'])){
							$new_user_count += $stat['count'];
						}
					}
				}

				$input = array('page_id' => $page_id);
				$common = array(
					'user_exceed_limit' => !$this->CI->socialhappen->is_developer_or_member_under_limit($input)
				);
				$this->CI->load->vars($common);
				
				$result['data'] = array(
					'page_id' => $page_id,
					'page_installed' => $page['page_installed'],
					'facebook_page_id' => $page['facebook_page_id'],
					'app_facebook_api_key' => $this->CI->config->item('facebook_app_id'),
					'facebook_tab_url' => $page['facebook_tab_url'],
					'header' => $this -> CI->socialhappen -> get_header( 
						array(
							'company_id' => $company['company_id'],
							'title' => $page['page_name'],
							'vars' => array(
								'page_id'=>$page_id,
								'company_id' => $company['company_id'],
								'per_page' => $per_page
							),
							'script' => array(
								'common/functions',
								'common/jquery.form',
								'common/bar',
								'common/jquery.pagination',
								'common/jquery.countdown.min',
								'page/page_apps',
								'page/page_campaigns',
								'page/page_report',
								'page/page_users',
								'page/page_tabs',
								//for fancybox in application tab
								'common/fancybox/jquery.mousewheel-3.0.4.pack',
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
								//'common/pagination',
								//for fancybox in application tab
								'common/fancybox/jquery.fancybox-1.3.4',
								//stat
								'stat/jquery.jqplot.min',
								'common/jquery.countdown'
							)
						)
					),
					'company_image_and_name' => $this -> CI->load -> view('company/company_image_and_name', 
						array(
							'company' => $company
						),
					TRUE),
					'breadcrumb' => $this -> CI->load -> view('common/breadcrumb', 
						array(
							'breadcrumb' => array( 
								$company['company_name'] => base_url() . "company/{$company['company_id']}",
								$page['page_name'] => base_url() . "page/{$page['page_id']}"
								),
							'settings_url' => base_url()."settings/page/{$page['page_id']}"
						),
					TRUE),
					'page_profile' => $this -> CI->load -> view('page/page_profile', 
						array('page_profile' => $page,
							'app_count' => $app_count,
							'campaign_count' => $campaign_count,
							'user_count' => $user_count,
							'new_user_count' => $new_user_count,
							'facebook' => array(
								'link' => issetor($facebook_page_graph['link']),
								'likes' =>  issetor($facebook_page_graph['likes'])
							)
						),
					TRUE),
					'page_tabs' => $this -> CI->load -> view('page/page_tabs', 
						array(
							'app_count' => $app_count,
							'campaign_count' => $campaign_count,
							'user_count' => $user_count
							),
					TRUE), 
					'page_apps' => $this -> CI->load -> view('page/page_apps', 
						array('app_count' => $app_count),
					TRUE), 
					'page_campaigns' => $this -> CI->load -> view('page/page_campaigns', 
						array('campaign_count' => $campaign_count, 'page_id'=>$page_id),
					TRUE),
					'page_users' => $this -> CI->load -> view('page/page_users', 
						array('user_count' => $user_count),
					TRUE),
					'page_report' => $this -> CI->load -> view('page/page_report', 
						array(),
					TRUE),
					'footer' => $this -> CI->socialhappen -> get_footer()
				);
				$result['success'] = TRUE;
			}
		}
	    return $result;
    }

	function json_count_apps(){
		
	}

	function json_count_campaigns(){
		
	}

	function json_count_user_apps(){
		
	}

	function json_count_user_campaigns(){
		
	}

	function json_count_users(){
		
	}

	/**
	 * JSON : Get page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_profile($page_id =NULL) {
		$this -> CI -> load -> model('page_model', 'pages');
		$profile = $this -> CI -> pages -> get_page_profile_by_page_id($page_id);
		return json_encode($profile);
	}

	/**
	 * JSON : Get install apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> CI -> load -> model('installed_apps_model', 'installed_apps');
		$this -> CI -> load -> model('user_model', 'user');
		
		$apps = $this -> CI -> installed_apps -> get_installed_apps_by_page_id($page_id, $limit, $offset);
		//echo '<pre>';
		//var_dump($apps);
		//echo '</pre>';
		$json_out = array();
		
		$action_id = $this->socialhappen->get_k('audit_action', 'User Visit');	
		foreach($apps as $app){
			$this -> CI->load->library('audit_lib');
			$this -> CI->load->library('app_url');
			date_default_timezone_set('UTC');
			$end_date = $this -> CI->audit_lib->_date();
			$start_date = date('Ymd', time() - 2592000);

			$active_user = $this -> CI->audit_lib->count_audit_range('subject', NULL, $action_id,
			 array('page_id' => (int)$page_id, 'app_install_id' => (int)$app['app_install_id']),
			  $start_date, $end_date);
			
			$a = array('app_image' => $app['app_image'],
						'app_install_id' => $app['app_install_id'],
						'app_name' => $app['app_name'],
						'app_description' => $app['app_description'],
						'app_install_status' => $app['app_install_status'],
						'app_url' => $this -> CI->app_url->translate_url($app['app_url'], $app['app_install_id']),
						'app_member' => $this -> CI->user->count_users_by_app_install_id($app['app_install_id']),
						'app_monthly_active_member' => $active_user
						);
			$json_out[] = $a;
		}
		return json_encode($json_out);
	}
/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> CI -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> CI -> campaigns -> get_page_campaigns_by_page_id($page_id, $limit, $offset);
		return json_encode($campaigns);
	}
/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status($page_id =NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this -> CI -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> CI -> campaigns -> get_page_campaigns_by_page_id_and_campaign_status_id($page_id, $campaign_status_id, $limit, $offset);
		return json_encode($campaigns);
	}

/**
	 * JSON : Get users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_users($page_id =NULL, $limit = NULL, $offset = NULL){
		$this -> CI -> load -> model('page_user_data_model', 'page_user');
		$users = $this -> CI -> page_user -> get_page_users_by_page_id($page_id, $limit, $offset);
		return json_encode($users);
	}

	function json_add($facebook_page_id = NULL ,$company_id = NULL ,$page_name = NULL ,$page_detail = NULL ,$page_all_member = NULL ,$page_new_member = NULL ,$page_image = NULL){
		$result = array('success' => FALSE);
		$this->CI-> load -> model('page_model', 'pages');
		$post_data = array(
			'facebook_page_id' => $facebook_page_id,
			'company_id' => $company_id,
			'page_name' => $page_name,
			'page_detail' => $page_detail,
			'page_all_member' => $page_all_member,
			'page_new_member' => $page_new_member,
			'page_image' => $page_image
		);
		
		if($this->CI->pages->get_page_id_by_facebook_page_id($post_data['facebook_page_id'])){
			log_message('error','Duplicated facebook page id');
			$result['error'] = 'This page has already installed Socialhappen';
		} else if($page_id = $this->CI-> pages -> add_page($post_data)) {
			$this->CI->load->library('audit_lib');
			$user_id = $this->CI->session->userdata('user_id');
			$action_id = $this->CI->socialhappen->get_k('audit_action','Install Page');
			$this->CI->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
						'page_id'=> $page_id,
						'company_id' => ($this->CI-> input -> post('company_id')),
						'app_install_id' => 0,
						'user_id' => $user_id
					)
			);
			$this->CI->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>0, 'page_id' => $page_id);
			$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
			
			$this->CI-> load -> model('user_pages_model', 'user_pages');
			$this->CI-> user_pages -> add_user_page(
				array(
				'user_id' => $user_id,
				'page_id' => $page_id,
				'user_role' => 1
				)
			);

			//Add Page user classes
			$this->CI->load->library('app_component_lib');
			$add_user_classes_result = $this->CI->app_component_lib->add_default_user_classes($page_id);
			if(!$add_user_classes_result){
		    	log_message('error','Add user classes failed , page_id : '.$page_id);
		    }

		    $result['success'] = TRUE;
		    $result['data'] = array(
			    'page_id' => $page_id
			);
		} else {
			log_message('error','page add failed');
			$result['error'] = 'Cannot add page, please contact administrator';
		}
		return $result;
	}

	function json_get_not_installed_facebook_pages(){
		
	}

	function json_update_page_order_in_dashboard(){
		
	}

	function addapp_lightbox(){
		
	}

	function get_stat_graph(){
		
	}

	function json_get_page_user_data(){
		
	}

	function config(){
		
	}



}
/* End of file page_ctrl.php */
/* Location: ./application/libraries/controller/page_ctrl.php */