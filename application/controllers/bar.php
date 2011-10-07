<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bar extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}

	/**
	 * Create company form
	 * @author Manassarn M.
	 * @todo views for created/error
	 */
	function create_company(){
		$this->socialhappen->check_logged_in();
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('bar/create_company_view');
		}
		else 
		{
			$company_image = $this->socialhappen->upload_image('company_image');
		 	$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => !$company_image ? base_url().'assets/images/default/company.png' : $company_image,
					       	'creator_user_id' => $this->socialhappen->get_user_id()
						);
			$this->load->model('company_model', 'companies');
			$company_id = $this->companies->add_company($company);
			
			if ($company_id) 
			{
				$this->load->model('user_companies_model','user_companies');
				$user_company = array(
						'user_id' => $this->socialhappen->get_user_id(),
						'company_id' => $company_id,
						'user_role' => 1 //Company Admin
					);
				if($result = $this->user_companies->add_user_company($user_company)){
					$this->load->view('common/redirect',array('redirect_parent'=>base_url().'company/'.$company_id));
				} else {
					echo "Error adding user company";
				}
			}
			else
			{
				log_message('error','company add failed');
				echo 'Error adding company';
			}
		}
	}
	
	/**
	 * Select company page
	 * @author Manassarn M.
	 */
	function select_company(){
		$this -> socialhappen -> check_logged_in();
		$user = $this->socialhappen->get_user();
		
		$this->load->model('user_companies_model','user_companies');
		$user_companies = $this->user_companies->get_user_companies_by_user_id($user['user_id']);
		
		if($user_companies)
		{
			$this->load->model('page_model','page');
			$this->load->model('installed_apps_model','installed_app');
			$this->load->model('campaign_model','campaigns');
			foreach($user_companies as &$company) 
			{
				$company['page_count'] = $this->page->count_all(array("company_id" => $company['company_id']));
				$company['app_count'] = $this->installed_app->count_all_distinct("app_id",array("company_id" => $company['company_id']));
				$company['campaign_count'] = $this->campaigns->count_campaigns_by_company_id($company['company_id']);
			}
		}
		
		$data = array(
			'user_companies' => $user_companies,
			'user_can_create_company' => $this->socialhappen->check_package_by_user_id_and_mode($user['user_id'], 'company')  //Check user can create company
		);
		$this->parser->parse('bar/select_company_view', $data);
		return $data;
	}
}  

/* End of file bar.php */
/* Location: ./application/controllers/bar.php */