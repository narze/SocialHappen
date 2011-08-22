<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('order_model','orders');
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
	
	/**
	 * Tests get_order_by_order_id()
	 * @author Weerapat P.
	 */
	function get_order_by_order_id_test(){
		$result = $this->orders->get_order_by_order_id(1);
		$this->unit->run($result,'is_array', 'get_order_by_order_id()');
		$this->unit->run($result['order_id'],'is_string','order_id');
		$this->unit->run($result['order_date'],'is_string','order_date');
		$this->unit->run($result['order_status'],'is_string','order_status');
		$this->unit->run($result['order_net_price'],'is_string','order_net_price');
		$this->unit->run($result['user_id'],'is_string','user_id');
		$this->unit->run($result['payment_method'],'is_string','payment_method');
		$this->unit->run($result['billing_info'],'is_string','billing_info');

		$this->unit->run(count($result) == 7,'is_true', 'number of column');
	}

	/**
	 * Tests get_all_orders()
	 * @author Weerapat P.
	 */
	function get_all_orders_test(){
		$result = $this->orders->get_all_orders($limit = 10, $offset = 0);
		$this->unit->run($result,'is_array', 'get_all_orders()');
		$this->unit->run($result[0]['order_id'],'is_string','order_id');
		$this->unit->run($result[0]['order_date'],'is_string','order_date');
		$this->unit->run($result[0]['order_status'],'is_string','order_status');
		$this->unit->run($result[0]['order_net_price'],'is_string','order_net_price');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['payment_method'],'is_string','payment_method');
		$this->unit->run($result[0]['billing_info'],'is_string','billing_info');
		
		$this->unit->run(count($result[0]) == 7,'is_true', 'number of column');
		$this->unit->run(count($result) <= $limit,'is_true', 'number of row');
	}
	
	/**
	 * Tests get_orders_by_user_id()
	 * @author Weerapat P.
	 */
	function get_orders_by_user_id_test(){
		$result = $this->orders->get_orders_by_user_id(1, $limit = 10, $offset = 0);
		$this->unit->run($result,'is_array', 'get_orders_by_user_id()');
		$this->unit->run($result[0]['order_id'],'is_string','order_id');
		$this->unit->run($result[0]['order_date'],'is_string','order_date');
		$this->unit->run($result[0]['order_status'],'is_string','order_status');
		$this->unit->run($result[0]['order_net_price'],'is_string','order_net_price');
		$this->unit->run($result[0]['user_id'],'is_string','user_id');
		$this->unit->run($result[0]['payment_method'],'is_string','payment_method');
		$this->unit->run($result[0]['billing_info'],'is_string','billing_info');
		
		$this->unit->run(count($result[0]) == 7,'is_true', 'number of column');
		$this->unit->run(count($result) <= $limit,'is_true', 'number of row');
	}
	
	/**
	 * Test add_user() and remove_user()
	 * @author Weerapat P.
	 */
	function add_order_and_remove_order_test(){
		$order = array(
				'order_id' => NULL,
				'order_date' => NULL,
				'order_status' => 'processed',
				'order_net_price' => 999,
				'user_id' => 1,
				'payment_method' => 'paypal',
				'billing_info' => 'Name: Address: Phone: Email: '
		);
		$order_id = $this->orders->add_order($order);
		$this->unit->run($order_id,'is_int','add_order()');
		
		$removed = $this->orders->remove_order($order_id);
		$this->unit->run($removed == 1,'is_true','remove_order()');
		
		$removed_again = $this->orders->remove_order($order_id);
		$this->unit->run($removed_again == 0,'is_true','remove_order()');
	}
	
	/**
	 * Test update_order_by_order_id()
	 * @author Weerapat P.
	 */
	function update_order_by_order_id_test(){
		$data = array(
			'order_status' => 'processed'
		);
		$result = $this->orders->update_order_by_order_id(1,$data);
		$this->unit->run($result === TRUE,'is_true', 'Updated order_status without error');
		
		$result = $this->orders->get_order_by_order_id(1);
		$this->unit->run($result['order_status'] == 'processed','is_true',"Updated order_status to {processed}");
		
	}
	
	/**
	 * Test count_orders()
	 * @author Weerapat P.
	 */
	function count_orders_test(){
		$result = $this->orders->count_orders();
		$this->unit->run($result,'is_int', 'count_orders()');
	}
	
	
}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */