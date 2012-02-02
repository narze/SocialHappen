<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reward Library
 *
 *
 * @author Manassarn M.
 */

class Reward_lib
{
	function __construct(){
		$this->CI =& get_instance();
	}

	function get_expired_redeem_items($page_id = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'end_timestamp'=> array('$lt'=>$now)
	    );
	    $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}

	function get_active_redeem_items($page_id = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'start_timestamp' => array('$lte'=>$now), 
	    	'end_timestamp'=> array('$gte'=>$now)
	    );
	    $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}

	function get_incoming_redeem_items($page_id = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'start_timestamp' => array('$gt'=>$now)
	    );
	    $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}
}
/* End of file reward_lib.php */
/* Location: ./application/libraries/reward_lib.php */