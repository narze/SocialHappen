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

				$challenge['start_date'] = $start_time;
				$challenge['end_date'] = $end_time;
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
			$this->load->vars(array(
				'page_id' => $page_id,
				'company_id' => $company_id
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

			if ($this->form_validation->run() == FALSE)
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
				$criteria = $this->input->post('criteria');
				foreach($criteria as &$one) {
					$one['query'] = array_cast_int($one['query']);
					$one['count'] = (int) $one['count'];
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
}
/* End of file page_challenge.php */
/* Location: ./application/controllers/settings/page_challenge.php */