<?php
/**
 * achievement info model class for achievement info object
 * @author Metwara Narksook
 */
class Achievement_info_model extends CI_Model {

	var $achievement_info;
	var $achievement_id;
	var $app_id;

	/**
	 * constructor
	 *
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = sh_mongodb_load( array(
			'collection' => 'achievement_info'
		));
	}

	/**
	 * create index for collection
	 *
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array(
										'app_id' => 1,
										'app_install_id' => 1,
										'info.page_id' => 1));
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
	function add($app_id = NULL, $app_install_id = NULL, $info = array()
							, $criteria = array()){
		$check_args = isset($app_id) && isset($info)
							 && isset($info['name']) && isset($info['description'])
							 && isset($info['criteria_string'])
							 && count($info['criteria_string']) > 0
							 && isset($criteria) && count($criteria) > 0;
		if($check_args){
			$achievement_info = array();

			/**
			 * keys
			 */
			$achievement_info['app_id'] = (int)$app_id;

			if($app_install_id){
				$achievement_info['app_install_id'] = (int)$app_install_id;
			}
			if(isset($info['page_id'])){
				$achievement_info['page_id'] = (int)$info['page_id'];
			}
			if(isset($info['campaign_id'])){
				$achievement_info['campaign_id'] = (int)$info['campaign_id'];
			}
			/**
			 * info fields
			 */
			$info_to_add = array();
			$info_to_add['name'] = $info['name'];
			$info_to_add['description'] = $info['description'];
			$info_to_add['hidden'] = isset($info['hidden']) ? $info['hidden'] : FALSE;
      $info_to_add['enable'] = isset($info['enable']) ? $info['enable'] : TRUE;
			$info_to_add['criteria_string'] = $info['criteria_string'];
			$info_to_add['badge_image'] = isset($info['badge_image']) ? $info['badge_image'] : base_url().'assets/images/badges/default.png';

      if(isset($info['class'])){
        $info_to_add['class'] = $info['class'];
      }

			$achievement_info['info'] = $info_to_add;

			$achievement_info['criteria'] = $criteria;

			$result = $this->collection->insert($achievement_info);

      return $result ? $achievement_info['_id'] : FALSE;

		}else{
			return FALSE;
		}
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
	function set($achievement_id = NULL, $app_id = NULL, $app_install_id = NULL, $info = array(), $criteria = array()){
		$check_args = isset($achievement_id) && isset($app_id)
							 && isset($info)
							 && isset($info['name']) && isset($info['description'])
							 && isset($info['criteria_string'])
							 && count($info['criteria_string']) > 0
							 && isset($criteria) && count($criteria) > 0;
		if($check_args){
			$achievement_info = array();

			/**
			 * keys
			 */
			$achievement_info['app_id'] = (int)$app_id;

			if($app_install_id){
				$achievement_info['app_install_id'] = (int)$app_install_id;
			}

			if(isset($info['page_id'])){
				$achievement_info['page_id'] = (int)$info['page_id'];
			}
			if(isset($info['campaign_id'])){
				$achievement_info['campaign_id'] = (int)$info['campaign_id'];
			}
			/**
			 * info fields
			 */
			$info_to_add = array();
			$info_to_add['name'] = $info['name'];
			$info_to_add['description'] = $info['description'];
			$info_to_add['hidden'] = isset($info['hidden']) ? $info['hidden'] : FALSE;
      $info_to_add['enable'] = isset($info['enable']) ? $info['enable'] : TRUE;
			$info_to_add['criteria_string'] = $info['criteria_string'];
			$info_to_add['badge_image'] = isset($info['badge_image']) ? $info['badge_image'] : base_url().'assets/images/badges/default.png';

			if(isset($info['class'])){
        $info_to_add['class'] = $info['class'];
      }

			$achievement_info['info'] = $info_to_add;

			$achievement_info['criteria'] = $criteria;

			return $this->collection->update(array('_id' => new MongoId($achievement_id)),
				$achievement_info);

		}else{
			return FALSE;
		}
	}


	/**
	 * get achievement info by id
	 * @param achievement_id
	 *
	 * @return result array
	 *
	 * @author Metwara Narksook
	 */
	function get_by_id($achievement_id = NULL){
		$check_args = isset($achievement_id);
		if($check_args){


			$res = $this->collection
									->find(array('_id' => new MongoId($achievement_id)))
									->limit(1);

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
	 * list achievement info
	 *
	 * @param criteria array of criteria
	 * @param limit int number of results
	 * @param offset int offset number
	 *
	 * @return result array
	 *
	 * @author Metwara Narksook
	 * @author Weerapat P. - Add limit and offset
	 */
	function list_info($criteria = array(), $limit = 0, $offset = 0){

		if($limit) $res = $this->collection->find($criteria)->skip($offset)->limit($limit);
		else $res = $this->collection->find($criteria);

		$result = array();
		foreach ($res as $stat) {
			$result[] = $stat;
		}
		return $result;
	}

	/**
	 * Count achievement info
	 *
	 * @param criteria array of criteria
	 *
	 * @author Weerapat P.
	 */
	function count($criteria = array()){
		if($criteria) {
			return $this->collection->find($criteria)->count();
		} else {
			return $this->collection->count();
		}
	}

	/**
	 * delete achievement info
	 * @param achievement_id
	 *
	 * @return result bolean
	 *
	 * @author Metwara Narksook
	 */
	function delete($achievement_id = NULL){
		$check_args = isset($achievement_id);
		if($check_args){


			return $this->collection
									->remove(array("_id" => new MongoId($achievement_id)),
									array('$atomic' => TRUE));
		}else{
			return FALSE;
		}
	}

	/**
	 * drop entire collection
	 * you will lost all achievement_info data
	 *
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		return $this->collection->drop();
	}

	function getOne($query){
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}
}

/* End of file achievement_info_model.php */
/* Location: ./application/models/achievement_info_model.php */