<?php
/**
 * achievement stat model class for achievement stat object
 * @author Metwara Narksook
 */
class Achievement_stat_model extends CI_Model {

	var $app_id = '';
	var $user_id = '';
	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->achievement_stat = sh_mongodb_load( array(
			'collection' => 'achievement_stat'
		));
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->achievement_stat->ensureIndex(array('app_id' => 1,
										'user_id' => 1));
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
	 * @param amount int amount to increment
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function increment($app_id = NULL, $user_id = NULL,
		 $info = array(), $amount = 1){
		
		$check_args = (isset($app_id) && isset($user_id)) 
			&& ( empty($info['campaign_id']) || isset($info['app_install_id']) );
		
		if($check_args){
			$criteria = array('app_id' => (int)$app_id, 'user_id' => (int)$user_id);
				
			$inc = array();
			$result = TRUE;
			if(isset($info['action_id'])){
				$inc['action.' . $info['action_id'] . '.count'] = $amount;
				if(isset($info['app_install_id'])){
					$inc['action.' . $info['action_id'] 
						. '.app_install.' . $info['app_install_id'] . '.count'] = $amount;
					if(isset($info['campaign_id'])){
						$inc['action.' . $info['action_id'] 
							. '.campaign.' . $info['campaign_id'] . '.count'] = $amount;
					}
					
					if(isset($info['page_id'])){
						$inc['action.' . $info['action_id'] . '.page.'
						 . $info['page_id'] . '.count'] = $amount;
					}
				}
				
				$result = $this->achievement_stat->update($criteria,
				array('$inc' => $inc), TRUE);
			}else if(isset($info['score'])){
				
				$inc['score'] = $amount;
				
				$result = $this->achievement_stat->update($criteria,
				array('$inc' => $inc), TRUE);
			}
			
			

			return $result;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * set achievement stat
	 * @param app_id int
	 * @param user_id int user_id
	 * @param info array of data to set
	 * 
	 * @return result boolean
	 */
	function set($app_id = NULL, $user_id = NULL, $info = array()){
		$check_args = isset($app_id) && isset($user_id)
									&& empty($info['action']) && !isset($info['app_id'])
									 && empty($info['user_id']);
		
		if($check_args){
			$criteria = array('app_id' => (int)$app_id, 'user_id' => (int)$user_id);
			
			$result = $this->achievement_stat->update($criteria,
				array('$set' => $info), TRUE);
				
			return $result;
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 * get stat achievement
	 * 
	 * @param app_id
	 * @param user_id
	 * 
	 * @return result
	 * 
	 * @author Metwara Narksook
	 */
	function get($app_id = NULL, $user_id = NULL){
		$check_args = isset($app_id) && isset($user_id);
		if($check_args){
			
			
			$res = $this->achievement_stat->find(array('app_id' => (int)$app_id,
				 'user_id' => (int)$user_id))->limit(1);
			$result = array();
			foreach ($res as $stat) {
				$result[] = $stat;
			}
			return count($result) > 0 ? $result[0] : NULL;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * list achievement stat
	 * 
	 * @param criteria array of criteria
	 * 
	 * @return result array
	 * 
	 */
	function list_stat($criteria = array()){
		$res = $this->achievement_stat->find($criteria);
		
		$result = array();
		foreach ($res as $stat) {
			$result[] = $stat;
		}
		return $result;
	}
	
	/**
	 * drop entire collection
	 * you will lost all achievement_stat data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->achievement_stat->drop();
	}
}

/* End of file achievement_stat_model.php */
/* Location: ./application/models/achievement_stat_model.php */