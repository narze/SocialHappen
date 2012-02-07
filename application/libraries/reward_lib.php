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

	function get_expired_redeem_items($page_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'end_timestamp'=> array('$lt'=>$now)
	    );
	    if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}

	function get_active_redeem_items($page_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'start_timestamp' => array('$lte'=>$now), 
	    	'end_timestamp'=> array('$gte'=>$now)
	    );
	    if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}

	function get_incoming_redeem_items($page_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
	    	'criteria_type' => 'page',
	    	'criteria_id' => $page_id,
	    	'type' => 'redeem',
	    	'start_timestamp' => array('$gt'=>$now)
	    );
	    if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		return $this->CI->reward_item->get($criteria, $sort_criteria);
	}

	function redeem_reward($page_id, $reward_item_id, $user_facebook_id){
		$this->CI->load->model('reward_item_model');
    	$reward_item = $this->CI->reward_item_model->get_by_reward_item_id($reward_item_id);
    	$this->CI->load->model('user_model');
    	$user = $this->CI->user_model->get_user_profile_by_user_facebook_id($user_facebook_id);
    	if(!$reward_item){
    		return FALSE;
    	}
    	if($reward_item['redeem']['amount_remain'] == 0){
    		return FALSE;
    	}
    	$this->CI->load->library('app_component_lib');
    	if(!$this->CI->app_component_lib->redeem_page_score($page_id, $user['user_id'], $reward_item['redeem']['point'])){
    		return FALSE;
    	}
    	foreach($reward_item['user_list'] as $rewarded_user){
    		if($rewarded_user['user_id'] == $user['user_id']){
    			$rewarded_user['count'] += 1;
    			$user_data = $rewarded_user;
    			break;
    		}
    	}
    	$input = array(
	    	'type' => 'redeem',
	    	'redeem' => array(
		    	'point' => $reward_item['redeem']['point'],
		    	'amount' => $reward_item['redeem']['amount'],
		    	'amount_remain' => $reward_item['redeem']['amount_remain'] - 1
	    	)
    	);
    	if(isset($user_data)){
    		$input['user'] = $user_data;
    	} else {
    		$input['user'] = array(
	    		'user_id' => (int) $user['user_id'],
	    		'user_facebook_id' => $user_facebook_id,
	    		'user_name' => $user['user_first_name'].' '.$user['user_last_name'],
	    		'user_image' => $user['user_image'],
	    		'count' => 1
	    	);
    	}
    	return $this->CI->reward_item_model->update($reward_item_id, $input);
	}
}
/* End of file reward_lib.php */
/* Location: ./application/libraries/reward_lib.php */