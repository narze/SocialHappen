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
	 * @param image string url to image
	 * 
	 * @return result boolean
	 * @author Metwara Narksook
	 */
	function add($user_id = NULL, $message = NULL, $link = NULL, $image = NULL){
		if(empty($user_id) || empty($message) || empty($link)){
			return FALSE;
		}
		date_default_timezone_set('Asia/Bangkok');
		$timestamp = time();
		$result = $this->CI->notification->add((int)$user_id, $message, $link,
		 $image, $timestamp);
		$this->_send_notification($user_id, $message, $link, $image, $timestamp);
		return $result;
	}
	
	/**
	 * private function
	 */
	function _send_notification($user_id = NULL, $message = NULL, $link = NULL,
	 $image = NULL, $timestamp = NULL){
		if(empty($user_id)){
			return FALSE;
		}
    
		$node_base_url = $this->CI->config->item('node_base_url_http');
		if(empty($message) || empty($link)){
		  $notification_amount = $this->count_unread($user_id);
		  
		  $result = $this->_get(
		  $node_base_url.'publish?key=WOW&user_id='.$user_id
		  .'&notification_amount='.$notification_amount);
		  
		}else if(isset($message) && isset($link)){
			$message_array = array(
				'message' => $message,
				'link' => $link,
				'image' => $image,
				'timestamp' => $timestamp
		  );
			
		  $notification_message = json_encode((object)$message_array);
		  
		  $notification_amount = $this->count_unread($user_id);
		  
		  $result = $this->_get(
		  $node_base_url.'publish?key=WOW&notification_message='
		  .urlencode($notification_message).'&user_id='.$user_id
		  .'&notification_amount='.$notification_amount);
		}
		
	}
	
	/**
	 * HTTP GET method wrapper
	 * @param url
	 * @return result
	 * @author Metwara Narksook
	 */
	function _get($url) {
		try{
			$return = @file_get_contents($url);
		}catch(exception $e){
			$return = FALSE;
		}
		return $return;
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
		
    $notification_id_list_criteria = array();
    foreach($notification_id_list as $notification){
      $notification_id_list_criteria[] = new MongoId($notification);
    }
    
		$result = $this->CI->notification->lists(array(
			'user_id' => (int)$user_id,
			'_id' => array('$in' => $notification_id_list_criteria)));
		
		$criteria = array();
		foreach($result as $notification){
			$criteria[] = new MongoId($notification['_id']);
		}
		
		$result = $this->CI->notification->update($criteria, array('read' => TRUE));
    $this->_send_notification($user_id);
    return $result;
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

/*

<script src="http://socialhappen.dyndns.org:8080/socket.io/socket.io.js"></script>
<script>
	var user_id = 20;
	var session = 'SESSIONNAJA';

  var socket = io.connect('http://socialhappen.dyndns.org:8080');
  
  socket.on('connect', function(){
  	console.log('send subscribe');
  	socket.emit('subscribe', user_id, session);
  });
  
  socket.on('subscribeResult', function (data) {
  	console.log('got subscribe result: ' + JSON.stringify(data));
  });
  
  socket.on('newNotificationAmount', function (notification_amount) {
  	console.log('notification_amount: ' + notification_amount);
  });
  
  socket.on('newNotificationMessage', function (notification_message) {
  	console.log('notification_message: ' + JSON.stringify(notification_message));
  });
</script>
http://socialhappen.dyndns.org:8080/publish?key=WOW&notification_message={%22message%22:%22You%20got%20new%20achievement!%22,%22link%22:%22http://socialhappen.com/passport%22}&user_id=5001&notification_amount=100

*/
/* End of file notification_lib.php */
/* Location: ./application/libraries/notification_lib.php */