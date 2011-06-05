<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class stat_app_model_test extends CI_Controller {
	
	var $stat_app;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('stat_app_model','stat_app');
	}

	function __destruct(){
		echo $this->unit->report();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->stat_app))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->stat_app->drop_collection();
	}
	
	function create_index_test(){
		$this->stat_app->create_index();
	}
	
	
	function end_test(){
		//$this->stat_app->drop_collection();
	}
}
/* End of file stat_app_model_test.php */
/* Location: ./application/controllers/test/stat_app_model_test.php */