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
										'common/platform',
										'common/main'
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
		if($this->socialhappen->get_user()){
			redirect();
		}
		
		$facebook_user = $this->facebook->getUser();
		$this->load->model('user_model','users');
		if($this->users->get_user_id_by_user_facebook_id($facebook_user['id'])){
			$is_registered = true;
		}
	
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
					),
					'user_companies' => FALSE
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
			'footer' => $this -> socialhappen -> get_footer(),
			'is_registered' => $is_registered
			);
		
		if(!$is_registered) 
		{
			$data['signup_form'] = $this -> load -> view('home/signup_form', 
				array(
					'user_profile_picture'=>$this->facebook->get_profile_picture($facebook_user['id']),
				),
			TRUE);
		}
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
						'user_profile_picture'=>$user_facebook_image,
						'is_registered'=>$is_registered
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
						
			$user_id = $this->users->add_user($user);
			
			$company = array(
					       	'company_name' => set_value('company_name'),
					       	'company_detail' => set_value('company_detail'),
					       	'company_image' => $company_image ? $company_image : $this->socialhappen->get_default_url('company_image'),
							'creator_user_id' => $user_id
						);
			$this->load->model('company_model','company');
			$company_id = $this->company->add_company($company);
			
			if ($user_id && $company_id)
			{	
				$this->load->model('user_companies_model','user_companies');
				$this->user_companies->add_user_company(array(
					'user_id' => $user_id,
					'company_id' => $company_id,
					'user_role' => 1 //Company admin
				));
				$this->socialhappen->login();
				if($this->input->post('package_id')) 
				{
					$redirect_path = base_url().'home/package?package_id='. $this->input->post('package_id') .'&payment=true';
				}
				else
				{
					//$redirect_path = base_url().'?logged_in=true';
					$redirect_path = base_url().'home/package?payment=true';
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
	 * Signup complete
	 * @author Weerapat P.
	 */
	function signup_complete()
	{
		$user = $this->socialhappen->get_user();
		$this->load->model('package_users_model','package_users');
		$data = array(
			'package' => $this->package_users->get_package_by_user_id($user['user_id'])
		);
		$this->load->view('home/signup_complete', $data);
	}
	
	/**
	 * Package page
	 * @author Weerapat P.
	 */
	function package()
	{
		
		$this->load->model('package_model','package');
		$this->load->model('package_users_model','package_users');
		$user = $this->socialhappen->get_user();
		$user_current_package = $this->package_users->get_package_by_user_id($user['user_id']);
		
		$user_current_package_id = isset($user_current_package['package_id']) ? $user_current_package['package_id'] : 0;
		$user_current_package_price = isset($user_current_package['package_price']) ? $user_current_package['package_price'] : 0;
		
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
						'common/platform',
						'common/main',
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
					'user_id' => $user['user_id'],
					'user_current_package_id' => $user_current_package_id,
					'user_current_package_price' => $user_current_package_price
				),
			TRUE),
			'footer' => $this -> socialhappen -> get_footer()
			);
		
		$this -> parser -> parse('home/home_view', $data);
	}
	
	/**
	 * App page - show all applications
	 * @author Weerapat P.
	 */
	function apps($app_id = NULL)
	{
		$this->load->model('app_model','app');
		
		$data = array(
			'header' => $this -> socialhappen -> get_header( 
				array(
					'title' => 'Applications',
					'script' => array(
						'common/functions',
						'common/jquery.form',
						'common/bar',
						'common/fancybox/jquery.fancybox-1.3.4.pack',
						'home/lightbox',
						'payment/payment'
					),
					'style' => array(
						'common/platform',
						'common/main',
						'common/fancybox/jquery.fancybox-1.3.4'
					)
				)
			),
			'breadcrumb' => $this->load->view('common/breadcrumb', 
				array(
					'breadcrumb' => array( 
						'Applications' => NULL
					)
				),
			TRUE),
			'footer' => $this->socialhappen->get_footer()
		);
		
		if(!$app_id)
		{
			$data['home'] = $this->load->view('home/apps', 
				array(
					'apps' => $this->app->get_all_apps()
				),
			TRUE);
		}
		else
		{
			$data['home'] = $this->load->view('home/app', 
				array(
					'app' => $this->app->get_app_by_app_id($app_id)
				),
			TRUE);
		}
		
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
			'next' => $this->input->get('next') ? $this->input->get('next') : ''
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