<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
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
		$campaigns = $this->campaigns->get_campaigns($page_id);
		echo json_encode($campaigns);
	}
	
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */