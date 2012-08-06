<?php

/**
 * Order_model
 */

class Order_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function get_order_by_order_id($order_id = NULL){
		$result = $this->db->get_where('order', array('order_id'=>$order_id))->result_array();
		return issetor($result[0]);
	}

	function get_order_by_txn_id($txn_id = NULL){
		$this->db->like('billing_info', $txn_id);
		$result = $this->db->get('order')->result_array();

		//Check again
		$result[0]['billing_info'] = unserialize($result[0]['billing_info']);
		if( $result[0]['billing_info']['txn_id'] == $txn_id)
		{
			return issetor($result[0]);
		}
		else
		{
			return FALSE;
		}
	}

	function add_order($data = array()){
		if(!$data){
			return FALSE;
		}
		$this -> db -> insert('order', $data);
		return $this->db->insert_id();
	}

	function update_order_by_order_id($order_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('order', $data, array('order_id' => $order_id));
	}

	function remove_order($order_id = NULL){
		$this->db->delete('order', array('order_id' => $order_id));
		return $this->db->affected_rows();
	}

	function get_all_orders($limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$result = $this->db->get_where('order',array())->result_array();
		return issetor($result, NULL);
	}

	function get_orders_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		return $this->db->get_where('order', array('user_id' => $user_id))->result_array();
	}

	/**
	 * Get latest ordered by user and item type, EX. get latest user ordered package, latest user ordered app.
	 * @param $user_id, $item_type_id
	 * @return array
	 * @author Weerapat P.
	 */
	function get_latest_ordered_by_user_id_and_item_type_id($user_id = NULL, $item_type_id = NULL){
		$this->db->join('order','order.order_id=order_items.order_id');
		$this->db->where(array('user_id' => $user_id, 'item_type_id' => $item_type_id));
		$this->db->order_by("order.order_id", "desc");
		$result = $this->db->get('order_items')->row_array();
		$result['billing_info'] = unserialize($result['billing_info']);
		return $result;
	}

	/**
	 * Get latest paypal profile_id by user id
	 * @param $user_id
	 * @return array
	 * @author Weerapat P.
	 */
	function get_latest_paypal_profile_id_by_user_id($user_id = NULL){
		$order_status_id = $this->socialhappen->get_k('order_status', 'Processed');
		$this->db->join('order','order.order_id=order_items.order_id');
		$this->db->where(array('user_id' => $user_id));
		$this->db->where(array('order_status_id' => $order_status_id));
		$this->db->like('billing_info', 'profile_id');
		$this->db->order_by("order.order_id", "desc");
		$result = $this->db->get('order_items')->row_array();
		if(count($result))
		{
			$result['billing_info'] = unserialize($result['billing_info']);
			return issetor($result['billing_info']['profile_id']);
		}
		return false;
	}

	function get_orders_by_month($month = NULL){
	}

	function count_orders(){
		return $this->db->count_all_results('order');
	}

}

/* End of file order_model.php */
/* Location: ./application/models/order_model.php */