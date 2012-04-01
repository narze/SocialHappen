<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Action Data Class
 * @author Manassarn M.
 */
class Action_user_data_lib {

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('action_user_data_model');
	}

	function add_action_user_data($company_id = NULL, $action_id = NULL, $action_data_id = NULL, 
								$challenge_id = NULL, $user_id = NULL, $user_data = NULL) {

		if(!$company_id || !$action_id || !$action_data_id || !$challenge_id || !$user_id || !$user_data) {
			return FALSE; 
		}

		$add_record = array(
			'company_id' => $company_id,
			'action_id' => $action_id,
			'action_data_id' => $action_data_id,
			'challenge_id' => $challenge_id,
			'user_id' => $user_id,
			'user_data' => $user_data,
		);
		if($action_user_data_id = $this->CI->action_user_data_model->add($add_record)) {
			return $action_user_data_id;
		} else {
			return FALSE;
		}

	}

	function get_action_user_data($action_user_data_id) {
		return $this->CI->action_user_data_model->getOne(array('_id' => new MongoId($action_user_data_id)));
	}

	function get_action_user_data_by_company($company_id) {
		return $this->CI->action_user_data_model->get(array('company_id' => $company_id));
	}

	function get_action_user_data_by_action($action_id) {
		return $this->CI->action_user_data_model->get(array('action_id' => $action_id));
	}

	function get_action_user_data_by_action_data($action_data_id) {
		return $this->CI->action_user_data_model->get(array('action_data_id' => $action_data_id));
	}
	
	function get_action_user_data_by_challenge($challenge_id) {
		return $this->CI->action_user_data_model->get(array('challenge_id' => $challenge_id));
	}
	
}