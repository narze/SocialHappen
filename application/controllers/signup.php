<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Signup
 * @category Controller
 */
class Signup extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('user_model','users');
		$this->load->model('company_model','companies');
	}

	/**
	 * Register form
	 * @author Manassarn M.
	 */
	function index(){
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');			
		$this->form_validation->set_rules('user_image', 'User image', 'trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');			
		$this->form_validation->set_rules('company_image', 'Company image', 'trim|xss_clean|max_length[255]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('signup_view');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$user = array(
					       	'user_first_name' => set_value('first_name'),
					       	'user_last_name' => set_value('last_name'),
					       	'user_email' => set_value('email'),
					       	'user_image' => set_value('user_image')
						);
			
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => set_value('company_image')
						);
					
			// run insert model to write data to db
			
			if ($user_id = $this->users->add_user($user) && $company_id = $this->companies->add_company($company)) // the information has therefore been successfully saved in the db
			{
				redirect('signup/success');   // or whatever logic needs to occur
			}
			else
			{
			echo 'An error occurred saving your information. Please try again later';
			// Or whatever error handling is necessary
			}
		}
		$data = array();
		
	}

		function success()
	{
			echo 'this form has been successfully submitted with all validation being passed. All messages or logic here. Please note
			sessions have not been used and would need to be added in to suit your app';
	}
	
	
}


/* End of file signup.php */
/* Location: ./application/controllers/signup.php */