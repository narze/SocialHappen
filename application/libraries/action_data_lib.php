<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Action Data Class
 * @author Manassarn M.
 */
class Action_data_lib {

	private $platform_actions = array(
		'qr' => 201,
		'feedback' => 202
	);

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('action_data_model');
	}

	function get_platform_action($action_name = NULL) {
		if($action_name) {
			return issetor($this->platform_actions[$action_name]);
		} else {
			return $this->platform_actions;
		}
	}

	function add_action_data($action_id = NULL, $action_data = NULL) {
		if(!$action_id || !$action_data) {
			return FALSE;
		}

		if(!in_array($action_id, $this->platform_actions)) {
			return FALSE;
		}

		//TODO : action data validation (different in each action id)

		$add_record = array(
			'action_id' => $action_id,
			'hash' => NULL,
			'data' => $action_data,
		);
		if($action_data_id = $this->CI->action_data_model->add($add_record)) {
			if($update_result = $this->CI->action_data_model->update(array(
				'_id' => new MongoId($action_data_id)),
				array(
					'$set' => array(
						'hash' => strrev(sha1($action_data_id))
					)
				)
			)) {
				return $action_data_id;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}

	}

	function get_action_data($action_data_id) {
		return $this->CI->action_data_model->getOne(array('_id' => new MongoId($action_data_id)));
	}

	function get_action_url($action_data_id) {
		if($action_data = $this->get_action_data($action_data_id)) {
			if($controller_name = array_search($action_data['action_id'], $this->platform_actions)) {
				return base_url().'actions/'.$controller_name.'?code='.strrev(sha1($action_data_id));
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function get_action_data_from_code() {
		$hash = $this->CI->input->get('code');
		return $this->CI->action_data_model->getOne(array('hash' => $hash));
	}
  
  
  /**
   * @param qr_done_message html
   * @param todo_message html
   */
	function add_qr_action_data($data_from_form) {
		$action_id = $this->get_platform_action('qr');
		$qr_data = array(
			//Book : redefine your data here
			'done_message' => $data_from_form['done_message'],
			'todo_message' => $data_from_form['todo_message']
		);
		return $this->add_action_data($action_id, $qr_data);
	}
	
  function get_qr_url($_id = NULL){
    return $_id ? base_url() . 'actions/qr/go/' . $_id : NULL;
  }
  
	function add_feedback_action_data($data_from_form) {
		$action_id = $this->get_platform_action('feedback');
		$feedback_data = array(
			//Phnx : redefine your data here
			'feedback_welcome_message' => $data_from_form['feedback_welcome_message'],
			'feedback_thankyou_message' => $data_from_form['feedback_thankyou_message']
		);
		return $this->add_action_data($action_id, $feedback_data);
	}
}