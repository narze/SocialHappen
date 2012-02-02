<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bar extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('controller/bar_ctrl');
	}

	/**
	 * Create company form
	 * @author Manassarn M.
	 * @todo views for created/error
	 */
	function create_company(){
		$this->socialhappen->check_logged_in();
		$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('bar/create_company_view');
		}
		else 
		{
			$user_id = $this->socialhappen->get_user_id();
			$company_name = set_value('company_name');
			$company_detail = set_value('company_detail');
    	 	$company_image = $this->socialhappen->upload_image('company_image');
			$input = compact('user_id', 'company_name', 'company_detail' ,'company_image');
			$result = $this->bar_ctrl->create_company($input);

			if($result['success']){
				$this->load->view('bar/create_company_view');
				$company_id = $result['data']['company_id'];
				$this->load->view('common/redirect',array('redirect_parent'=>base_url().'company/'.$company_id));
			} else {
				echo $result['error'];
				$this->load->view('bar/create_company_view');
			}
		}
	}
	
	/**
	 * Select company page
	 * @author Manassarn M.
	 */
	function select_company(){
		$this -> socialhappen -> check_logged_in();
		$user_id = $this->socialhappen->get_user_id();
		
		$result = $this->bar_ctrl->select_company($user_id);
		if($result['success']){
			$this->parser->parse('bar/select_company_view', $result['data']);
		} else {
			echo $result['error'];
		}
	}
}  

/* End of file bar.php */
/* Location: ./application/controllers/bar.php */