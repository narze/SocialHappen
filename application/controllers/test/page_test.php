<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	function __destruct(){
		echo $this->unit->report();
	}

	function index(){
	}
	
	/**
	 * Tests json_page_app_list()
	 * @author Manassarn M.
	 */
}

/* End of file api.php */
/* Location: ./application/controllers/test/api.php */