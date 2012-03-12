<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Challenge Class
 * @author Manassarn M.
 */
class Challenge_lib {

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('challenge_model');
	}

	function add($data) {
		$result = $this->CI->challenge_model->add($data);
		return $result;
	}
	
	function get($criteria) {
		$result = $this->CI->challenge_model->get($criteria);
		return $result;
	}

	function get_one($criteria) {
		$result = $this->CI->challenge_model->getOne($criteria);
		return $result;
	}

	function update($criteria, $data) {
		if(!$challenge = $this->get_one($criteria)) {
			return FALSE;
		}
		if(isset($data['$set']['end']) && $data['$set']['end'] < $challenge['start']) {
			return FALSE;
		}
		$data = array_cast_int($data);
		return $this->CI->challenge_model->update($criteria, $data);
	}

	function remove($criteria) {
		return $this->CI->challenge_model->delete($criteria);
	}

	function check_challenge($company_id = NULL, $user_id = NULL,
		$info = array()) {
		
		$company_id = (int) $company_id;
		$user_id = (int) $user_id;
		$result = array(
			'success' => TRUE,
			'completed' => array()
		);
		
		$this->CI->load->model('achievement_user_model','achievement_user');
		
		$user_achieved = $this->CI->achievement_user->list_user(
			array('user_id' => $user_id, 'company_id' => $company_id));
		
		$user_achieved_id_list = array();
		foreach ($user_achieved as $achieved){
			$user_achieved_id_list[] = $achieved['achievement_id']['$id'];
			if($achieved['achievement_id']['$ref'] === 'challenge'){
				$result['completed'][] = ''.$achieved['achievement_id']['$id'];
			}
		}

		//if user achieved something, exclude them out with $nin
		if(count($user_achieved_id_list) > 0 ){
			$candidate_achievement_criteria = 
				array('_id' => array('$nin' => $user_achieved_id_list),
				 			'company_id' => $company_id);
		} else {
			$candidate_achievement_criteria = array('company_id' => $company_id);
                                              // 'info.enable' => TRUE);
		}
		
		// Copied from achievement_lib
		// $candidate_achievement_criteria['$and'] = array();
		// if(isset($info['app_id'])){
		// 	$candidate_achievement_criteria['$and'][] = array('$or' => array(array('app_id' => (int) $info['app_id']),array('app_id' => array('$exists' => FALSE))));
		// } else {
		// 	$candidate_achievement_criteria['$and'][] = array('app_id' => array('$exists' => FALSE));
		// }

		// if(isset($info['app_install_id'])){
		// 	$candidate_achievement_criteria['$and'][] = array('$or' => array(array('app_install_id' => (int) $info['app_install_id']),array('app_install_id' => array('$exists' => FALSE))));
		// } else {
		// 	$candidate_achievement_criteria['$and'][] = array('app_install_id' => array('$exists' => FALSE));
		// }
		
		// if(isset($info['campaign_id'])){
		// 	$candidate_achievement_criteria['$and'][] = array('$or' => array(array('campaign_id' => (int) $info['campaign_id']),array('campaign_id' => array('$exists' => FALSE))));
		// } else {
		// 	$candidate_achievement_criteria['$and'][] = array('campaign_id' => array('$exists' => FALSE));
		// }
		$challenge_list = 
			$this->CI->challenge_model->get($candidate_achievement_criteria);

		$this->CI->load->model('achievement_stat_model', 'achievement_stat');
		foreach ($challenge_list as $challenge) {
	  		$match_all_criteria = TRUE;
			foreach($challenge['criteria'] as $criteria){
				$query = $criteria['query'];
				$count = $criteria['count'];
				$action_query = 'action.'.$query['action_id'].'.company.'.$company_id.'.count';
				$stat_criteria = array(
					'app_id' => $query['app_id'],
					'user_id' => $user_id,
					$action_query => array('$gte' => $count)
				);

			    $matched_achievement_stat = 
					$this->CI->achievement_stat->list_stat($stat_criteria);
				if(!$matched_achievement_stat) {
					$match_all_criteria = FALSE;
				}
			}
      
  		if($match_all_criteria) {
  			$result['completed'][] = ''.$challenge['_id'];
  			//This user completed this challenge
  			$achieved_info = array(
  				'company_id' => $company_id
  			);
			
				if(isset($info['campaign_id'])){
					$achieved_info['campaign_id'] = $info['campaign_id'];
				}
				
				$ref = 'challenge';
				if(!$this->CI->achievement_user->add($user_id, $challenge['_id'], 
					$query['app_id'] = 0, $info['app_install_id'] = 0, $achieved_info, $ref)){
					$result['success'] = FALSE;
				}
					
				$this->CI->load->library('notification_lib');
				$message = 'You have completed a challenge : ' . $challenge['detail']['name'] . '.';
				$link = '#';
				$image = $challenge['detail']['image'];
				$this->CI->notification_lib->add($user_id, $message, $link, $image);
    	}
		}
		return $result;
	}
}