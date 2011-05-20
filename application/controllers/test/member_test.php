<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_test extends CI_Controller {
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
	 * Tests json_get_profile()
	 * @author Manassarn M.
	 */
	function json_get_profile_test(){
		$content = file_get_contents(base_url().'member/json_get_profile/1');
		$array = json_decode($content);
		$this->unit->run($array, 'is_array', 'json_get_profile');
		$this->unit->run($array[0], 'is_object', 'First row');
		$this->unit->run($array[0]->user_id,'is_string','user_id');
		$this->unit->run($array[0]->user_facebook_id,'is_string','user_facebook_id');
		$this->unit->run($array[0]->user_register_date,'is_string','user_register_date');
		$this->unit->run($array[0]->user_last_seen,'is_string','user_last_seen');
		$this->unit->run(count((array)$array[0]) == 4, 'is_true', 'number of column');
	}
}

/* End of file member_test.php */
/* Location: ./application/controllers/test/member_test.php */