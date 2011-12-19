<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_ctrl_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('controller/company_ctrl');
		$this->unit->reset_dbs();
	}
	
	function __destruct(){
		$this->unit->report_with_counter();
	}

	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	/**
	 * Tests json_get_pages().
	 * @author Manassarn M.
	 */
	function json_get_pages_test(){
		$company_id = 1;
		$content = $this->company_ctrl->json_get_pages($company_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_pages()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['page_name'],'is_string','page_name');
		$this->unit->run($array[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($array[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($array[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($array[0]['page_image'],'is_string','page_image');
	}
	
	/**
	 * Tests json_get_apps().
	 * @author Manassarn M.
	 */
	function json_get_apps_test(){
		$company_id = 1;
		$content = $this->company_ctrl->json_get_apps($company_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_apps()');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['available_date'],'is_string','available_date');
		$this->unit->run($array[0]['app_name'],'is_string','app_name');
		$this->unit->run($array[0]['app_type_id'] == 1,'is_true',"app_type_id == 1");
		$this->unit->run($array[0]['app_type'] == "Page Only",'is_true',"app_type == 'Page Only");
		$this->unit->run($array[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($array[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($array[0]['app_description'],'is_string','app_description');
		$this->unit->run($array[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($array[0]['app_url'],'is_string','app_url');
		$this->unit->run($array[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($array[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($array[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($array[0]['app_image'],'is_string','app_image');
	}

	/**
	 * Tests json_get_installed_apps()
	 * @author Manassarn M.
	 */
	function json_get_installed_apps_test(){
		$company_id = 1;
		$content = $this->company_ctrl->json_get_installed_apps($company_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_installed_apps()');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status_id'] == 1,'is_true','app_install_status_id == 1');
		$this->unit->run($array[0]['app_install_status'] == "Installed",'is_true','app_install_status == "Installed"');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($array[0]['app_name'],'is_string','app_name');
		$this->unit->run($array[0]['app_type_id'] == 1,'is_true',"app_type_id == 1");
		$this->unit->run($array[0]['app_type'] == "Page Only",'is_true',"app_type == 'Page Only");
		$this->unit->run($array[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($array[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($array[0]['app_description'],'is_string','app_description');
		$this->unit->run($array[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($array[0]['app_url'],'is_string','app_url');
		$this->unit->run($array[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($array[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($array[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($array[0]['app_image'],'is_string','app_image');
		$this->unit->run($array[0]['app_facebook_api_key'],'is_string','app_facebook_api_key');
	}
	
	/**
	 * Test json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$company_id = 1;
		$content = $this->company_ctrl->json_get_profile($company_id);
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_profile()');
		$this->unit->run($array['company_id'],'is_string','company_id');
		$this->unit->run($array['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($array['company_name'],'is_string','company_name');
		$this->unit->run($array['company_detail'],'is_string','company_detail');
		$this->unit->run($array['company_address'],'is_string','company_address');
		$this->unit->run($array['company_email'],'is_string','company_email');
		$this->unit->run($array['company_telephone'],'is_string','company_telephone');
		$this->unit->run($array['company_register_date'],'is_string','company_register_date');
		$this->unit->run($array['company_username'],'is_string','company_username');
		$this->unit->run($array['company_password'],'is_string','company_password');
		$this->unit->run($array['company_image'],'is_string','company_image');
	}


	function index_test(){
		
	}

	function company_package_limited_test(){
		
	}

	function page_installed_test(){
		
	}

	function json_get_user_companies_test(){
		
	}

	function json_get_pages_count_test(){
		
	}

	function json_get_installed_apps_count_test(){
		
	}

	function json_get_campaigns_count_test(){
		
	}

	function json_get_installed_apps_count_not_in_page_test(){
		
	}

	function json_get_all_apps_test(){
	
	}	

	function json_get_installed_apps_not_in_page_test(){
		
	}

	function json_get_not_installed_apps_test(){
		
	}

}

/* End of file company_ctrl_test.php */
/* Location: ./application/controllers/test/company_ctrl_test.php */
