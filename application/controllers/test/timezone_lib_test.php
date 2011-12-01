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
		$result = $this->timezone_lib->get_minute_offset_from_timezone('Asia/Bangkok');
		$this->unit->run($result === 7*60, TRUE, 'get_minute_offset_from_timezone', $result);
		$result = $this->timezone_lib->get_minute_offset_from_timezone('Europe/unknown_place');
		$this->unit->run($result === FALSE, TRUE, 'get_minute_offset_from_timezone', $result);
		$result = $this->timezone_lib->get_minute_offset_from_timezone('');
		$this->unit->run($result === FALSE, TRUE, 'get_minute_offset_from_timezone', $result);
	}

	function convert_time_test(){
		$time = '2011-11-11 11:11:11';
		$offset = '420'; //+0700
		$expected = '2011-11-11 18:11:11';
		$result = $this->timezone_lib->convert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'convert_time', $result);

		$time = '2011-11-11 11:11:11';
		$offset = 0; //+0000
		$expected = '2011-11-11 11:11:11';
		$result = $this->timezone_lib->convert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'convert_time', $result);

		$time = '2011-11-11 11:11:11';
		$offset = -690; //-1130
		$expected = '2011-11-10 23:41:11';
		$result = $this->timezone_lib->convert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'convert_time', $result);
	}

	function unconvert_time_test(){
		$expected = '2011-11-11 11:11:11';
		$offset = '420'; //+0700
		$time = '2011-11-11 18:11:11';
		$result = $this->timezone_lib->unconvert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'unconvert_time', $result);

		$expected = '2011-11-11 11:11:11';
		$offset = 0; //+0000
		$time = '2011-11-11 11:11:11';
		$result = $this->timezone_lib->unconvert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'unconvert_time', $result);

		$expected = '2011-11-11 11:11:11';
		$offset = -690; //-1130
		$time = '2011-11-10 23:41:11';
		$result = $this->timezone_lib->unconvert_time($time, $offset);
		$this->unit->run($result == $expected, TRUE, 'unconvert_time', $result);
	}

	function convest_fail_test(){
		$time = '2011-11-11 11:11:11';
		$result = $this->timezone_lib->convert_time($time);
		$this->unit->run($result === FAlSE, TRUE, 'convert fail', $result);
		$time = '2011-11-11 11:11:11';
		$result = $this->timezone_lib->unconvert_time($time);
		$this->unit->run($result === FAlSE, TRUE, 'unconvert fail', $result);
	}
}
/* End of file timezone_lib_test.php */
/* Location: ./application/controllers/test/timezone_lib_test.php */