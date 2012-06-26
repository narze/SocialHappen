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
    
    //Add user coupon
    $this->load->model('coupon_model');
    $coupon = array(
      'reward_item_id' => $this->reward_item_id,
      'user_id' => 1,
      'company_id' => 1,
    );
    $this->coupon_id = $this->coupon_model->add($coupon);
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

  function redeem_with_coupon_test() {
    $coupon_id = $this->coupon_id;
    $user_id = 1;
    $confirm_user_id = 5;
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, TRUE, "\$result", $result);

    //Cannot redeem again
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, FALSE, "\$result", $result);

    //Cannot redeem because user don't have coupon
    $user_id = 2;
    $confirm_user_id = 5;
    $result = $this->reward_lib->redeem_with_coupon($coupon_id, $user_id);
    $this->unit->run($result, FALSE, "\$result", $result);

    //Check user inventory
    // @todo : add into user's inventory
  }
}
/* End of file reward_lib_test.php */
/* Location: ./application/controllers/test/reward_lib_test.php */