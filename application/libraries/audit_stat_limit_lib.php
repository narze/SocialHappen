<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MongoDB Audit Stat Limit Library
 *
 * A library to record audit stat to the NoSQL database MongoDB. 
 *
 * @author Metwara Narksook
 */

class Audit_stat_limit_lib
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
		$this->CI->load->model('audit_stats_model', 'stats');
	}
	
	/**
	 * create index for all collection in database
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		$this->CI->stats->create_index();
	}
	
	/**
	 * add new audit stat entry to database
	 * 
	 * @param user_id (require)
	 * @param action_no (require)
	 * @param app_install_id (require)
	 * @param campaign_id (optional)
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add($user_id = NULL, $action_no = NULL, $app_install_id = NULL,
	 $campaign_id = NULL){
		$check_args = isset($user_id) && isset($action_no)
		 && isset($app_install_id);
		
		if(!$check_args){
			return FALSE;
		}
		
		$data = array('user_id' => (int)$user_id,
									'action_no' => (int)$action_no,
									'app_install_id' => (int)$app_install_id);
		if(isset($campaign_id)){
			$data['campaign_id'] = (int)$campaign_id;
		}
		return $this->CI->stats->add_stat($data);
	}
	
	/**
	 * count stat in the past
	 * @param user_id (require)
	 * @param action_no (require)
	 * @param app_install_id (require)
	 * @param campaign_id (optional)
	 * @param back_time_interval time in second for querying back 
	 * 														(require - 1 hour default)
	 * 
	 * @return count (int)
	 * 
	 * @author Metwara Narksook
	 */
	function count($user_id = NULL, $action_no = NULL, $app_install_id = NULL,
	 $campaign_id = NULL, $back_time_interval = 3600){
		date_default_timezone_set('Asia/Bangkok');
		$now = time();
		$criteria = array();
		if(isset($user_id)){
			$criteria['user_id'] = (int)$user_id;
		}
		if(isset($action_no)){
			$criteria['action_no'] = (int)$action_no;
		}
		if(isset($app_install_id)){
			$criteria['app_install_id'] = (int)$app_install_id;
		}
		if(isset($campaign_id)){
			$criteria['campaign_id'] = (int)$campaign_id;
		}
		$criteria['timestamp'] = array('$gte' => $now - (int)$back_time_interval);
		return $this->CI->stats->count_stat($criteria);
	}
	
	/**
	 * prune collection by time
	 * audit entry that inserted before this interval from now will be removed
	 * 
	 * @param back_time_interval (int) time interval (require - default 1 day)
	 * 
	 * @return boolean
	 * 
	 * @author Metwara Narksook
	 */
	function prune($back_time_interval = 86400){
		date_default_timezone_set('Asia/Bangkok');
		$now = time();
		$criteria = array('timestamp' => 
			array('$lte' => $now - $back_time_interval));
		
		return $this->CI->stats->remove($criteria);
	}
}
/* End of file audit_stat_limit_lib.php */
/* Location: ./application/libraries/audit_stat_limit_lib.php */