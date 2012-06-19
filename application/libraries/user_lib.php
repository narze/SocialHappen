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
		if(isset($challenge['repeat']) && ($challenge['repeat'] === 'daily')) {
			$update_record = array(
				'$addToSet' => array('daily_challenge.'.date('Ymd') => get_mongo_id($challenge)),
			);
		} else {
			$update_record = array(
				'$addToSet' => array('challenge' => get_mongo_id($challenge)),
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