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
	function logout(){
		$this->session->unset_userdata('SHBackend_auth');
		//var_dump($this->session->userdata('SHBackend_auth'));
		redirect('backend');
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
		$this->backend_session_verify(true);
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
								'app_install_page_url' => str_replace(';', '', $this->input->post('app_install_page_url', TRUE)),
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
			$this->App->update_app_by_app_id($app_id, array('app_name' => $this->input->post('app_name', TRUE),
								'app_url' => str_replace(';', '', $this->input->post('app_url', TRUE)),
								'app_install_url' => str_replace(';', '', $this->input->post('app_install_url', TRUE)),
								'app_install_page_url' => str_replace(';', '', $this->input->post('app_install_page_url', TRUE)),
								'app_config_url' => str_replace(';', '', $this->input->post('app_config_url', TRUE)),
								'app_support_page_tab' => $this->input->post('app_support_page_tab', FALSE) == 'app_support_page_tab',
								'app_description' => $this->input->post('app_description', TRUE),
								'app_type_id' => $this->input->post('app_type_id', TRUE),
								'app_facebook_api_key' => $this->input->post('app_facebook_api_key', TRUE)
								));
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
		$default_audit_action_list = array();
		$custom_audit_action_list = array();
		foreach($audit_action_list as $action){
			if($action['action_id'] > 999){
				$custom_audit_action_list[] = $action;
			}else{
				$default_audit_action_list[] = $action;
			}
		}
		
		if($app_id != 0){
			$audit_action_list = $this->audit_lib->list_audit_action(0);
			foreach($audit_action_list as $action){
				if($action['action_id'] <= 999){
					$default_audit_action_list[] = $action;
				}
			}
		}
		
		
		$data['default_audit_action_list'] = $default_audit_action_list;
		$data['custom_audit_action_list'] = $custom_audit_action_list;
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
							 'field'   => 'format_string',
							 'label'   => 'Format String',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'score',
							 'label'   => 'Score',
							 'rules'   => 'required|is_natural|trim|xss_clean'
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
			$format_string = $this->input->post('format_string', TRUE);
			$score = $this->input->post('score', TRUE);
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
			
			$result = $this->audit_lib->add_audit_action((int)$app_id, (int)$action_id, $description, $stat_app, $stat_page, $stat_campaign, $format_string, $score);

			redirect('backend/list_audit_action/'.$app_id);
		}else{
			$data['app_id'] = $app_id;
			$this->load->model('App_model', 'App');
			$app = $this->App->get_app_by_app_id($app_id);
			
			$data['app_name'] = count($app['app_name']) > 0 ? $app['app_name'] : 'Platform';
			
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
							 'field'   => 'format_string',
							 'label'   => 'Format String',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'score',
							 'label'   => 'Score',
							 'rules'   => 'required|is_natural|trim|xss_clean'
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
			$score = $this->input->post('score', TRUE);
			$format_string = $this->input->post('format_string', TRUE);
			$stat_app = $this->input->post('stat_app', TRUE) == 'stat_app';
			$stat_page = $this->input->post('stat_page', TRUE) == 'stat_page';
			$stat_campaign = $this->input->post('stat_campaign', TRUE) == 'stat_campaign';
			
			$result = $this->audit_lib->edit_audit_action((int)$app_id, (int)$action_id,
			 array('description' => $description,
			  'format_string' => $format_string,
			  'stat_app' => $stat_app,
			  'stat_page' => $stat_page,
			  'stat_campaign' => $stat_campaign,
				'score' => $score));

			redirect('backend/list_audit_action/'.$app_id);
		}else{
			
			$this->load->library('audit_lib');
			$result = $this->audit_lib->get_audit_action($app_id, $action_id);
			//var_dump($result);
			$data['app_id'] = $app_id;
			$data['action_id'] = $action_id;
			$data['description'] = $result['description'];
			$data['format_string'] = issetor($result['format_string']);
			$data['stat_app'] = $result['stat_app'];
			$data['stat_page'] = $result['stat_page'];
			$data['stat_campaign'] = $result['stat_campaign'];
			$data['score'] = isset($result['score'])? $result['score'] : 0;
			$this->load->view('backend_views/edit_audit_action_view', $data);
		}
	}

	function delete_audit_action($app_id, $action_id){
		$this->backend_session_verify(true);
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
	
	
	function company(){
		$this->backend_session_verify(true);
		$this->load->model('Company_model', 'Company');
		
		
		$this->load->library('pagination');
		
		$offset = $this->uri->segment(3, 0);
		
		$config['base_url'] = base_url() . 'backend/company/';
		$config['total_rows'] = $this->Company->count_company_profile();
		$config['per_page'] = '25'; 
		
		
		$this->pagination->initialize($config); 
		
		$data['total_company'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		
		$company_list = $this->Company->get_company_profile($config['per_page'], $offset);
		$data['company_list'] = $company_list;
		$this->load->view('backend_views/list_company_view', $data);
		//var_dump($company_list);
	}
	
	function company_detail($company_id){
		$this->backend_session_verify(true);
		$this->load->model('Company_model', 'Company');
		$company_profile = $this->Company->get_company_profile_by_company_id($company_id);
		$data['company'] = $company_profile;
		
		
		$this->load->model('Page_model', 'Page');
		$page_list = $this->Page->get_company_pages_by_company_id($company_id);
		$data['page_list'] = $page_list;
		
		$this->load->model('Installed_apps_model', 'App');
		
		$this->load->library('pagination');
		
		$config['uri_segment'] = 4;
		$offset = $this->uri->segment($config['uri_segment'], 0);
		
		$config['base_url'] = base_url() . 'backend/company_detail/'.$this->uri->segment(3, 0).'/';
		$config['total_rows'] = $this->App->count_installed_apps_by_company_id_not_in_page($company_id);
		$config['per_page'] = '25'; 
		
		$this->pagination->initialize($config); 
		
		$data['total_app'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		
		$app_list = $this->App->get_installed_apps_by_company_id_not_in_page($company_id, $config['per_page'], $offset);
		$data['app_list'] = $app_list;
		
		$this->load->library('audit_lib');
		$activity = $this->audit_lib->list_audit(array('company_id'=>$company_id));
		$data['activity_list'] = $activity;
		
		$this->load->view('backend_views/company_detail_view', $data);
	}
	
	function page($page_id){
		$this->backend_session_verify(true);
		$this->load->model('Page_model', 'Page');
		$page_profile = $this->Page->get_page_profile_by_page_id($page_id);
		$data['page'] = $page_profile;
		
		$this->load->model('Installed_apps_model', 'App');
		
		$this->load->library('pagination');
		$config['uri_segment'] = 4;
		$offset = $this->uri->segment($config['uri_segment'], 0);
		$config['base_url'] = base_url() . 'backend/page/'.$this->uri->segment(3, 0).'/';
		//$config['total_rows'] =  5;
		$config['total_rows'] = $this->App->count_installed_apps_by_page_id($page_id);
		$config['per_page'] = '15'; 
		$this->pagination->initialize($config);
		$data['total_app'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		$app_list = $this->App->get_installed_apps_by_page_id($page_id, $config['per_page'], $offset);
		$data['app_list'] = $app_list;
		
		$this->load->library('audit_lib');
		$activity = $this->audit_lib->list_audit(array('page_id'=>$page_id));
		$data['activity_list'] = $activity;
		
		$this->load->view('backend_views/page_detail_view', $data);
	}
	
	function app_install($app_install_id){
		$this->backend_session_verify(true);
		$this->load->model('Installed_apps_model', 'Installed_app');
		$installed_app = $this->Installed_app->get_app_profile_by_app_install_id($app_install_id);
		$data['app_install'] = $installed_app;
		//$this->dump($installed_app);
		$this->load->model('App_model', 'App');
		$app = $this->App->get_app_by_app_id($installed_app['app_id']);
		$data['app'] = $app;
		//$this->dump($app);
		$this->load->library('app_url');
		$data['app_url'] = $this->app_url->translate_url($app['app_url'], $app_install_id);
		
		$this->load->model('Campaign_model', 'Campaign');
		$data['campaign_list'] = $this->Campaign->get_app_campaigns_by_app_install_id($app_install_id);
		
		$this->load->library('audit_lib');
		$activity = $this->audit_lib->list_audit(array('app_install_id'=>$app_install_id));
		$data['activity_list'] = $activity;
		
		$this->load->view('backend_views/app_install_view', $data);
	}
	
	function campaign($campaign_id){
		$this->backend_session_verify(true);
		$this->load->model('Campaign_model', 'Campaign');
		$data['campaign'] = $this->Campaign->get_campaign_profile_by_campaign_id($campaign_id);
		//$this->dump($data['campaign']);
		
		$this->load->model('User_campaigns_model', 'User');
		
		$this->load->library('pagination');
		$config['uri_segment'] = 4;
		$offset = $this->uri->segment($config['uri_segment'], 0);
		$config['base_url'] = base_url() . 'backend/campaign/'.$this->uri->segment(3, 0).'/';
		//$config['total_rows'] =  5;
		$config['total_rows'] = $this->User->count_campaign_users_by_campaign_id($campaign_id);
		$config['per_page'] = '25'; 
		$this->pagination->initialize($config);
		$data['total_user'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		
		$data['user_list'] = $this->User->get_campaign_users_by_campaign_id($campaign_id, $config['per_page'], $offset);
		//$data['user_list'] = $this->User->get_campaign_users_by_campaign_id($campaign_id);
		//$this->dump($data['user_list']);
		
		$this->load->library('audit_lib');
		$activity = $this->audit_lib->list_audit(array('campaign_id'=>$campaign_id));
		$data['activity_list'] = $activity;
		
		
		$this->load->view('backend_views/campaign_detail_view', $data);
	}
	
	function user($user_id){
		$this->backend_session_verify(true);
		$this->load->model('User_model', 'User');
		$data['user'] = $this->User->get_user_profile_by_user_id($user_id);
		//$this->dump($data['user']);
		
		$this->load->model('User_companies_model', 'User_companies');
		$data['company_list'] = $this->User_companies->get_user_companies_by_user_id($user_id);
		//$this->dump($data['company_list']);
		
		
		
		$this->load->model('Installed_apps_model', 'Installed_apps');
		
		$this->load->library('pagination');
		$config['uri_segment'] = 4;
		$offset = $this->uri->segment($config['uri_segment'], 0);
		$config['base_url'] = base_url() . 'backend/user/'.$this->uri->segment(3, 0).'/';
		//$config['total_rows'] =  5;
		
		$this->load->model('User_apps_model', 'User_apps');
		
		$config['total_rows'] = $this->User_apps->count_user_apps_by_user_id($user_id);
		$config['per_page'] = '15'; 
		$this->pagination->initialize($config);
		$data['total_app'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		
		$app_list = $this->User_apps->get_user_apps_by_user_id($user_id, $config['per_page'], $offset);
		$al = array();
		foreach($app_list as $app){
			$app_install = $this->Installed_apps->get_app_profile_by_app_install_id($user_id);
			foreach ($app_install as $key => $value) {
				$app[$key] = $value;
			}
			$al []= $app;
		}
		$data['app_list'] = $al;
		//$this->dump($data['app_list']);
		
		$this->load->library('audit_lib');
		$activity = $this->audit_lib->list_audit(array('user_id'=>$user_id));
		$data['activity_list'] = $activity;
		//$this->dump($data['activity_list']);
		
		$this->load->view('backend_views/user_detail_view', $data);
	}

	function users(){
		$this->backend_session_verify(true);
		$this->load->model('User_model', 'User');
		$this->load->library('pagination');
		
		$offset = $this->uri->segment(3, 0);
		
		$config['base_url'] = base_url() . 'backend/users/';
		$config['total_rows'] = $this->User->count_users();
		$config['per_page'] = '50'; 
		
		
		$this->pagination->initialize($config); 
		
		$data['total_user'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		$data['user_list'] = $this->User->get_all_user_profile($config['per_page'], $offset);
		//$this->dump($data['user_list']);
		$this->load->view('backend_views/user_list_view', $data);
	}
	
	/**
	 * add new package to platform
	 */
	function add_new_package(){
		
		$this->backend_session_verify(true);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Package_model', 'Package');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'package_name',
							 'label'   => 'Package Name',
							 'rules'   => 'required|trim'
						),
						array(
							 'field'   => 'package_detail',
							 'label'   => 'Package Detail',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_image',
							 'label'   => 'Package Image',
							 'rules'   => 'xss_clean'
						),
						array(
							 'field'   => 'package_max_companies',
							 'label'   => 'Max companies',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_max_pages',
							 'label'   => 'Max pages',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_max_users',
							 'label'   => 'Max users',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_price',
							 'label'   => 'Price',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_custom_badge',
							 'label'   => 'Custom badge',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_duration',
							 'label'   => 'Duration',
							 'rules'   => 'required|trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
				
		if($this->form_validation->run()){
			$package_image = $this->socialhappen->upload_image('package_image');
			$data = array(
				'package_name' => $this->input->post('package_name', TRUE),
				'package_detail' => $this->input->post('package_detail', TRUE),
				'package_image' => $package_image == 0 ? '' : $package_image,
				'package_max_companies' => $this->input->post('package_max_companies', TRUE),
				'package_max_pages' => $this->input->post('package_max_pages', TRUE),
				'package_max_users' => $this->input->post('package_max_users', TRUE),
				'package_price' => $this->input->post('package_price', TRUE),
				'package_custom_badge' => set_value('package_custom_badge') == 'on' ? 1 : 0,
				'package_duration' => $this->input->post('package_duration', TRUE)
			);
			$this->Package->add_package($data);
			redirect('backend/dashboard');
		}else{
			$this->load->view('backend_views/add_new_package_view');
		}
	}
	
	function edit_package($package_id){
		$this->backend_session_verify(true);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Package_model', 'Package');
		
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'package_name',
							 'label'   => 'Package Name',
							 'rules'   => 'required|trim'
						),
						array(
							 'field'   => 'package_detail',
							 'label'   => 'Package Detail',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_image',
							 'label'   => 'Package Image',
							 'rules'   => 'xss_clean'
						),
						array(
							 'field'   => 'package_max_companies',
							 'label'   => 'Max companies',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_max_pages',
							 'label'   => 'Max pages',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_max_users',
							 'label'   => 'Max users',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'package_price',
							 'label'   => 'Price',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_custom_badge',
							 'label'   => 'Custom badge',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'package_duration',
							 'label'   => 'Duration',
							 'rules'   => 'required|trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		
		if($this->form_validation->run()){
			$data = array(
				'package_name' => $this->input->post('package_name', TRUE),
				'package_detail' => $this->input->post('package_detail', TRUE),
				'package_max_companies' => $this->input->post('package_max_companies', TRUE),
				'package_max_pages' => $this->input->post('package_max_pages', TRUE),
				'package_max_users' => $this->input->post('package_max_users', TRUE),
				'package_price' => $this->input->post('package_price', TRUE),
				'package_custom_badge' => set_value('package_custom_badge') == 'on' ? 1 : 0,
				'package_duration' => $this->input->post('package_duration', TRUE)
			);
			if($package_image = $this->socialhappen->replace_image('package_image', $this->input->post('package_image_old', TRUE))){
				$data['package_image'] = $package_image;
			}
			$result = $this->Package->update_package_by_package_id($package_id, $data);
			if($result) redirect('backend/packages');
		}else{
			$data = $this->Package->get_package_by_package_id($package_id);
			$this->load->view('backend_views/edit_package_view', $data);	
		}
	}
	
	function packages(){
		$this->backend_session_verify(true);
		$this->load->model('Package_model', 'Package');
		$this->load->library('pagination');
		
		$offset = $this->uri->segment(3, 0);
		
		$config['base_url'] = base_url() . 'backend/packages/';
		$config['total_rows'] = $this->Package->count_packages();
		$config['per_page'] = '10'; 
		
		
		$this->pagination->initialize($config); 
		
		$data['total_package'] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
		
		$data['package_list'] = $this->Package->get_packages($config['per_page'], $offset);
		//$this->dump($data['package_list']);
		$this->load->view('backend_views/package_list_view', $data);
	}
	
	function achievements(){
		$this->backend_session_verify(true);
		$data = array();
		$this->load->library('achievement_lib');
		$data['achievement_list'] = $this->achievement_lib->list_achievement_info();
		$this->load->view('backend_views/achievement_list_view', $data);
	}
	
	function new_achievement_info(){
		$this->backend_session_verify(true);
		
		$this->load->helper('form');
		$this->load->library('form_validation');
				
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'name',
							 'label'   => 'Achievement Name',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'description',
							 'label'   => 'Achievement Description',
							 'rules'   => 'required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_id',
							 'label'   => 'App ID',
							 'rules'   => 'is_natural|required|trim|xss_clean'
						),
						array(
							 'field'   => 'app_install_id',
							 'label'   => 'App Install ID',
							 'rules'   => 'is_natural|trim|xss_clean'
						),
						array(
							 'field'   => 'page_id',
							 'label'   => 'Page ID',
							 'rules'   => 'is_natural|trim|xss_clean'
						),
						array(
							 'field'   => 'campaign_id',
							 'label'   => 'Campaign ID',
							 'rules'   => 'is_natural|trim|xss_clean'
						),
						array(
							 'field'   => 'criteria_string[]',
							 'label'   => 'Criteria String',
							 'rules'   => 'required|xss_clean'
						),
						array(
							 'field'   => 'criteria_key[]',
							 'label'   => 'criteria_key',
							 'rules'   => 'required|xss_clean'
						),
						array(
							 'field'   => 'criteria_value[]',
							 'label'   => 'Criteria',
							 'rules'   => 'required|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		if($this->form_validation->run()){
			$name = str_replace(';', '', $this->input->post('name', TRUE));
			$description = str_replace(';', '', $this->input->post('description', TRUE));
			$app_id = str_replace(';', '', $this->input->post('app_id', TRUE));
			$app_install_id = str_replace(';', '', $this->input->post('app_install_id', TRUE));
			$page_id = str_replace(';', '', $this->input->post('page_id', TRUE));
			$campaign_id = str_replace(';', '', $this->input->post('campaign_id', TRUE));
			
			$criteria_string = $this->input->post('criteria_string', TRUE);
			
			
			if($criteria_string === FALSE){
				$criteria_string = array();
			}

			$info = array(
				'name' => $name,
				'description' => $description,
				'criteria_string' => $criteria_string,
			);
			
			if(strlen($app_install_id) > 0){
				$info['app_install_id'] = $app_install_id;
			}
			
			if(strlen($page_id) > 0){
				$info['page_id'] = $page_id;
			}
			
			if(strlen($campaign_id) > 0){
				$info['campaign_id'] = $campaign_id;
			}
			
			$criteria_key = $this->input->post('criteria_key', TRUE);
			$criteria_value = $this->input->post('criteria_value', TRUE);

			$criteria = array();
			if($criteria_key !== FALSE && $criteria_value !== FALSE){
				for($i = 0; $i < count($criteria_key); $i++){
					$criteria[$criteria_key[$i]] = $criteria_value[$i];
				}
			}
			
			$this->load->library('achievement_lib');
			$this->achievement_lib->add_achievement_info(
				$app_id, $app_install_id,
				$info, $criteria);
			redirect('backend/achievements');
		}else{
			$this->load->view('backend_views/achievement_add_view');
		}
	}

	function edit_achievement_info(){
		
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
	
	function dump($s){
		echo '<pre>';
		var_dump($s);
		echo '</pre>';
	}
	
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */