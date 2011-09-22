<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configs extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
		$this->load->library('form_validation');
	}
	
	function index($page_id = NULL){
		if($this->input->get('p')){
			$page_id = $this->input->get('p');
		}
		$config_name = $this->input->get('c');
		$app_install_id = $this->input->get('id');
		$config_names_and_ids = array('signup_fields','badges','app');
	
		if(!in_array($config_name, $config_names_and_ids)){
			redirect("configs?p={$page_id}&c=signup_fields");
		}
		
		$this->load->model('page_model','page');
		$this->load->model('company_model','company');
		$this->load->model('installed_apps_model','installed_apps');
		$page = $this->page->get_page_profile_by_page_id($page_id);
			$company = $this -> company -> get_company_profile_by_company_id($page['company_id']);
			$user = $this->socialhappen->get_user();
			$this->load->vars(array(
				'page' => $page,
				'company' => $company,
				'user' => $user,
				'page_apps' => $this->installed_apps->get_installed_apps_by_page_id($page_id)
			));
			$data = array(
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'App configs',
						'vars' => array('page_id'=>$page_id,
										'config_name' => $config_name,
										'app_install_id' => $app_install_id),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'configs/main',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'go_back' => $this -> load -> view('configs/go_back', NULL, TRUE),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', NULL, TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							'Config' => NULL
							)
						)
					,
				TRUE),
				'sidebar' => $this -> load -> view('configs/sidebar', NULL, TRUE),
				'main' => $this -> load -> view("configs/main", NULL, TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
			$this -> parser -> parse('configs/configs_view', $data);
	}
	
	function signup_fields($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->model('page_model','page');
			$page = $this->page->get_page_profile_by_page_id($page_id);
			$fields = $this->page->get_user_field_templates();
			$this->load->vars(array(
				'page' => $page,
				'fields' => $fields
			));
			$this->load->view('configs/signup_fields');
		}
	}
	
	function badges($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
				
		}
	}
	
	function app($app_install_id = NULL){
		if(!$this->socialhappen->check_admin(array('app_install_id' => $app_install_id),array('role_app_edit','role_all_company_apps_edit'))){
			//no access
		} else {
				
		}
	
	}
}
/* End of file configs.php */
/* Location: ./application/controllers/configs.php */