<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_started_model_test extends CI_Controller {
	
	var $all_get_started_info = array(
		array('id'=>101, 'type' =>'page', 'group' =>'config_page', 'link' => '{base_url}settings/page_apps/{page_id}', 'name' => 'Configure Your Own Sign-Up Form'),
		array('id'=>102, 'type' =>'page', 'group' =>'config_page', 'link' => '#', 'name' => 'View How Your Members See The Sign-Up Form'),
		array('id'=>103, 'type' =>'all', 'group' =>'install_app', 'link' => '{base_url}home/apps?pid={page_id}', 'name' => 'Go To Application List'),
		array('id'=>104, 'type' =>'all', 'group' =>'install_app', 'link' => '#', 'name' => 'See Where I Can Manage My Applications'),
		array('id'=>105, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to Manage Your Page and Applications'),
		array('id'=>106, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How Your Members See SocialHappen Tab'),
		array('id'=>107, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How Your Members Interact With Your Page'),
		array('id'=>108, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to View Members Profiles and Their Activities'),
		array('id'=>109, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to Manage Campaign')
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
		$all_get_started_info = $this->all_get_started_info;
		$result = TRUE;
		foreach($all_get_started_info as $data) {
			if($this->get_started->add_get_started_info($data) == FALSE) { $result = FALSE; break; }
		}
		$this->unit->run($result, TRUE,'Add get_started with full data');
		
		$this->unit->run($this->get_started->count_all_info(), count($all_get_started_info), 'count all get_started_info');
	}

	function add_get_started_info_fail_test(){
		$data = $this->all_get_started_info;
		unset($data[0]['id']);
		$result = $this->get_started->add_get_started_info($data);
		$this->unit->run($result, FALSE,'Add empty id to get_started info');
	}

	/**
	 * Test add_get_started_stat()
	 */
	function add_get_started_stat_test(){
		$done_list_data = $this->done_list_data;
		$result = $this->get_started->add_get_started_stat($done_list_data['id'], $done_list_data['type'], $done_list_data['items']);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 1, 'count all get_started_stat');

		$done_list_data['id'] = 3;
		$done_list_data['type'] = 'page';
		$done_list_data['items'] = array(102, 105, 107);
		unset($done_list_data['_id']);
		$result = $this->get_started->add_get_started_stat($done_list_data['id'], $done_list_data['type'], $done_list_data['items']);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 2, 'count all get_started_stat');

		$done_list_data['id'] = 4;
		$done_list_data['type'] = 'page';
		$done_list_data['items'] = array(101,102);
		unset($done_list_data['_id']);
		$result = $this->get_started->add_get_started_stat($done_list_data['id'], $done_list_data['type'], $done_list_data['items']);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 3, 'count all get_started_stat');

		$done_list_data['id'] = 4;
		$done_list_data['type'] = 'page';
		$done_list_data['items'] = array(106);
		unset($done_list_data['_id']);
		$result = $this->get_started->add_get_started_stat($done_list_data['id'], $done_list_data['type'], $done_list_data['items']);
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 3, 'count all get_started_stat');
	}

	function add_get_started_stat_fail_test(){
		$done_list_data = $this->done_list_data;
		unset($done_list_data['_id']);
		$done_list_data['id'] = 1;
		$done_list_data['items'] = array(108);
		$result = $this->get_started->add_get_started_stat($done_list_data['id'], $done_list_data['type'], $done_list_data['items']);
		$this->unit->run($result, TRUE,'Add more item(s) to get_started stat');
	}

	function get_all_page_todo_list_test() {
		$result = $this->get_started->get_all_page_todo_list();
		$this->unit->run(count($result), count($this->all_get_started_info), 'get all page todo list');
	}

	function get_all_app_todo_list_test() {
		$result = $this->get_started->get_all_page_todo_list();
		$this->unit->run(count($result), count($this->all_get_started_info), 'get all app todo list');
	}

	function get_todo_list_by_page_id_test(){
		$done_list_data = $this->done_list_data;
		//echo '<h2>ITEMS</h2><pre>'; print_r($this->all_get_started_info); echo '</pre>';
		$items = $this->all_get_started_info;
		$result = $this->get_started->get_todo_list_by_page_id(1);
		//echo '<h2>RESULT</h2><pre>'; print_r($result); echo '</pre>';
		$this->unit->run(count($result), count($this->all_get_started_info), 'get list by page_id');
		
		$result = $this->get_started->get_todo_list_by_page_id('1');
		$this->unit->run(count($result), count($this->all_get_started_info), 'get list by page_id');
		
		$result = $this->get_started->get_todo_list_by_page_id(3); 
		$this->unit->run($result['1']['status'], 1, 'get list by page_id');
	}

	function get_todo_list_by_page_id_fail_test(){
		$result = $this->get_started->get_todo_list_by_page_id(0);
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->get_started->get_todo_list_by_page_id();
		$this->unit->run($result, FALSE, 'not found');
		
		$result = $this->get_started->get_todo_list_by_page_id(NULL);
		$this->unit->run($result, FALSE, 'not found');

		$result = $this->get_started->get_todo_list_by_page_id(5);
		foreach($result as $item) {
			if(!$item['status']) $result = FALSE;
		}
		$this->unit->run($result, FALSE, 'not found');
	}

	function update_get_started_stat_items_test(){
		$result = $this->get_started->update_get_started_stat_items($id = 1, $type = 'page', $items = array(109));
		$this->unit->run($result, TRUE,'Add get_started_stat');
		$this->unit->run($this->get_started->count_all_stat(), 3, 'count all get_started_stat');
	}

	function is_completed_test(){
		$id = 2;
		$this->get_started->add_get_started_stat($id, 'page', array(102,103));
		$result = $this->get_started->is_completed($id, $type = 'page');
		$this->unit->run($result, FALSE,'Get-started not complete');

		$this->get_started->update_get_started_stat_items($id, $type = 'page', $items = array(101,104,105,106,107,108,109));
		$result = $this->get_started->is_completed($id, $type = 'page');
		$this->unit->run($result, TRUE,'Get-started completed');
	}

}
/* End of file get_started_model_test.php */
/* Location: ./application/controllers/test/get_started_model_test.php */