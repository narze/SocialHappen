<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Coupon Library
 *
 */

Class Coupon_lib{

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model('coupon_model');
	}

	function create_coupon($data){
		if($id = $this->CI->coupon_model->add_coupon($data)) {
	      $result = $this->CI->coupon_model->update(array(
	        '_id' => new MongoId($id)
	        ), array(
	          '$set' => array('hash' => strrev(sha1($id))
	      )));
	      if($result['updatedExisting']) {
	        return $id;
	      }
	    } 
	    return FALSE;
	}

	function get_coupon($criteria, $limit = 100){
		$result = $this->CI->coupon_model->get($criteria, $limit);
   		return $result;
	}

	function get_one($criteria) {
		$result = $this->CI->coupon_model->getOne($criteria);
		return $result;
	}

	function get_by_hash($hash) {
		return $this->CI->coupon_model->getOne(array('hash' => $hash));
	}

	function confirm_counpon($coupon_id = NULL, $admin_user_id = NULL){
		if((isset($coupon_id) && $coupon_id != '' && (isset($admin_user_id) && $admin_user_id != '')){
			
			if($this->CI->confirm_coupon($coupon_id, $admin_user_id))
				return true;
		}
	
		return false;
	}

	function user_coupon($user_id){
		if((isset($user_id) && $user_id != '') ){
			
			if($coupon = $this->CI->get_by_user_and_challenge($user_id))
				return coupon;
		}
	
		return false;
		
	}

}
/* End of file coupon_lib.php */
/* Location: ./application/libraries/coupon_lib.php */