<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timezone_lib_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->library('timezone_lib');
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

	function get_minute_offset_from_timezone_test(){
		$result = $this->timezone_lib->get_minute_offset_from_timezone('UTC');
		$this->unit->run($result === 0, TRUE, 'get_minute_offset_from_timezone', $result);
		echo $result = $this->timezone_lib->get_minute_offset_from_timezone('Asia/Bangkok');
		$this->unit->run($result === 7*60, TRUE, 'get_minute_offset_from_timezone', $result);
		$result = $this->timezone_lib->get_minute_offset_from_timezone('Europe/unknown_place');
		$this->unit->run($result === FALSE, TRUE, 'get_minute_offset_from_timezone', $result);
		$result = $this->timezone_lib->get_minute_offset_from_timezone('');
		$this->unit->run($result === FALSE, TRUE, 'get_minute_offset_from_timezone', $result);
	}
}
/* End of file timezone_lib_test.php */
/* Location: ./application/controllers/test/timezone_lib_test.php */