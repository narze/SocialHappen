<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
		$this->load->library('form_validation');
		//$this->facebook->authentication($this->uri->uri_string());
	}

	/**
	 * Home page
	 * @author Manassarn M.
	 */
	function index(){
		$data['authenticate'] = $this->facebook->is_authentication();
		if($data['authenticate']){
			$facebook_user = $this->facebook->getUser();
			$this->load->model('user_model','users');
			if($profile = $this->users->get_user_profile_by_user_facebook_id($facebook_user['id'])){
				$this->load->model('user_companies_model','user_companies');
				echo $profile->user_id;
				if($companies = $this->user_companies->get_user_companies_by_user_id($profile->user_id)){
					var_dump($companies);
				} else {
					redirect('home/create_company');
				}
			} else {
				redirect('signup');
			}
		}else{		
			$data['facebook_app_id'] = $this->config->item('facebook_app_id');
			$data['facebook_default_scope'] = $this->config->item('facebook_default_scope');
		}
		
		$this->load->view('home_view',$data);
	}
	
	/**
	 * Create company
	 * @author Manassarn M.
	 */
	function create_company(){
		$this->load->view('create_company_view');
	}

	/**
	 * Create company form
	 * @author Manassarn M.
	 */
	function create_company_form(){		
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');			
		$this->form_validation->set_rules('company_image', 'Company image', 'trim|xss_clean|max_length[255]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('create_company_form');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => set_value('company_image')
						);
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company));
		
			if ($company_add_result->status == 'OK') // the information has therefore been successfully saved in the db
			{
				echo "Company id = {$company_add_result->company_id}";   // or whatever logic needs to occur
			}
			else
			{
				echo '$company_add_result->status = '.$company_add_result->status;
			// Or whatever error handling is necessary
			}
		}
	}
}  

/* End of file home.php */
/* Location: ./application/controllers/home.php */