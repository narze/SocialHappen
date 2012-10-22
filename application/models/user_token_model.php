<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_token_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = sh_mongodb_load( array(
			'collection' => 'user_token'
		));
		$this->int_values = array('user_id');
	}

	//Basic functions (reindex & CRUD)
	function recreateIndex() {
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array('user_id' => 1))
			&& $this->collection->ensureIndex(array('device_token' => 1))
			&& $this->collection->ensureIndex(array('device' => 1, 'device_token' => 1), array('unique' => 1));
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

	function get($query, $sort = array()){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->find($query);
		if($sort) {
			$result = $result->sort($sort);
		}
		return cursor2array($result);
	}

	function getOne($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}

	function update($query, $data, $options = array()) {
		$query = array_cast_int($query, $this->int_values);
	  try {
	    $update_result = $this->collection->update($query, $data, array_merge(array('safe' => TRUE), $options));
	    return isset($update_result['n']) && ($update_result['n'] > 0);
	  } catch(MongoCursorException $e){
	    log_message('error', 'Mongodb error : '. $e);
	    return FALSE;
	  }
	}

  function upsert($query, $data) {
  	$query = array_cast_int($query, $this->int_values);
    try {
      $update_result = $this->collection->update($query, $data, array('safe' => TRUE, 'upsert' => TRUE));
      return isset($update_result['n']) && ($update_result['n'] > 0);
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
	 * Add user's token
	 * @param array $data
	 * @return $data with login_token
	 */
	function add_user_token($data) {
		if(!all_not_null($data, array('user_id', 'device', 'device_token'))) {
			return FALSE;
		}

		//create
		/**
	   * user_id
	   * created : timestamp
	   * device : 'ios' / 'android
	   * device_token :
	   * login_token :
	   * last_active : timestamp
	   * last_message_sent : timestamp
	   * message_queue : []
		 */
		$data['user_id'] = (int) $data['user_id'];

		$data['login_token'] = md5(uniqid(mt_rand(), true)); //32 chars

		$data['last_active'] = $data['created'] = time();

		$data['last_message_sent'] = 0;

		$data['message_queue'] = array();

		$query = array(
			'device' => $data['device'],
			'device_token' => $data['device_token']
		);

		if($this->upsert($query, $data)) {
			return $data;
		}

		return FALSE;
	}

	function remove_user_token($criteria) {
		return $this->delete($criteria);
	}

	function add_push_message($criteria, $message) {
		$data = array(
			'$push' => array(
				'message_queue' => $message
			)
		);
		$options = array(
			'multiple' => 1
		);
		return $this->update($criteria, $data, $options);
	}

	function update_last_active($criteria) {
		$data = array(
			'$set' => array(
				'last_active' => time()
			)
		);
		return $this->update($criteria, $data);
	}

	/**
	 * Pull the latest active user message (if message_queue is not empty)
	 * and update last_message_sent
	 * Use this function to push message notification
	 */
	function pull_active_user_message() {
		$get_criteria = array(
			'message_queue' => array( '$ne' => array() )
		);
		$sort = array(
			'last_active' => -1
		);

		if(!$users = $this->get($get_criteria, $sort)) { return NULL; }

		$user = $users[0];
		$id = $user['_id'];
		$timestamp = time();

		$update_criteria = array(
			'$pop' => array(
				'message_queue' => -1
			),
			'$set' => array( 'last_message_sent' => $timestamp )
		);

		if(!$this->update(array('_id' => $id), $update_criteria, array('safe' => TRUE))) {
			return FALSE;
		}

		//Mock message & user and return
		$message = array_shift($user['message_queue']);
		$user['last_message_sent'] = $timestamp;

		$result = compact('user', 'message');
		return $result;
	}

	function list_all_user_with_message() {
		$criteria = array(
			'message_queue' => array( '$ne' => array() )
		);
		return $this->get($criteria);
	}
}