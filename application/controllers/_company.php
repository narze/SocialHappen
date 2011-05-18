<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class _Company extends CI_Controller {

	function __construct(){
		parent::__construct();
		
		$this->load->library('facebook');
		$this->facebook->authentication($this->uri->uri_string());
	}

	function index(){
		echo "hi!";
	}
	
	/**
	 * creat new company page
	 */
	function create_new_company(){
	
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'company_name',
							 'label'   => '',
							 'rules'   => 'required|trim'
						),
						array(
							 'field'   => 'company_address',
							 'label'   => '',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'company_email',
							 'label'   => '',
							 'rules'   => 'required|trim|valid_email|xss_clean'
						),
						array(
							 'field'   => 'company_telephone',
							 'label'   => '',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							'field'   => 'accept',
							'label'   => '',
							'rules'   => 'required|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		
		if($this->form_validation->run()){
			// pass validation, add new company to database
			$this->load->library('facebook');
			$this->load->model('company_model', 'company');
			$this->load->model('user_companies_model', 'user_companies');
			
			$facebook_user = $this->facebook->getUser();
			
			$company_id = $this->company->add(array(
												'company_name'=>$company_name = $this->input->post('company_name', TRUE),
												'company_address'=>$company_address = $this->input->post('company_address', TRUE),
												'company_email'=>$company_email = $this->input->post('company_email', TRUE),
												'company_telephone'=>$company_telephone = $this->input->post('company_telephone', TRUE)
											));
	
			$this->user_companies->add(array(
											'user_facebook_id'=>$facebook_user['id'],
											'company_id'=>$company_id,
											'user_role'=>0
										));
			redirect('admin/dashboard/' . $company_id);
		}else{
			$this->load->view('company_views/create_new_company_view');	
		}
	}

	/**
	 * edit company profile page
	 */
	function edit_company_profile($company_id){
		$this->load->model('User_companies_model','User_companies');
		
		$facebook_user = $this->facebook->getUser();
		$user_facebook_id = $facebook_user['id'];
		
		if(!$this->User_companies->is_user_company_admin($user_facebook_id, $company_id))
			show_error('No Permission');
			
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set up validation rules
		$config = array(
						array(
							 'field'   => 'company_name',
							 'label'   => '',
							 'rules'   => 'required|trim'
						),
						array(
							 'field'   => 'company_address',
							 'label'   => '',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'company_email',
							 'label'   => '',
							 'rules'   => 'required|trim|valid_email|xss_clean'
						),
						array(
							 'field'   => 'company_telephone',
							 'label'   => '',
							 'rules'   => 'trim|xss_clean'
						),
						array(
							 'field'   => 'admin_list_string',
							 'label'   => '',
							 'rules'   => 'trim|xss_clean'
						)
				);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules($config); 
		
		if($this->form_validation->run()){
			$this->load->model('Company_model', 'Company');
			$this->Company->update(array('company_name' => $this->input->post('company_name', TRUE),
								'company_address' => $this->input->post('company_address', TRUE),
								'company_email' => $this->input->post('company_email',TRUE),
								'company_telephone' => $this->input->post('company_telephone', TRUE))
								, array('company_id' => $company_id));
			
			$admin_list_string = $this->input->post('admin_list_string', TRUE);
			$admin_list = explode(',', $admin_list_string);
			for ($i=0; $i < count($admin_list); $i++) { 
				$admin_list[$i] = trim($admin_list[$i]);
			}
			
			$this->load->model('User_companies_model', 'User_Companies');
			$admin_list_query = $this->User_Companies->get_user_companies_list_by_company($company_id);
			$admin_list_old = array();
			foreach ($admin_list_query as $admin) {
				$admin_list_old[] = $admin->user_facebook_id;
			}
			
			//delete
			$this->User_Companies->delete_admin($company_id);
			
			//add
			$admin_list = array_unique($admin_list);
			$admin_role0 = $this->User_Companies->get_user_companies_admin($company_id);
			foreach ($admin_list as $admin) {
				if(is_numeric($admin) && $admin > 0 && $admin != $admin_role0)
				$this->User_Companies->add(array(
											'user_facebook_id' => $admin,
											'company_id' => $company_id,
											'user_role' => 1));
			}
			
			
			redirect('admin/dashboard/'.$company_id);
		}else{
			$this->load->model('Company_model', 'Company');
			$company = $this->Company->get_company($company_id, 1, 0);
			$company = $company[0];
			$data['company_name'] = $company->company_name;
			$data['company_address'] = $company->company_address;
			$data['company_email'] = $company->company_email;
			$data['company_telephone'] = $company->company_telephone;
			$data['company_id'] = $company_id;
			
			$this->load->model('User_companies_model', 'User_Companies');
			$admin_list_query = $this->User_Companies->get_user_companies_list_by_company($company_id);
			$admin_list = array();
			foreach ($admin_list_query as $value) {
				$admin_list[] = $value->user_facebook_id;
			}
			$data['admin_list_string'] = implode(',',$admin_list);
			
			$this->load->view('company_views/edit_company_profile_view', $data);	
		}
	}
}

/* End of file company.php */
/* Location: ./application/controllers/company.php */