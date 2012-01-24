<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_reward extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($page_id = NULL){
		$this->load->library('settings');
		$config_name = 'reward';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->library('form_validation');
			$this->load->library('app_component_lib');
			$this->load->model('page_model','page');
			$this->load->model('app_component_page_model','app_component_page');
			$page = $this->page->get_page_profile_by_page_id($page_id);
			$page_component = $this->app_component_lib->get_page($page_id);
			log_message('error', print_r($page_component, true));
			$terms_and_conditions = $page_component['reward']['terms_and_conditions'];
			$this->load->model('reward_item_model', 'reward_item');
			$criteria = array(
				'criteria_type' => 'page',
				'criteria_id' => $page_id
			);
			$reward_items = $this->reward_item->get($criteria);

			$this->load->vars(array(
				'page_id' => $page_id,
				'reward_items' => $reward_items,
				'terms_and_conditions' => $terms_and_conditions,
				'updated' => FALSE
			));

			$this->form_validation->set_rules('terms_and_conditions', 'Terms and Conditions', 'trim|xss_clean');
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

			if ($this->form_validation->run() == FALSE){
			
			} else {
				$terms_and_conditions = set_value('terms_and_conditions');
				$result = $this->app_component_page->set_terms_and_conditions($page_id, $terms_and_conditions);
				// $this->load->vars(array('terms_and_conditions' => $terms_and_conditions));
			}
			$this->load->view('settings/page_apps/reward');
		}
	}
}
/* End of file page_reward.php */
/* Location: ./application/controllers/settings/page_reward.php */