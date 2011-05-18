<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_apps_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('page_apps_model','page_apps');
	}

	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
		$this->get_page_apps_test();
		
		
	}
	
	/* 
	 * Tests get apps from page_id
	 * @author Manassarn M.
	 */
	function get_page_apps_test(){
		$result = $this->page_apps->get_page_apps(1);
		$this->unit->run($result, 'is_array', 'Get company page');
		$this->unit->run($result[0]->page_id,'is_string','page_id');
		$this->unit->run($result[0]->app_install_id,'is_string','app_install_id');
	}
}
/* End of file page_apps_model_test.php */
/* Location: ./application/controllers/test/page_apps_model_test.php */