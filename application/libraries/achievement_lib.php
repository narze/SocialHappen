<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Achievement Library
 *
 *
 * @author Metwara Narksook
 */

class Achievement_lib
{
	
	private $CI;
	
	/**
	 *	------------------------------------------------------------------------
	 *	CONSTRUCTOR
	 *	------------------------------------------------------------------------
	 *
	 *	Automatically check if the Mongo PECL extension has been 
	 *  installed/enabled.
	 * 
	 * @author Metwara Narksook
	 */
	
	function __construct(){
		if(!class_exists('Mongo')){
			show_error("The MongoDB PECL extension has not been installed or enabled",
			 500);
		}
		$this->CI =& get_instance();
	}
	
	/**
	 * create index for all collection in database
	 */
	function create_index(){
		$this->CI->load->model('achievement_info_model','achievement_info');
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		$this->CI->achievement_info->create_index();
		$this->CI->achievement_stat->create_index();
		$this->CI->achievement_user->create_index();
	}
	
	/**
	 * add new achievement info
	 * 
	 * @param app_id int app_id
	 * @param app_install_id int app_install_id
	 * @param info array of info contains 
	 * 				['name',*
	 * 				 'description', *
	 * 				 'criteria_string' = >array('criteria1'...), *
	 * 				 'page_id', 
	 * 				 'campaign_id',
	 * 				 'hidden']
	 * @param criteria array of criteria and amount
	 * 				ex. array('friend' => 500),
	 * 				ex.	array('action.5.page.count' => 20,
	 * 									'action.10.count' => 23)
	 */
	function add_achievement_info($app_id = NULL, $app_install_id = NULL,
							 $info = array(), $criteria = array()){
		if(empty($app_id) || empty($app_install_id)
		 || empty($info) || empty($criteria)) return FALSE;
		$this->CI->load->model('achievement_info_model','achievement_info');
		
		return $this->CI->achievement_info->add($app_id, $app_install_id, $info
							, $criteria);
	}
	
	/**
	 * set exists achievement info
	 * 
	 * @param achievement_id string achievement_id
	 * @param app_id int app_id
	 * @param app_install_id int app_install_id
	 * @param info array of info contains 
	 * 				['name',
	 * 				 'description', 
	 * 				 'criteria_string' = >array('criteria1'...), 
	 * 				 'page_id', 
	 * 				 'campaign_id',
	 * 				 'hidden']
	 * @param criteria array of criteria and amount
	 * 				ex. array('friend' => 500),
	 * 				ex.	array('action.5.page.count' => 20,
	 * 									'action.10.count' => 23)
	 */
	function set_achievement_info($achievement_id = NULL, $app_id = NULL,
			$app_install_id = NULL, $info = array(), $criteria = array()){
		if(empty($app_id) || empty($app_install_id)
		 || empty($info) || empty($criteria) || empty($achievement_id))
		  return FALSE;
		$this->CI->load->model('achievement_info_model','achievement_info');
		
		return $this->CI->achievement_info->set($achievement_id, $app_id,
		 $app_install_id, $info, $criteria);
	}
	
	/**
	 * list achievement info by app_id
	 * 
	 * @param app_id
	 * 
	 * @return result array
	 */
	function list_achievement_info_by_app_id($app_id = NULL){
		$this->CI->load->model('achievement_info_model','achievement_info');
		if(empty($app_id)) return NULL;
		return $this->CI->achievement_info->list_info(array('app_id' => $app_id));
	}
	
	/**
	 * list achievement info by page_id
	 * 
	 * @param page_id
	 * 
	 * @return result array
	 */
	function list_achievement_info_by_page_id($page_id = NULL){
		$this->CI->load->model('achievement_info_model','achievement_info');
		if(empty($page_id)) return NULL;
		return $this->CI->achievement_info->list_info(array('page_id' => $page_id));
	}
	
	/**
	 * list achievement info by campaign_id
	 * 
	 * @param campaign_id
	 * 
	 * @return result array
	 */
	function list_achievement_info_by_campaign_id($campaign_id = NULL){
		$this->CI->load->model('achievement_info_model','achievement_info');
		if(empty($campaign_id)) return NULL;
		return $this->CI->achievement_info->list_info(array(
			'campaign_id' => $campaign_id));
	}
	
