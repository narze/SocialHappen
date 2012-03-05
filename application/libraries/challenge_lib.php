<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Challenge Class
 * @author Manassarn M.
 */
class Challenge_lib {

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('challenge_model');
	}

	function add($data) {
		$result = $this->CI->challenge_model->add($data);
		return $result;
	}
	
	function get($criteria) {
		$criteria = array_cast_int($criteria);
		$result = $this->CI->challenge_model->get($criteria);
		return $result;
	}

	function get_one($criteria) {
		$criteria = array_cast_int($criteria);
		$result = $this->CI->challenge_model->getOne($criteria);
		return $result;
	}

	function update($criteria, $data) {
		$criteria = array_cast_int($criteria);
		if(!$challenge = $this->get_one($criteria)) {
			return FALSE;
		}
		if(isset($data['$set']['end']) && $data['$set']['end'] < $challenge['start']) {
			return FALSE;
		}
		$data = array_cast_int($data);
		return $this->CI->challenge_model->update($criteria, $data);
	}

	function remove($criteria) {
		$criteria = array_cast_int($criteria);
		return $this->CI->challenge_model->delete($criteria);
	}

}