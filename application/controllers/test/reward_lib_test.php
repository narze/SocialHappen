<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('Company_id', 1);
class Reward_lib_test extends CI_Controller {
	
	var $reward_lib;
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
  	$this->load->library('reward_lib');
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

	function setup_before_test(){
  	$add_reward_item = file_get_contents(base_url().'test/reward_item_model_test/add_test_1');
  	$this->unit->run($add_reward_item, TRUE, "\$add_reward_item", $add_reward_item);
  	$add_reward_item = file_get_contents(base_url().'test/reward_item_model_test/add_test_1');
  	$this->unit->run($add_reward_item, TRUE, "\$add_reward_item", $add_reward_item);
  	$add_reward_item = file_get_contents(base_url().'test/reward_item_model_test/add_test_1');
  	$this->unit->run($add_reward_item, TRUE, "\$add_reward_item", $add_reward_item);

  	$this->load->model('reward_item_model','reward_item');
  	//get ids
  	$reward_items = $this->reward_item->get(array());
  	$reward_items_id = array($reward_items[0]['_id'],$reward_items[1]['_id'],$reward_items[2]['_id']);
    $this->reward_item_id = $reward_items_id[0];
  	//change rewards to published status
  	$update = array(
  		'status' => 'published'
  	);
  	$now = time();
		$id = $reward_items_id[0];
		$id = $id->{'$id'};
		$result = $this->reward_item->update($id, array('status'=>'published',
			'name' => 'incoming',
			'start_timestamp' => $now + 3600,
			'end_timestamp' => $now + 7200
		));
		$this->unit->run($result, TRUE, "\$result", $result);
		$id = $reward_items_id[1];
		$id = $id->{'$id'};
		$result = $this->reward_item->update($id, array('status'=>'published',
			'name' => 'active',
			'start_timestamp' => $now - 3600,
			'end_timestamp' => $now + 3600
		));
		$this->unit->run($result, TRUE, "\$result", $result);
		$id = $reward_items_id[2];
		$id = $id->{'$id'};
		$result = $this->reward_item->update($id, array('status'=>'published',
			'name' => 'expired',
			'start_timestamp' => $now - 7200,
			'end_timestamp' => $now - 3600
		));
		$this->unit->run($result, TRUE, "\$result", $result);

    //create user 1
    $user_id = 1;
    $this->load->library('user_lib');
    $this->user_lib->create_user(1);

    //Add company 1 score
    $company_id = 1;
    $this->load->model('achievement_stat_company_model');
    $this->achievement_lib->increment_company_score($company_id, $user_id, 500);
	}

	function get_expired_redeem_items_test(){
		$company_id = Company_id;
		$result = $this->reward_lib->get_expired_redeem_items($company_id);
		$this->unit->run(count($result), 1, "count(\$result)", count($result));
		$this->unit->run($result[0]['name'], 'expired', "\$result[0]['name']", $result[0]['name']);
	}

	function get_active_redeem_items_test(){
		$company_id = Company_id;
		$result = $this->reward_lib->get_active_redeem_items($company_id);
		$this->unit->run(count($result), 1, "count(\$result)", count($result));
		$this->unit->run($result[0]['name'], 'active', "\$result[0]['name']", $result[0]['name']);
	}

	function get_incoming_redeem_items_test(){
		$company_id = Company_id;
		$result = $this->reward_lib->get_incoming_redeem_items($company_id);
		$this->unit->run(count($result), 1, "count(\$result)", count($result));
		$this->unit->run($result[0]['name'], 'incoming', "\$result[0]['name']", $result[0]['name']);
	}

	function redeem_reward_test(){
		//now test is in tab_ctrl_test
	}

  function purchase_coupon_test() {
    $user_id = 1;
    $company_id = 1;
    $reward_item_id = $this->reward_item_id;
    $result = $this->reward_lib->purchase_coupon($user_id, $reward_item_id, $company_id);
    $this->unit->run($result, 'is_string', "\$result", $result);
    $this->coupon_id = $result;

    $user_id = 2;
    $result = $this->reward_lib->purchase_coupon($user_id, $reward_item_id, $company_id);
    $this->unit->run($result, FALSE, "\$result", $result);
  }

  function redeem_with_coupon_test() {
    $coupon_id = $this->coupon_id;
    $user_id = 1;
    $confirm_user_id = 5;
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, TRUE, "\$result", $result);

    //Cannot redeem again
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, FALSE, "\$result", $result);

    //Check user 1 inventory
    $this->load->model('user_mongo_model');
    $user = $this->user_mongo_model->get_user($user_id);
    $rewards = $user['reward_items'];
    $this->unit->run(in_array($this->reward_item_id, $rewards), TRUE, "in_array($this->reward_item_id, $rewards)", in_array($this->reward_item_id, $rewards));
    
    //Cannot redeem because user don't have coupon
    $user_id = 2;
    $confirm_user_id = 5;
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, FALSE, "\$result", $result);
  }
}
/* End of file reward_lib_test.php */
/* Location: ./application/controllers/test/reward_lib_test.php */