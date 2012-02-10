<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($app_install_id = NULL){
    	$result = array(
	    	'success' => FALSE
	    );
    	if(!$this->CI->socialhappen->check_admin(array('app_install_id' => $app_install_id),array())){
			$result['error'] = 'User is not admin';
		} else {
			$this ->CI-> load -> model('installed_apps_model', 'installed_apps');
			$app = $this->CI->installed_apps->get_app_profile_by_app_install_id($app_install_id);
			if(!$app) {
				$result['error'] = 'App not found'; // This case is hard to reach
			} else {
				$this ->CI-> load -> model('company_model', 'companies');
				$company = $this ->CI-> companies -> get_company_profile_by_app_install_id($app_install_id);
				$this->CI->load->model('page_model','pages');
				$page = $this->CI->pages->get_page_profile_by_app_install_id($app_install_id);
				$this ->CI-> load -> model('campaign_model', 'campaigns');
				$campaigns = $this ->CI-> campaigns -> get_campaigns_by_app_install_id($app_install_id);

				$campaign_count = $this->CI->campaigns->count_campaigns_by_app_install_id($app_install_id);
				$user_count = $this->CI->users->count_users_by_app_install_id($app_install_id);
				$this->CI->config->load('pagination', TRUE);
				$per_page = $this->CI->config->item('per_page','pagination');
				
				$this->CI->load->library('audit_lib');
				$action_id = $this->socialhappen->get_k('audit_action', 'User Register App');
				$new_users = $this->CI->audit_lib->list_stat_app((int)$app_install_id, $action_id, $this->CI->audit_lib->_date());
				$new_users = count($new_users) == 0 ? 0 : $new_users[0]['count'];
				
				$this ->CI-> load -> model('user_model', 'user');
				$all_users = $this->CI->user->count_users_by_app_install_id($app_install_id);

				$input = array('app_install_id' => $app_install_id);
				$common = array(
					'user_count' => $user_count,
					'user_exceed_limit' => !$this->CI->socialhappen->is_developer_or_member_under_limit($input)
				);
				$this->CI->load->vars($common);
				
				$result['data'] = array(
					'app_install_id' => $app_install_id,
					'header' => $this ->CI-> socialhappen -> get_header( 
						array(
							'company_id' => $company['company_id'],
							'title' => $app['app_name'],
							'vars' => array(
								'app_install_id'=>$app_install_id,
								'per_page' => $per_page,
								'page_id' => $page['page_id']
							),
							'script' => array(
								'common/functions',
								'common/jquery.form',
								'common/bar',
								'common/jquery.pagination',
								'common/jquery.countdown.min',
								//'app/app_stat',
								'app/app_users',
								'app/app_campaigns',
								'app/app_tabs',
								'app/main',
								'common/fancybox/jquery.fancybox-1.3.4.pack'
							),
							'style' => array(
								'common/main',
								'common/platform',
								'common/fancybox/jquery.fancybox-1.3.4',
								'common/jquery.countdown'
							)
						)
					),
					'company_image_and_name' => $this ->CI-> load -> view('company/company_image_and_name', 
						array(
							'company' => $company
						),
					TRUE),
					'breadcrumb' => $this ->CI-> load -> view('common/breadcrumb', 
						array('breadcrumb' => 
							array(
								$company['company_name'] => base_url() . "company/{$company['company_id']}",
								$page['page_name'] => base_url() . "page/{$page['page_id']}",
								$app['app_name'] => base_url() . "app/{$app['app_install_id']}"
								)
							)
						,
					TRUE),
					'app_profile' => $this ->CI-> load -> view('app/app_profile', 
						array('app_profile' => $app,
							'new_users' => $new_users,
							'all_users' => $all_users,
							'count_installed_on' => $this->CI->pages->count_pages_by_app_id($app['app_id']),
							'company_id' => $company['company_id']),
					TRUE),
					'app_tabs' => $this ->CI-> load -> view('app/app_tabs', 
						array(
							'campaign_count' => $campaign_count,
							'user_count' => $user_count
							),
					TRUE), 
					'app_campaigns' => $this ->CI-> load -> view('app/app_campaigns', 
						array(),
					TRUE),
					'app_users' => $this ->CI-> load -> view('app/app_users', 
						array(),
					TRUE),
					'footer' => $this ->CI-> socialhappen -> get_footer()
				);
				$result['success'] = TRUE;
			}
		}
		return $result;
    }
	
	/** 
	 * JSON : Gets app profile
	 * @param $app_install_id
	 * @return json $profile
	 * @author Manassarn M.
	 */
    function json_get_profile($app_install_id = NULL){
    	$this->CI->load->model('installed_apps_model','installed_apps');
		$profile = $this->CI->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		return json_encode($profile);
    }

    /**
	 * JSON : Get app campaigns
	 * @param $app_install_id
	 * @param $limit
	 * @param $offset
	 * @return json $campaigns
	 * @author Manassarn M.
	 */
	function json_get_campaigns($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('campaign_model','campaigns');
		$campaigns = $this->CI->campaigns->get_app_campaigns_by_app_install_id($app_install_id, $limit, $offset);
		return json_encode($campaigns);
	}

	/**
	 * JSON : Get campaigns
	 * @param $app_install_id
	 * @param $campaign_status_id
	 * @param $limit
	 * @param $offset
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status($app_install_id =NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this -> CI -> load -> model('campaign_model', 'campaigns');
		$campaigns = $this -> CI -> campaigns -> get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id, $campaign_status_id, $limit, $offset);
		return json_encode($campaigns);
	}

	/**
	 * JSON : Get app users
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_users($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_apps_model','user_apps');
		$users = $this->CI->user_apps->get_app_users_by_app_install_id($app_install_id, $limit, $offset);
		return json_encode($users);
	}

	/**
	 * JSON : Get pages
	 * @param : $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_pages($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('page_model','pages');
		$pages = $this->CI->pages->get_app_pages_by_app_install_id($app_install_id, $limit, $offset);
		return json_encode($pages);
	}
}

/* End of file app_ctrl.php */
/* Location: ./application/libraries/controller/app_ctrl.php */