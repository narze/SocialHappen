<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reward Library
 *
 *
 * @author Manassarn M.
 * @author Weerapat P.
 */

class Reward_lib
{
	function __construct(){
		$this->CI =& get_instance();
	}

	function get_reward_currency($page_id){
		$this->CI->load->model('app_component_page_model', 'app_component_page');
		$app_component_page = $this->CI->app_component_page->get_by_page_id($page_id);
		return issetor($app_component_page['reward']['item_currency'], NULL);
	}

	function get_reward_item($reward_item_id){
		$this->CI->load->model('reward_item_model','reward_item');
		$reward_item = $this->CI->reward_item->get_by_reward_item_id($reward_item_id);
		// return $this->_add_reward_status($reward_item);
		return $reward_item;
	}

	function get_reward_items($company_id, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$criteria = array(
			'criteria_type' => 'company',
			'criteria_id' => $company_id
		);
		if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		$reward_items = $this->CI->reward_item->get($criteria, $sort_criteria);
		foreach ($reward_items as &$reward_item) $reward_item = $this->_add_reward_status($reward_item);
		return $reward_items;
	}

	function get_published_redeem_items($company_id, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$criteria = array(
			'criteria_type' => 'company',
			'criteria_id' => $company_id,
			'type' => 'redeem',
			'status' => 'published'
		);
		if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		$redeem_items = $this->CI->reward_item->get($criteria, $sort_criteria);
		foreach ($redeem_items as &$redeem_item) $redeem_item = $this->_add_reward_status($redeem_item);
		return $redeem_items;
	}

