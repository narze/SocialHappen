<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = $this->user = sh_mongodb_load( array(
			'collection' => 'coupon'
		));
		$this->int_values = array('user_id');
	}

	//Basic functions (reindex & CRUD)
	function recreateIndex() {
		return $this->collection->deleteIndexes() 
			&& $this->collection->ensureIndex(array(
				'user_id' => 1,
				'company_id' => 1,
				'reward_item_id' => 1,
				'timestamp' => 1,
				'confirmed_timestamp' => 1
			));
	}
        
	function add($data)	{
		$data = array_cast_int($data, $this->int_values);
		try	{
			$this->collection->insert($data, array('safe' => TRUE));
			return ''.$data['_id'];
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}
	
	function get($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->find($query);
		return cursor2array($result);
	}

	function getOne($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}
		
	function update($query, $data)
	{
		$query = array_cast_int($query, $this->int_values);
		try	{
			return $this->collection->update($query, $data, array('safe' => TRUE));
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}

	function delete($query){
		$query = array_cast_int($query, $this->int_values);
		return $this->collection->remove($query, array('$atomic' => TRUE));
	}
	//End of basic functions

	/**
	 * List user's coupons
	 */
	function list_user_coupons($user_id)  {
		$query = array('user_id' => $user_id);
		return $this->get($query);
	}

	/**
	 * Confirm coupon
	 */
	function confirm_coupon($coupon_id, $admin_user_id) {
		$criteria = array('_id' => new MongoId($coupon_id));
		$update = array(
			'$set' => array(
				'confirmed' => TRUE,
				'confirmed_timestamp' => time(),
				'confirmed_by_id' => $admin_user_id
			)
		);
		return $this->update($criteria, $update);
	}

	/**
	 * Add new coupon
	 */
	function add_coupon($data = NULL) {
		if(!$data) { return FALSE; }
		if(!isset($data['reward_item_id']) || !isset($data['user_id']) || !isset($data['company_id'])) { return FALSE; }

		$data['timestamp'] = time();
		$data['confirmed'] = FALSE;
		$data['confirmed_timestamp'] = NULL;
		$data['confirmed_by_id'] = NULL;

		if(!isset($data['challenge_id'])) {
			$data['challenge_id'] = NULL;
		}
		return $this->add($data);
	}

	/**
	 * Get coupon by coupon id
	 */
	function get_by_id($coupon_id = NULL) {
		if(!$coupon_id)	{ return FALSE; }
		return $this->getOne(array('_id' => new MongoId($coupon_id)));
	}

	/**
	 * Get coupon(s) by user_id and challenge_id
	 */
	function get_by_user_and_challenge($user_id = NULL, $challenge_id = NULL) {
		if(!$user_id || !$challenge_id) { return FALSE; }
		$query = array('user_id' => $user_id, 'challenge_id' => $challenge_id);
		return $this->get($query);
	}
}