<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($page_id = NULL){
		$this->load->library('settings');
		$setting_name = 'company';
		$this->settings->view_settings($setting_name, $page_id, NULL);
	}
	
	function view($page_id = NULL){
		//$this->socialhappen->ajax_check();
		$this->load->library('form_validation');
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->model('page_model','pages');
			$page = $this->pages->get_page_profile_by_page_id($page_id);
			$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
			
			$this->load->model('installed_apps_model','installed_apps');
			$page_apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
			
			$this->load->model('user_pages_model','user_pages');
			$page_users = $this->user_pages->get_page_users_by_page_id($page_id);
			
			$this->load->model('user_companies_model','user_companies');
			$company_users = $this->user_companies->get_company_users_by_company_id($page['company_id']);
			
			
			foreach($company_users as $key => &$value){ //Company admins
				if(!($company_users[$key]['role_all'] || $company_users[$key]['role_all_company_pages_edit'])){
					unset($company_users[$key]);
				}
			}
			
			foreach($page_users as $key => &$value){ //Page admins
				if(!($page_users[$key]['role_all'] || $page_users[$key]['role_page_edit'])){
					unset($page_users[$key]);
				} else {
					foreach($company_users as $company_user){
						if($company_user['user_id'] == $page_users[$key]['user_id']){
							unset($page_users[$key]);
							break;
						}
					}
				}
			}
			
			$page_facebook = $this->facebook->get_page_info($page['facebook_page_id']);
			
			$this->form_validation->set_rules('page_name', 'Page name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('page_detail', 'Page detail', 'trim|xss_clean');
			$this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/page', array('page'=>$page, 'page_apps' => $page_apps, 'company_users' => $company_users, 'page_users' => $page_users, 'page_facebook' => $page_facebook, 'page_user_fields' => $page_user_fields));
			}
			else 
			{
				if(set_value('use_facebook_picture')){
					$page_image = issetor($page_facebook['picture']);
				} else if (!$page_image = $this->socialhappen->replace_image('page_image', $page['page_image'])){
					$page_image = $page['page_image'];
				}
				
				$page_update_data = array(
								'page_name' => set_value('page_name'),
								'page_detail' => set_value('page_detail'),
								'page_image' => $page_image
							);
			
				if ($this->pages->update_page_profile_by_page_id($page_id,$page_update_data))
				{
					$this->load->view('settings/page', array('page'=>array_merge($page,$page_update_data), 'page_apps' => $page_apps, 'company_users' => $company_users, 'page_users' => $page_users, 'page_facebook' => $page_facebook, 'success'=>TRUE, 'page_user_fields' => $page_user_fields));
				}
				else
				{
					log_message('error','update company failed');
					echo 'An error occurred saving your information. Please try again later';
				}
			}
		}
	}
	
	function admin($page_id = NULL){
		//$this->socialhappen->ajax_check();
		$this->load->library('form_validation');
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->form_validation->set_rules('user_id','required|trim|integer|xss_clean|max_length[20]');
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			if ($this->form_validation->run() == FALSE)
			{
				redirect("settings/page/view/{$page_id}");
			}
			else 
			{
				$this->load->model('company_model','companies');
				$company = $this->companies->get_company_profile_by_page_id($page_id);
				if($this->socialhappen->check_user(set_value('user_id'))){
					if(!$this->user_companies->is_company_admin(set_value('user_id'),$company['company_id'])){
						$company_admin = array(
									'user_id' => set_value('user_id'),
									'company_id' => $company['company_id'],
									'user_role' => 2 //Page admin
								);
						$this->user_companies->add_user_company($company_admin);
					}
					$page_admin = array(
								'user_id' => set_value('user_id'),
								'page_id' => $page_id,
								'user_role' => 2 //Page admin
							);
					$this->user_pages->add_user_page($page_admin);
				}
				redirect("settings/page/view/{$page_id}?success=1");
			}
		}
	}
	
	/**
	 * DEPRECATED
	 */
	function user_fields($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('label', 'Label', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('type', 'Type', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('required', 'Required', 'trim|xss_clean');		
			//$this->form_validation->set_rules('rules', 'Rules', 'trim|xss_clean');			
			$this->form_validation->set_rules('items', 'Items', 'trim|xss_clean');			
			$this->form_validation->set_rules('order', 'Order', 'required|trim|xss_clean|is_numeric|max_length[5]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			$success = FALSE;
			if ($this->form_validation->run() != FALSE)
			{
				$this->load->model('page_model','pages');
				$new_page_user_fields = array(
					array(
						'name' => set_value('name'),
						'label' => set_value('label'),
						'type' => set_value('type'),
						'required' => set_value('required') == 1,
						//'rules' => set_value('rules'),
						'items' => explode(',',set_value('items')),
						'order' => set_value('order')
					)
				);
				
				//var_export($new_page_user_fields);
				if ($this->pages->add_page_user_fields_by_page_id($page_id, $new_page_user_fields))
				{
					$success = TRUE;
				}
				
			}
			
			$this->form_validation->set_rules('edit_name[]', 'Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('edit_label[]', 'Label', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('edit_type[]', 'Type', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('edit_required[]', 'Required', 'trim|xss_clean|max_length[1]');		
			//$this->form_validation->set_rules('edit_rules[]', 'Rules', 'trim|xss_clean');			
			$this->form_validation->set_rules('edit_items[]', 'Items', 'trim|xss_clean');			
			$this->form_validation->set_rules('edit_order[]', 'Order', 'required|trim|xss_clean|is_numeric|max_length[5]');
		
			$required_checked = $this->input->post('edit_required');
			$fields_count = count($this->input->post('edit_name'));
			$edit_required = array();
			foreach(range(0,$fields_count-1) as $nth){
				$edit_required[$nth] = in_array($nth, $required_checked) ? TRUE : FALSE;
			}
			
			$this->load->model('page_model','pages');
			$temp_page_user_fields = array(
				
				'name' => $this->input->post('edit_name'),
				'label' => $this->input->post('edit_label'),
				'type' => $this->input->post('edit_type'),
				'required' => $edit_required,
				//'rules' => $this->input->post('edit_rules'),
				'items' => $this->input->post('edit_items'),
				'order' => $this->input->post('edit_order')
			);
			$page_user_fields = array();
			$field_ids = $this->input->post('id');
			foreach($temp_page_user_fields as $field_name => $data_array) {
				foreach($data_array as $key => $value) {
					if($field_name == 'items' && $value != ''){
						$value = explode(',',$value);
					}
					$page_user_fields[$field_ids[$key]][$field_name] = $value;
				}
			}
			//var_export($page_user_fields);
			if ($this->pages->update_page_user_fields_by_page_id($page_id, $page_user_fields))
			{
				$success = TRUE;
			}
			
			if($success) {
				redirect("settings/page/view/{$page_id}?success=1");
			}
			redirect("settings/page/view/{$page_id}");
		}
	}

	function disable_facebook_tab($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->model('page_model');
			$result = $this->page_model->update_page_profile_by_page_id($page_id, array('enable_facebook_page_tab' => 0));
			$this->load->vars(array(
				'partial' => 'facebook-page-information',
				'page' => $page = $this->page_model->get_page_profile_by_page_id($page_id),
				'page_facebook' => $this->facebook->get_page_info($page['facebook_page_id'])
			));
			$socialhappen_facebook_app_id = $this->config->item('facebook_app_id');
			$remove_facebook_tab_result = $this->facebook->remove_facebook_page_tab($socialhappen_facebook_app_id, $page['facebook_page_id']);
			$this->load->view('settings/page');
		}
	}

	function enable_facebook_tab($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->model('page_model');
			$result = $this->page_model->update_page_profile_by_page_id($page_id, array('enable_facebook_page_tab' => 1));
			$this->load->vars(array(
				'partial' => 'facebook-page-information',
				'page' => $page = $this->page_model->get_page_profile_by_page_id($page_id),
				'page_facebook' => $this->facebook->get_page_info($page['facebook_page_id'])
			));
			$socialhappen_facebook_app_id = $this->config->item('facebook_app_id');
			$install_facebook_tab_result = $this->facebook->install_facebook_app_to_facebook_page_tab($socialhappen_facebook_app_id, $page['facebook_page_id']);
			$this->load->view('settings/page');
		}
	}
}
/* End of file page.php */
/* Location: ./application/controllers/settings/page.php */