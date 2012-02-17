<?php
/**
 * achievement info model class for achievement info object
 * @author Metwara Narksook
 */
class Notification_model extends CI_Model {

	var $user_id;
	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->notification = sh_mongodb_load( array(
			'collection' => 'notification'
		));
		
		// initialize value
		$this->DEFAULT_LIMIT = 10;
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->notification->ensureIndex(array(
										'user_id' => 1));
	}
	
	/**
	 * add new notification
	 * @param user_id
	 * @param message string
	 * @param link string
	 * @param image string url to image
	 * @param timestamp int
	 * 
	 * @return result boolean
	 * @author Metwara Narksook
	 */
	function add($user_id = NULL, $message = NULL, $link = NULL, $image = NULL, $timestamp = NULL){
		if(empty($user_id) || empty($message) || empty($link)){
			return FALSE;
		}
		
		date_default_timezone_set('UTC');
		$timestamp = isset($timestamp) ? (int)$timestamp : time();
		
		$notification = array('user_id' => (int) $user_id,
													'message' => $message,
													'link' => $link,
													'read' => FALSE,
													'image' => $image,
													'timestamp' => $timestamp);
		return $this->notification->insert($notification);
	}
	
	/**
	 * update notification
	 * @param notification_id_list array of notification_id
	 * @param data array
	 * 
	 * @return result boolean
	 * @author Metwara Narksook
	 */
	function update($notification_id_list = array(), $data = array()){
		$check_args = (isset($notification_id_list) 
			&& count($notification_id_list) > 0)
		 || isset($data);
		
		if(!$check_args){
			return FALSE;
		}
		$criteria = array();
		foreach($notification_id_list as $notification){
			$criteria[] = new MongoId($notification . '');
		}

		return $this->notification->update(array('_id' => array('$in' => $criteria))
		, array('$set' => $data), array('multiple' => TRUE));
	}
	
	/**
	 * list notification
	 * @param criteria array
	 * @param limit int
	 * @param offset int
	 * 
	 * @return result array
	 * @author Metwara Narksook
	 */
	function lists($criteria = array(), $limit = NULL, $offset = 0){
		
		if(empty($limit)){
			$limit = $this->DEFAULT_LIMIT;
		}
		
		$res = $this->notification->find($criteria)->sort(array('_id' => -1))
		->skip($offset)->limit($limit);
		
		$result = array();
		if(isset($res)){
			foreach ($res as $stat) {
				$result[] = $stat;
			}
		}
		
		return $result;
	}
	
	/**
	 * count notification by criteria
	 * @param criteria array
	 * 
	 * @return number
	 * @author Metwara Narksook
	 */
	function count($criteria = array()){
		return $this->notification->count($criteria);
	}
	
	/**
	 * drop entire collection
	 * you will lost all notification data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->notification->drop();
	}
}

/* End of file notification_model.php */
/* Location: ./application/models/notification_model.php */