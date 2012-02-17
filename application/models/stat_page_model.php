<?php
/**
 * stat page model class for stat page object
 * @author Metwara Narksook
 */
class Stat_page_model extends CI_Model {
	
	var $app_id = '';
	var $page_id = '';
	var $action_id = '';
	var $date = '';
	var $count = '';
	
	/**
	 * constructor
	 * 
	 * @author Metwara Narksook
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->pages = sh_mongodb_load( array(
			'collection' => 'stat_pages'
		));
	}
		
	/**
	 * create index for collection
	 * 
	 * @author Metwara Narksook
	 */
	function create_index(){
		return $this->pages->ensureIndex(array('app_id' => 1,
										'page_id' => 1,
										'action_id' => 1, 
										'date' => -1));
	}
		 
	/**
	 * add new stat page entry
	 * 
	 * @param app_id int
	 * @param page_id int page_id
	 * @param action_id int action number
	 * @param date int date informat ymd ex. 20110531
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_stat_page($app_id = NULL, $page_id = NULL, $action_id = NULL, $date = NULL){
		$check_args = isset($app_id) && isset($action_id) && isset($page_id) && isset($date);
		if($check_args){
			$data_to_add = array('app_id' => (int)$app_id,
								'page_id' => (int)$page_id,
								'action_id' => (int)$action_id,
								'date' => (int)$date,
								'count' => 1);
			$this->pages->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * increment stat of page
	 * if increment non-exist stat, it'll create new stat entry
	 * 
	 * @param app_id int
	 * @param page_id int page_id
	 * @param action_id int action number
	 * @param date int date informat ymd ex. 20110531
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function increment_stat_page($app_id = NULL, $page_id = NULL, $action_id = NULL, $date = NULL){
		$check_args = isset($app_id) && isset($action_id) && isset($page_id) && isset($date);
	 	if($check_args){
			$criteria = array('app_id' => (int)$app_id,
							  'page_id' => (int)$page_id,
							  'action_id' => (int)$action_id,
							  'date' => (int)$date);
			$this->pages->update($criteria, array(
												'$inc' => array('count' => 1)
											), TRUE);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * get stat page in specific date
	 * 
	 * @param param criteria may contains ['page_id', 'action_id', 'date']
	 * 
	 * @return result in array
	 * 
	 * @author Metwara Narksook
	 */
	function get_stat_page($param = NULL, $skip = 0, $limit = 0){
		$check_args = isset($param) && (isset($param['page_id'])
						 || isset($param['action_id']) || isset($param['date']));
		if($check_args){
			
			$criteria = array();
		
			if(isset($param['page_id'])){
				$criteria['page_id'] = $param['page_id'];
			}
			
			if(isset($param['action_id'])){
				$criteria['action_id'] = $param['action_id'];
			}
			
			if(isset($param['date'])){
				$criteria['date'] = $param['date'];
			}
			
			$res = $this->pages->find($criteria)->sort(array('date' => 1, '_id' => 1))->skip($skip)->limit($limit);
			$result = array();
			foreach ($res as $entry) {
				$result[] = $entry;
			}
			return $result;
		}else{
			return FALSE;
		}
	 }
	 
	 /**
	  * delete stat page entry
	  * 
	  * @param _id MongoDB ID
	  * 
	  * @return result boolean
	  * 
	  * @author Metwara Narksook
	  */
	 function delete_stat_page($_id){
	 	if(empty($_id)){
	 		show_error("Invalid or missing args", 500);
	 	}else{
	 		try
		 	{
				$this->pages->remove(array("_id" => new MongoId($_id)), 
									array("safe" => true));
		 		return(TRUE);
		 	}
		 	catch(MongoCursorException $e)
		 	{
		 		show_error("Delete of data in MongoDB failed: {$e->getMessage()}", 500);
		 	}
	 	}
	 }
	 
	 /**
	 * drop entire collection
	 * you will lost all stat page data
	 * 
	 * @author Metwara Narksook
	 */
	function drop_collection(){
		$this->pages->drop();
	}
}

/* End of file stat_page_model.php */
/* Location: ./application/models/stat_page_model.php */