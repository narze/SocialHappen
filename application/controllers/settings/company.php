<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($company_id = NULL){
		if(!$this->socialhappen->check_admin(array('company_id' => $company_id),array('role_company_edit'))){
			exit('You are not admin');
		}
		$this->load->library('settings');
		$setting_name = 'company';
		$this->settings->view_settings($setting_name, $company_id, $company_id);
	}
	
	
	function view($company_id = NULL){
		//$this->socialhappen->ajax_check();
		$this->load->library('form_validation');
		if(!$this->socialhappen->check_admin(array('company_id' => $company_id),array('role_company_edit'))){
			//no access
		} else {
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_company_id($company_id);
			
			$this->load->model('company_apps_model','company_apps');
			$company_apps = $this->company_apps->get_company_apps_by_company_id($company_id);
			
			$this->load->model('user_companies_model','user_companies');
			$company_users = $this->user_companies->get_company_users_by_company_id($company_id);
			
			$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_email', 'Contact email', 'required|trim|xss_clean|valid_email|max_length[255]');			
			$this->form_validation->set_rules('company_telephone', 'Contact telephone', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('company_website', 'Company website', 'trim|xss_clean|max_length[255]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/company', array('company'=>$company, 'company_apps' => $company_apps, 'company_users' => $company_users, 'success'=>$this->input->get('success')));
			}
			else 
			{
				if (!$company_image = $this->socialhappen->replace_image('company_image', $company['company_image'])){
					$company_image = $company['company_image'];
				}
				
				$company_update_data = array(
								'company_name' => set_value('company_name'),
								'company_detail' => set_value('company_detail'),
								'company_email' => set_value('company_email'),
								'company_telephone' => set_value('company_telephone'),
								'company_website' => set_value('company_website'),
								'company_image' => $company_image
							);
			
				if ($this->companies->update_company_profile_by_company_id($company_id, $company_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/company', array('company'=>array_merge($company,$company_update_data), 'company_apps' => $company_apps, 'company_users' => $company_users, 'success'=>TRUE));
				}
				else
				{
					log_message('error','update company failed');
					echo 'An error occurred saving your information. Please try again later';
				}
			}
		}
	}
	
	function admin($company_id = NULL){
		//$this->socialhappen->ajax_check();
		$this->load->library('form_validation');
		if(!$this->socialhappen->check_admin(array('company_id' => $company_id),array('role_company_edit'))){
			//no access
		} else {
			$this->form_validation->set_rules('user_id','required|trim|integer|xss_clean|max_length[20]');
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				redirect("settings/company/view/{$company_id}");
			}
			else 
			{
				if($this->socialhappen->check_user(set_value('user_id'))){
					$company_admin = array(
								'user_id' => set_value('user_id'),
								'company_id' => $company_id,
								'user_role' => 1 // Company Admin
							);
			
					if ($this->user_companies->add_user_company($company_admin)) // the information has therefore been successfully saved in the db
					{
						redirect("settings/company/view/{$company_id}?success=1");
					}
				}
				log_message('error','check user_id failed');
				redirect("settings/company/view/{$company_id}?error=1");
			}
		}
	}
}
/* End of file company.php */
/* Location: ./application/controllers/settings/company.php */