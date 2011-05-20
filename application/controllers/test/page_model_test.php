<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('page_model','page');
	}

	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
		$this->get_company_pages_test();
		
	}
	
	/**
	 * Test get page profile from page_id
	 * @author Manassarn M.
	 */
	function get_profile_test(){
		$result = $this->page->get_profile(1);
		$this->unit->run($result, 'is_array', 'Get page profile');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->facebook_page_id,'is_string','facebook_page_id');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->page_name,'is_string','page_name');
		$this->unit->run($result[0]->page_detail,'is_string','page_detail');
		$this->unit->run($result[0]->page_all_member,'is_string','page_all_member');
		$this->unit->run($result[0]->page_new_member,'is_string','page_new_member');
		$this->unit->run($result[0]->page_image,'is_string','page_image');
		$this->unit->run(count((array)$result[0]) == 8, 'is_true', 'number of column');
	}
	
	/** 
	 * Tests get pages from company_id
	 * @author Manassarn M.
	 */
	function get_company_pages_test(){
		$result = $this->page->get_company_pages(1);
		$this->unit->run($result, 'is_array', 'Get company page');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->facebook_page_id,'is_string','facebook_page_id');
		$this->unit->run($result[0]->company_id,'is_string','company_id');
		$this->unit->run($result[0]->page_name,'is_string','page_name');
		$this->unit->run($result[0]->page_detail,'is_string','page_detail');
		$this->unit->run($result[0]->page_all_member,'is_string','page_all_member');
		$this->unit->run($result[0]->page_new_member,'is_string','page_new_member');
		$this->unit->run($result[0]->page_image,'is_string','page_image');
		$this->unit->run(count((array)$result[0]) == 8, 'is_true', 'number of column');
	}
}
/* End of file page_model_test.php */
/* Location: ./application/controllers/test/page_model_test.php */