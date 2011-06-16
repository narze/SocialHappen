<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in('home');
		
		$this->load->library('form_validation');
	}
	
	function index($company_id = NULL, $setting_name = NULL, $param_id = NULL){
		$setting_names_and_ids = array('account'=>'user_id','company'=>'company_id','page'=>'page_id','package'=>'user_id','reference'=>'user_id');
		if($company_id){
			if(!array_key_exists($setting_name, $setting_names_and_ids)){
				redirect("settings/{$company_id}/account/".$this->socialhappen->get_user_id());
			}
			$user = $this->socialhappen->get_user();
			$user_companies = $this->socialhappen->get_user_companies();
			$this->load->model('page_model','pages');
			$company_pages = array();
			foreach ($user_companies as $user_company){
				$company_pages[$user_company['company_id']] = $this->pages->get_company_pages_by_company_id($user_company['company_id']);
			}
			
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_company_id($company_id);
			$data = array(
				'company_id' => $company_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'Settings',
						'vars' => array('company_id'=>$company_id,
										'setting_name' => $setting_name,
										'param_id' => $param_id),
						'script' => array(
							'common/bar',
							'settings/account',
							'settings/company',
							'settings/sidebar',
							'settings/main'
						),
						'style' => array(
							'common/main',
							'settings/main'
						)
					)
				),
				'go_back' => $this -> load -> view('settings/go_back', 
					array(
						'company' => $company
					),
				TRUE),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							'Settings' => base_url() . "settings"
							)
						)
					,
				TRUE),
				'sidebar' => $this -> load -> view('settings/sidebar', 
					array(
						'company_pages' => $company_pages
					),
				TRUE),
				'main' => $this -> load -> view("settings/main", 
					array(
						//'setting_name' => $setting_name,
						//$setting_names_and_ids[$setting_name] => $param_id
					),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
			$this -> parser -> parse('settings/settings_view', $data);
		}
	}
	
	function account($user_id = NULL){
		$this->output->enable_profiler(TRUE);
		if($user_id && $user_id == $this->socialhappen->get_user_id()){
			$user = $this->socialhappen->get_user();
		
			$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');			
			$this->form_validation->set_rules('user_image', 'User image', 'trim|xss_clean|max_length[255]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/account', array('user'=>$user));
			}
			else // passed validation proceed to post success logic
			{
				// build array for the model
				$user_update_data = array(
								'user_first_name' => set_value('first_name'),
								'user_last_name' => set_value('last_name'),
								'user_email' => set_value('email'),
								'user_image' => set_value('user_image')
							);
				$this->load->model('user_model','users');
				if ($this->users->update_user_profile_by_user_id($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/account', array('user'=>$user,'success' => TRUE));
				}
				else
				{
					echo 'error occured';
				}
			}
		}
	}
	
	function company_pages($user_id = NULL){
	
	}
	
	function company($company_id = NULL){
		if($company_id) {
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_company_id($company_id);
			$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_email', 'Contact email', 'required|trim|xss_clean|valid_email|max_length[255]');			
			$this->form_validation->set_rules('company_telephone', 'Contact telephone', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('company_website', 'Company website', 'trim|xss_clean|max_length[255]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/company', array('company'=>$company));
			}
			else // passed validation proceed to post success logic
			{
				// build array for the model
				
				$company_update_data = array(
								'company_name' => set_value('company_name'),
								'company_detail' => set_value('company_detail'),
								'company_email' => set_value('company_email'),
								'company_telephone' => set_value('company_telephone'),
								'company_website' => set_value('company_website')
							);
			
				if ($this->companies->update_company_profile_by_company_id($company_id, $company_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/company', array('company'=>$company, 'success'=>TRUE));
				}
				else
				{
				echo 'An error occurred saving your information. Please try again later';
				// Or whatever error handling is necessary
				}
			}
		}
	}
	
	function page($page_id = NULL){
	
	}
	
	function package(){
	
	}
	
	function reference(){
	
	}
}
/* End of file settings.php */
/* Location: ./application/controllers/settings.php */