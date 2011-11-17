<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_signup_fields extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($page_id = NULL){
		$this->load->library('settings');
		$config_name = 'signup_fields';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){
		$this->load->library('form_validation');
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
			
				//add to get-started done list
				if(count($submitted_fields) > 0 && count($remove_ids) == 0) {
					$this->load->model('get_started_model', 'get_started');
					$this->get_started->add_get_started_stat($page_id, 'page', array(101));
				}

				$this->load->vars(array(
					'signup_fields' => $this->page->get_page_user_fields_by_page_id($page_id),
					'updated' => TRUE
				));
				
			}
			
			$this->load->view('settings/page_apps/signup_fields');
		}
	}
}
/* End of file page_signup_fields.php */
/* Location: ./application/controllers/settings/page_signup_fields.php */