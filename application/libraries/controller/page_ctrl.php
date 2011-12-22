<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Page_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

	function json_count_apps_test(){
		
	}

	function json_count_campaigns_test(){
		
	}

	function json_count_user_apps_test(){
		
	}

	function json_count_user_campaigns_test(){
		
	}

	function json_count_users_test(){
		
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
		
		foreach($apps as $app){
			$this -> CI->load->library('audit_lib');
			$this -> CI->load->library('app_url');
			date_default_timezone_set('UTC');
			$end_date = $this -> CI->audit_lib->_date();
			$start_date = date('Ymd', time() - 2592000);
			
			$active_user = $this -> CI->audit_lib->count_audit_range('subject', NULL, 103,
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

	function json_add_test(){
		
	}

	function json_get_not_installed_facebook_pages_test(){
		
	}

	function json_update_page_order_in_dashboard_test(){
		
	}

	function addapp_lightbox_test(){
		
	}

	function get_stat_graph_test(){
		
	}

	function json_get_page_user_data_test(){
		
	}

	function config_test(){
		
	}



}
/* End of file page_ctrl.php */
/* Location: ./application/libraries/controller/page_ctrl.php */