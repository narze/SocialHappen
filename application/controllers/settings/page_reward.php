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
			$terms_and_conditions = issetor($page_component['reward']['terms_and_conditions']);
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

	function add_item($page_id = NULL, $update = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			$return = array(
				'success' => FALSE,
				'error' => 'You are not admin'
			);
			echo json_encode($return);
		} else {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('point', 'Point', 'required|trim|xss_clean|is_numeric|max_length[10]');			
			$this->form_validation->set_rules('amount', 'Amount', 'required|trim|xss_clean|is_numeric|max_length[10]');
			$this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('start_date', 'Start date', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('end_date', 'End date', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('status', 'Status', 'required|trim|xss_clean|max_length[10]');			
			// $this->form_validation->set_rules('type', 'Type', 'required|trim|xss_clean|max_length[10]');

			$this->form_validation->set_rules('image', 'Image', 'required|trim|xss_clean|max_length[255]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			$this->load->vars(array(
				'page_id' => $page_id
			));
			if($update){
				if($this->input->post('reward_item_id')){
					$reward_item_id = $this->input->post('reward_item_id');
				}
				if($this->input->get('reward_item_id')){
					$reward_item_id = $this->input->get('reward_item_id');
				}
				$this->load->model('reward_item_model');
				$reward_item = $this->reward_item_model->get_by_reward_item_id($reward_item_id);
			}

			if ($this->form_validation->run() == FALSE)
			{	
				if($update){

					$user = $this->socialhappen->get_user();
					$this->load->library('timezone_lib');
					$start_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $reward_item['start_timestamp']), $user['user_timezone_offset']);
					$end_time = $this->timezone_lib->convert_time(date('Y-m-d H:i:s', $reward_item['end_timestamp']), $user['user_timezone_offset']);

					$reward_item['start_date'] = $start_time;
					$reward_item['end_date'] = $end_time;
					$this->load->vars(array(
						'reward_item' => $reward_item,
						'reward_item_id' => $reward_item_id,
						'update' => TRUE
					));
				}
				$this->load->view('settings/page_apps/reward_item_form');
			}
			else
			{
				$user = $this->socialhappen->get_user();
				$this->load->library('timezone_lib');
				$start_timestamp = $this->timezone_lib->unconvert_time(set_value('start_date'), $user['user_timezone_offset']);
				$end_timestamp = $this->timezone_lib->unconvert_time(set_value('end_date'), $user['user_timezone_offset']);
	

				$this->load->model('reward_item_model');
				$input = array(
			       	'name' => set_value('name'),
			       	'start_timestamp' => strtotime($start_timestamp),
			       	'end_timestamp' => strtotime($end_timestamp),
			       	'status' => set_value('status'),
			       	'type' => 'redeem', //set_value('type'),
			       	'criteria_type' => 'page',
			       	'criteria_id' => $page_id,
			       	'redeem' => array(
				       	'point' => set_value('point'),
					    'amount' => set_value('amount')
				    ),
				    'image' => set_value('image')
				);
				if($update){
					$input['redeem']['amount_remain'] = issetor($reward_item['redeem']['amount_remain'], $reward_item['redeem']['amount']);
					$reward_item_id = $this->input->post('reward_item_id');
					$update_result = $this->reward_item_model->update($reward_item_id, $input);
				} else {
					$reward_item_id = $this->reward_item_model->add($input);
				}
			
				if ($reward_item_id)
				{
					if($update){
						//update success
					}
					$this->load->vars(array('reward_item'=>$input, 'reward_item_id'=>$reward_item_id));
					$this->load->view('settings/page_apps/reward_item');
				}
				else
				{
					echo 'An error occurred saving your information. Please try again later';
				}
			}
		}
	}

	function remove_item($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			$return = array(
				'success' => FALSE,
				'error' => 'You are not admin'
			);
		} else {
			$reward_item_id = $this->input->get('reward_item_id');
			$this->load->model('reward_item_model');
			$result = $this->reward_item_model->remove($reward_item_id);
			$return = array(
				'success' => $result
			);
		}
		echo json_encode($return);
	}

	function update_item($page_id = NULL){
		$this->add_item($page_id, TRUE);
	}
}
/* End of file page_reward.php */
/* Location: ./application/controllers/settings/page_reward.php */