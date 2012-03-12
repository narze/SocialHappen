<?php
/**
 * achievement user model class for achievement user object
 * @author Metwara Narksook
 */
class Achievement_user_model extends CI_Model {
	
	var $achievement_user;
	var $achievement_id;
	var $user_id;
	var $db;
	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->achievement_user = sh_mongodb_load( array(
			'collection' => 'achievement_user'
		));
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->achievement_user->deleteIndexes() 
			&& $this->achievement_user->ensureIndex(array(
										'user_id' => 1,
										'app_install_id' => 1,
										'page_id' => 1));
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
	function add($user_id = NULL, $achievement_id = NULL, $app_id = NULL, 
							$app_install_id = NULL, $info = array(), $ref = 'achievement_info'){
									
		$check_args = isset($user_id) && isset($app_id) && isset($app_install_id);
		if($check_args){
			$achievement_user = array();
			
			$achievement_id_ref = MongoDBRef::create($ref, new MongoId($achievement_id));

			if($this->achievement_user->find(array('user_id' => $user_id,
							'achievement_id' => $achievement_id_ref))->count() > 0){
				return FALSE;
			}
			/**
			 * keys
			 */
			$achievement_user['user_id'] = (int)$user_id;
			
			$achievement_user['achievement_id'] = $achievement_id_ref;
			$achievement_user['app_id'] = (int)$app_id;
			$achievement_user['app_install_id'] = (int)$app_install_id;
			
			date_default_timezone_set('UTC');
			$achievement_user['timestamp'] = time();

			if(isset($info['company_id'])){
				$achievement_user['company_id'] = (int)$info['company_id'];
			}
			if(isset($info['page_id'])){
				$achievement_user['page_id'] = (int)$info['page_id'];
			}
			if(isset($info['campaign_id'])){
				$achievement_user['campaign_id'] = (int)$info['campaign_id'];
			}
			
			return $this->achievement_user->insert($achievement_user);
			
		} else {
			return FALSE;
		}
	}
	
	
	/**
	 * list achievement user
	 * 
	 * @param criteria array of criteria
	 * 
	 * @return result array
	 */
	function list_user($criteria = array()){
		$res = $this->achievement_user->find($criteria);
		
		$result = array();
		foreach ($res as $stat) {
			$stat['achievement_info'] = $this->mongo_db->getDBRef($stat['achievement_id']);
			$result[] = $stat;
		}
		return $result;
	}

	/**
	 * Count achievement user
	 * 
	 * @param criteria array of criteria
	 * 
	 * @author Weerapat P.
	 */
	function count($criteria = array()){
		if($criteria) {
			return $this->achievement_user->find($criteria)->count();	
		} else {
			return $this->achievement_user->count();
		}
	}
	
	/**
	 * delete achievement info
	 * 
	 * @param user_id
	 * @param achievement_id
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete($user_id = NULL, $achievement_id = NULL, $ref = 'achievement_info'){
		$check_args = isset($user_id) && isset($achievement_id);
		if($check_args){
			$achievement_id_ref = MongoDBRef::create($ref, new MongoId($achievement_id));
			
			return $this->achievement_user
									->remove(array('user_id' => $user_id,
									'achievement_id' => $achievement_id_ref), 
									array('$atomic' => TRUE));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * drop entire collection
	 * you will lost all achievement_user data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->achievement_user->drop();
	}
}

/* End of file achievement_user_model.php */
/* Location: ./application/models/achievement_user_model.php */