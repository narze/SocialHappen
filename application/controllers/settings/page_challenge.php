<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_challenge extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			exit('You are not admin');
		}
		$this->load->library('settings');
		$config_name = 'challenge';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			
			$sort_criteria = array('start' => -1);
			$user = $this->socialhappen->get_user();
			$this->load->library('timezone_lib');
			$this->load->library('challenge_lib');
			$now = time();
			
			$this->load->model('page_model');
			$page = $this->page_model->get_page_profile_by_page_id($page_id);
			$company_id = $page['company_id'];
			$challenges = $this->challenge_lib->get(array('company_id' => $company_id));
			foreach($challenges as &$challenge){
				
				$start_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['start']), $user['user_timezone_offset']);
				$end_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['end']), $user['user_timezone_offset']);

				$challenge['start_date'] = date('d/m/Y', strtotime($start_time));
				$challenge['end_date'] = date('d/m/Y', strtotime($end_time));
			} unset($challenge);
			$this->load->vars(array(
				'company_id' => $company_id,
				'page_id' => $page_id,
				'challenges' => $challenges
			));
			$this->load->view('settings/page_apps/challenge');
		}
	}

	function form($page_id = NULL, $update = FALSE){ 
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			$return = array(
				'success' => FALSE,
				'error' => 'You are not admin'
			);
			echo json_encode($return);
		} else {
			$this->load->library('form_validation');
			// $this->form_validation->set_rules('name', 'challenge Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('start_date', 'Start date', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('end_date', 'End date', 'required|trim|xss_clean|max_length[20]');			
			// $this->form_validation->set_rules('description', 'Challenge Description', 'trim|xss_clean');
				
			$this->form_validation->set_error_delimiters('<li class="error">', '</li>');

			$this->load->model('page_model');
			$page = $this->page_model->get_page_profile_by_page_id($page_id);
			$company_id = $page['company_id'];
			$company_pages = $this->page_model->get_company_pages_by_company_id($company_id);
			$pages = array();
			foreach($company_pages as $company_page) {
				$pages[$company_page['page_id']] = $company_page['page_name'];
			}
			$this->load->library('action_data_lib');
			$temp_platform_actions = $this->action_data_lib->get_platform_action();
			$platform_actions = array('' => 'Select Platform Action');
			foreach($temp_platform_actions as $action_name => $platform_action) {
				$platform_actions[$platform_action['id']] = ucfirst($action_name);
			}
			$this->load->vars(array(
				'page_id' => $page_id,
				'company_id' => $company_id,
				'company_pages' => $pages,
				'platform_actions' => $platform_actions
			));

			$this->load->library('challenge_lib');
			if($update){
				if($this->input->post('challenge_id')){
					$challenge_id = $this->input->post('challenge_id');
				}
				if($this->input->get('challenge_id')){
					$challenge_id = $this->input->get('challenge_id');
				}
				$challenge = $this->challenge_lib->get_one(array('_id' => new MongoId($challenge_id)));
			} else {
				if(isset($_FILES['image']) && !$_FILES['image']['name']) {
					$this->form_validation->set_rules('image', 'Image', 'required|trim|xss_clean|max_length[255]');
				}
			}

			if (($this->form_validation->run() == FALSE) || !is_array($this->input->post('criteria')))
			{	
				if($update){
					$success = $this->input->get('success');
					$this->load->vars('success', $success);

					$user = $this->socialhappen->get_user();
					$this->load->library('timezone_lib');
					$start_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['start']), $user['user_timezone_offset']);
					$end_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $challenge['end']), $user['user_timezone_offset']);

					$challenge['start_date'] = $start_time;
					$challenge['end_date'] = $end_time;
					$this->load->vars(array(
						'challenge' => $challenge,
						'challenge_id' => $challenge_id,
						'update' => TRUE
					));
				}
				$this->load->view('settings/page_apps/challenge_form');
			}
			else
			{
				$user = $this->socialhappen->get_user();
				$this->load->library('timezone_lib');
				$start_timestamp = $this->timezone_lib->unconvert_time(set_value('start_date'), $user['user_timezone_offset']);
				$end_timestamp = $this->timezone_lib->unconvert_time(set_value('end_date'), $user['user_timezone_offset']);
				$criteria = array_values($this->input->post('criteria'));
				foreach($criteria as &$one) {
					$one['query'] = array_cast_int($one['query']);
					$one['count'] = (int) $one['count'];
					if($one['query']['platform_action_id']) {
						$one['is_platform_action'] = TRUE;
						unset($one['query']['page_id']);
						unset($one['query']['app_id']);
						unset($one['query']['action_id']);
						if(!$update) {
							
							$action_data = $this->input->post('platform_action_setting');
							$action_data_id = $this->action_data_lib->add_action_data($one['query']['platform_action_id'],$action_data);
							$one['action_data_id'] = $action_data_id;

						}
					} else {
						$one['is_platform_action'] = FALSE;
					}
				} unset($one);
				
				$input = array(
					'company_id' => $company_id,
	       	'start' => strtotime($start_timestamp),
	       	'end' => strtotime($end_timestamp),
	       	// 'status' => set_value('status'),
	       	'detail' => $this->input->post('detail'),
			    'criteria' => $criteria
				);
				if($update){
					$challenge_id = $this->input->post('challenge_id');

					// $exist_challenge = $this->challenge_lib->get_by_challenge_id($challenge_id);

					// $input['image'] = $this->socialhappen->replace_image('image', $exist_reward_item['image']);
					// if($input['image'] == '') unset($input['image']);
					
					$update_result = $this->challenge_lib->update(array('_id' => new MongoId($challenge_id)), $input);
				} else {
					// $input['image'] = $this->socialhappen->upload_image('image');
					$challenge_id = $this->challenge_lib->add($input);
				}
			
				if ($challenge_id)
				{
					redirect('settings/page_challenge/update/'.$page_id.'?success=1&challenge_id='.$challenge_id);
				}
				else
				{
					echo 'An error occurred saving your information. Please try again later';
				}
			}
		}
	}

	function remove($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			$return = array(
				'success' => FALSE,
				'error' => 'You are not admin'
			);
		} else {
			$challenge_id = $this->input->get('challenge_id');
			$this->load->library('challenge_lib');
			$result = $this->challenge_lib->remove(array('_id' => new MongoId($challenge_id)));
			$return = array(
				'success' => $result
			);
		}
		echo json_encode($return);
	}

	function add($page_id = NULL){
		$this->form($page_id, FALSE);
	}

	function update($page_id = NULL){
		$this->form($page_id, TRUE);
	}

	function ajax_get_page_apps() {
		if($this->input->is_ajax_request()) {
			$this->load->helper('form');
			$page_id = $this->input->post('page_id');
			$this->load->model('installed_apps_model');
			if($page_id && ($page_apps = $this->installed_apps_model->get_installed_apps_by_page_id($page_id))) {
				$apps = array('' => 'Select App');
				foreach($page_apps as $page_app) {
					$apps[$page_app['app_id']] = $page_app['app_name'];
				}
				// array_unshift($apps, 'Select App');
			} else {
				$apps = array('' => 'This page has no apps');
			}
			echo form_dropdown('select_app', $apps);
		} else {
			return;
		}
	}

	function ajax_get_app_actions() {
		if($this->input->is_ajax_request()) {
			$this->load->helper('form');
			$app_id = $this->input->post('app_id');
			$this->load->model('audit_action_model');
			if($app_id && ($app_actions = $this->audit_action_model->get_action($app_id))) {
				$actions = array('' => 'Select Action');
				foreach($app_actions as $app_action) {
					$actions[$app_action['action_id']] = $app_action['description'];
				}
				// array_unshift($actions, 'Select Actions');
			} else {
				$actions = array('' => 'This app has no actions');
			}
			echo form_dropdown('select_action', $actions);
		} else {
			return;
		}
	}

	/**
	 * Get platform action setting form
	 */
	function get_platform_action_setting_form($action_id) {
		$this->load->library('action_data_lib');
		$platform_actions = $this->action_data_lib->get_platform_action();
		foreach($platform_actions as $name => $data) {
			if($data['id'] == $action_id) {
				$action_name = $name;
			}
		}
		if(!isset($action_name)) {
			return;
		}
		$this->load->helper('form');
		$this->load->view("actions/{$action_name}/{$action_name}_setting_form");
	}
}
/* End of file page_challenge.php */
/* Location: ./application/controllers/settings/page_challenge.php */