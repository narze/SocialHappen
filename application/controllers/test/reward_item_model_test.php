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
	
	function _add_reward_test(){
		$this->load->model('reward_model');
		$page_id = 1;
		$start_timestamp = time() + 3600;
		$end_timestamp = time() + 7200;
		$input = compact('page_id','start_timestamp','end_timestamp');
		
		$this->reward_id = $result = $this->reward_model->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_model->count_all();
		$this->unit->run($count, 1, 'count', $count);
	}

	function add_test(){
		$name = name . '1';
		$enable = TRUE;
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'redeem', 'reward_id');
		
		$this->reward_item_1 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 1, 'count', $count);
		
		$name = name . '2';
		$enable = TRUE;
		$type = random;
		$random = array(
			'amount' => 3
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'random', 'reward_id');
		
		$this->reward_item_2 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 2, 'count', $count);
		
		$name = name . '3';
		$enable = FALSE;
		$type = top_score;
		$top_score = array(
			'first_place' => 1,
			'last_place' => 4
		);
		$reward_id = $this->reward_id . 'nonrewarditem';
		$input = compact('name', 'enable', 'type', 'top_score', 'reward_id');
		
		$this->reward_item_3 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function add_fail_test(){
		$name = name . '1';
		$enable = TRUE;
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$reward_id = NULL;
		$input = compact('name', 'enable', 'type', 'redeem', 'reward_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //no reward_id

		$input = compact('name', 'enable', 'type', 'redeem');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //no reward_id

		$name = name . '1';
		$enable = TRUE;
		$type = random;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'redeem', 'reward_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //type mismatch with data

		$name = name . '1';
		$enable = FALSE;
		$type = top_score;
		$top_score = array(
			'first_place' => 0,
			'last_place' => 4
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'top_score', 'reward_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //first_place < 1

		$name = name . '1';
		$enable = FALSE;
		$type = top_score;
		$top_score = array(
			'first_place' => 3,
			'last_place' => 2
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'top_score', 'reward_id');
		
		$result = $this->reward_item->add($input);
		$this->unit->run($result, FALSE, "\$result", $result); //first_place < last_place

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 3, 'count', $count);
	}

	function get_test(){
		$result = $this->reward_item->get($this->reward_item_1);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
		$this->unit->run($result['redeem']['amount_remain'], $result['redeem']['amount'], "\$result['redeem']['amount_remain']", $result['redeem']['amount_remain']);

		$result = $this->reward_item->get($this->reward_item_2);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
		$result = $this->reward_item->get($this->reward_item_3);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['user_list'], 'is_array', "\$result['user_list']", $result['user_list']);
	}

	function get_reward_items_by_reward_id_test(){
		$reward_id = $this->reward_id;
		$result = $this->reward_item->get_reward_items_by_reward_id($reward_id);
		$this->unit->run(count($result), 2, "count($result)", count($result));
	}

	function update_test(){
		$new_name = 'new_name';
		$new_enable = FALSE;
		$this->reward_id = 'new_reward_id';
		$input = array(
			'name' => $new_name,
			'enable' => $new_enable,
			'type' => random,
			'random' => array( //change from redeem type
				'amount' => 10
			),
			'reward_id' => $this->reward_id
		);
		$result = $this->reward_item->update($this->reward_item_1, $input);
		$this->unit->run($result, TRUE, "\$result", $result);

		$result = $this->reward_item->get($this->reward_item_1);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result['name'], $new_name, "\$result['name']", $result['name']);
		$this->unit->run($result['enable'], $new_enable, "\$result['enable']", $result['enable']);
		$this->unit->run(isset($result['redeem']), FALSE, "isset(\$result['redeem'])", isset($result['redeem']));
		$this->unit->run($result['reward_id'], $this->reward_id, "\$result['reward_id']", $result['reward_id']);
		$this->unit->run($result['random']['amount'], 10, "\$result['random']['amount']", $result['random']['amount']);

		//FAIL tests
		$input['type'] = redeem; //no redeem options specified
		$result = $this->reward_item->update($this->reward_item_1, $input);
		$this->unit->run($result, FALSE, "\$result", $result);
	}

	function remove_test(){
		$result = $this->reward_item->remove('icannotfindyou'); // record to delete
		$this->unit->run($result, FALSE, "\$result", $result);
		$count = $this->reward_item->count_all();
		$this->unit->run($count, 3, 'count', $count);

		$result = $this->reward_item->remove($this->reward_item_1);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward_item->count_all();
		$this->unit->run($count, 2, 'count', $count);
		$result = $this->reward_item->remove($this->reward_item_2);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward_item->count_all();
		$this->unit->run($count, 1, 'count', $count);
		$result = $this->reward_item->remove($this->reward_item_3);
		$this->unit->run($result, TRUE, "\$result", $result);
		$count = $this->reward_item->count_all();
		$this->unit->run($count, 0, 'count', $count);

		$result = $this->reward_item->remove($this->reward_item_3); //already deleted
		$this->unit->run($result, FALSE, "\$result", $result);
		$count = $this->reward_item->count_all();
		$this->unit->run($count, 0, 'count', $count);
	}


	function add_again_test(){
		$name = name . '1';
		$enable = TRUE;
		$type = redeem;
		$redeem = array(
			'point' => 20,
			'amount' => 5
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'redeem', 'reward_id');
		
		$this->reward_item_1 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 1, 'count', $count);
		
		$name = name . '2';
		$enable = TRUE;
		$type = random;
		$random = array(
			'amount' => 3
		);
		$reward_id = $this->reward_id;
		$input = compact('name', 'enable', 'type', 'random', 'reward_id');
		
		$this->reward_item_2 = $result = $this->reward_item->add($input);
		$this->unit->run($result, 'is_string', "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 2, 'count', $count);
	}

	function remove_by_reward_id_test(){
		$reward_id = $this->reward_id;
		$result = $this->reward_item->remove_by_reward_id($reward_id);
		$this->unit->run($result, 2, "\$result", $result);

		$count = $this->reward_item->count_all();
		$this->unit->run($count, 0, 'count', $count);
	}
}
/* End of file reward_model_test.php */
/* Location: ./application/controllers/test/reward_model_test.php */