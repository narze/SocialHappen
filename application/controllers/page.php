<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/**
	 * JSON : get page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_profile($page_id = NULL){
		$this->load->model('page_model','pages');
		$profile = $this->pages->get_page_profile_by_id($page_id);
		echo json_encode($profile);
	}
	
	/** 
	 * JSON : get installed app list
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_installed_app_list($page_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps($page_id);
		echo json_encode($apps);
	}
	
	/**
	 * JSON : get campaign list
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_campaign_list($page_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$campaigns = $this->campaigns->get_campaigns_by_page_id($page_id);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : get member list
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_member_list($page_id = NULL){
		$this->load->model('user_model','users');
		$members = $this->users->get_page_users($page_id);
		echo json_encode($members);
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */