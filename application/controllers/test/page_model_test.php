<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('page_model','pages');
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
	 * Tests get_page_profile_by_page_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_page_id_test(){
		$result = $this->pages->get_page_profile_by_page_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_page_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run(count($result) == 8,'is_true', 'number of column');
	}
	
	/** 
	 * Tests get_company_pages_by_company_id()
	 * @author Manassarn M.
	 */
	function get_company_pages_by_company_id_test(){
		$result = $this->pages->get_company_pages_by_company_id(1);
		$this->unit->run($result,'is_array', 'get_company_pages_by_company_id()');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['page_name'],'is_string','page_name');
		$this->unit->run($result[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($result[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result[0]['page_image'],'is_string','page_image');
		$this->unit->run(count($result[0]) == 8,'is_true', 'number of column');
	}
	
	/**
	 * Test add_page() and remove_page()
	 * @author Manassarn M.
	 */
	function add_page_and_remove_page_test(){
		$page = array(
							'facebook_page_id' => '1',
							'company_id' => '1',
							'page_name' => 'test',
							'page_detail' => 'test',
							'page_all_member' => 'test',
							'page_new_member' => 'test',
							'page_image' => 'test'
						);
		$page_id = $this->pages->add_page($page);
		$this->unit->run($page_id,'is_int','add_page()');
		
		$removed = $this->pages->remove_page($page_id);
		$this->unit->run($removed == 1,'is_true','remove_page()');
		
		$removed_again = $this->pages->remove_page($page_id);
		$this->unit->run($removed_again == 0,'is_true','remove_page()');
	}

	/**
	 * Test get_app_pages_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_app_pages_by_app_install_id_test(){
		$result = $this->pages->get_app_pages_by_app_install_id(1,1);
		$this->unit->run($result,'is_array', 'get_app_pages_by_app_install_id()');
		$this->unit->run($result[0]['app_install_id'],'is_string','app_install_id');
		$this->unit->run($result[0]['company_id'],'is_string','company_id');
		$this->unit->run($result[0]['app_id'],'is_string','app_id');
		$this->unit->run($result[0]['app_install_status'],'is_string','app_install_status');
		$this->unit->run($result[0]['app_install_date'],'is_string','app_install_date');
		$this->unit->run($result[0]['page_id'],'is_string','page_id');
		$this->unit->run($result[0]['app_install_secret_key'],'is_string','app_install_secret_key');
		$this->unit->run($result[0]['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result[0]['page_name'],'is_string','page_name');
		$this->unit->run($result[0]['page_detail'],'is_string','page_detail');
		$this->unit->run($result[0]['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result[0]['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result[0]['page_image'],'is_string','page_image');
		$this->unit->run(count($result[0]) == 13,'is_true', 'number of column');
	}
	
	/**
	 * Test get_page_profile_by_campaign_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_campaign_id_test(){
		$result = $this->pages->get_page_profile_by_campaign_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_campaign_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run(count($result) == 8,'is_true', 'number of column');
	}
	
	/**
	 * Test get_page_profile_by_app_install_id()
	 * @author Manassarn M.
	 */
	function get_page_profile_by_app_install_id_test(){
		$result = $this->pages->get_page_profile_by_app_install_id(1);
		$this->unit->run($result,'is_array', 'get_page_profile_by_app_install_id()');
		$this->unit->run($result['page_id'],'is_string','page_id');
		$this->unit->run($result['facebook_page_id'],'is_string','facebook_page_id');
		$this->unit->run($result['company_id'],'is_string','company_id');
		$this->unit->run($result['page_name'],'is_string','page_name');
		$this->unit->run($result['page_detail'],'is_string','page_detail');
		$this->unit->run($result['page_all_member'],'is_string','page_all_member');
		$this->unit->run($result['page_new_member'],'is_string','page_new_member');
		$this->unit->run($result['page_image'],'is_string','page_image');
		$this->unit->run(count($result) == 8,'is_true', 'number of column');
	}
	
	/**
	 * Test update_page_profile_by_page_id()
	 * @author Manassarn M.
	 */
	function update_page_profile_by_page_id_test(){
		$new_page_name = rand(1,10000);
		$data = array(
			'page_name' => $new_page_name
		);
		$result = $this->pages->update_page_profile_by_page_id(1,$data);
		$this->unit->run($result === TRUE,'is_true', 'Updated new_page_name without error');
		
		$result = $this->pages->get_page_profile_by_page_id(1);
		$this->unit->run($result['page_name'] == $new_page_name,'is_true',"Updated page_name to {$new_page_name}");
	}
	
	/**
	 * Test count_pages_by_app_id()
	 * @author Manassarn M.
	 */
	function count_pages_by_app_id_test(){
		$result = $this->pages->count_pages_by_app_id(1);
		$this->unit->run($result,'is_int', 'count_pages_by_app_id()');
	}
}
/* End of file page_model_test.php */
/* Location: ./application/controllers/test/page_model_test.php */