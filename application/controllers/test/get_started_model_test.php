<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_started_model_test extends CI_Controller {
	
	var $all_get_started_data = array(
		array('id'=>101, 'type' =>'page', 'link' => '#', 'name' => 'Configure Your Own Sign-Up Form'),
		array('id'=>102, 'type' =>'page', 'link' => '#', 'name' => 'View How Your Members See The Sign-Up Form'),
		array('id'=>103, 'type' =>'app', 'link' => '#', 'name' => 'Go To Application List'),
		array('id'=>104, 'type' =>'app', 'link' => '#', 'name' => 'See Where I Can Manage My Applications'),
		array('id'=>105, 'type' =>'all', 'link' => '#', 'name' => 'Learn How to Manage Your Page and Applications'),
		array('id'=>106, 'type' =>'all', 'link' => '#', 'name' => 'Learn How Your Members See SocialHappen Tab'),
		array('id'=>107, 'type' =>'all', 'link' => '#', 'name' => 'Learn How Your Members Interact With Your Page'),
		array('id'=>108, 'type' =>'all', 'link' => '#', 'name' => 'Learn How to View Members Profiles and Their Activities'),
		array('id'=>109, 'type' =>'all', 'link' => '#', 'name' => 'Learn How to Manage Campaign')
	);

	var $done_list_data = array(
		'id' => 1,
		'type' => 'page',
		'items' => array(101,102)
	);
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('get_started_model','get_started');
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
	
	function clear_data_before_test(){
		$this->get_started->drop_collection();
	}
	
	function create_index_before_test(){
		$this->get_started->create_index();
	}
	
	//Get started
	
	/**
	 * Test add_get_started_info()
	 */
	function add_get_started_info_test(){
		$data = $this->all_get_started_data;
		$result = $this->get_started->add_get_started_info($data);
		$this->unit->run($result, TRUE,'Add get_started with full data');
		
		$this->unit->run($this->get_started->count_all_info(), count($data), 'count all get_started_info');
	}

	function add_get_started_info_fail_test(){
		$data = $this->all_get_started_data;
		unset($data[0]['id']);
		$result = $this->get_started->add_get_started_info($data);
		$this->unit->run($result, FALSE,'Add empty id to get_started info');
	}

	/**
	 * Test add_get_started_stat()
	 */
	function add_get_started_stat_test(){
		$done_list_data = $this->done_list_data;
		$result = $this->get_started->add_get_started_stat($done_list_data);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 1, 'count all get_started_stat');

		$done_list_data['id'] = 3;
		$done_list_data['type'] = 'page';
		$done_list_data['items'] = array(102, 105, 107);
		unset($done_list_data['_id']);
		$result = $this->get_started->add_get_started_stat($done_list_data);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 2, 'count all get_started_stat');
	}

	function add_get_started_stat_fail_test(){
		$done_list_data = $this->done_list_data;
		unset($done_list_data['_id']);
		$done_list_data['items'] = array(108);
		$result = $this->get_started->add_get_started_stat($done_list_data);
		$this->unit->run($result, FALSE,'Add more item(s) to get_started stat');
	}

	function get_list_by_page_id_test(){
		$done_list_data = $this->done_list_data;
		//echo '<h2>ITEMS</h2><pre>'; print_r($this->all_get_started_data); echo '</pre>';
		$items = $this->all_get_started_data;
		$result = $this->get_started->get_list_by_page_id(1);
		//echo '<h2>RESULT</h2><pre>'; print_r($result); echo '</pre>';
		$this->unit->run(count($result), count($done_list_data['items']), 'get list by page_id');
		
		$result = $this->get_started->get_list_by_page_id('1');
		$this->unit->run(count($result), count($done_list_data['items']), 'get list by page_id');
		
		$result = $this->get_started->get_list_by_page_id(3); 
		$this->unit->run(count($result), 3, 'get list by page_id');
		
		$result = $this->get_started->get_list_by_page_id(5);
		$this->unit->run(count($result), 0, 'get list by page_id');
	}

	function get_list_by_page_id_fail_test(){
		$result = $this->get_started->get_list_by_page_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->get_started->get_list_by_page_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->get_started->get_list_by_page_id(NULL);
		$this->unit->run($result, FALSE, 'not found');
	}

	function update_get_started_stat_items_test(){
		$result = $this->get_started->update_get_started_stat_items($id = 1, $type = 'page', $items = array(109));
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 2, 'count all get_started_stat');
	}

}
/* End of file get_started_model_test.php */
/* Location: ./application/controllers/test/get_started_model_test.php */