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
	
	private $INVITE_ACTION_ID = 113;
  
	private $SHARE_ACTION_ID = 108;
  
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
    	$this->CI->load->model('achievement_stat_page_model','achievement_stat_page');
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		$this->CI->achievement_info->create_index();
		$this->CI->achievement_stat->create_index();
    	$this->CI->achievement_stat_page->create_index();
		$this->CI->achievement_user->create_index();
	}
	
	/**
	 * add new achievement info
	 * 
	 * @param app_id int app_id*
	 * @param app_install_id int app_install_id [optional]
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
		if(!isset($app_id) || empty($info) || empty($criteria)) return FALSE;
		$this->CI->load->model('achievement_info_model','achievement_info');

		return $this->CI->achievement_info->add($app_id, $app_install_id, $info
							, $criteria);
	}
	
	/**
	 * set exists achievement info
	 * 
	 * @param achievement_id string achievement_id*
	 * @param app_id int app_id*
	 * @param app_install_id int app_install_id [optional]
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
		if(!isset($app_id) || empty($info) || empty($criteria) || empty($achievement_id))
		  return FALSE;
		$this->CI->load->model('achievement_info_model','achievement_info');

		return $this->CI->achievement_info->set($achievement_id, $app_id,
		 $app_install_id, $info, $criteria);
	}
	
	/**
	 * list all achievement info
	 * 
	 * @return result array
	 */
	function list_achievement_info(){
		$this->CI->load->model('achievement_info_model','achievement_info');
		return $this->CI->achievement_info->list_info(array());
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
		if(!isset($app_id)) return NULL;
		return $this->CI->achievement_info->list_info(array('app_id' => $app_id));
	}
	
	/**
	 * get achievement info
	 * @param achievement_id
	 * 
	 * @return achievement info object
	 */
	function get_achievement_info($achievement_id = NULL){
		if(empty($achievement_id)){
			return FALSE;
		}else{
			$this->CI->load->model('achievement_info_model','achievement_info');
			return $this->CI->achievement_info->get($achievement_id);
		}
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
		if(!isset($app_id) || !isset($app_install_id)
		 || empty($user_id) || empty($achievement_id))
		  return FALSE;
		
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		return $this->CI->achievement_user->add((int)$user_id, (int)$achievement_id, (int)$app_id, 
			(int)$app_install_id, $info);
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
		$this->CI->load->model('achievement_info_model','achievement_info');
		if(empty($user_id)) return NULL;
		return $this->CI->achievement_user->list_user(array('user_id' => (int)$user_id));
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
	 * @param data array of data to set ex. array('friend' => 5)
	 * 				*array's key cannot be 'action' or 'score'
	 * @param info array of data to add 
	 * 				may contain['app_install_id' ,'page_id', 'campaign_id']
	 * 				ex. array('app_install_id'=>2)
	 * 				app_install_id is required*
	 * @return result boolean
	 */
	function set_achievement_stat(
		$app_id = NULL, $user_id = NULL, $data = array(), $info = array()){
		if(empty($user_id) || !isset($app_id) || empty($data) || empty($info)||
			 !isset($info['app_install_id'])){ return FALSE; }
		
		$keys = array_keys($data);
		$check_args = TRUE;
		foreach ($keys as $key => $value) {
			if(preg_match("/^((action.?)|(score.?))+/", $value)){
				return FALSE;
			}
		}
				 
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		$result = $this->CI->achievement_stat->set($app_id, $user_id, $data);
		if($result){
			$this->_test_reward_achievement($app_id, $user_id, $info);
		}
		
		return $result;
	}
	
	/**
	 * increment stat of achievement
	 * if increment non-exist stat, it'll create new stat entry
	 * 
	 * @param app_id int
	 * @param user_id int user_id
	 * @param info array of data to add 
	 * 				may contain['action_id', 'app_install_id' ,'page_id', 'campaign_id']
	 * 				ex. array('action_id'=>5,'app_install_id'=>2)
	 * 				app_install_id is required*
	 * @param amount int amount to increment
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function increment_achievement_stat($app_id = NULL, $user_id = NULL,
		 $info = array(), $amount = 1){
		
		if(!isset($user_id) || !isset($app_id) || empty($info) ||
			 !isset($info['app_install_id'])) return FALSE;
		
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		
		$increment_result = $this->CI->achievement_stat->increment($app_id, 
			$user_id, $info, $amount);
    
    // increment page score if is SHARE or INVITE action
    if($increment_result && isset($info['page_id'])
     && ($info['action_id'] == $this->INVITE_ACTION_ID
      || $info['action_id'] == $this->SHARE_ACTION_ID)){
      
      $increment_page_result = $this->CI->achievement_stat_page
        ->increment($info['page_id'], 
        $user_id, $info, $amount);
      if(isset($info['campaign_id'])){
        $this->_increment_page_score($info['page_id'], $user_id, $info, $amount);
      }
    }
    
		if($increment_result){
			if(isset($info['action_id'])){
				$this->_increment_platform_score($user_id, $app_id, $info['action_id'],
				 $amount);
			}
			$this->_test_reward_achievement($app_id, $user_id, $info);
		}
		
		return $increment_result;
	}
	
  function _increment_page_score($page_id, $user_id, $info, $amount){
    $this->CI->load->model('app_component_model','app_component_model');
    
    $campaign = $this->CI->app_component_model
      ->get_by_campaign_id($info['campaign_id']);
    
    if(isset($campaign)){
      $page_score = 0;
      $campaign_score = 0;
      
      if($info['action_id'] == $this->INVITE_ACTION_ID){
        $page_score = $campaign['invite']['criteria']['score'];
        $campaign_score = $campaign['invite']['criteria']['score'];
      }else if($info['action_id'] == $this->SHARE_ACTION_ID){
        $page_score = $campaign['sharebutton']['criteria']['score'];
        $campaign_score = $campaign['sharebutton']['criteria']['score'];
      }
      
      if($page_score > 0 || $campaign_score > 0){
        $info = array('campaign_score' => $campaign_score,
                      'page_score' => $page_score,
                      'campaign_id' => $info['campaign_id']);
        $increment_page_score_result = $this->CI->achievement_stat_page
          ->increment($page_id, $user_id, $info, $amount);
      }
      
    }
  }
  
	function _increment_platform_score($user_id = NULL, $app_id = NULL,
		 $action_id = NULL, $amount = 0){
		if(empty($user_id) || !isset($app_id) || empty($action_id)
		 || empty($amount)) return FALSE;
		
		$this->CI->load->library('audit_lib');
		$action = $this->CI->audit_lib->get_audit_action($app_id, $action_id);
		
		$platform_app_id = 0;
		
		if($action != NULL && isset($action['score']) && $action['score'] > 0){
			$score = $amount * $action['score'];
			$this->CI->achievement_stat->increment($platform_app_id, 
				$user_id, array('score' => 'score'), $score);
			// echo '<br/>_increment_platform_score for user_id: '.$user_id.'<br/>';
		}
	}
	
	function _test_reward_achievement($app_id = NULL, $user_id = NULL,
		$info = array()){
		
		$app_id = (int) $app_id;
		$user_id = (int) $user_id;
		
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		$user_achieved = $this->CI->achievement_user->list_user(
			array('user_id' => $user_id, 'app_id' => $app_id));
		
		$user_achieved_id_list = array();
		foreach ($user_achieved as $achieved){
			$user_achieved_id_list[] = $achieved['achievement_id']['$id'];
		}

		// echo '<br/>$user_achieved_id_list:<br/>';
		// var_dump($user_achieved_id_list);
		
		if(count($user_achieved_id_list) > 0 ){
			$candidate_achievement_criteria = 
				array('_id' => array('$nin' => $user_achieved_id_list),
				 			'app_id' => $app_id);
		}else{
			$candidate_achievement_criteria = array('app_id' => $app_id);
		}
		
		$candidate_achievement_criteria['$and'] = array();
		if(isset($info['page_id'])){
			$candidate_achievement_criteria['$and'][] = array('$or' => array(array('page_id' => (int) $info['page_id']),array('page_id' => array('$exists' => FALSE))));
		} else {
			$candidate_achievement_criteria['$and'][] = array('page_id' => array('$exists' => FALSE));
		}
		
		if(isset($info['campaign_id'])){
			$candidate_achievement_criteria['$and'][] = array('$or' => array(array('campaign_id' => (int) $info['campaign_id']),array('campaign_id' => array('$exists' => FALSE))));
		} else {
			$candidate_achievement_criteria['$and'][] = array('campaign_id' => array('$exists' => FALSE));
		}
		// echo "<br /><br />";
		// echo json_encode($candidate_achievement_criteria);
// 		
		// echo '<br/><h1>$candidate_achievement_criteria:</h1><br/>';
		// echo '<pre>';
		// var_dump($candidate_achievement_criteria);
		// echo '</pre>';
		
		$this->CI->load->model('achievement_info_model','achievement_info');
		$achievement_list = 
			$this->CI->achievement_info->list_info($candidate_achievement_criteria);
		
		// echo '<br/>$achievement_list:<br/>';
		// echo '<pre>';
		// print_r($achievement_list);
		// echo '</pre>';
		
		foreach ($achievement_list as $achievement) {
			
			$stat_criteria = array('app_id' => $app_id,
														 'user_id' => $user_id);
			$score = NULL;
      
      $stat_page_criteria = array();
      
      if(isset($info['page_id'])){
        $stat_page_criteria = array('page_id' => $info['page_id'],
                                    'user_id' => $user_id);
      }
			
      $page_criteria_couunt = 0;
      $score_criteria_couunt = 0;
      
			foreach($achievement['criteria'] as $key => $value){
				if($key != 'score'){
					$stat_criteria[$key] = array('$gte' => $value);
					
          $explode = explode('.', $key, 2); // explode to array('page', 'action.10.count')
          
          $is_page = $explode[0] === 'page';
          
          if(isset($info['page_id']) && $is_page && count($explode) === 2){
            $stat_page_criteria[$explode[1]] = array('$gte' => $value);
            $page_criteria_couunt++;
          }
          
				}else{
					$score = $value;
          $score_criteria_couunt++;
				}
			}
      
      $page_achievement_only = count($achievement['criteria'])
       == ($page_criteria_couunt + $score_criteria_couunt);

			// echo '<br/>$stat_criteria:<br/>';
			// echo '<pre>';
			// var_dump($stat_criteria);
			// echo '</pre>';
//       
      // echo "<br /><br />";
      // echo json_encode($stat_page_criteria);
      
      // echo '<br/>$stat_page_criteria:<br/>';
      // echo '<pre>';
      // var_dump($stat_page_criteria);
      // echo '</pre>';
			
			$matched_achievement = 
				$this->CI->achievement_stat->list_stat($stat_criteria);
			
			// echo "<br /><br />";
			// echo count($stat_page_criteria);
      // echo "<br /><br />";
      if(count($stat_page_criteria) > 2){
        $matched_achievement_page = 
          $this->CI->achievement_stat_page->list_stat($stat_page_criteria);
      }
      
      // echo '<br/>$matched_achievement_page:<br/>';
      // echo '<pre>';
      // var_dump($matched_achievement_page);
      // echo '</pre>';
      
			if($score != NULL){
				$matched_score = 
					count($this->CI->achievement_stat->list_stat(
								array('app_id' => 0,
											'score' => array('$gte' => $score)))) > 0;
			}else{
				$matched_score = TRUE;
			}
      
			// echo '<br/>';
      // echo 'X ' . count($matched_achievement) . ' '
       // . $matched_score . ' ' . $page_achievement_only . ' ';
      
      
      
			if((count($matched_achievement) > 0 || $page_achievement_only)
       && $matched_score){
				
        // echo 'Y';
        
        if(count($stat_page_criteria) > 2 
          && (count($matched_achievement_page) === 0)){
            continue;
          }
        
        // echo 'Z';
        
				$achieved_info = array();
				if(isset($info['page_id'])){
					$achieved_info['page_id'] = $info['page_id'];
				}
				if(isset($info['campaign_id'])){
					$achieved_info['campaign_id'] = $info['campaign_id'];
				}
				
				$this->CI->achievement_user->add($user_id, $achievement['_id'], 
					$app_id, $info['app_install_id'], $achieved_info);
					
				$this->CI->load->library('notification_lib');
				$message = 'You have unlocked a new achievement: ' . $achievement['info']['name'] . '.';
				$link = '#';
				$image = $achievement['info']['badge_image'];
				$this->CI->notification_lib->add($user_id, $message, $link, $image);
				// $this->CI->notification_lib->add($user_id, 'You have unlocked new achievement.', '');
				// echo 'user_id: '.$user_id.' got reward!';
			}
		}
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
		if(empty($user_id) || !isset($app_id)) return NULL;
		
		$this->CI->load->model('achievement_stat_model','achievement_stat');
		
		return $this->CI->achievement_stat->get($app_id, $user_id);
	}
	
	
}
/* End of file achievement_lib.php */
/* Location: ./application/libraries/achievement_lib.php */