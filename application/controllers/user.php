<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
	}

	function index($user_id = NULL){
		if($user_id){
			$data['user_id'] = $user_id;
			$this->load->view('user_view',$data);
			return $data;
		}
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_profile($user_id = NULL){
		$this->load->model('user_model','users');
		$profile = $this->users->get_user_profile_by_user_id($user_id);
		echo json_encode($profile);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL){
		$this->load->model('user_apps_model','user_apps');
		$apps = $this->user_apps->get_user_apps_by_user_id($user_id);
		echo json_encode($apps);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL){
		$this->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->users_campaigns->get_user_campaigns_by_user_id($user_id);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function json_get_facebook_pages_owned_by_user(){
		echo json_encode($this->facebook->get_user_pages());
	}
	
	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('user_model','users');
		$post_data = array('user_first_name' => $this->input->post('user_first_name'));
		if($user_id = $this->users->add_user($post_data)){
			var_dump ($user_id);
			$result->status = 'OK';
			$result->user_id = $user_id;
		} else {
			$result->status = 'ERROR';
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL){
		$this->load->model('user_companies_model','user_companies');
		$companies = $this->user_companies->get_user_companies_by_user_id($user_id);
		echo json_encode($companies);
	}
}


/* End of file user.php */
/* Location: ./application/controllers/user.php */