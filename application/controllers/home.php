<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}

	/**
	 * Home page
	 * @author Manassarn M.
	 */
	function index(){
		if(!$this->facebook->is_authentication()){
			$data = array(
							'facebook_app_id' => $this->config->item('facebook_app_id'),
							'facebook_default_scope' => $this->config->item('facebook_default_scope'),
							'header' => $this -> socialhappen -> get_header( 
								array(
									'title' => 'Home page',
									'script' => array(
										''
									)
								)
							),
							'footer' => $this -> socialhappen -> get_footer()
						);
			$this->parser->parse('home/home_view',$data);
		} else if(!$profile = $this->socialhappen->get_user()){
			$data = array(
							'header' => $this -> socialhappen -> get_header( 
								array(
									'title' => 'Login page',
									'script' => array(
										''
									)
								)
							),
							'footer' => $this -> socialhappen -> get_footer()
						);
			$this->parser->parse('home/login_view',$data);
		} else {
			$this->load->model('user_companies_model','user_companies');
			if($companies = $this->user_companies->get_user_companies_by_user_id($profile['user_id'])){
				redirect('home/select_company');
			} else {
				redirect('home/splash');
			}
		}	
	}
	
	/**
	 * Login and redirect to home page
	 * @author Manassarn M.
	 */
	function login(){
		$this->socialhappen->login('home');
	}
	
	/**
	 * Logout and redirect to home page
	 * @author Manassarn M.
	 */
	function logout(){
		$this->socialhappen->logout('home');
	}
	
	/**
	 * Create company
	 * @author Manassarn M.
	 */
	function create_company(){
		$this->socialhappen->check_logged_in('home');
		$this->load->view('home/create_company_view');
	}

	/**
	 * Create company form
	 * @author Manassarn M.
	 * @todo views for created/error
	 */
	function create_company_form(){
		$this->socialhappen->check_logged_in('home');
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');			
		$this->form_validation->set_rules('company_image', 'Company image', 'trim|xss_clean|max_length[255]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('home/create_company_form');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => set_value('company_image'),
					       	'creator_user_id' => $this->socialhappen->get_user_id()
						);
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company), TRUE);
			
			if ($company_add_result['status'] == 'OK') // the information has therefore been successfully saved in the db
			{
				$this->load->model('user_companies_model','user_companies');
				$user_company = array(
						'user_id' => $this->socialhappen->get_user_id(),
						'company_id' => $company_add_result['company_id'],
						'user_role' => 0
					);
				if($this->user_companies->add_user_company($user_company)){
					echo "Company created<br />";   // or whatever logic needs to occur
					echo anchor("company/{$company_add_result['company_id']}", 'Go to company');
				} else {
				echo "ERROR : cannot add user company";
				}
			}
			else
			{
				echo '$company_add_result->status = '.$company_add_result['status'];
			// Or whatever error handling is necessary
			}
		}
	}

	/**
	 * Splash page
	 * @author Manassarn M.
	 */
	function splash(){
		$this->socialhappen->check_logged_in('home');
		$this->load->view('home/splash_view');
	}
	
	/**
	 * Select company page
	 * @author Manassarn M.
	 */
	function select_company(){
		$this->socialhappen->check_logged_in('home');
		$user = $this->socialhappen->get_user();
		$this->load->model('user_companies_model','user_companies');
		$data = array(
						'user_companies' => $this->user_companies->get_user_companies_by_user_id($user['user_id']),
						'user' => $user,
						'header' => $this -> socialhappen -> get_header( 
							array(
								'title' => 'Select company',
								'script' => array(
									''
								)
							)
						),
						'footer' => $this -> socialhappen -> get_footer()
					);
		$this->parser->parse('home/select_company_view', $data);
	}
}  

/* End of file home.php */
/* Location: ./application/controllers/home.php */