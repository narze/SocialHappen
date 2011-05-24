<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index($app_install_id = NULL){
		if($app_install_id){
			$data['app_install_id'] = $app_install_id;
			$this->load->view('app_view',$data);	
			return $data;
		}
	}
	
	/** 
	 * JSON : Gets app profile
	 * @param $app_install_id
	 * @author Prachya P.
	 */
	function json_get_profile($app_install_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$profile = $this->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		echo json_encode($profile);
	}
	
	/**
	 * JSON : Get app campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($app_install_id = NULL){
		$this->load->model('campaign_model','campaigns');
		$campaigns = $this->campaigns->get_app_campaigns_by_app_install_id($app_install_id);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get app users
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_users($app_install_id = NULL){
		$this->load->model('user_apps_model','user_apps');
		$users = $this->user_apps->get_app_users_by_app_install_id($app_install_id);
		echo json_encode($users);
	}
}


/* End of file app.php */
/* Location: ./application/controllers/app.php */