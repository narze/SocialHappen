<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Signup
 * @category Controller
 */
class Signup extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}

	/**
	 * Signup page
	 * @author Manassarn M.
	 */
	function index(){
		$this->socialhappen->check_logged_in('home');
		$facebook_user = $this->facebook->getUser();
		$this->load->model('user_model','users');
		$user = $this->users->get_user_id_by_user_facebook_id($facebook_user['id']);
		if($user){
			$this->socialhappen->login('home');
		} else { 
			$data = array(
				
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'Settings',
						'vars' => array(),
						'script' => array(
							'common/bar',
							'signup/form'
						),
						'style' => array(
							'common/main'
						)
					)
				),
				'tutorial' => $this -> load -> view('signup/tutorial', 
					array(
						
					),
				TRUE),
				'form' => $this -> load -> view('signup/form', 
					array(
						'user_profile_picture'=>$this->facebook->get_profile_picture($facebook_user['id'])
					),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
			$this -> parser -> parse('signup/signup_view', $data);
		}
	}

	/**
	 * Signup form
	 * @author Manassarn M.
	 */
	function form()
	{
		$facebook_user = $this->facebook->getUser();
		$user_image = $this->facebook->get_profile_picture($facebook_user['id']);
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email|max_length[255]');
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this -> load -> view('signup/form', 
					array(
						'user_profile_picture'=>$this->facebook->get_profile_picture($user_image)
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
			if ($user_add_result['status'] == 'OK' && $company_add_result['status'] == 'OK') // the information has therefore been successfully saved in the db
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
				echo 'error occured, register again';
				echo '$user_add_result["status"] = '.$user_add_result['status'];
				echo '$company_add_result["status"] = '.$company_add_result['status'];
			// Or whatever error handling is necessary
			}
		}
	}
}


/* End of file signup.php */
/* Location: ./application/controllers/signup.php */