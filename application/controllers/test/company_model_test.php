<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('company_model','companies');
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
	 * Tests get_company_profile_by_company_id()
	 * @author Manassarn M.
	 */
	function get_company_profile_by_company_id_test(){
		$result = $this->companies->get_company_profile_by_company_id(1);
		$this->unit->run($result,'is_array', 'get_company_profile_by_company_id()');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($result['company_name'],'is_string','company_name');
		$this->unit->run($result['company_detail'],'is_string','company_detail');
		$this->unit->run($result['company_address'],'is_string','company_address');
		$this->unit->run($result['company_email'],'is_string','company_email');
		$this->unit->run($result['company_telephone'],'is_string','company_telephone');
		$this->unit->run($result['company_register_date'],'is_string','company_register_date');
		$this->unit->run($result['company_username'],'is_string','company_username');
		$this->unit->run($result['company_password'],'is_string','company_password');
		$this->unit->run($result['company_image'],'is_string','company_image');
		$this->unit->run(count($result) == 11,'is_true', 'number of column');
	}

	/**
	 * Test add_company() and remove_company()
	 * @author Manassarn M.
	 */
	function add_company_and_remove_company_test(){
		$company = array(
							'creator_user_id' => '1',
							'company_name' => 'test',
							'company_detail' => 'test',
							'company_address' => 'test',
							'company_email' => 'test@test.com',
							'company_telephone' => '021234567',
							'company_register_date' => '2011-05-09 17:52:17',
							'company_username' => 'test',
							'company_password' => 'test',
							'company_image' => 'test.jpg'
						);
		$company_id = $this->companies->add_company($company);
		$this->unit->run($company_id,'is_int','add_company()');
		
		$removed = $this->companies->remove_company($company_id);
		$this->unit->run($removed == 1,'is_true','remove_company()');
		
		$removed_again = $this->companies->remove_company($company_id);
		$this->unit->run($removed_again == 0,'is_true','remove_company()');
	}
	
	/**
	 * Tests get_company_profile_by_page_id()
	 * @author Manassarn M.
	 */
	function get_company_profile_by_page_id_test(){
		$result = $this->companies->get_company_profile_by_page_id(1);
		$this->unit->run($result,'is_array', 'get_company_profile_by_page_id()');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($result['company_name'],'is_string','company_name');
		$this->unit->run($result['company_detail'],'is_string','company_detail');
		$this->unit->run($result['company_address'],'is_string','company_address');
		$this->unit->run($result['company_email'],'is_string','company_email');
		$this->unit->run($result['company_telephone'],'is_string','company_telephone');
		$this->unit->run($result['company_register_date'],'is_string','company_register_date');
		$this->unit->run($result['company_username'],'is_string','company_username');
		$this->unit->run($result['company_password'],'is_string','company_password');
		$this->unit->run($result['company_image'],'is_string','company_image');
		$this->unit->run(count($result) == 11,'is_true', 'number of column');

		$this->unit->run($result['company_id'] == 1,'is_true','$company_id == 1');
	}
	
	/**
	 * Tests get_company_profile_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_company_profile_by_campaign_id_test(){
		$result = $this->companies->get_company_profile_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_company_profile_by_campaign_id()');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($result['company_name'],'is_string','company_name');
		$this->unit->run($result['company_detail'],'is_string','company_detail');
		$this->unit->run($result['company_address'],'is_string','company_address');
		$this->unit->run($result['company_email'],'is_string','company_email');
		$this->unit->run($result['company_telephone'],'is_string','company_telephone');
		$this->unit->run($result['company_register_date'],'is_string','company_register_date');
		$this->unit->run($result['company_username'],'is_string','company_username');
		$this->unit->run($result['company_password'],'is_string','company_password');
		$this->unit->run($result['company_image'],'is_string','company_image');
		$this->unit->run(count($result) == 11,'is_true', 'number of column');

		$this->unit->run($result['company_id'] == 1,'is_true','$company_id == 1');
	}
	
	/**
	 * Tests get_company_profile_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_company_profile_by_app_install_id_test(){
		$result = $this->companies->get_company_profile_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_company_profile_by_app_install_id()');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($result['company_name'],'is_string','company_name');
		$this->unit->run($result['company_detail'],'is_string','company_detail');
		$this->unit->run($result['company_address'],'is_string','company_address');
		$this->unit->run($result['company_email'],'is_string','company_email');
		$this->unit->run($result['company_telephone'],'is_string','company_telephone');
		$this->unit->run($result['company_register_date'],'is_string','company_register_date');
		$this->unit->run($result['company_username'],'is_string','company_username');
		$this->unit->run($result['company_password'],'is_string','company_password');
		$this->unit->run($result['company_image'],'is_string','company_image');
		$this->unit->run(count($result) == 11,'is_true', 'number of column');

		$this->unit->run($result['company_id'] == 1,'is_true','$company_id == 1');
	}
	
	/**
	 * Tests get_companies_by_user_id()
	 * @author Weerapat P.
	 */
	function get_companies_by_user_id_test(){
		$user_id = 1;
		$results = $this->companies->get_companies_by_user_id($user_id);
		$this->unit->run($results,'is_array', 'get_companies_by_user_id()');
		$this->unit->run($results[0]['company_id'],'is_string','company_id');
		$this->unit->run($results[0]['creator_user_id'],'is_string','creator_user_id');
		$this->unit->run($results[0]['company_name'],'is_string','company_name');
		$this->unit->run($results[0]['company_detail'],'is_string','company_detail');
		$this->unit->run($results[0]['company_address'],'is_string','company_address');
		$this->unit->run($results[0]['company_email'],'is_string','company_email');
		$this->unit->run($results[0]['company_telephone'],'is_string','company_telephone');
		$this->unit->run($results[0]['company_register_date'],'is_string','company_register_date');
		$this->unit->run($results[0]['company_username'],'is_string','company_username');
		$this->unit->run($results[0]['company_password'],'is_string','company_password');
		$this->unit->run($results[0]['company_image'],'is_string','company_image');
		$this->unit->run(count($results[0]),12, 'number of column');

		$this->unit->run($results[0]['company_id'],1,'$company_id == 1');
	}
	
	/**
	 * Test update_company_profile_by_company_id()
	 * @author Manassarn M.
	 */
	function update_company_profile_by_company_id_test(){
		$new_company_name = rand(1,10000);
		$data = array(
			'company_name' => $new_company_name
		);
		$result = $this->companies->update_company_profile_by_company_id(1,$data);
		$this->unit->run($result === TRUE,'is_true', 'Updated new_company_name without error');
		
		$result = $this->companies->get_company_profile_by_company_id(1);
		$this->unit->run($result['company_name'] == $new_company_name,'is_true',"Updated company_name to {$new_company_name}");
		
	}
}
/* End of file company_model_test.php */
/* Location: ./application/controllers/test/company_model_test_model_test.php */