	/**
	 * delete achievement info
	 * 
	 * @param achievement_id
	 * 
	 * @return result boolean
	 */
	function delete_achievement_info($achievement_id = NULL){
		$this->CI->load->model('achievement_info_model','achievement_info');
		if(empty($achievement_id)) return FALSE;
		return $this->CI->achievement_info->delete($achievement_id);
	}
	/**
	 * add new achievement user
	 * 
	 * @param user_id int user_id*
	 * @param achievement_id 
	 * @param app_id int app_id*
	 * @param app_install_id int app_install_id*
	 * @param info array of info contains 
	 * 				['page_id',
	 * 				 'campaign_id']
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function reward_user($user_id = NULL, $achievement_id = NULL, $app_id = NULL, 
							$app_install_id = NULL, $info = array()){
		if(empty($app_id) || empty($app_install_id)
		 || empty($user_id) || empty($achievement_id))
		  return FALSE;
		
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		return $this->CI->achievement_user->add($user_id, $achievement_id, $app_id, 
			$app_install_id, $info);
	}
	
	/**
	 * list user achieved
	 * 
	 * @param user_id
	 * 
	 * @return result array
	 */
	function list_user_achieved_by_user_id($user_id = NULL){
		$this->CI->load->model('achievement_user_model','achievement_user');
		if(empty($user_id)) return NULL;
		return $this->CI->achievement_user->list_user(array('user_id' => $user_id));
	}
	
	/**
	 * list user achieved in page_id
	 * 
	 * @param user_id
	 * @param page_id
	 * 
	 * @return result array
	 */
	function list_user_achieved_in_page($user_id = NULL, $page_id = NULL){
		$this->CI->load->model('achievement_user_model','achievement_user');
		if(empty($user_id) || empty($page_id)) return NULL;
		return $this->CI->achievement_user->list_user(array('user_id' => $user_id
			,'page_id' => $page_id));
	}
	
	/**
	 * list user achieved in page_id
	 * 
	 * @param user_id
	 * @param campaign_id
	 * 
	 * @return result array
	 */
	function list_user_achieved_in_campaign($user_id = NULL, $campaign_id = NULL){
		$this->CI->load->model('achievement_user_model','achievement_user');
		if(empty($user_id) || empty($campaign_id)) return NULL;
		return $this->CI->achievement_user->list_user(array('user_id' => $user_id
			,'campaign_id' => $campaign_id));
	}
	
	/**
	 * delete achievement of user by user_id and achievement_id
	 * 
	 * @param user_id
	 * @param campaign_id
	 * 
	 * @return result boolean
	 */
	function delete_user_achieved($user_id = NULL, $achievement_id = NULL){
		$this->CI->load->model('achievement_user_model','achievement_user');
		if(empty($user_id) || empty($achievement_id)) return FALSE;
		return $this->CI->achievement_user->delete($user_id, $achievement_id);
	}
	
	/**
	 * set achievement stat
	 * @param app_id int
	 * @param user_id int user_id
	 * @param info array of data to set ex. friends
	 * 
	 * @return result boolean
	 */
	function set_achievement_stat(
		$app_id = NULL, $user_id = NULL, $info = array()){
		if(empty($user_id) || empty($app_id) || empty($info)) return FALSE;
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		
		return $this->CI->achievement_stat->set($app_id, $user_id, $info);
	}
	
	/**
	 * get achievement stat of user by app_id
	 * 
	 * @param param criteria may contains ['app_install_id', 'action_id', 'date']
	 * 
	 * @return result array
	 * 
	 * @author Metwara Narksook
	 */
	function get_achievement_stat_of_user_in_app($app_id = NULL, $user_id = NULL){
		if(empty($user_id) || empty($app_id)) return NULL;
		
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		
		return $this->CI->achievement_stat->get($app_id, $user_id);
	}
}
/* End of file achievement_lib.php */
/* Location: ./application/libraries/achievement_lib.php */