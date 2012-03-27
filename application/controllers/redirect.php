<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redirect extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		// $this->load->library('controller/redirect_ctrl');
	}
	
	/**
	 * Redirect back if param not specified
	 * @author Manassarn M.
	 */
	function index(){
		redirect_back();
	}
	
	function app($app_install_id){
		$this->load->library('controller/tab_ctrl');
		$facebook_tab_url = $this->tab_ctrl->get_facebook_app_tab_url($app_install_id);
		redirect($facebook_tab_url);
	}

	function user($filter, $user_id, $app_id){
		$this->load->library('controller/tab_ctrl');
		if($filter === 'page'){
			//now filter is always == app
		} else if($filter === 'app'){
			//TODO : no app install id sent from translate_format_string
			//$facebook_tab_url = $this->tab_ctrl->get_facebook_app_tab_url($app_install_id);
		} else if($filter === 'campaign'){
			//now filter is always == app
		}
		// $facebook_tab_url .= "&app_data="; //specify app_data to redirect to user's profile
		// redirect($facebook_tab_url);
	}

	function campaign($campaign_id){

	}

	function page($page_id){
		$this->load->library('controller/tab_ctrl');
		$facebook_tab_url = $this->tab_ctrl->get_facebook_page_tab_url($page_id);
		redirect($facebook_tab_url);
	}

	function company($company_id){

	}

	function package($package_id){

	}

	function reward($reward_id){

	}

	function c() { //Challenge
		$challenge_hash = $this->input->get('hash');
		$this->load->model('challenge_model');
		redirect('player/challenge/'.$challenge_hash);
	}
}  

/* End of file redirect.php */
/* Location: ./application/controllers/redirect.php */