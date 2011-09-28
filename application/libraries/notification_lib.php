<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MongoDB Audit Stat Limit Library
 *
 * A library to record audit stat to the NoSQL database MongoDB. 
 *
 * @author Metwara Narksook
 */

class Notification_lib
{
	
	private $CI;
	
	/**
	 *	--------------------------------------------------------------------------
	 *	CONSTRUCTOR
	 *	--------------------------------------------------------------------------
	 *
	 *	Automatically check if the Mongo PECL extension has been installed/enabled.
	 * 
	 * @author Metwara Narksook
	 */
	
	function __construct(){
		if(!class_exists('Mongo')){
			show_error("The MongoDB PECL extension has not been installed or enabled",
			 500);
		}
		$this->CI =& get_instance();
		$this->CI->load->model('notification_model', 'notification');
	}
	
	/**
	 * create index for all collection in database
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		$this->CI->notification->create_index();
	}
	
	/**
	 * add new notification
	 * @param user_id
	 * @param message string
	 * @param link string
	 * 
	 * @return result boolean
	 * @author Metwara Narksook
	 */
	function add($user_id = NULL, $message = NULL, $link = NULL){
		if(empty($user_id) || empty($message) || empty($link)){
			return FALSE;
		}
		return $this->CI->notification->add((int)$user_id, $message, $link);
	}
	
	/**
	 * update notification
	 * @param notification_id_list array of notification_id
	 * 
	 * @return result boolean
	 * @author Metwara Narksook
	 */
	function read($user_id = NULL, $notification_id_list = array()){
		$check_args = isset($user_id) && isset($notification_id_list)
		 && count($notification_id_list) > 0;
		if(!$check_args){
			return FALSE;
		}
		
		$result = $this->CI->notification->lists(array(
			'user_id' => (int)$user_id,
			'_id' => array('$in' => $notification_id_list)));
		
		$criteria = array();
		foreach($result as $notification){
			$criteria[] = new MongoId($notification['_id']);
		}
		
		return $this->CI->notification->update($criteria, array('read' => TRUE));
	}
	
	/**
	 * list notification
	 * @param user_id int
	 * @param limit int
	 * @param offset int
	 * 
	 * @return result array
	 * @author Metwara Narksook
	 */
	function lists($user_id = NULL, $limit = NULL, $offset = 0){
		if(empty($user_id)){
			return NULL;
		}
		$criteria = array('user_id' => (int)$user_id);
		return $this->CI->notification->lists($criteria, $limit, $offset);
	}
	
	function count_unread($user_id = NULL){
		if(empty($user_id)){
			return FALSE;
		}
		$criteria = array('user_id' => (int)$user_id,
											'read' => FALSE);
		return $this->CI->notification->count($criteria);
	}
}

/* End of file notification_lib.php */
/* Location: ./application/libraries/notification_lib.php */