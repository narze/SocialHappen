<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }


	function index(){
		
	}

	function _get_user_activity_page(){
		
	}

	function _get_user_activity_recent_apps(){
		
	}

	function _get_user_activity_recent_campaigns(){
		
	}

	function _get_user_activity_app(){
		
	}

	function _get_user_activity_campaign(){
		
	}

	function page(){
		
	}

	function app(){
		
	}

	function campaign(){
		
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_profile($user_id = NULL){
		$this->CI->load->model('user_model','users');
		$profile = $this->CI->users->get_user_profile_by_user_id($user_id);
		return json_encode($profile);
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_apps_model','user_apps');
		$apps = $this->CI->user_apps->get_user_apps_by_user_id($user_id, $limit, $offset);
		return json_encode($apps);
	}

	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->CI->users_campaigns->get_user_campaigns_by_user_id($user_id, $limit, $offset);
		return json_encode($campaigns);
	}

	function json_get_facebook_pages_owned_by_user(){
		
	}

	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->CI->load->model('user_model','users');
		$post_data = array(
							'user_first_name' => $this->CI->input->post('user_first_name'),
							'user_last_name' => $this->CI->input->post('user_last_name'),
							'user_email' => $this->CI->input->post('user_email'),
							'user_image' => $this->CI->input->post('user_image'),
							'user_facebook_id' => $this->CI->input->post('user_facebook_id')
						);
		if($user_id = $this->CI->users->add_user($post_data)){
			$result->status = 'OK';
			$result->user_id = $user_id;
			
			$this->CI->load->library('audit_lib');
			$action_id = $this->CI->socialhappen->get_k('audit_action','User Register SocialHappen');
			$this->CI->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_install_id' => 0,
					'user_id' => $user_id
				)
			);
			
			$this->CI->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>0);
			$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		} else {
			log_message('error','add user failed');
			$result->status = 'ERROR';
		}
		return json_encode($result);
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('user_companies_model','user_companies');
		$companies = $this->CI->user_companies->get_user_companies_by_user_id($user_id, $limit, $offset);
		return json_encode($companies);
	}

	function get_stat_graph(){
		
	}

	function json_get_user_activities(){
		
	}

	function json_count_user_activities(){
		
	}


}

/* End of file company_ctrl.php */
/* Location: ./application/libraries/controller/company_ctrl.php */