	function get_expired_redeem_items($company_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
			'criteria_type' => 'company',
			'criteria_id' => $company_id,
			'type' => 'redeem',
			'end_timestamp'=> array('$lt'=>$now)
		);
		if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		$redeem_items = $this->CI->reward_item->get($criteria, $sort_criteria);
		foreach ($redeem_items as &$redeem_item) $redeem_item = $this->_add_reward_status($redeem_item);
		return $redeem_items;
	}

	function get_active_redeem_items($company_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
			'criteria_type' => 'company',
			'criteria_id' => $company_id,
			'type' => 'redeem',
			'start_timestamp' => array('$lte'=>$now),
			'end_timestamp'=> array('$gte'=>$now)
		);
		if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		$redeem_items = $this->CI->reward_item->get($criteria, $sort_criteria);
		foreach ($redeem_items as &$redeem_item) $redeem_item = $this->_add_reward_status($redeem_item);
		return $redeem_items;
	}

	function get_incoming_redeem_items($company_id = NULL, $sort_criteria = NULL){
		$this->CI->load->model('reward_item_model','reward_item');
		$now = time();
		$criteria = array(
			'criteria_type' => 'company',
			'criteria_id' => $company_id,
			'type' => 'redeem',
			'start_timestamp' => array('$gt'=>$now)
		);
		if(!$sort_criteria) $sort_criteria = array('start_timestamp' => -1);
		$redeem_items = $this->CI->reward_item->get($criteria, $sort_criteria);
		foreach ($redeem_items as &$redeem_item) $redeem_item = $this->_add_reward_status($redeem_item);
		return $redeem_items;
	}

	/**
	 * DEPRECATED : Use coupon to redeem instead
	 */
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
		foreach($reward_item['user_list'] as $rewarded_user){
			if($rewarded_user['user_id'] == $user['user_id']){
				if($reward_item['redeem']['once']){ //Cannot redeem again if reward is once redeemable
					return FALSE;
				}
				$rewarded_user['count'] += 1;
				$user_data = $rewarded_user;
				break;
			}
		}
		$this->CI->load->model('page_model');
		$page = $this->CI->page_model->get_page_profile_by_page_id($page_id);
		$this->CI->load->library('app_component_lib');
		if(!$this
			->CI
			->app_component_lib
			->redeem_page_score(
				$page['company_id'],
				$page_id,
				$user['user_id'],
				$reward_item['redeem']['point']
			)){
			return FALSE;
		}

		$input = array(
			'type' => 'redeem',
			'redeem' => array(
				'point' => $reward_item['redeem']['point'],
				'amount' => $reward_item['redeem']['amount'],
				'amount_remain' => $reward_item['redeem']['amount_remain'] - 1,
				'once' => $reward_item['redeem']['once']
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
		$this->CI->load->library('audit_lib');
		$audit_add_result = $this->CI->audit_lib->audit_add(array(
			'app_id' => 0,
			'action_id' =>
				$this->CI->socialhappen->get_k('audit_action', 'User Redeem Reward'),
			'object' => $reward_item['name'],
			'objecti' => $reward_item_id,
			'user_id' => $user['user_id'],
			'page_id' => $page_id
		));
		return $this->CI->reward_item_model->update($reward_item_id, $input);
	}

	function _add_reward_status($reward_item) {
		$now = time();
		$start_time = $reward_item['start_timestamp'];
		$end_time = $reward_item['end_timestamp'];
		if($now < $start_time){
			$reward_status = 'soon';
		} else if ($now > $end_time){
			$reward_status = 'expired';
		} else if (isset($reward_item['redeem'])
			&& $reward_item['redeem']['amount_remain'] == 0){
			$reward_status = 'no_more';
		} else {
			$reward_status = 'active';
		}
		$reward_item['reward_status'] = $reward_status;

		return $reward_item;
	}

	function redeem_with_coupon($coupon_id = NULL, $user_id = NULL, $confirm_user_id = NULl) {
		if(!$coupon_id || !$user_id) { return FALSE; }

		// Check coupon
		$this->CI->load->model('coupon_model');
		$coupon = $this->CI->coupon_model->get_by_id($coupon_id);

		// Check if not confirmed or user_id did not match
		if((isset($coupon['confirmed']) && $coupon['confirmed']) || ($coupon['user_id'] !== $user_id)) {
			return FALSE;
		}

		// Confirm the coupon
		if(!$confirm_coupon_result = $this->CI->coupon_model->confirm_coupon($coupon_id, $confirm_user_id)) {
			return FALSE;
		}

		// Add into user's inventory
		$this->CI->load->model('user_mongo_model');
		$reward_item_id = $coupon['reward_item_id'];
		if(!$add_user_reward_item = $this->CI->user_mongo_model->add_reward_item($user_id, $reward_item_id)) {
			return FALSE;
		}

		$this->CI->load->model('reward_item_model');
		$reward_item = $this->CI->reward_item_model->get_one(array('_id' => new MongoId($reward_item_id)));

		$this->CI->load->library('audit_lib');
		//Add action
		if(!$audit_add_result = $this->CI->audit_lib->audit_add(array(
			'app_id' => 0,
			'action_id' =>
				$this->CI->socialhappen->get_k('audit_action', 'User Redeem Reward'),
			'object' => $reward_item['name'],
			'objecti' => $reward_item_id,
			'user_id' => $user_id,
			'company_id' => $reward_item['company_id']
		))) {
			return FALSE;
		}

		//Add notification
		$this->CI->load->library('notification_lib');
		$message = "You have used the coupon for <strong>{$reward_item['name']}</strong>.";
		$link = '#';
		$image = $reward_item['image'];
		if(!$this->CI->notification_lib->add($user_id, $message, $link, $image)) {
			return FALSE;
		}

		return TRUE;
	}

	function purchase_coupon($user_id = NULL, $reward_item_id, $company_id) {
		if(!$user_id || !$reward_item_id || !$company_id) { return FALSE; }

		$this->CI->load->model('reward_item_model');

		if(!$reward_item =
			$this->CI->reward_item_model->get_by_reward_item_id($reward_item_id)){
			return array('success' => FALSE, 'data' => 'Reward not found');
		}

		//Check if no reward remains
		if(!isset($reward_item['redeem']['amount_remain'])
			|| ($reward_item['redeem']['amount_remain'] == 0)) {
			return array('success' => FALSE, 'data' => 'Reward used up');
		}

		//Check if redeemable once and redeemed already
		if($reward_item['redeem']['once']) {
			$this->CI->load->model('user_mongo_model');
			if(isset($user['reward_items']) && in_array($reward_item_id, $user['reward_items'])) {
				return array('success' => FALSE, 'data' => 'Already redeemed');
			}
		}

		//Check if company point is sufficient
		$this->CI->load->library('achievement_lib');
		$company_stat = $this->CI->achievement_lib->get_company_stat($company_id, $user_id);
		if(!isset($company_stat['company_score'])) {
			//No stat = 0 points = cannot purchase
			return array('success' => FALSE, 'data' => 'Insufficient score');
		}

		$company_score = $company_stat['company_score'];

		if($company_score < $reward_item['redeem']['point']) {
			//Insufficient points
			return array('success' => FALSE, 'data' => 'Insufficient score');
		}

		//Update the company point of the user
		$reward_points = - abs($reward_item['redeem']['point']);
		if(!$increment_result = $this->CI->achievement_lib->increment_company_score($company_id, $user_id, $reward_points)) {
			return array('success' => FALSE, 'data' => 'Score update failed');
		}

		//Give user coupon
		$this->CI->load->library('coupon_lib');
		if(!$coupon_id = $this->CI->coupon_lib->create_coupon(array(
			'reward_item_id' => get_mongo_id($reward_item),
			'user_id' => $user_id,
			'company_id' => $company_id,
		))) {
			return array('success' => FALSE, 'data' => 'Cannot create coupon');
		}

		//Decrement amount_remain
		$reward_update = array(
			'type' => 'redeem',
			'redeem' => array(
				'point' => $reward_item['redeem']['point'],
				'amount' => $reward_item['redeem']['amount'],
				'amount_remain' => $reward_item['redeem']['amount_remain'] - 1,
				'once' => $reward_item['redeem']['once']
			)
		);
		if(!$this->CI->reward_item_model->update($reward_item_id, $reward_update)) {
			return array('success' => FALSE, 'data' => 'Failed decrement reward');
		}

		//Add action
		$this->CI->load->library('audit_lib');
		if(!$audit_add_result = $this->CI->audit_lib->audit_add(array(
			'app_id' => 0,
			'action_id' =>
				$this->CI->socialhappen->get_k('audit_action', 'User Receive Coupon'),
			'object' => $reward_item['name'],
			'objecti' => $reward_item_id,
			'user_id' => $user_id,
			'company_id' => $company_id
		))) {
			return array('success' => FALSE, 'data' => 'Add action failed');
		}

		return array('success' => TRUE, 'data' => array(
			'coupon_id' => $coupon_id,
			'points_remain' => ($company_score - $reward_item['redeem']['point'])
		));
	}
}
/* End of file reward_lib.php */
/* Location: ./application/libraries/reward_lib.php */