<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
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