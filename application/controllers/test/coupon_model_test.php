<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon_model_test extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('coupon_model');
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
	 
	function add_coupon_test() {
		$data = array(
			'reward_item_id' => 'testrewardid',
			'user_id' => 1,
			'company_id' => 3,
			'challenge_id' => 123
		);
		$result = $this->coupon_model->add_coupon($data);
		$this->unit->run($result, TRUE, "\$result", $result);
		$this->coupon_id = $result;
	}

	function get_by_id_test() {
		$coupon_id = $this->coupon_id;
		$result = $this->coupon_model->get_by_id($coupon_id);
		$this->unit->run($result['user_id'], 1, "\$result['user_id']", $result['user_id']);
		$this->unit->run($result['confirmed'], FALSE, "\$result['confirmed']", $result['confirmed']);
		$this->unit->run($result['confirmed_timestamp'], NULL, "\$result['confirmed_timestamp']", $result['confirmed_timestamp']);
		$this->unit->run($result['confirmed_by_id'], NULL, "\$result['confirmed_by_id']", $result['confirmed_by_id']);
		$this->unit->run($result['timestamp'], 'is_int', "\$result['timestamp']", $result['timestamp']);
		$this->unit->run($result['reward_item_id'], 'testrewardid', "\$result['reward_item_id']", $result['reward_item_id']);
		$this->unit->run($result['company_id'], 3, "\$result['company_id']", $result['company_id']);
		$this->unit->run($result['challenge_id'], 123, "\$result['challenge_id']", $result['challenge_id']);
	}

	function list_user_coupons_test() {
		$user_id = 1;
		$result = $this->coupon_model->list_user_coupons($user_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);

		$this->unit->run($result[0]['user_id'], 1, "\$result[0]['user_id']", $result[0]['user_id']);
		$this->unit->run($result[0]['confirmed'], FALSE, "\$result[0]['confirmed']", $result[0]['confirmed']);
		$this->unit->run($result[0]['confirmed_timestamp'], NULL, "\$result[0]['confirmed_timestamp']", $result[0]['confirmed_timestamp']);
		$this->unit->run($result[0]['confirmed_by_id'], NULL, "\$result[0]['confirmed_by_id']", $result[0]['confirmed_by_id']);
		$this->unit->run($result[0]['timestamp'], 'is_int', "\$result[0]['timestamp']", $result[0]['timestamp']);
		$this->unit->run($result[0]['reward_item_id'], 'testrewardid', "\$result[0]['reward_item_id']", $result[0]['reward_item_id']);
		$this->unit->run($result[0]['company_id'], 3, "\$result[0]['company_id']", $result[0]['company_id']);
		$this->unit->run($result[0]['challenge_id'], 123, "\$result[0]['challenge_id']", $result[0]['challenge_id']);

	}

	function confirm_coupon_test() {
		$coupon_id = $this->coupon_id;
		$admin_user_id = 2;
		$result = $this->coupon_model->confirm_coupon($coupon_id, $admin_user_id);
		$this->unit->run($result, TRUE, "\$result", $result);
	}

	function get_by_id_test_2() {
		$coupon_id = $this->coupon_id;
		$result = $this->coupon_model->get_by_id($coupon_id);
		$this->unit->run($result['user_id'], 1, "\$result['user_id']", $result['user_id']);
		$this->unit->run($result['confirmed'], TRUE, "\$result['confirmed']", $result['confirmed']);
		$this->unit->run($result['confirmed_timestamp'], 'is_int', "\$result['confirmed_timestamp']", $result['confirmed_timestamp']);
		$this->unit->run($result['confirmed_by_id'], 2, "\$result['confirmed_by_id']", $result['confirmed_by_id']);
		$this->unit->run($result['timestamp'], 'is_int', "\$result['timestamp']", $result['timestamp']);
		$this->unit->run($result['reward_item_id'], 'testrewardid', "\$result['reward_item_id']", $result['reward_item_id']);
		$this->unit->run($result['company_id'], 3, "\$result['company_id']", $result['company_id']);
		$this->unit->run($result['challenge_id'], 123, "\$result['challenge_id']", $result['challenge_id']);
	}

	function get_by_user_and_challenge_test() {
		$user_id = 1;
		$challenge_id = 123;
		$result = $this->coupon_model->get_by_user_and_challenge($user_id, $challenge_id);
		$this->unit->run($result, 'is_array', "\$result", $result);
		$this->unit->run($result[0], 'is_array', "\$result[0]", $result[0]);
		$this->unit->run($result[0]['_id'], new MongoId($this->coupon_id), "\$result[0]['_id']", $result[0]['_id']);
	}

}
/* End of file coupon_model_test.php */
/* Location: ./application/controllers/test/coupon_model_test.php */