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
			$data = array(
							'header' => $this -> socialhappen -> get_header( 
								array(
									'vars' => array(),
									'title' => 'Facebook login',
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
							'home' => $this->load->view('home/home', array(), TRUE),
							'footer' => $this -> socialhappen -> get_footer()
						);
			$this->parser->parse('home/home_view',$data);
	
	}
	
	/**
	 * Signup page
	 * @author Manassarn M.
	 */
	function signup(){
		$facebook_user = $this->facebook->getUser();
		
		$user = $this->socialhappen->get_user();
	
		$data = array(
			'header' => $this -> socialhappen -> get_header( 
				array(
					'title' => 'Signup',
					'script' => array(
						'common/functions',
						'common/jquery.form',
						'common/bar',
						'common/fancybox/jquery.fancybox-1.3.4.pack',
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
						'Signup' => NULL
					)
				),
			TRUE),
			'tutorial' => $this -> load -> view('home/tutorial', 
				array(
					
				),
			TRUE),
			'signup_form' => $this -> load -> view('home/signup_form', 
				array(
					'user' => $user,
					'user_profile_picture'=>$this->facebook->get_profile_picture($facebook_user['id'])
				),
			TRUE),
			'footer' => $this -> socialhappen -> get_footer()
			);
		$this -> parser -> parse('home/signup_view', $data);
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
		
		$user_facebook_image = $this->facebook->get_profile_picture($facebook_user['id']);
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this -> load -> view('home/signup_form', 
					array(
						'user_profile_picture'=>$user_facebook_image
					)
			);
		}
		else
		{
			if (!$user_image = $this->socialhappen->upload_image('user_image')){
				$user_image = $user_facebook_image;
			}
			$company_image = $this->socialhappen->upload_image('company_image');
			
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
				$this->socialhappen->login();
				$this->load->view('common/redirect',array('refresh_parent' => TRUE));
			}
			else
			{
				echo 'Error occured';
			}
		}
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
	
	function logout(){
		$this->socialhappen->logout();
	}
}  

/* End of file home.php */
/* Location: ./application/controllers/home.php */