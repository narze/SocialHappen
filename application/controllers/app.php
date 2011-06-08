<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index($app_install_id = NULL){
		$this->socialhappen->check_logged_in('home');
		if($app_install_id){
			$data = array(
						'app_install_id' => $app_install_id,
						'header' => $this->socialhappen->get_header(),
						'footer' => $this->socialhappen->get_footer()
					);
			$this->parser->parse('app/app_view', $data);
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
	
	/**
	 * JSON : Get pages
	 * @param : $app_install_id
	 * @author Manassarn M.
	 */
	function json_get_pages($app_install_id = NULL){
		$this->load->model('page_model','pages');
		$pages = $this->pages->get_app_pages_by_app_install_id($app_install_id);
		echo json_encode($pages);
	}
	
	/**
	 * JSON : Add app
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('installed_apps_model','installed_apps');
		$post_data = array(
							'company_id' =>  $this->input->post('company_id'),
							'app_id' =>  $this->input->post('app_id'),
							'app_install_status' =>  $this->input->post('app_install_status'),
							'page_id' =>  $this->input->post('page_id'),
							'app_install_secret_key' =>  $this->input->post('app_install_secret_key')
						);
		if($app_install_id = $this->installed_apps->add_installed_app($post_data)){
			$result['status'] = 'OK';
			$result['app_install_id'] = $app_install_id;
		} else {
			$result['status'] = 'ERROR';
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get app install status by app_install_status_name
	 * @param : $status_name
	 * @author Prachya P.
	 */
	function json_get_app_install_status_by_status_name($status_name = NULL){
		$this->load->model('app_model','app');
		$app_install_status = $this->app->get_app_install_status_by_status_name($status_name);
		echo json_encode($app_install_status);
	}
	
	/**
	 * JSON : Get all app install status
	 * @author Prachya P.
	 */
	function json_get_all_app_install_status(){
		$this->load->model('app_model','app');
		$app_install_statuses = $this->app->get_all_app_install_status();
		echo json_encode($app_install_statuses);
	}
	
	/**
	 * JSON : application profile by fb_app_api_key
	 * @param $fb_app_api_key
	 * @author Prachya P.
	 */
	function json_get_app_by_api_key($fb_app_api_key){
		$this->load->model('app_model','app');
		$app = $this->app->get_app_by_fb_app_api_key($fb_app_api_key);
		echo json_encode($app);
	}
}


/* End of file app.php */
/* Location: ./application/controllers/app.php */