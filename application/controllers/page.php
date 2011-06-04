<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {
	function __construct(){
		parent::__construct();
	}

	function index($page_id = NULL){
		$this->socialhappen->check_logged_in('home');
		if ($page_id) {
			$data = array(
						'page_id' => $page_id,
						'header' => $this->socialhappen->get_header(),
						'footer' => $this->socialhappen->get_footer()
					);
			$this->parser->parse('page/page_view', $data);
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

	/**
	 * JSON : Add page
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('page_model','pages');
		$post_data = array(
							'facebook_page_id' => $this->input->post('facebook_page_id'),
							'company_id' => $this->input->post('company_id'),
							'page_name' => $this->input->post('page_name'),
							'page_detail' => $this->input->post('page_detail'),
							'page_all_member' => $this->input->post('page_all_member'),
							'page_new_member' => $this->input->post('page_new_member'),
							'page_image' => $this->input->post('page_image')
						);
		if($page_id = $this->pages->add_page($post_data)){
			$result['status'] = 'OK';
			$result['page_id'] = $page_id;
		} else {
			$result['status'] = 'ERROR';
		}
		echo json_encode($result);
	}
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */