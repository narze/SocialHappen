<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bar extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	/**
	 * Create company
	 * @author Manassarn M.
	 */
	function create_company(){
		$this->socialhappen->check_logged_in();
		$this->load->view('bar/create_company_view');
	}

	/**
	 * Create company form
	 * @author Manassarn M.
	 * @todo views for created/error
	 */
	function create_company_form(){
		$this->socialhappen->check_logged_in();
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('bar/create_company_form');
		}
		else 
		{
			$company_image = $this->socialhappen->upload_image('company_image');
		 	$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => !$company_image ? base_url().'images/thumb80-80-3.jpg' : $company_image,
					       	'creator_user_id' => $this->socialhappen->get_user_id()
						);
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company), TRUE);
			
			if ($company_add_result['status'] == 'OK') 
			{
				$this->load->model('user_companies_model','user_companies');
				$user_company = array(
						'user_id' => $this->socialhappen->get_user_id(),
						'company_id' => $company_add_result['company_id'],
						'user_role' => 1
					);
				if($this->user_companies->add_user_company($user_company)){
					echo "Company created<br />";  
					echo anchor("company/{$company_add_result['company_id']}", 'Go to company');
				} else {
					echo "Error adding user company";
				}
			}
			else
			{
				echo 'Error adding company';
			}
		}
	}

	/**
	 * Splash page
	 * @author Manassarn M.
	 */
	function splash(){
		$this->socialhappen->check_logged_in();
		$this->load->view('bar/splash_view');
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
		$data = array(
			'user_companies' => $user_companies,
		);
		$this->parser->parse('bar/select_company_view', $data);
		return $data;
	}

	
	/**
	 * JSON : Check login
	 * @param $redirect_url
	 * @author Manassarn M.
	 */
	function json_check_login($redirect_path = NULL, $current_url = NULL){
		$this->socialhappen->ajax_check();
		$json = array(
			'logged_in' => $this->socialhappen->is_logged_in(),
			'redirect' => base_url().issetor($redirect_path,'')
		);
		echo json_encode($json);
	}
}  

/* End of file bar.php */
/* Location: ./application/controllers/bar.php */