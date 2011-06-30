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
			$this->socialhappen->logout();
			$data = array(
							'header' => $this -> socialhappen -> get_header( 
								array(
									'title' => 'Home page',
									'style' => array(
										'common/main',
										'common/platform'
									)
								)
							),
							'breadcrumb' => $this -> load -> view('common/breadcrumb', 
								array(
									'breadcrumb' => array( 
										'Facebook login' => NULL
									)
								),
							TRUE),
							'home' => $this->load->view('home/home',
								array(
									'facebook_app_id' => $this->config->item('facebook_app_id'),
									'facebook_default_scope' => $this->config->item('facebook_default_scope')
								),
							TRUE),
							'footer' => $this -> socialhappen -> get_footer()
						);
			$this->parser->parse('home/home_view',$data);
		} else {
			$this->socialhappen->login();
			$facebook_user = $this->facebook->getUser();
			$user = $user_companies = $popup_name = NULL;
			
			if($user = $this->socialhappen->get_user()){
				$this->load->model('user_companies_model','user_companies');
				if($user_companies = $this->user_companies->get_user_companies_by_user_id($user['user_id'])){
					$popup_name = 'select_company';
				} else {
					$popup_name = 'splash';
				}
			}
			$data = array(
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'Signup',
						'vars' => array(
							'popup_name' => $popup_name,
							'user_companies' => $user_companies
						),
						'script' => array(
							'common/bar',
							'common/fancybox/jquery.fancybox-1.3.4.pack',
							'common/jquery.form',
							'home/lightbox',
							'home/signup'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array(
						'breadcrumb' => array( 
							'Facebook login' => NULL
						)
					),
				TRUE),
				'tutorial' => $this -> load -> view('home/tutorial', 
					array(
						
					),
				TRUE),
				'signup_form' => $this -> load -> view('home/signup_form', 
					array(
						'user_profile_picture'=>$this->facebook->get_profile_picture($facebook_user['id'])
					),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
			$this -> parser -> parse('home/signup_view', $data);
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
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('home/create_company_form');
		}
		else 
		{
		 	$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => set_value('company_image'),
					       	'creator_user_id' => $this->socialhappen->get_user_id()
						);
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company), TRUE);
			
			if ($company_add_result['status'] == 'OK') 
			{
				$this->load->model('user_companies_model','user_companies');
				$user_company = array(
						'user_id' => $this->socialhappen->get_user_id(),
						'company_id' => $company_add_result['company_id'],
						'user_role' => 0
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
		$this->socialhappen->check_logged_in('home');
		$this->load->view('home/splash_view');
	}
	
	/**
	 * Select company page
	 * @author Manassarn M.
	 */
	function select_company(){
		$this -> socialhappen -> check_logged_in('home');
		$user = $this->socialhappen->get_user();
		
		$this->load->model('user_companies_model','user_companies');
		$user_companies = $this->user_companies->get_user_companies_by_user_id($user['user_id']);
		$data = array(
			'user_companies' => $user_companies,
		);
		$this->parser->parse('home/select_company_view', $data);
		return $data;
	}
	
	/**
	 * Signup form
	 * @author Manassarn M.
	 */
	function signup_form()
	{
		$facebook_user = $this->facebook->getUser();
		$this->load->model('user_model','users');
		if($this->users->get_user_id_by_user_facebook_id($facebook_user['id'])){
			echo 'You have already registered SocialHappen';
			return;
		}
		
		$user_image = $this->facebook->get_profile_picture($facebook_user['id']);
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this -> load -> view('home/signup_form', 
					array(
						'user_profile_picture'=>$user_image
					)
			);
		}
		else // passed validation proceed to post success logic
		{
			$config['upload_path'] = './uploads/images/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '100';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('user_image')){
				$upload_data = $this->upload->data();
				$user_image = base_url()."uploads/images/{$upload_data['file_name']}";
				$this->socialhappen->resize_image($upload_data,array(16,24,50,128));
			}
			if ($this->upload->do_upload('company_image')){
				$upload_data = $this->upload->data();
				$company_image = base_url()."uploads/images/{$upload_data['file_name']}";
				$this->socialhappen->resize_image($upload_data,array(16,24,50,128));
			}
			
			$user = array(
					       	'user_first_name' => set_value('first_name'),
					       	'user_last_name' => set_value('last_name'),
					       	'user_email' => set_value('email'),
					       	'user_image' => $user_image,
					       	'user_facebook_id' => $facebook_user['id']
						);
			
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => issetor($company_image)
						);
					
			$user_add_result = json_decode($this->curl->simple_post(base_url().'user/json_add', $user), TRUE);
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company), TRUE);
			if ($user_add_result['status'] == 'OK' && $company_add_result['status'] == 'OK')
			{	
				$this->load->model('user_companies_model','user_companies');
				$this->user_companies->add_user_company(array(
					'user_id' => $user_add_result['user_id'],
					'company_id' => $company_add_result['company_id']
				));
				$this->socialhappen->login('home');
			}
			else
			{
				echo 'Error occured';
			}
		}
	}
}  

/* End of file home.php */
/* Location: ./application/controllers/home.php */