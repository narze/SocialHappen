<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('name', "Foot massage ");
define('redeem', 'redeem');
define('random', 'random');
define('top_score', 'top_score');
class Reward_item_model_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('reward_item_model', 'reward_item');
		$this->unit->reset_mongodb();
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
		$this->reward_item->drop_collection();
	}
	
	function create_index_test(){
		$this->reward_item->create_index();
	}

	function add_test(){
		$name = name . '1';
		$status = 'draft';
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$this->reward_item_1 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 1, 'count', $count);
		
		$name = name . '2';
		$status = 'published';
		$type = random;
		$random = array(
			'amount' => 3
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'app';
		$criteria_id = '2';
		$input = compact('name', 'status', 'type', 'random', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$this->reward_item_2 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 2, 'count', $count);
		
		$name = name . '3';
		$status = 'cancelled';
		$type = top_score;
		$top_score = array(
			'first_place' => 1,
			'last_place' => 4
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'campaign';
		$criteria_id = '3';
		$input = compact('name', 'status', 'type', 'top_score', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$this->reward_item_3 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function add_fail_test(){
		$name = name . '1';
		$status = 'published';
		$type = random;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //type mismatch with data

		$name = name . '1';
		$status = 'draft';
		$type = top_score;
		$top_score = array(
			'first_place' => 0,
			'last_place' => 4
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'top_score', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //first_place < 1

		$name = name . '1';
		$status = 'draft';
		$type = top_score;
		$top_score = array(
			'first_place' => 3,
			'last_place' => 2
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'top_score', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //first_place < last_place

		$name = name . '1';
		$status = 'draft';
		$type = top_score;
		$top_score = array(
			'first_place' => 3,
			'last_place' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 1000;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'top_score', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //start_timestamp > end_timestamp

		$name = name . '1';
		$status = 'draft';
		$type = top_score;
		$top_score = array(
			'first_place' => 3,
			'last_place' => 5
		);
		$start_timestamp = time() - 2;
		$end_timestamp = time() - 1;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'top_score', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //end_timestamp < time()

		$name = name . '1';
		$status = 'badstatus';
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); // bad status

		$name = name . '1';
		$status = 'draft';
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'badtype';
		$criteria_id = 1;
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); // bad criteria_type

		$name = name . '1';
		$status = 'draft';
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$criteria_type = 'page';
		$criteria_id = NULL;
		$input = compact('name', 'status', 'type', 'redeem', 'start_timestamp', 'end_timestamp','criteria_type','criteria_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); // bad criteria_id

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function get_by_reward_item_id_test(){
		$result = $this->reward_item->get_by_reward_item_id($this->reward_item_1);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
		$this->unit->run($result['redeem']['amount_remain'], $result['redeem']['amount'], "\$result['redeem']['amount_remain']", $result['redeem']['amount_remain']);
		$this->unit->run($result['start_timestamp'], time() + 3600, "\$result['start_timestamp']", $result['start_timestamp']);
		$this->unit->run($result['end_timestamp'], time() + 7200, "\$result['end_timestamp']", $result['end_timestamp']);
		$this->unit->run($result['status'], 'draft', "\$result['status']", $result['status']);
		$this->unit->run($result['criteria_type'], 'page', "\$result['criteria_type']", $result['criteria_type']);
		$this->unit->run($result['criteria_id']===1, TRUE, "\$result['criteria_id']", $result['criteria_id']);
		$this->unit->run(isset($result['reward_id']), FALSE, "\$result['reward_id']", isset($result['reward_id']));

		$result = $this->reward_item->get_by_reward_item_id($this->reward_item_2);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
		$result = $this->reward_item->get_by_reward_item_id($this->reward_item_3);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
	}

	function update_test(){
		$new_name = 'new_name';
		$new_status = 'published';
		$input = array(
			'name' => $new_name,
			'status' => $new_status,
			'type' => random,
			'random' => array( //change from redeem type
				'amount' => 10
			),
			'start_timestamp' => time()-3600,
			'end_timestamp' => time()+3600,
			'criteria_type' => 'campaign',
			'criteria_id' => '2'
		);
		$result = $this->reward_item->update($this->reward_item_1, $input);
		$this->unit->run($result, TRUE, "\$result", $result);

		$result = $this->reward_item->get_by_reward_item_id($this->reward_item_1);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['name'], $new_name, "\$result['name']", $result['name']);
		$this->unit->run($result['status'], $new_status, "\$result['status']", $result['status']);
		$this->unit->run(isset($result['redeem']), FALSE, "isset(\$result['redeem'])", isset($result['redeem']));
		$this->unit->run($result['random']['amount'], 10, "\$result['random']['amount']", $result['random']['amount']);
		$this->unit->run($result['start_timestamp'], time() - 3600, "\$result['start_timestamp']", $result['start_timestamp']);
		$this->unit->run($result['end_timestamp'], time() + 3600, "\$result['end_timestamp']", $result['end_timestamp']);
		$this->unit->run($result['criteria_type'], 'campaign', "\$result['criteria_type']", $result['criteria_type']);
		$this->unit->run($result['criteria_id']===2, TRUE, "\$result['criteria_id']", $result['criteria_id']);


		//FAIL tests
		$input['type'] = redeem; //no redeem options specified
		$result = $this->reward_item->update($this->reward_item_1, $input);
		$this->unit->run($result, FALSE, "\$result", $result);
	}

	function get_test(){
		$criteria = array(
			'criteria_type' => 'campaign',
			'criteria_id' => '2');
		$result = $this->reward_item->get($criteria);
		$this->unit->run(count($result), 1, "\count($result)", count($result));
		$this->unit->run($result[0]['name'], 'new_name', "\$result[0]['name']", $result[0]['name']);
	}

	// function remove_test(){
	// 	$result = $this->reward_item->remove('icannotfindyou'); // record to delete
	// 	$this->unit->run($result, FALSE, "\$result", $result);
	// 	$count = $this->reward_item->count_all();
	// 	$this->unit->run($count, 3, 'count', $count);

	// 	$result = $this->reward_item->remove($this->reward_item_1);
	// 	$this->unit->run($result, TRUE, "\$result", $result);
	// 	$count = $this->reward_item->count_all();
	// 	$this->unit->run($count, 2, 'count', $count);
	// 	$result = $this->reward_item->remove($this->reward_item_2);
	// 	$this->unit->run($result, TRUE, "\$result", $result);
	// 	$count = $this->reward_item->count_all();
	// 	$this->unit->run($count, 1, 'count', $count);
	// 	$result = $this->reward_item->remove($this->reward_item_3);
	// 	$this->unit->run($result, TRUE, "\$result", $result);
	// 	$count = $this->reward_item->count_all();
	// 	$this->unit->run($count, 0, 'count', $count);

	// 	$result = $this->reward_item->remove($this->reward_item_3); //already deleted
	// 	$this->unit->run($result, FALSE, "\$result", $result);
	// 	$count = $this->reward_item->count_all();
	// 	$this->unit->run($count, 0, 'count', $count);
	// }
}
/* End of file reward_item_model_test.php */
/* Location: ./application/controllers/test/reward_item_model_test.php */