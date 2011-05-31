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
	 * Signup page
	 * @author Manassarn M.
	 */
	function index(){
		$this->load->view('signup_view');
		
	}

	/**
	 * Signup form
	 * @author Manassarn M.
	 */
	function form()
	{
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
			$this->load->view('signup_form');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			$facebook_user = $this->facebook->getUser();
			$user = array(
					       	'user_first_name' => set_value('first_name'),
					       	'user_last_name' => set_value('last_name'),
					       	'user_email' => set_value('email'),
					       	'user_image' => set_value('user_image'),
					       	'user_facebook_id' => $facebook_user['id']
						);
			
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => set_value('company_image')
						);
					
			$user_add_result = json_decode($this->curl->simple_post(base_url().'user/json_add', $user));
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company));
		
			if ($user_add_result->status == 'OK' && $company_add_result->status == 'OK') // the information has therefore been successfully saved in the db
			{
				echo "User id = {$user_add_result->user_id}<br />";
				echo "Company id = {$company_add_result->company_id}";   // or whatever logic needs to occur
			}
			else
			{
				echo '$user_add_result->status = '.$user_add_result->status;
				echo '$company_add_result->status = '.$company_add_result->status;
			// Or whatever error handling is necessary
			}
		}

	}
	
	
}


/* End of file signup.php */
/* Location: ./application/controllers/signup.php */