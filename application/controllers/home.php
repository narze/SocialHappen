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
						
			$user_add_result = json_decode($this->curl->simple_post(base_url().'user/json_add', $user), TRUE);
			
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => issetor($company_image),
							'creator_user_id' => $user_add_result['user_id']
						);
			
			$company_add_result = json_decode($this->curl->simple_post(base_url().'company/json_add', $company), TRUE);
			if ($user_add_result['status'] == 'OK' && $company_add_result['status'] == 'OK')
			{	
				$this->load->model('user_companies_model','user_companies');
				$this->user_companies->add_user_company(array(
					'user_id' => $user_add_result['user_id'],
					'company_id' => $company_add_result['company_id'],
					'user_role' => 1 //Company admin
				));
				$this->socialhappen->login();
				if($this->input->post('package_id')) 
				{
					$redirect_path = base_url().'home/package?package_id='. $this->input->post('package_id') .'&payment=true';
				}
				else
				{
					$redirect_path = base_url().'?logged_in=true';
				}
				$this->load->view('common/redirect',array('redirect_parent'=>$redirect_path));
			}
			else
			{
				echo 'Error occured';
			}
		}
	}
	
	/**
	 * Package page
	 * @author Weerapat P.
	 */
	function package()
	{
		$this->load->model('package_model','package');
		$user = $this->socialhappen->get_user();
		$this->load->model('package_users_model','package_users');
		$user_current_package = $this->package_users->get_package_by_user_id($user['user_id']);
		
		$data = array(
			'header' => $this -> socialhappen -> get_header( 
				array(
					'title' => 'Package',
					'script' => array(
						'common/functions',
						'common/jquery.form',
						'common/bar',
						'common/fancybox/jquery.fancybox-1.3.4.pack',
						'home/lightbox',
						'payment/payment'
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
						'Package' => NULL
					)
				),
			TRUE),
			'tutorial' => $this -> load -> view('home/tutorial', 
				array(
					
				),
			TRUE),
			'home' => $this -> load -> view('home/package', 
				array(
					'packages' => $this->package->get_packages(),
					'user_current_package' => $user_current_package
					//'user' => $user,
					//'facebook_user' => $facebook_user,
					//'user_profile_picture'=>$this->facebook->get_profile_picture($facebook_user['id'])
				),
			TRUE),
			'footer' => $this -> socialhappen -> get_footer()
			);
		$this -> parser -> parse('home/home_view', $data);
	}
	
	/**
	 * Facebook connect popup
	 * @author Weerapat P.
	 */
	function facebook_connect() {
		$this->socialhappen->ajax_check();
		$data = array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_default_scope' => $this->config->item('facebook_default_scope'),
			'next' => $this->config->item('next')
		);
		$this -> load -> view('home/facebook_connect', $data);
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