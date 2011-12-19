<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class stat_page_model_test extends CI_Controller {
	
	var $stat_page;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('stat_page_model','stat_page');
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
	
	function start_test(){
		$this->stat_page->drop_collection();
	}
	
	function create_index_test(){
		$this->stat_page->create_index();
	}
	
	function add_stat_page_test(){
		$page_id = NULL;
		$action_id = NULL;
		$date = NULL;
		$app_id = 1;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_false', 'add stat page fail - invalid args', '');
		
		$page_id = 1;
		$action_id = 1;
		$date = 20110510;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat page success', '');
		
		$page_id = 2;
		$action_id = 1;
		$date = 20110510;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat page success', '');
		
		$page_id = 3;
		$action_id = 1;
		$date = 2011006;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat page success', '');
		
		$page_id = 3;
		$action_id = 2;
		$date = 20110507;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat page success', '');
		
		$page_id = 3;
		$action_id = 2;
		$date = 20110510;
		$result = $this->stat_page->add_stat_page($app_id, $page_id, $action_id, $date);
		$this->unit->run($result, 'is_true', 'add stat page success', '');
	}

	function increment_stat_page_test(){
		for($i = 0; $i < 5; $i++){
			$page_id = 3;
			$action_id = 2;
			$date = 20110507;
			$app_id = 1;
			$result = $this->stat_page->increment_stat_page($app_id, $page_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat page success', '');
			
			$page_id = 3;
			$action_id = 2;
			$date = 20110510;
			$result = $this->stat_page->increment_stat_page($app_id, $page_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat page success', '');
			
			$page_id = 3;
			$action_id = 2;
			$date = 20110509;
			$result = $this->stat_page->increment_stat_page($app_id, $page_id, $action_id, $date);
			$this->unit->run($result, 'is_true', 'add stat page success', '');
		}
	}

	function get_stat_page_test(){
		// page_id
		$result = $this->stat_page->get_stat_page(array('page_id' => 2));
		$this->unit->run(count($result), 1, 'count add stat page page_id 1', 'count: ' . count($result) );
		
		$result = $this->stat_page->get_stat_page(array('page_id' => 3));
		$this->unit->run(count($result), 4, 'count add stat page page_id 3', 'count: ' . count($result) );
		
		$count = array(1, 6, 5, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat page page_id 3', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		
		// date
		$result = $this->stat_page->get_stat_page(array('date' => 20110509));
		$this->unit->run(count($result), 1, 'count add stat page date 20110506', 'count: ' . count($result) );
		
		$result = $this->stat_page->get_stat_page(array('date' => 20110510));
		$this->unit->run(count($result), 3, 'count add stat page date 20110506', 'count: ' . count($result) );
		
		$count = array(1, 1, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat stat page date 20110506', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		// action_id
		$result = $this->stat_page->get_stat_page(array('action_id' => 5));
		$this->unit->run(count($result), 0, 'count add stat page action_id 5', 'count: ' . count($result) );
		
		$result = $this->stat_page->get_stat_page(array('action_id' => 2));
		$this->unit->run(count($result), 3, 'count add stat page action_id 2', 'count: ' . count($result) );
		
		$count = array(6, 5, 6);
		$match = TRUE;
		for($i = 0; $i < count($result); $i++){
			$match = $match && $result[$i]['count'] == $count[$i];
		}
		$this->unit->run($match, 'is_true', 'count stat stat page action_id 2', '<pre>' . print_r($result, TRUE) . '</pre>');
	}

	function delete_stat_page_test(){
		// get 
		$result = $this->stat_page->get_stat_page(array('action_id' => 2, 'page_id' => 3, 'date' => 20110509));
		$this->unit->run(count($result), 1, 'count add stat page action_id 2, page_id 3, date 20110509', 'count: ' . count($result) );
		$match = $result[0]['date'] == 20110509;
		$this->unit->run($match, 'is_true', 'count stat stat page action_id 2, page_id 3, date 20110509', '<pre>' . print_r($result, TRUE) . '</pre>');
		
		
		//echo "<b>" . $result[0]['_id'] . "</b>";
		// delete
		$this->stat_page->delete_stat_page($result[0]['_id']);
		
		// get 
		$result = $this->stat_page->get_stat_page(array('action_id' => 2, 'page_id' => 3, 'date' => 20110509));
		$this->unit->run(count($result), 0, 'count add stat page action_id 2, page_id 3, date 20110509', 'count: ' . count($result) );
	}
	
	function end_test(){
		$this->stat_page->drop_collection();
	}
}
/* End of file stat_page_model_test.php */
/* Location: ./application/controllers/test/stat_page_model_test.php */