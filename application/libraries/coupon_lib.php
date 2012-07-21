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
	          '$set' => array(
	          					'hash' => strrev(sha1($id))
	      )));
	      if($result['updatedExisting']) {
	        return $id;
	      }
	    }
	    return FALSE;
	}

	function get_one($criteria) {
		$result = $this->CI->coupon_model->getOne($criteria);
		return $result;
	}

	function get_by_hash($hash) {
		return $this->CI->coupon_model->getOne(array('hash' => $hash));
	}

	function confirm_coupon($coupon_id = NULL, $admin_user_id = NULL){
		//not tested yet
		if($coupon_id && $admin_user_id) {
			$this->CI->load->library('reward_lib');
			$coupon = $this->CI->coupon_model->get_by_id($coupon_id);

			if($coupon){
				if($this->CI->reward_lib->redeem_with_coupon($coupon_id, $coupon['user_id'])){
					//return coupon with confirmed data
					return $this->CI->coupon_model->get_by_id($coupon_id);
				}
			}
		}
		return FALSE;
	}

	function get_coupon_admin_url($data){
		$coupon = NULL;
		if(array_key_exists('coupon_hash', $data)){
			$coupon = $this->CI->coupon_model->get(array('hash' => $data['coupon_hash']));
		}else if(array_key_exists('coupon_id', $data)){

			$coupon = $this->CI->coupon_model->get(array('_id' => new MongoId($data['coupon_id'])));

		}

		if($coupon){
			return base_url().'redirect/coupon/'.$coupon[0]['hash'];

		}else{
			return FALSE;
		}

	}

	function list_user_challenge_coupon($user_id, $challenge_id){
		if((isset($user_id) && $user_id != '') &&
			(isset($challenge_id) && $challenge_id != '')
		){

		if($coupon_list = $this->CI->coupon_model->get_by_user_and_challenge($user_id, $challenge_id))
			return $coupon_list;
		}

		return FALSE;

	}

	function list_user_coupon($user_id = NULL){
		$this->CI->load->library('reward_lib');

		$coupons =  $this->CI->coupon_model->get_by_user($user_id);

		// get reward data for each coupon
		// for ($i = 0; $i < count($coupons); $i++) {
		// 	$reward = $this->CI->reward_lib->get_reward_item($coupons[$i]['reward_item_id']);
		// 	$coupons[$i]['reward'] = $reward;
		// }

		return $coupons;
	}

	function list_challenge_coupon($challenge_id = NULL){
		return $this->CI->coupon_model->get_by_challenge($challenge_id);
	}

	function list_company_coupon($company_id = NULL){
		return $this->CI->coupon_model->get_by_company($company_id);
	}

}
/* End of file coupon_lib.php */
/* Location: ./application/libraries/coupon_lib.php */