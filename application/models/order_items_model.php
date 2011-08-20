<?php

/**
 * Order_items_model
 */

class Order_items_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	function get_order_items_by_order_id($order_id = NULL){
		$results = $this->db->get_where('order_items', array('order_id'=>$order_id))->result_array();
		foreach($results as &$result) {
			$result['item_type'] = $this->socialhappen->get_v('item_type',$result['item_type']);
		}
		unset($result);
		return $results;
	}
	
	function add_order_item($data = array()){
		if(!$data){
			return FALSE;
		}
		return $this -> db -> insert('order_items', $data);
	}
	
	function update_order_item_by_order_id_and_item_id_and_item_type($order_id = NULL, $item_id = NULL, $item_type = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('order_items', $data, array('order_id' => $order_id, 'item_id' => $item_id, 'item_type' => $item_type));
	}
	
	function remove_item_by_order_id_and_item_id_and_item_type($order_id = NULL, $item_id = NULL, $item_type = NULL){
		$this->db->delete('order_items', array('order_id' => $order_id, 'item_id' => $item_id, 'item_type' => $item_type));
		return $this->db->affected_rows() == 1;
	}
	
	function remove_items_by_order_id($order_id = NULL){
		$this->db->delete('order_items', array('order_id' => $order_id));
		return $this->db->affected_rows();
	}

}

/* End of file order_items_model.php */
/* Location: ./application/models/order_items_model.php */