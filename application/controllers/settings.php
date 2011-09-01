<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
		$this->load->library('form_validation');
	}
	
	function index(){
		$company_id = $this->input->get('c');
		$setting_name = $this->input->get('s');
		$param_id = $this->input->get('id');
		
		$setting_names_and_ids = array('account'=>'user_id','company_pages' => 'company_id', 'company'=>'company_id','page'=>'page_id','package'=>'user_id','reference'=>'user_id');
		
			if(!array_key_exists($setting_name, $setting_names_and_ids)){
				redirect("settings?s=account&id=".$this->socialhappen->get_user_id());
			}
			$user = $this->socialhappen->get_user();
			if($user_companies = $this->socialhappen->get_user_companies()){
				$this->load->model('page_model','pages');
				$company_pages = array();
				foreach ($user_companies as $user_company){
					$company_pages[$user_company['company_id']] = $this->pages->get_company_pages_by_company_id($user_company['company_id']);
				}
			}
			
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_company_id($company_id);
			$data = array(
				'company_id' => $company_id,
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'Settings',
						'vars' => array('company_id'=>$company_id,
										'setting_name' => $setting_name,
										'param_id' => $param_id),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'settings/main',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'go_back' => $this -> load -> view('settings/go_back', 
					array(
						'company' => $company
					),
				TRUE),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							'Settings' => base_url() . "settings"
							)
						)
					,
				TRUE),
				'sidebar' => $this -> load -> view('settings/sidebar', 
					array(
						'company_pages' => issetor($company_pages)
					),
				TRUE),
				'main' => $this -> load -> view("settings/main", 
					array(
						
					),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
			$this -> parser -> parse('settings/settings_view', $data);
		
	}
	
	function account($user_id = NULL){
		//$this->socialhappen->ajax_check();
		if($user_id && $user_id == $this->socialhappen->get_user_id()){
			$user = $this->socialhappen->get_user();
			$user_facebook = $this->facebook->getUser($user['user_facebook_id']);
		
			$this->form_validation->set_rules('first_name', 'First name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('last_name', 'Last name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('gender', 'Gender', 'required|xss_clean');
			$this->form_validation->set_rules('birth_date', 'Birth date', 'trim|xss_clean');
			$this->form_validation->set_rules('about', 'About', 'trim|xss_clean');
			$this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/account', array('user'=>$user,'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id'])));
			}
			else // passed validation proceed to post success logic
			{
				if(set_value('use_facebook_picture')){
					$user_image = issetor($this->facebook->get_profile_picture($user['user_facebook_id']));
				} else if (!$user_image = $this->socialhappen->upload_image('user_image')){
					$user_image = $user['user_image'];
				}
			
				// build array for the model
				$user_update_data = array(
								'user_first_name' => set_value('first_name'),
								'user_last_name' => set_value('last_name'),
								'user_gender' => set_value('gender'),
								'user_birth_date' => set_value('birth_date'),
								'user_about' => set_value('about'),
								'user_image' => $user_image
							);
				$this->load->model('user_model','users');
				if ($this->users->update_user($user_id, $user_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/account', array('user'=>array_merge($user,$user_update_data), 'user_facebook' => $user_facebook, 'user_profile_picture'=>$this->facebook->get_profile_picture($user['user_facebook_id']),'success' => TRUE));
				}
				else
				{
					echo 'error occured';
				}
			}
		}
	}
	
	function company_pages($user_id = NULL){
		//$this->socialhappen->ajax_check();
		if($user_id && $user_id == $this->socialhappen->get_user_id()){
			$user_companies = $this->socialhappen->get_user_companies();
			$this->load->model('page_model','pages');
			$company_pages = array();
			foreach ($user_companies as $user_company){
				$company_pages[$user_company['company_id']] = $this->pages->get_company_pages_by_company_id($user_company['company_id']);
			}
			$this->load->view('settings/companies_and_pages',array('company_pages' => $company_pages, 'user_companies' => $user_companies));
		}
	}
	
	function company($company_id = NULL){
		//$this->socialhappen->ajax_check();
		if(!$this->socialhappen->check_admin(array('company_id' => $company_id),array('role_company_edit'))){
			//no access
		} else {
			$this->load->model('company_model','companies');
			$company = $this->companies->get_company_profile_by_company_id($company_id);
			
			$this->load->model('company_apps_model','company_apps');
			$company_apps = $this->company_apps->get_company_apps_by_company_id($company_id);
			
			$this->load->model('user_companies_model','user_companies');
			$company_users = $this->user_companies->get_company_users_by_company_id($company_id);
			
			$this->form_validation->set_rules('company_name', 'Company name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_detail', 'Company detail', 'trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('company_email', 'Contact email', 'required|trim|xss_clean|valid_email|max_length[255]');			
			$this->form_validation->set_rules('company_telephone', 'Contact telephone', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('company_website', 'Company website', 'trim|xss_clean|max_length[255]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/company', array('company'=>$company, 'company_apps' => $company_apps, 'company_users' => $company_users, 'success'=>$this->input->get('success')));
			}
			else 
			{
				if (!$company_image = $this->socialhappen->upload_image('company_image')){
					$company_image = $company['company_image'];
				}
				
				$company_update_data = array(
								'company_name' => set_value('company_name'),
								'company_detail' => set_value('company_detail'),
								'company_email' => set_value('company_email'),
								'company_telephone' => set_value('company_telephone'),
								'company_website' => set_value('company_website'),
								'company_image' => $company_image
							);
			
				if ($this->companies->update_company_profile_by_company_id($company_id, $company_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/company', array('company'=>array_merge($company,$company_update_data), 'company_apps' => $company_apps, 'company_users' => $company_users, 'success'=>TRUE));
				}
				else
				{
					echo 'An error occurred saving your information. Please try again later';
				}
			}
		}
	}
	
	function company_admin($company_id = NULL){
		//$this->socialhappen->ajax_check();
		if(!$this->socialhappen->check_admin(array('company_id' => $company_id),array('role_company_edit'))){
			//no access
		} else {
			$this->form_validation->set_rules('user_id','required|trim|integer|xss_clean|max_length[20]');
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				redirect("settings/company/{$company_id}");
			}
			else 
			{
				if($this->socialhappen->check_user(set_value('user_id'))){
					$company_admin = array(
								'user_id' => set_value('user_id'),
								'company_id' => $company_id,
								'user_role' => 1
							);
			
					if ($this->user_companies->add_user_company($company_admin)) // the information has therefore been successfully saved in the db
					{
						redirect("settings/company/{$company_id}?success=1");
					}
				}
				redirect("settings/company/{$company_id}?error=1");
			}
		}
	}
	
	function page($page_id = NULL){
		//$this->socialhappen->ajax_check();
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->model('page_model','pages');
			$page = $this->pages->get_page_profile_by_page_id($page_id);
			$page_user_fields = $this->pages->get_page_user_fields_by_page_id($page_id);
			
			$this->load->model('installed_apps_model','installed_apps');
			$page_apps = $this->installed_apps->get_installed_apps_by_page_id($page_id);
			
			$this->load->model('user_pages_model','user_pages');
			$page_users = $this->user_pages->get_page_users_by_page_id($page_id);
			
			$this->load->model('user_companies_model','user_companies');
			$company_users = $this->user_companies->get_company_users_by_company_id($page['company_id']);
			
			
			foreach($company_users as $key => &$value){ //Company admins
				if(!($company_users[$key]['role_all'] || $company_users[$key]['role_all_company_pages_edit'])){
					unset($company_users[$key]);
				}
			}
			
			foreach($page_users as $key => &$value){ //Page admins
				if(!($page_users[$key]['role_all'] || $page_users[$key]['role_page_edit'])){
					unset($page_users[$key]);
				} else {
					foreach($company_users as $company_user){
						if($company_user['user_id'] == $page_users[$key]['user_id']){
							unset($page_users[$key]);
							break;
						}
					}
				}
			}
			
			$page_facebook = $this->facebook->get_page_info($page['facebook_page_id']);
			
			$this->form_validation->set_rules('page_name', 'Page name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('page_detail', 'Page detail', 'trim|xss_clean');
			$this->form_validation->set_rules('use_facebook_picture', 'Use facebook picture', '');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE) // validation hasn't been passed
			{
				$this->load->view('settings/page', array('page'=>$page, 'page_apps' => $page_apps, 'company_users' => $company_users, 'page_users' => $page_users, 'page_facebook' => $page_facebook, 'page_user_fields' => $page_user_fields));
			}
			else 
			{
				if(set_value('use_facebook_picture')){
					$page_image = issetor($page_facebook['picture']);
				} else if (!$page_image = $this->socialhappen->upload_image('page_image')){
					$page_image = $page['page_image'];
				}
				
				$page_update_data = array(
								'page_name' => set_value('page_name'),
								'page_detail' => set_value('page_detail'),
								'page_image' => $page_image
							);
						
				// run insert model to write data to db
			
				if ($this->pages->update_page_profile_by_page_id($page_id,$page_update_data)) // the information has therefore been successfully saved in the db
				{
					$this->load->view('settings/page', array('page'=>array_merge($page,$page_update_data), 'page_apps' => $page_apps, 'company_users' => $company_users, 'page_users' => $page_users, 'page_facebook' => $page_facebook, 'success'=>TRUE, 'page_user_fields' => $page_user_fields));
				}
				else
				{
				echo 'An error occurred saving your information. Please try again later';
				// Or whatever error handling is necessary
				}
			}
		}
	}
	
	function page_admin($page_id = NULL){
		//$this->socialhappen->ajax_check();
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->form_validation->set_rules('user_id','required|trim|integer|xss_clean|max_length[20]');
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			if ($this->form_validation->run() == FALSE)
			{
				redirect("settings/page/{$page_id}");
			}
			else 
			{
				$this->load->model('company_model','companies');
				$company = $this->companies->get_company_profile_by_page_id($page_id);
				if($this->socialhappen->check_user(set_value('user_id'))){
					if(!$this->user_companies->is_company_admin(set_value('user_id'),$company['company_id'])){
						$company_admin = array(
									'user_id' => set_value('user_id'),
									'company_id' => $company['company_id'],
									'user_role' => 2
								);
						$this->user_companies->add_user_company($company_admin);
					}
					$page_admin = array(
								'user_id' => set_value('user_id'),
								'page_id' => $page_id,
								'user_role' => 2
							);
					$this->user_pages->add_user_page($page_admin);
				}
				redirect("settings/page/{$page_id}?success=1");
			}
		}
	}
	
	function page_user_fields($page_id = NULL){
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('label', 'Label', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('type', 'Type', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('required', 'Required', 'trim|xss_clean');		
			//$this->form_validation->set_rules('rules', 'Rules', 'trim|xss_clean');			
			$this->form_validation->set_rules('items', 'Items', 'trim|xss_clean');			
			$this->form_validation->set_rules('order', 'Order', 'required|trim|xss_clean|is_numeric|max_length[5]');
				
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
			
			$success = FALSE;
			if ($this->form_validation->run() != FALSE)
			{
				$this->load->model('page_model','pages');
				$new_page_user_fields = array(
					array(
						'name' => set_value('name'),
						'label' => set_value('label'),
						'type' => set_value('type'),
						'required' => set_value('required') == 1,
						//'rules' => set_value('rules'),
						'items' => explode(',',set_value('items')),
						'order' => set_value('order')
					)
				);
				
				//var_export($new_page_user_fields);
				if ($this->pages->add_page_user_fields_by_page_id($page_id, $new_page_user_fields))
				{
					$success = TRUE;
				}
				
			}
			
			$this->form_validation->set_rules('edit_name[]', 'Name', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('edit_label[]', 'Label', 'required|trim|xss_clean|max_length[255]');			
			$this->form_validation->set_rules('edit_type[]', 'Type', 'required|trim|xss_clean|max_length[20]');			
			$this->form_validation->set_rules('edit_required[]', 'Required', 'trim|xss_clean|max_length[1]');		
			//$this->form_validation->set_rules('edit_rules[]', 'Rules', 'trim|xss_clean');			
			$this->form_validation->set_rules('edit_items[]', 'Items', 'trim|xss_clean');			
			$this->form_validation->set_rules('edit_order[]', 'Order', 'required|trim|xss_clean|is_numeric|max_length[5]');
		
			$required_checked = $this->input->post('edit_required');
			$fields_count = count($this->input->post('edit_name'));
			$edit_required = array();
			foreach(range(0,$fields_count-1) as $nth){
				$edit_required[$nth] = in_array($nth, $required_checked) ? TRUE : FALSE;
			}
			
			$this->load->model('page_model','pages');
			$temp_page_user_fields = array(
				
				'name' => $this->input->post('edit_name'),
				'label' => $this->input->post('edit_label'),
				'type' => $this->input->post('edit_type'),
				'required' => $edit_required,
				//'rules' => $this->input->post('edit_rules'),
				'items' => $this->input->post('edit_items'),
				'order' => $this->input->post('edit_order')
			);
			$page_user_fields = array();
			$field_ids = $this->input->post('id');
			foreach($temp_page_user_fields as $field_name => $data_array) {
				foreach($data_array as $key => $value) {
					if($field_name == 'items' && $value != ''){
						$value = explode(',',$value);
					}
					$page_user_fields[$field_ids[$key]][$field_name] = $value;
				}
			}
			//var_export($page_user_fields);
			if ($this->pages->update_page_user_fields_by_page_id($page_id, $page_user_fields))
			{
				$success = TRUE;
			}
			
			if($success) {
				redirect("settings/page/{$page_id}?success=1");
			}
			redirect("settings/page/{$page_id}");
		}
	}
	
	function package($user_id = NULL){
		//$this->socialhappen->ajax_check();
		if($user_id && $user_id == $this->socialhappen->get_user_id())
		{
			//Get all orders
			$this->load->model('order_model','orders');
			$orders = $this->orders->get_orders_by_user_id($user_id, $limit = NULL, $offset = NULL);
			$this->load->model('order_items_model','order_items');
			foreach($orders as &$order)
			{
				$items = $this->order_items->get_order_items_by_order_id($order['order_id']);
				$order['package_name'] = $items[0]['item_name'];
			}
			arsort($orders); //reverse order
			
			//Get current package
			$this->load->model('package_users_model','package_users');
			$current_package = $this->package_users->get_package_by_user_id($user_id);
			
			//Get package apps
			$this->load->model('package_apps_model','package_apps');
			$apps = $this->package_apps->get_apps_by_package_id($current_package['package_id']);
			
			//Count user companies
			$this->load->model('company_model','companies');
			$user_companies = $this->companies->get_companies_by_user_id($user_id);
			
			//Count user pages
			$this->load->model('user_pages_model','user_pages');
			$user_pages = $this->user_pages->get_user_pages_by_user_id($user_id);
			
			//Count members
			$members = $this->package_users->count_user_members_by_user_id($user_id);
			
			//is upgradable?
			$this->load->model('package_model','package');
			$is_upgradable = $this->package->is_upgradable($current_package['package_id']);
			
			$data = array(
				'orders' => $orders,
				'current_package' => $current_package,
				'user_companies' => count($user_companies),
				'user_pages' => count($user_pages),
				'members' => $members,
				'apps' => $apps,
				'is_upgradable' => $is_upgradable
			);
			$this->load->view('settings/package',$data);
		}
	}
	
	function reference(){
	
	}
}
/* End of file settings.php */
/* Location: ./application/controllers/settings.php */