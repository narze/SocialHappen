<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_started_model extends CI_Model {
	
	/**
	 * Connect to mongodb
	 * @author Manassarn M.
	 */
	function __construct(){
		parent::__construct();
		
		$this->config->load('mongo_db');
		$mongo_user = $this->config->item('mongo_user');
		$mongo_pass = $this->config->item('mongo_pass');
		$mongo_host = $this->config->item('mongo_host');
		$mongo_port = $this->config->item('mongo_port');
		$mongo_db = $this->config->item('mongo_db');
		
		try{
			// connect to database
			$this->connection = new Mongo("mongodb://".$mongo_user.":"
			.$mongo_pass
			."@".$mongo_host.":".$mongo_port);
			
			// select database
			$this->db = $this->connection->get_started;
			
			// select collection
			$this->get_started_info = $this->db->get_started_info;
			$this->get_started_stat = $this->db->get_started_stat;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
	
	/** 
	 * Drop all get-started
	 * @author Weerapat P.
	 */
	function drop_collection(){
		return $this->get_started_info->drop() && $this->get_started_stat->drop();
	}
	
	/**
	 * Create index for get_started_info collection
	 * @author Weerapat P.
	 */
	function create_index(){
		return $this->get_started_info->ensureIndex(array('id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all get-started item
	 * @author Weerapat P.
	 */
	function count_all_info(){
		return $this->get_started_info->count();
	}

	/**
	 * Count all get-started stat
	 * @author Weerapat P.
	 */
	function count_all_stat(){
		return $this->get_started_stat->count();
	}
	
	/**
	 * Add get_started_info
	 * @param $item_data = [id, type, link, name]
	 * @author Weerapat P.
	 */
	function add_get_started_info($item_data = array()){
		if(count($item_data) < 1) return FALSE;
		$check_args = !empty($item_data['id']) && !empty($item_data['type']) && !empty($item_data['link']) && !empty($item_data['name']);
		if(!$check_args){
			return FALSE;
		} else { 
			return $this->get_started_info->insert($item_data);
		}
	}

	/**
	 * Add get_started_stat
	 * @param $data = array(
	 *	'id' => [int],
	 *	'type' => [text 'page' or 'app'],
	 *	'items' => [array of done item id]
	 * )
	 * @author Weerapat P.
	 */
	function add_get_started_stat($data = array()){
		if(count($data['items']) < 1) return FALSE;
		$data['id'] = (int)$data['id'];
		$result = $this->get_started_stat->findOne(array('id' => $data['id'], 'type' => $data['type']));
		sort($data['items']);

		if($result) { //Stat exist
			return $this->update_get_started_stat_items($data['id'], $data['type'], $data['items']);
		}
		return $this->get_started_stat->insert($data);
	}

	/**
	 * Get all page get_started todo list
	 * @author Weerapat P.
	 */
	function get_all_page_todo_list(){
		$cursor = $this->get_started_info->find( array( 
			'$or' => array( array('type' => 'page'), array('type' => 'all') )
			) 
		);
		return iterator_to_array($cursor, false);
	}

	/**
	 * Get all app get_started todo list
	 * @author Weerapat P.
	 */
	function get_all_app_todo_list(){
		$cursor = $this->get_started_info->find( array( 
			'$or' => array( array('type' => 'app'), array('type' => 'all') )
			) 
		);
		return iterator_to_array($cursor, false);
	}

	/**
	 * Get get_started list by page_id
	 * @param $id
	 * @author Weerapat P.
	 */
	function get_todo_list_by_page_id($id = NULL){
		if(!$id) return FALSE;
		$lists = $this->get_all_page_todo_list();
		$result = $this->get_started_stat->findOne(array('id' => (int)$id, 'type' => 'page'));
		$result = obj2array($result);
		if(count($lists)) {
			foreach($lists as &$item){
				if( is_array($result['items']) && in_array($item['id'], $result['items']) ) {
					$item['status'] = 1;
				} else {
					$item['status'] = 0;
				}
			}
		}	
		return $lists;
	}

	/**
	 * Get get_started list by app_id
	 * @param $id
	 * @author Weerapat P.
	 */
	function get_todo_list_by_app_id($id = NULL){
		$result = $this->get_started_stat->findOne(array('id' => (int)$id, 'type' => 'app'));
		if(!$result) return FALSE;
		$result = obj2array($result);
		$lists = $this->get_all_app_todo_list();
		if(count($lists)) {
			foreach($lists as &$item){
				if( is_array($result['items']) && in_array($item['id'], $result['items']) ) {
					$item['status'] = 1;
				} else {
					$item['status'] = 0;
				}
			}
		}
		return $lists;
	}

	/**
	 * Update get_started_stat items by id,type
	 * @param $id
	 * @param $type = ['app' or 'page']
	 * @param $items = [array of id]
	 * @author Weerapat P.
	 */
	function update_get_started_stat_items($id = NULL, $type = NULL, $items = array()){
		
		$check_args = !empty($id) && !empty($type) && count($items);
		if(!$check_args){
			return FALSE;

		} else {
			$result = $this->get_started_stat->findOne(array('id' => (int)$id, 'type' => $type));
			$result = obj2array($result);
			if(count($result['items']) > 0 ) {
				$items = array_merge($items, $result['items']);
				$items = array_unique($items);
				sort($result['items']);
			}
			sort($items);

			if( $items == $result['items'] ) return TRUE; //if nothing change after merged, do nothing

			return $this->get_started_stat->update(array('id'=>(int)$id, 'type'=>$type),
				array('$set' => array( 'items'=> $items ) )
			);
		}
	}
}

/* End of file get_started_model.php */
/* Location: ./application/models/get_started_model.php */