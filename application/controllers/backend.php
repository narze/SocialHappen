<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backend extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('session');
	}
	
	/**
	 * index page for backend
	 * @todo: improve authentication
	 */
	function index(){
		$key1 = $this->input->post('key1');
		$key2 = $this->input->post('key2');
		$time = $this->input->post('time');
		
		echo $key1;
		echo $key2;
		echo $time;
		
		if($this->backend_session_verify(false))
			redirect('backend/dashboard');
		
		$this->session->unset_userdata('SHBackend_auth');
		
		if($key1=='key1'&&$key2=='key2'&&$time<time()){
			//initial session
			$backend_auth = array(
								'authenticated' => true
							);
			$this->session->set_userdata('SHBackend_auth',$backend_auth);
			redirect('backend/dashboard');
		
		}else{
			$this->load->helper('form');
			$this->load->view('backend_views/backend_login');
		}
		
	}
	
	/**
	 * backend dashboard
	 */
	function dashboard(){
	
		$this->backend_session_verify(true);
		
		$data = array();
		
		//$this->load->model('App_model', 'App');
		//$data['app_list'] = $this->App->get_all_apps();
		
		$this->load->view('backend_views/backend_dashboard_view', $data);	
	}
	
	function app(){
		$this->load->model('App_model', 'App');
		$data['app_list'] = $this->App->get_all_apps();
		$this->load->view('backend_views/backend_app_view', $data);	
	}
	
	
	/**
	 * [Deprecated]
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
	 * [Deprecated]
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
		
		$this->backend_session_verify(true);
		
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
							 'field'   => 'app_install_page_url',
							 'label'   => 'App Install to Page URL',
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
						),
						array(
							 'field'   => 'app_facebook_api_key',
							 'label'   => 'App Facebook API Key',
							 'rules'   => 'required|trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
				
		if($this->form_validation->run()){
			$this->load->model('App_model', 'App');
			$this->App->add_app(array('app_name' => $this->input->post('app_name', TRUE),
								'app_url' => str_replace(';', '', $this->input->post('app_url', TRUE)),
								'app_install_url' => str_replace(';', '', $this->input->post('app_install_url', TRUE)),
								'app_config_url' => str_replace(';', '', $this->input->post('app_config_url', TRUE)),
								'app_support_page_tab' => $this->input->post('app_support_page_tab', FALSE) == 'app_support_page_tab',
								'app_description' => $this->input->post('app_description', TRUE),
								'app_type_id' => $this->input->post('app_type_id', TRUE),
								'app_facebook_api_key' => $this->input->post('app_facebook_api_key', TRUE),
								'app_secret_key' => md5($this->_generate_random_string())));
			redirect('backend/dashboard');
		}else{
			$this->load->view('backend_views/add_new_app_view');	
		}
	}
	
	/**
	 * edit app detail
	 */
	function edit_app($app_id){
	
		$this->backend_session_verify(true);
		
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
							 'field'   => 'app_install_page_url',
							 'label'   => 'App Install to Page URL',
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
						),
						array(
							 'field'   => 'app_facebook_api_key',
							 'label'   => 'App Facebook API Key',
							 'rules'   => 'required|trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		if($this->form_validation->run()){
			$this->load->model('App_model', 'App');
			$this->App->update(array('app_name' => $this->input->post('app_name', TRUE),
								'app_url' => str_replace(';', '', $this->input->post('app_url', TRUE)),
								'app_install_url' => str_replace(';', '', $this->input->post('app_install_url', TRUE)),
								'app_install_page_url' => str_replace(';', '', $this->input->post('app_install_page_url', TRUE)),
								'app_config_url' => str_replace(';', '', $this->input->post('app_config_url', TRUE)),
								'app_support_page_tab' => $this->input->post('app_support_page_tab', FALSE) == 'app_support_page_tab',
								'app_description' => $this->input->post('app_description', TRUE),
								'app_type_id' => $this->input->post('app_type_id', TRUE),
								'app_facebook_api_key' => $this->input->post('app_facebook_api_key', TRUE))
								, array('app_id' => $app_id));
			redirect('backend');
		}else{
			$this->load->model('App_model', 'App');
			$app = $this->App->get_app_by_app_id($app_id);
			$data['app_name'] = $app['app_name'];
			$data['app_description'] = $app['app_description'];
			$data['app_url'] = $app['app_url'];
			$data['app_config_url'] = $app['app_config_url'];
			$data['app_support_page_tab'] = $app['app_support_page_tab'];
			$data['app_type_id'] = $app['app_type_id'];
			$data['app_install_url'] = $app['app_install_url'];
			$data['app_install_page_url'] = $app['app_install_page_url'];
			$data['app_facebook_api_key'] = $app['app_facebook_api_key'];
			$data['app_id'] = $app_id;
			$this->load->view('backend_views/edit_app_view', $data);
		}
	}
	
	function list_audit_action($app_id){
		$this->backend_session_verify(true);
		
		$this->load->model('App_model', 'App');
		$app = $this->App->get_app_by_app_id($app_id);
		$data['app_name'] = count($app['app_name']) > 0 ? $app['app_name'] : 'Platform';
		
		
		$this->load->library('audit_lib');
		$audit_action_list = $this->audit_lib->list_audit_action((int)$app_id);
		$data['audit_action_list'] = $audit_action_list;
		$data['app_id'] = $app_id;
		$this->load->view('backend_views/list_audit_action_view', $data);
	}
	
	function add_audit_action($app_id){
		$this->backend_session_verify(true);
		
		$this->load->helper('form');
		$this->load->library('form_validation');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'action_id',
							 'label'   => 'Action ID',
							 'rules'   => 'required|is_natural|trim|xss_clean'
						),
						array(
							 'field'   => 'description',
							 'label'   => 'Action Description',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'stat_app',
							 'label'   => 'Collect Stat for App',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'stat_page',
							 'label'   => 'Collect Stat for Page',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'stat_campaign',
							 'label'   => 'Collect Stat for Campaign',
							 'rules'   => 'trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		
		$this->load->library('audit_lib');
		$audit_action_list = $this->audit_lib->list_audit_action((int)$app_id);
		
		$duplicate_action = FALSE;
		foreach($audit_action_list as $audit_action){
			if($audit_action['action_id'] == $this->input->post('action_id', TRUE)){
				$duplicate_action = TRUE;
				break;
			}
		}
		if($this->input->post('action_id', TRUE)){
			$action_id = (int)$this->input->post('action_id', TRUE);
			$invalid_action_id = (int)$app_id != 0 && $action_id < 1000;
		}else{
			$invalid_action_id = FALSE;
		}
		
		
		if($this->form_validation->run() && !$duplicate_action && !$invalid_action_id){
			
			$action_id = $this->input->post('action_id', TRUE);
			$description = $this->input->post('description', TRUE);
			$stat_app = $this->input->post('stat_app', TRUE) == 'stat_app';
			$stat_page = $this->input->post('stat_page', TRUE) == 'stat_page';
			$stat_campaign = $this->input->post('stat_campaign', TRUE) == 'stat_campaign';
			/*
			echo $app_id . "<br>";
			echo $action_id . "<br>";
			echo $description . "<br>";
			var_dump($stat_app);
			var_dump($stat_page);
			var_dump($stat_campaign);
			 */
			//echo var_dump($stat_app, true) . "<br>";
			//echo var_dump($stat_page, true) . "<br>";
			//echo var_dump($stat_campaign, true) . "<br>";
			
			//$action_list = $this
			
			$result = $this->audit_lib->add_audit_action((int)$app_id, (int)$action_id, $description, $stat_app, $stat_page, $stat_campaign);

			redirect('backend/list_audit_action/'.$app_id);
		}else{
			$data['app_id'] = $app_id;
			$data['duplicate_action_id'] = $duplicate_action?'<div class="error">Duplicate Action ID</div>':'';
			$data['invalid_action_id'] = $invalid_action_id?'<div class="error">Invalid Action ID, please use Action ID greater than 999.</div>':'';
			$this->load->view('backend_views/add_audit_action_view', $data);
		}
	}
	
	function edit_audit_action($app_id, $action_id){
		$this->backend_session_verify(true);
		
		$this->load->helper('form');
		$this->load->library('form_validation');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'action_id',
							 'label'   => 'Action ID',
							 'rules'   => 'required|is_natural|trim|xss_clean'
						),
						array(
							 'field'   => 'description',
							 'label'   => 'Action Description',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'stat_app',
							 'label'   => 'Collect Stat for App',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'stat_page',
							 'label'   => 'Collect Stat for Page',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'stat_campaign',
							 'label'   => 'Collect Stat for Campaign',
							 'rules'   => 'trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
				
		if($this->form_validation->run()){
			$this->load->library('audit_lib');
			$action_id = $this->input->post('action_id', TRUE);
			$description = $this->input->post('description', TRUE);
			$stat_app = $this->input->post('stat_app', TRUE) == 'stat_app';
			$stat_page = $this->input->post('stat_page', TRUE) == 'stat_page';
			$stat_campaign = $this->input->post('stat_campaign', TRUE) == 'stat_campaign';
			
			$result = $this->audit_lib->edit_audit_action((int)$app_id, (int)$action_id,
			 array('description' => $description,
			  'stat_app' => $stat_app,
			  'stat_page' => $stat_page,
			  'stat_campaign' => $stat_campaign));

			redirect('backend/list_audit_action/'.$app_id);
		}else{
			
			$this->load->library('audit_lib');
			$result = $this->audit_lib->get_audit_action($app_id, $action_id);
			//var_dump($result);
			$data['app_id'] = $app_id;
			$data['action_id'] = $action_id;
			$data['description'] = $result['description'];
			$data['stat_app'] = $result['stat_app'];
			$data['stat_page'] = $result['stat_page'];
			$data['stat_campaign'] = $result['stat_campaign'];
			$this->load->view('backend_views/edit_audit_action_view', $data);
		}
	}

	function delete_audit_action($app_id, $action_id){
		$this->load->library('audit_lib');
		$result = $this->audit_lib->delete_audit_action($app_id, $action_id);
		//var_dump($result);
		redirect('backend/list_audit_action/'.$app_id);
	}
	
	function edit_platform(){
		$this->list_audit_action(0);
	}
	
	/**
	 * delete app from platform
	 */
	function delete_app($id = null){
	
		$this->backend_session_verify(true);
	
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
	
	/**
	 *	Check session
	 *
	 */
	function backend_session_verify($autoredirectonfalse = false){
		$token = $this->session->userdata('SHBackend_auth');
		
		if(@$token['authenticated']){
			return true;
		}else{
			if(!$autoredirectonfalse){
				return false;
			}else{
				//echo 'authentication fail';
				redirect('/backend');
				exit();
			}
		}
		
	}
	
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */