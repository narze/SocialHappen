<?php
/**
 * stat page model class for stat page object
 * @author Metwara Narksook
 */
class Stat_page_model extends CI_Model {
	var $_id = '';
	var $action = '';
	var $page_id = '';
	var $date = '';
	var $count = '';
	
	/**
	 * constructor
	 */
	function __construct() {
		parent::__construct();
		
		// connect to database
		$this->connection = new Mongo('localhost:27017');
		
		// select stat database
		$this->db = $this->connection->stat;
		
		// select pages collection
		$this->pages = $this->db->pages;
	}
		
	/**
	 * create index for collection
	 */
	 function create_index(){
	 	$this->pages->ensureIndex(array('page_id' => 1, 'action' => 1, 'date' => -1));
	 }
	 
	 /**
	  * add new stat page entry
	  * 
	  * @param action int action number
	  * @param page_id int page_id
	  * @param date int date informat ymd ex. 20110531
	  * 
	  * @return result boolean
	  */
	 function add_stat_page($action = NULL, $page_id = NULL, $date = NULL){
	 	$check_args = isset($action) && isset($page_id) && isset($date);
	 	if($check_args){
			$data_to_add = array('action' => $action,
								'page_id' => $page_id,
								'date' => $date,
								'count' => 1);
			$this->pages->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
	 }
	 
	 /**
	  * increment stat of page
	  * 
	  * @param action int action number
	  * @param page_id int page_id
	  * @param date int date informat ymd ex. 20110531
	  * 
	  * @return result boolean
	  */
	 function increment_stat_page($action = NULL, $page_id = NULL, $date = NULL){
	 	$check_args = isset($action) && isset($page_id) && isset($date);
	 	if($check_args){
			$criteria = array('action' => $action,
								'page_id' => $page_id,
								'date' => $date);
			$this->pages->update($criteria, array('$inc' => array('count' => 1)), TRUE);
			return TRUE;
		}else{
			return FALSE;
		}
	 }
	 
	 /**
	  * get stat page in specific date
	  * @param param criteria may contains ['page_id', 'action', 'date']
	  * 
	  * @return result in array
	  */
	 function get_stat_page($param = NULL, $skip = 0, $limit = 50){
	 	$check_args = isset($param) && (isset($param['page_id']) || isset($param['action']) || isset($param['date']));
	 	if($check_args){
	 		
	 		$criteria = array();
			
			if(isset($param['page_id'])){
				$criteria['page_id'] = $param['page_id'];
			}
			
			if(isset($param['action'])){
				$criteria['action'] = $param['action'];
			}
			
			if(isset($param['date'])){
				$criteria['date'] = $param['date'];
			}
			
			$res = $this->pages->find().skip($skip).limit($limit);
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
	  */
	 function delete_stat_page($_id){
	 	if(empty($_id)){
	 		show_error("Invalid or missing args", 500);
	 	}else{
	 		try
		 	{
				$this->pages->remove(array("_id"=>new MongoId($_id)),array("safe" => true));
		 		return(TRUE);
		 	}
		 	catch(MongoCursorException $e)
		 	{
		 		show_error("Delete of data in MongoDB failed: {$e->getMessage()}", 500);
		 	}
	 	}
	 }
}

/* End of file stat_model.php */
/* Location: ./application/models/stat_model.php */