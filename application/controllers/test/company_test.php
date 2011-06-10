<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	function __destruct(){
		echo $this->unit->report();
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
		$content = file_get_contents(base_url().'company/json_get_pages/1');
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
		$this->unit->run(count($array[0]) == 8,'is_true', 'number of column');
	}
	
	/**
	 * Tests json_get_apps().
	 * @author Manassarn M.
	 */
	function json_get_apps_test(){
		$content = file_get_contents(base_url().'company/json_get_apps/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_apps()');
		$this->unit->run(count($array[0]) == 15,'is_true', 'number of column');
		$this->unit->run($array[0],'is_array', 'First row');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['available_date'],'is_string','available_date');
		$this->unit->run($array[0]['app_name'],'is_string','app_name');
		$this->unit->run($array[0]['app_type_id'],'is_string','app_type_id');
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
		$content = file_get_contents(base_url().'company/json_get_installed_apps/1');
		$array = json_decode($content, TRUE);
		$this->unit->run($array,'is_array', 'json_get_installed_apps()');
		$this->unit->run(count($array[0]) == 19,'is_true', 'number of column');
		$this->unit->run($array[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($array[0]['company_id'],'is_string','company_id');
		$this->unit->run($array[0]['app_id'],'is_string','app_id');
		$this->unit->run($array[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($array[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($array[0]['page_id'],'is_string','page_id');
		$this->unit->run($array[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($array[0]['app_name'],'is_string','app_name');
		$this->unit->run($array[0]['app_type_id'],'is_string','app_type_id');
		$this->unit->run($array[0]['app_maintainance'],'is_string','app_maintainance');
		$this->unit->run($array[0]['app_show_in_list'],'is_string','app_show_in_list');
		$this->unit->run($array[0]['app_description'],'is_string','app_description');
		$this->unit->run($array[0]['app_secret_key'],'is_string','app_secret_key');
		$this->unit->run($array[0]['app_url'],'is_string','app_url');
		$this->unit->run($array[0]['app_install_url'],'is_string','app_install_url');
		$this->unit->run($array[0]['app_config_url'],'is_string','app_config_url');
		$this->unit->run($array[0]['app_support_page_tab'],'is_string','app_support_page_tab');
		$this->unit->run($array[0]['app_image'],'is_string','app_image');
		$this->unit->run($array[0]['facebook_app_api_key'],'is_string','facebook_app_api_key');
	}
	
	/**
	 * Test json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$content = file_get_contents(base_url().'company/json_get_profile/1');
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
		$this->unit->run(count($array) == 11,'is_true', 'number of column');
	}

	/**
	 * Tests json_add()
	 * @author Manassarn M.
	 */
	function json_add_test(){
		$company = array(
							'creator_user_id' => 1,
							'company_name' => 'test',
							'company_detail' => 'test',
							'company_address' => 'test',
							'company_email' => 'test@test.com',
							'company_telephone' => '6543211',
							'company_username' => 'test',
							'company_password' => 'test',
							'company_image' => 'test'
						);
		$content = $this->curl->simple_post('company/json_add', $company, array(CURLOPT_RETURNTRANSFER => 1));
		$content = json_decode($content, TRUE);
		$this->unit->run($content,'is_array', 'json_add()');
		$this->unit->run($content['company_id'],'is_int','company_id');
		$this->unit->run($content['status'] == 'OK','is_true', 'status');
		$this->unit->run(count($content) == 2,'is_true','return count');
	}

function json_add_test_test(){
	$company = array(
							'creator_user_id' => 1,
							'company_name' => 'test',
							'company_detail' => 'test',
							'company_address' => 'test',
							'company_email' => 'test@test.com',
							'company_telephone' => '6543211',
							'company_username' => 'test',
							'company_password' => 'test',
							'company_image' => 'test'
						);
	 $ch = curl_init(base_url()."company/json_add");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $company);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);       
        curl_close($ch);
        echo $output;
}
}

/* End of file company_test.php */
/* Location: ./application/controllers/test/company_test.php */
