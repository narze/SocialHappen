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
	function join_challenge($user_id = NULL, $challenge_hash = NULL) {
		$this->CI->load->library('challenge_lib');
		if((!$challenge = $this->CI->challenge_lib->get_by_hash($challenge_hash)) || (!$user_mongo_id = $this->create_user($user_id, NULL))){
			return FALSE;
		}
		$challenge_id = get_mongo_id($challenge);

		//Add audit
		$this->CI->load->library('audit_lib');
		$this->CI->audit_lib->audit_add(array(
			'company_id' => $challenge['company_id'],
			'action_id' => $this->CI->socialhappen->get_k('audit_action', 'User Join Challenge'),
			'objecti' => $challenge_hash,
			'user_id' => $user_id,
			'app_id' => 0
		));

		$update_criteria = array(
			'user_id' => $user_id
		);
		if(isset($challenge['repeat']) && (is_int($days = $challenge['repeat'])) && ($days > 0)) {
			$start_date = date('Ymd');
			$end_date = date('Ymd', time() + ($days-1)*60*60*24);
			$update_record = array(
				'$addToSet' => array('daily_challenge.'.$challenge_id => array('start_date' => $start_date, 'end_date' => $end_date))
			);
		} else {
			$update_record = array(
				'$addToSet' => array('challenge' => $challenge_id),
			);
		}
		return $update_result = $this->CI->user_mongo_model->update($update_criteria, $update_record);
	}

	/**
	 * Get a User by criteria
	 */
	function get_user($user_id = NULL) {
		return $this->CI->user_mongo_model->getOne(array('user_id' => $user_id));
	}
}