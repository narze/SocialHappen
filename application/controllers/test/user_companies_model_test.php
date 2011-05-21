<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_companies_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('user_companies_model','user_companies');
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
	 * TO-DO : do this
	 * @author Manassarn M.
	 */
	function get_page_profile_by_id_test(){
		$this->unit->run(TRUE,'is_false');
	}
}
/* End of file page_model_test.php */
/* Location: ./application/controllers/test/page_model_test.php */