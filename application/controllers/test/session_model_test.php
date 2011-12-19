<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Session_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('session_model','sessions');
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
	 * Tests get_session_id_by_user_id()
	 * @author Manassarn M.
	 */
	function get_session_id_by_user_id_test(){
		$result = $this->sessions->get_session_id_by_user_id(55555);
		$this->unit->run($result, 'is_string', 'get_session_id_by_user_id()');
		$this->unit->run($result == '1111','is_true','$result == 1111');
	}	
	
	/**
	 * Tests get_user_id_by_session_id()
	 * @author Manassarn M.
	 */
	function get_user_id_by_session_id_test(){
		$result = $this->sessions->get_user_id_by_session_id(1111);
		$this->unit->run($result, 'is_string', 'get_user_id_by_session_id()');
		$this->unit->run($result == '55555','is_true','$result == 55555');
	}

}
/* End of file session_model_test.php */
/* Location: ./application/controllers/test/session_model_test.php */