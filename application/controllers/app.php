<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/** 
	 * JSON : get app profile by app_install_id
	 * @param $app_install_id
	 * @author Prachya P.
	 * 
	 */
	function json_get_profile($app_install_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$profile = $this->installed_apps->get_app_profile_by_id($app_install_id);
		echo json_encode($profile);
	}
	
	/**
	 * JSON : get campaign list by app_install_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_campaign_list($app_install_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$campaigns = $this->campaigns->get_campaigns_by_app_install_id($app_install_id);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : get member list by app_install_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_member_list($app_install_id = NULL){
		$this->load->model('user_apps_model','user_apps');
		$members = $this->user_apps->get_users_by_app_install_id($app_install_id);
		echo json_encode($members);
	}
}


/* End of file app.php */
/* Location: ./application/controllers/app.php */