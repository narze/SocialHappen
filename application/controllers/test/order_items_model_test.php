<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_items_model_test extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('order_items_model','order_items');
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
	
	/**
	 * Tests get_order_items_by_order_id()
	 * @author Weerapat P.
	 */
	function get_order_items_by_order_id_test(){
		$result = $this->order_items->get_order_items_by_order_id(1);
		$this->unit->run($result,'is_array', 'get_order_items_by_order_id()');
		$this->unit->run($result[0]['order_id'],'is_string','order_id');
		$this->unit->run($result[0]['item_id'],'is_string','item_id');
		$this->unit->run($result[0]['item_type_id'],'is_string','item_type_id');
		$this->unit->run($result[0]['item_type'],'is_string','item_type');
		$this->unit->run($result[0]['item_name'],'is_string','item_name');
		$this->unit->run($result[0]['item_description'],'is_string','item_description');
		$this->unit->run($result[0]['item_price'],'is_string','item_price');
		$this->unit->run($result[0]['item_unit'],'is_string','item_unit');
		$this->unit->run($result[0]['item_discount'],'is_string','item_discount');

	}
	
	/**
	 * Test add_order_item() and remove_item_by_order_id_and_item_id_and_item_type()
	 * @author Weerapat P.
	 */
	function add_order_item_and_remove_item_by_order_id_and_item_id_and_item_type_test(){
		$order = array( 
			array(
				'order_id' => 3,
				'item_id' => 2,
				'item_type_id' => 1,
				'item_name' => 'Enterprise package',
				'item_description' => 'For enterprise',
				'item_price' => 999,
				'item_unit' => 1,
				'item_discount' => 0
			),
			array(
				'order_id' => 3,
				'item_id' => 3,
				'item_type_id' => 2,
				'item_name' => 'Enterprise package',
				'item_description' => 'For enterprise',
				'item_price' => 999,
				'item_unit' => 1,
				'item_discount' => 0
			)
		);
		
		$result = $this->order_items->add_order_item($order[0]);
		$this->unit->run($result,'is_true','add_order()');
		
		$this->order_items->add_order_item($order[1]);
		$removed_all = $this->order_items->remove_items_by_order_id(3);
		$this->unit->run($removed_all == count($order),'is_true','remove_items_by_order_id()');
		
		$this->order_items->add_order_item($order[0]);
		$removed = $this->order_items->remove_item_by_order_id_and_item_id_and_item_type(3, 2, 1);
		$this->unit->run($removed,'is_true','remove_item_by_order_id_and_item_id_and_item_type()');
		
		$removed_again = $this->order_items->remove_item_by_order_id_and_item_id_and_item_type(3, 2, 1);
		$this->unit->run($removed_again,'is_false','remove_item_by_order_id_and_item_id_and_item_type() again');
	}
	
	/**
	 * Test update_order_item_by_order_id_and_item_id_and_item_type()
	 * @author Weerapat P.
	 */
	function update_order_item_by_order_id_and_item_id_and_item_type_test(){
		$order_id = 1; 
		$item_id = 2;
		$item_type = 1;
		$data = array(
			'item_unit' => '2'
		);
		$result = $this->order_items->update_order_item_by_order_id_and_item_id_and_item_type($order_id, $item_id, $item_type, $data);
		$this->unit->run($result === TRUE,'is_true', 'Updated item_unit without error');
		
		$result = $this->order_items->get_order_items_by_order_id($order_id);
		$this->unit->run($result[0]['item_unit'] == '2','is_true',"Updated item_unit to {2}");
	}	

}
/* End of file campaign_model_test.php */
/* Location: ./application/controllers/test/campaign_model_test.php */