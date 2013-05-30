<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Library
 * @author Manassarn M.
 */
class User_lib {

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('user_model');
		$this->CI->load->model('user_mongo_model');
	}

	function create_index() {
		$this->CI->user_mongo_model->recreateIndex();
	}

	/**
	 * Create user (if not exist)
	 */
	function create_user($user_id = NULL, $data = array()) {
		if(!$user = $this->CI->user_model->get_user_profile_by_user_id($user_id)) {
			return FALSE;
		}

		if($mongo_user = $this->CI->user_mongo_model->getOne(array('user_id' => $user_id))) {
			return ''.$mongo_user['_id'];
		} else {
			$user_record = array(
				'user_id' => $user_id
			);
			if(isset($data['challenge'])) {
				$user_record['challenge'] = $data['challenge'];
			}
			return $this->CI->user_mongo_model->add($user_record);
		}
	}

	/**
	 * Join a challenge
	 * User will be created if not exist
	 */
	function join_challenge($user_id = NULL, $challenge_hash = NULL, $day_delay = 0) {
		$this->CI->load->library('challenge_lib');
		if((!$challenge = $this->CI->challenge_lib->get_by_hash($challenge_hash)) || (!$user_mongo_id = $this->create_user($user_id, NULL))){
			return FALSE;
		}
		$challenge_id = get_mongo_id($challenge);
		$user_id = (int) $user_id;
		$is_daily_challenge = isset($challenge['repeat']) && ($days = (int) $challenge['repeat']) && ($days > 0);

		// return if user already joined
		$user_mongo = $this->CI->user_mongo_model->get_user($user_id);
		if(!$is_daily_challenge && isset($user_mongo['challenge']) && array_search($challenge_id, $user_mongo['challenge']) !== FALSE) {
			return TRUE;
		} else if ($is_daily_challenge && isset($user_mongo['daily_challenge']) && array_search($challenge_id, $user_mongo['daily_challenge']) !== FALSE) {
			return TRUE;
		}
		$this->CI->load->model('user_model');
		$user = $this->CI->user_model->get_user_profile_by_user_id($user_id);

		//Add audit
		$this->CI->load->library('audit_lib');
		$this->CI->audit_lib->audit_add(array(
			'company_id' => $challenge['company_id'],
			'action_id' => $this->CI->socialhappen->get_k('audit_action', 'User Join Challenge'),
			'objecti' => $challenge_hash,
			'user_id' => $user_id,
			'app_id' => 0,
			'image' => $challenge['detail']['image']
		));

		//Make user's company stat (if not exists)
		$this->CI->load->model('achievement_stat_company_model');
		$this->CI->achievement_stat_company_model->increment($challenge['company_id'], $user_id, array( 'company_score' => 0 ));


		$update_criteria = array(
			'user_id' => $user_id
		);
		if($is_daily_challenge) {
			$start_date = date('Ymd', time() + $day_delay * 60 * 60 * 24);
			$end_date = date('Ymd', time() + ($day_delay + ($days-1)) *60*60*24);
			$update_record = array(
				'$addToSet' => array(
					'daily_challenge.'.$challenge_id => array('start_date' => $start_date, 'end_date' => $end_date))
			);
		} else {
			$update_record = array(
				'$addToSet' => array(
					'challenge' => $challenge_id
				)
			);
		}
		return $update_result = $this->CI->user_mongo_model->update($update_criteria, $update_record);
	}

	function join_challenge_by_challenge_id($user_id = NULL, $challenge_id = NULL, $day_delay = 0) {
		return $this->join_challenge($user_id, strrev(sha1($challenge_id)), $day_delay);
	}

	/**
	 * Get a User by criteria
	 */
	function get_user($user_id = NULL) {
		return $this->CI->user_mongo_model->getOne(array('user_id' => $user_id));
	}

	/**
	 * Increment user connect count (by company)
	 */
	function increment_connect_count_by_company($user_id = NULL, $company_id = NULL) {
		if(!$user_id || !$company_id) { return FALSE; }
		$update = array('$inc' => array('connect_count_by_company.'.$company_id => 1));
		return $this->CI->user_mongo_model->update(array('user_id' => (int) $user_id), $update);
	}

	/**
	 * Increment user challenge done count (by company)
	 */
	function increment_challenge_done_count_by_company($user_id = NULL, $company_id = NULL) {
		if(!$user_id || !$company_id) { return FALSE; }
		$update = array('$inc' => array('challenge_done_count_by_company.'.$company_id => 1));
		return $this->CI->user_mongo_model->update(array('user_id' => (int) $user_id), $update);
	}
}