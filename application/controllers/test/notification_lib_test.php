<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notification_lib_test extends CI_Controller {
	
	var $achievement_info;
	
	var $added_info = array();
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('notification_model','notification');
		$this->load->library('notification_lib');
	}

	function __destruct(){
		$this->unit->report_with_counter();
	}
	
	function index(){
		$class_methods = get_class_methods($this);
		echo 'Functions : '.(count(get_class_methods($this->notification_lib))-3).' Tests :'.count($class_methods);
		foreach ($class_methods as $method) {
    		if(preg_match("/(_test)$/",$method)){
    			$this->$method();
    		}
		}
	}
	
	function start_test(){
		$this->notification->drop_collection();
	}
	
	function create_index_test(){
		$this->notification_lib->create_index();
	}
	
	function add_invalid_test(){
		$user_id = NULL;
		$message = 'noti1';
		$link = 'http://1';
		$result = $this->notification_lib->add($user_id, $message, $link);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$user_id = '4';
		$message = NULL;
		$link = 'http://1';
		$result = $this->notification_lib->add($user_id, $message, $link);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$user_id = '4';
		$message = 'noti1';
		$link = NULL;
		$result = $this->notification_lib->add($user_id, $message, $link);
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->notification_lib->add();
		$this->unit->run($result, 'is_false', 'add', print_r($result, TRUE));
		
		$result = $this->notification_lib->lists();
		$this->unit->run(count($result), 0, 'list', print_r($result, TRUE));
	}
	
	function add_test(){
		$user_id = '4';
		$message = 'noti1';
		$link = 'http://1';
		$image = 'https://socialhappen.dyndns.org/socialhappen/assets/images/badges/default.png';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 1, 'list', print_r(count($result), TRUE));
		
		$user_id = '4';
		$message = 'noti2';
		$link = 'http://2';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 2, 'list', print_r($result, TRUE));
		
		$user_id = '4';
		$message = 'noti3';
		$link = 'http://3';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 3, 'list', print_r($result, TRUE));
		
		$user_id = '4';
		$message = 'noti4';
		$link = 'http://4';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 4, 'list', print_r($result, TRUE));
		
		$user_id = '4';
		$message = 'noti5';
		$link = 'http://5';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 5, 'list', print_r($result, TRUE));
		
		$user_id = '21';
		$message = 'noti1';
		$link = 'http://1';
		$result = $this->notification_lib->add($user_id, $message, $link, $image);
		$this->unit->run($result, 'is_true', 'add', print_r($result, TRUE));
		$result = $this->notification->lists();
		$this->unit->run(count($result), 6, 'list', print_r($result, TRUE));
	}

	function list_test(){
		$result = $this->notification->lists();
		$this->unit->run(count($result), 6, 'list', print_r($result, TRUE));
		
		$result = $this->notification_lib->lists(4);
		$this->unit->run(count($result), 5, 'list', print_r($result, TRUE));
		
		$result = $this->notification_lib->lists(4, 2, 0);
		$this->unit->run(count($result), 2, 'list', print_r($result, TRUE));
		
		$result = $this->notification_lib->lists(21);
		$this->unit->run(count($result), 1, 'list', print_r($result, TRUE));
	}
	
	function update_test(){
		$notification_list = $this->notification_lib->lists('4', 4, 0);
		$this->unit->run(count($notification_list), 4, 'list', print_r($notification_list, TRUE));
		
		$result = $this->notification_lib->read();
		$this->unit->run($result, 'is_false', 'update', print_r($result, TRUE));
		
		$result = $this->notification_lib->read('4', array());
		$this->unit->run($result, 'is_false', 'update', print_r($result, TRUE));
		
		$result = $this->notification_lib->read('4', array($notification_list[0]['_id'], $notification_list[1]['_id']), '4e8213a36803fa330600000f', '4e8213a36803fa330600000a');
		$this->unit->run($result, 'is_true', 'update', print_r($result, TRUE));
		
		$notification_list = $this->notification_lib->lists(4);

		$this->unit->run($notification_list[0]['read'], 'is_true', 'update', print_r($result, TRUE));
		$this->unit->run($notification_list[0]['user_id'], 4, 'update', print_r($result, TRUE));
		$this->unit->run($notification_list[0]['message'], 'noti5', 'update', print_r($result, TRUE));
		$this->unit->run($notification_list[0]['link'], 'http://5', 'update', print_r($result, TRUE));
		
		$this->unit->run($notification_list[1]['read'], 'is_true', 'update', print_r($result, TRUE));
		
		$this->unit->run($notification_list[2]['read'], 'is_false', 'update', print_r($result, TRUE));
		
		$this->unit->run($notification_list[3]['read'], 'is_false', 'update', print_r($result, TRUE));
		
		$this->unit->run($notification_list[4]['read'], 'is_false', 'update', print_r($result, TRUE));
	}

	function count_unread_test(){
		$result = $this->notification_lib->count_unread('4');
		$this->unit->run($result, 3, 'count', print_r($result, TRUE));
		
		$result = $this->notification_lib->count_unread();
		$this->unit->run($result, 'is_false', 'count', print_r($result, TRUE));
		
		$result = $this->notification_lib->count_unread('21');
		$this->unit->run($result, 1, 'count', print_r($result, TRUE));
		
		$result = $this->notification_lib->count_unread('21000');
		$this->unit->run($result, 0, print_r($result, TRUE));
	}
	
	function end_test(){
		// $this->notification->drop_collection();
	}
	
}

/* End of file notification_lib_test.php */
/* Location: ./application/controllers/test/notification_lib_test.php */