<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index($page_id = NULL){
		if ($page_id) {
			$data['page_id'] = $page_id;
			$this->load->view('page_view', $data);
			return $data;
		}
	}
	
	/**
	 * JSON : Get page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_profile($page_id = NULL){
		$this->load->model('page_model','pages');
		$profile = $this->pages->get_page_profile_by_page_id($page_id);
		echo json_encode($profile);
	}
	
	/** 
	 * JSON : Get install apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($page_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
		echo json_encode($apps);
	}
	
	/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($page_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$campaigns = $this->campaigns->get_page_campaigns_by_page_id($page_id);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_users($page_id = NULL){
		$this->load->model('user_model','users');
		$users = $this->users->get_page_users_by_page_id($page_id);
		echo json_encode($users);
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */