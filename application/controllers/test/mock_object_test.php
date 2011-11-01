<?php
require_once 'MockMe.php';
use \Mockery as m;

class Mock_object_test extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('app_model', 'some_model');
		$this->load->library('socialhappen', 'some_lib');
		$this->set_mock_objects();
	}
	
	function __destruct(){
		m::close();
		$this->unit->report_with_counter();
	}
	
	function set_mock_objects(){
		$this->some_model = m::mock('modellll');
        $this->some_model->shouldReceive('getsomething')->once()->andReturn(10);
		$this->some_lib = m::mock('libbbbbb');
        $this->some_lib->shouldReceive('getsomethingtoo')->once()->andReturn(555);
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function get_something_test(){
		$something = $this->some_model->getsomething();
		$this->unit->run($something, 10, 'test mock object');
		$something = $this->some_lib->getsomethingtoo();
		$this->unit->run($something, 555, 'test another mock object');
	}
}