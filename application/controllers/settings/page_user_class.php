<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_user_class extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			exit('You are not admin');
		}
		$this->load->library('settings');
		$config_name = 'user_class';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->library('form_validation');
			$this->load->library('app_component_lib');
			$this->load->model('page_model','page');
			$page = $this->page->get_page_profile_by_page_id($page_id);
			$page_component = $this->app_component_lib->get_page($page_id);
			$page_user_class = issetor($page_component['classes']);

			$this->load->vars(array(
				'page_id' => $page_id,
				'page_user_class' => $page_user_class,
				'updated' => FALSE
			));

			//$this->form_validation->set_rules('enable', 'Enable', 'trim|xss_clean');
			if($page_user_class){
				foreach($page_user_class as $user_class){
					$this->form_validation->set_rules('name['.$user_class['achievement_id'].']', 'Name', 'required|trim|xss_clean|max_length[50]');	
					$this->form_validation->set_rules('invite_accepted['.$user_class['achievement_id'].']', 'Invite Accepted', 'required|trim|is_numeric|max_length[10]');
				}
			}
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
		
			if ($this->form_validation->run() == FALSE){
				//$this->load->vars(array('test'=>print_r($page_user_class,true)));
			} else {
				//$enable = set_value('enable') == 1;
				$achievement_ids = $this->input->post('aid');
				$names = $this->input->post('name');
				$invite_accepteds = $this->input->post('invite_accepted');
				$classes = array();
				foreach($achievement_ids as $aid){
					$class = array(
						'name' => $names[$aid],
						'invite_accepted' => $invite_accepteds[$aid],
						'achievement_id' => $aid
					);
					$classes[] = $class;
				}
				if($this->app_component_lib->update_page_classes($page_id, $classes)){
					$this->load->vars(array('updated' => TRUE));
				}
				
			}
			$this->load->view('settings/page_apps/user_class');
		}
	}

	function add_default_classes($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			exit('You are not admin');
		}
		$this->load->library('app_component_lib');
		$page_component = $this->app_component_lib->get_page($page_id);
		if(!$page_component || !isset($page_component['classes']) || count($page_component['classes']) != 3){
			$this->app_component_lib->add_default_user_classes($page_id);
		}
		redirect('settings/page_user_class/'.$page_id);
	}
}
/* End of file page_user_class.php */
/* Location: ./application/controllers/settings/page_user_class.php */