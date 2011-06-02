<?php
/**
 * stat app model class for stat app object
 * @author Metwara Narksook
 */
class Stat_app_model extends CI_Model {

	var $app_install_id = '';
	var $action_id = '';
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
		
		// select apps collection
		$this->apps = $this->db->apps;
	}
		
	/**
	 * create index for collection
	 */
	function create_index(){
		$this->apps->ensureIndex(array('app_install_id' => 1,
										'action_id' => 1, 
										'date' => -1));
	}
		 
	/**
	 * add new stat app entry
	 * 
	 * @param app_install_id int app_install_id
	 * @param action_id int action number
	 * @param date int date informat ymd ex. 20110531
	 * 
	 * @return result boolean
	 */
	function add_stat_app($app_install_id = NULL, $action_id = NULL, $date = NULL){
		$check_args = isset($action_id) && isset($app_install_id) && isset($date);
		if($check_args){
			$data_to_add = array('app_install_id' => $app_install_id,
							'action_id' => $action_id,
							'date' => $date,
							'count' => 1);
			$this->apps->insert($data_to_add);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * increment stat of app
	 * if increment non-exist stat, it'll create new stat entry
	 * 
	 * @param app_install_id int app_install_id
	 * @param action_id int action number
	 * @param date int date informat ymd ex. 20110531
	 * 
	 * @return result boolean
	 */
	function increment_stat_app($app_install_id = NULL, $action_id = NULL, $date = NULL){
		$check_args = isset($action_id) && isset($app_install_id) && isset($date);
	 	if($check_args){
			$criteria = array('app_install_id' => $app_install_id,
							  'action_id' => $action_id,
							  'date' => $date);
			$this->apps->update($criteria, array(
												'$inc' => array('count' => 1)
											), TRUE);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * get stat app in specific date
	 * @param param criteria may contains ['app_install_id', 'action_id', 'date']
	 * 
	 * @return result in array
	 */
	function get_stat_app($param = NULL, $skip = 0, $limit = 0){
		$check_args = isset($param) && (isset($param['app_install_id'])
						 || isset($param['action_id']) || isset($param['date']));
		if($check_args){
			
			$criteria = array();
		
			if(isset($param['app_install_id'])){
				$criteria['app_install_id'] = $param['app_install_id'];
			}
			
			if(isset($param['action_id'])){
				$criteria['action_id'] = $param['action_id'];
			}
			
			if(isset($param['date'])){
				$criteria['date'] = $param['date'];
			}
			
			$res = $this->apps->find($criteria)->skip($skip)->limit($limit);
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
	  * delete stat app entry
	  * 
	  * @param _id MongoDB ID
	  */
	 function delete_stat_app($_id){
	 	if(empty($_id)){
	 		show_error("Invalid or missing args", 500);
	 	}else{
	 		try
		 	{
				$this->apps->remove(array("_id" => new MongoId($_id)), 
									array("safe" => true));
		 		return(TRUE);
		 	}
		 	catch(MongoCursorException $e)
		 	{
		 		show_error("Delete of data in MongoDB failed: {$e->getMessage()}", 500);
		 	}
	 	}
	 }
}

/* End of file stat_app_model.php */
/* Location: ./application/models/stat_app_model.php */