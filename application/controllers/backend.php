<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backend extends CI_Controller {
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * index page for backend
	 * @todo: authentication
	 */
	function index(){
		redirect('backend/dashboard');
	}
	
	/**
	 * backend dashboard
	 */
	function dashboard(){
		$data = array();
		
		$this->load->model('App_model', 'App');
		$data['app_list'] = $this->App->get_app_list();
		
		$this->load->model('Company_model', 'Company');
		$data['company_list'] = $this->Company->get_company_list();
		
		$this->load->view('backend_views/backend_dashboard_view', $data);	
	}
	
	/**
	 * see community detail
	 */
	function view_company($company_id){
		$data = array();
		
		$this->load->model('Company_model', 'Company');
		$this->load->model('App_model', 'App');
		$this->load->model('Company_apps_model', 'Company_apps');
		
		$company = $this->Company->get_company($company_id, 1, 0);
		$data['company'] = $company[0];
		
		$app_id_list = $this->Company_apps->get_app_by_company($company_id);
		
		$app_list = array();
		
		foreach($app_id_list as $app){
			$app_meta = $this->App->get_app($app->app_id);
			$app_meta = $app_meta[0];
			foreach($app_meta as $var => $key) {
				$app -> {$var} = $key;
			}
			$app_list[] = $app; 
		}
		
		$data['app_list'] = $app_list;
		
		$this->load->view('backend_views/backend_company_view', $data);	
	}
	
	/**
	 * edit company
	 */
	function edit_company($company_id){
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set up validation rules
			// set up validation rules
		$config = array(
						array(
							 'field'   => 'company_name',
							 'label'   => 'Company Name',
							 'rules'   => 'required|trim'
						),
						array(
							 'field'   => 'company_address',
							 'label'   => 'Copany Address',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'company_email',
							 'label'   => 'Company Email',
							 'rules'   => 'required|trim|valid_email|xss_clean'
						),
						array(
							 'field'   => 'company_telephone',
							 'label'   => 'Company Telephone',
							 'rules'   => 'trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		
		if($this->form_validation->run()){
			$this->load->model('Company_model', 'Company');
			$this->App->update(array('company_name' => $this->input->post('company_name', TRUE),
								'company_address' => $this->input->post('company_address', TRUE),
								'company_email' => $this->input->post('company_email',TRUE),
								'company_telephone' => $this->input->post('company_telephone', TRUE))
								, array('company_id' => $company_id));
			redirect('backend');
		}else{
			$this->load->model('Company_model', 'Company');
			$company = $this->Company->get_company($company_id, 1, 0);
			$company = $company[0];
			$data['company_name'] = $company->company_name;
			$data['company_address'] = $company->company_address;
			$data['company_email'] = $company->company_email;
			$data['company_telephone'] = $company->company_telephone;
			$data['company_id'] = $company_id;
			$this->load->view('backend_views/edit_company_view', $data);	
		}
	}
	
	/**
	 * add new app to platform
	 */
	function add_new_app(){
		
		$this->load->helper('form');
		$this->load->library('form_validation');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'app_name',
							 'label'   => 'App Name',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_description',
							 'label'   => 'App Description',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_url',
							 'label'   => 'App URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_install_url',
							 'label'   => 'App Install URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_config_url',
							 'label'   => 'App Config URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_support_page_tab',
							 'label'   => 'App Support Page Tab',
							 'rules'   => 'xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
				
		if($this->form_validation->run()){
			$this->load->model('App_model', 'App');
			$this->App->add(array('app_name' => $this->input->post('app_name', TRUE),
								'app_maintainance' => FALSE,
								'app_show_in_list' => TRUE,
								'app_url' => str_replace(';', '', $this->input->post('app_url', TRUE)),
								'app_install_url' => str_replace(';', '', $this->input->post('app_install_url', TRUE)),
								'app_config_url' => str_replace(';', '', $this->input->post('app_config_url', TRUE)),
								'app_support_page_tab' => $this->input->post('app_support_page_tab', FALSE) == 'app_support_page_tab',
								'app_description' => $this->input->post('app_description', TRUE),
								'app_secret_key' => md5($this->_generate_random_string())));
			redirect('backend');
		}else{
			$this->load->view('backend_views/add_new_app_view');	
		}
	}
	
	/**
	 * edit app detail
	 */
	function edit_app($app_id){
		
		$this->load->helper('form');
		$this->load->library('form_validation');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'app_name',
							 'label'   => 'App Name',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_description',
							 'label'   => 'App Description',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_url',
							 'label'   => 'App URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_install_url',
							 'label'   => 'App Install URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_config_url',
							 'label'   => 'App Config URL',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_support_page_tab',
							 'label'   => 'App Support Page Tab',
							 'rules'   => 'xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		if($this->form_validation->run()){
			$this->load->model('App_model', 'App');
			$this->App->update(array('app_name' => $this->input->post('app_name', TRUE),
								'app_maintainance' => FALSE,
								'app_show_in_list' => TRUE,
								'app_url' => str_replace(';', '', $this->input->post('app_url', TRUE)),
								'app_install_url' => str_replace(';', '', $this->input->post('app_install_url', TRUE)),
								'app_config_url' => str_replace(';', '', $this->input->post('app_config_url', TRUE)),
								'app_support_page_tab' => $this->input->post('app_support_page_tab', FALSE) == 'app_support_page_tab',
								'app_description' => $this->input->post('app_description', TRUE))
								, array('app_id' => $app_id));
			redirect('backend');
		}else{
			$this->load->model('App_model', 'App');
			$app = $this->App->get_app($app_id, 1, 0);
			$app = $app[0];
			$data['app_name'] = $app->app_name;
			$data['app_description'] = $app->app_description;
			$data['app_url'] = $app->app_url;
			$data['app_config_url'] = $app->app_config_url;
			$data['app_support_page_tab'] = $app->app_support_page_tab;
			$data['app_install_url'] = $app->app_install_url;
			$data['app_id'] = $app_id;
			
			$this->load->view('backend_views/edit_app_view', $data);	
		}
	}
	
	/**
	 * delete app from platform
	 */
	function delete_app($id = null){
		$this->load->model('App_model', 'App');
		if(isset($id)){
			$this->App->delete($id);
			echo "app $id was deleted.";
		}
	}
	
	/**
	 * generate random string 
	 */
	function _generate_random_string(){
	    $length = 10;
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = '';    
	
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }

    	return $string;
	}
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */