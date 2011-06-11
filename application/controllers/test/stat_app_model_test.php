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
	
	function add_stat_app_test(){
		$app_install_id = NULL;
		$action_id = NULL;
		$date = NULL;
		$app_id = 1;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_false', 'add stat app fail - invalid args', '');
		
		$app_install_id = 1;
		$action_id = 1;
		$date = 20110510;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat app success', '');
		
		$app_install_id = 2;
		$action_id = 1;
		$date = 20110510;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat app success', '');
		
		$app_install_id = 3;
		$action_id = 1;
		$date = 2011006;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat app success', '');
		
		$app_install_id = 3;
		$action_id = 2;
		$date = 20110507;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat app success', '');
		
		$app_install_id = 3;
		$action_id = 2;
		$date = 20110510;
		$result = $this->stat_app->add_stat_app($app_id, $app_install_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat app success', '');
	}

	function increment_stat_app_test(){
		for($i = 0; $i < 5; $i++){
			$app_install_id = 3;
			$action_id = 2;
			$date = 20110507;
			$app_id = 1;
			$result = $this->stat_app->increment_stat_app($app_id, $app_install_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat app success', '');
			
			$app_install_id = 3;
			$action_id = 2;
			$date = 20110510;
			$result = $this->stat_app->increment_stat_app($app_id, $app_install_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat app success', '');
			
			$app_install_id = 3;
			$action_id = 2;
			$date = 20110509;
			$result = $this->stat_app->increment_stat_app($app_id, $app_install_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat app success', '');
		}
	}

	function get_stat_app_test(){
		// app_install_id
		$result = $this->stat_app->get_stat_app(array('app_install_id' => 2));
		$this->unit->run(count($result), 1, 'count add stat app app_install_id 1', 'count: ' . count($result) );
		
		$result = $this->stat_app->get_stat_app(array('app_install_id' => 3));
		$this->unit->run(count($result), 4, 'count add stat app app_install_id 3', 'count: ' . count($result) );
		
		$count = array(1, 6, 5, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat app app_install_id 3', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		
		// date
		$result = $this->stat_app->get_stat_app(array('date' => 20110509));
		$this->unit->run(count($result), 1, 'count add stat app date 20110506', 'count: ' . count($result) );
		
		$result = $this->stat_app->get_stat_app(array('date' => 20110510));
		$this->unit->run(count($result), 3, 'count add stat app date 20110506', 'count: ' . count($result) );
		
		$count = array(1, 1, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat stat app date 20110506', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		// action_id
		$result = $this->stat_app->get_stat_app(array('action_id' => 5));
		$this->unit->run(count($result), 0, 'count add stat app action_id 5', 'count: ' . count($result) );
		
		$result = $this->stat_app->get_stat_app(array('action_id' => 2));
		$this->unit->run(count($result), 3, 'count add stat app action_id 2', 'count: ' . count($result) );
		
		$count = array(6, 5, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat stat app action_id 2', '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function delete_stat_app_test(){
		// get 
		$result = $this->stat_app->get_stat_app(array('action_id' => 2, 'app_install_id' => 3, 'date' => 20110509));
		$this->unit->run(count($result), 1, 'count add stat app action_id 2, app_install_id 3, date 20110509', 'count: ' . count($result) );
		$match = $result[0]['date'] == 20110509;
		$this->unit->run($match, 'is_true', 'count stat stat app action_id 2, app_install_id 3, date 20110509', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		
		//echo "<b>" . $result[0]['_id'] . "</b>";
		// delete
		$this->stat_app->delete_stat_app($result[0]['_id']);
		
		// get 
		$result = $this->stat_app->get_stat_app(array('action_id' => 2, 'app_install_id' => 3, 'date' => 20110509));
		$this->unit->run(count($result), 0, 'count add stat app action_id 2, app_install_id 3, date 20110509', 'count: ' . count($result) );
	}
	
	function end_test(){
		$this->stat_app->drop_collection();
	}
}
/* End of file stat_app_model_test.php */
/* Location: ./application/controllers/test/stat_app_model_test.php */