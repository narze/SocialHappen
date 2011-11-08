<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_apps extends CI_Controller {

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
			redirect("settings/page_apps?p={$page_id}&c=signup_fields");
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
			$default_fields = $this->page->get_user_field_templates();
			$signup_fields = $this->page->get_page_user_fields_by_page_id($page_id);
			$field_names = array_map(create_function('$field', 'return $field["name"];'), $signup_fields);
			$this->load->vars(array(
				'page' => $page,
				'signup_fields' => $signup_fields,
				'signup_field_names' => $field_names,
				'test' => print_r($this->input->post(),true),
				'default_fields' => $default_fields,
				'updated' => FALSE
			));
			if(!$this->input->post('submit-form')){
			
			} else {
				$submitted_fields = $this->input->post();
				
				unset($submitted_fields['submit-form']);
				//signup - fields = remove
				//fields - signup = add
				foreach($signup_fields as $key => $signup_field){
					if(isset($submitted_fields[$signup_field['name']])){
						unset($signup_fields[$key]); //To remove
						//unset($submitted_fields[$signup_field['name']]); //To add, unused because we need to update
					}
				}
				
				$add_result = $this->page->add_page_user_fields_by_page_id($page_id,$submitted_fields);
				
				$remove_ids = array_keys($signup_fields);
				$this->page->remove_page_user_fields_by_page_id($page_id,$remove_ids);
			
				$this->load->vars(array(
					'signup_fields' => $this->page->get_page_user_fields_by_page_id($page_id),
					'updated' => TRUE
				));
				
			}
			
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
/* End of file page_apps.php */
/* Location: ./application/controllers/settings/page_apps.php */