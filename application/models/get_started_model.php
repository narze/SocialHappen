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
	 * @param $lists_data = array(
	 *			array('id'=>101, 'link' => '#', 'name' => 'Configure Your Own Sign-Up Form'),
	 *			array(...)
	 *		)
	 * )
	 * @author Weerapat P.
	 */
	function add_get_started_info($items_data = array()){
		if(count($items_data) < 1) return FALSE;
		foreach($items_data as $item_id=>$item) {
			$check_args = !empty($item['id']) && !empty($item['type']) && !empty($item['link']) && !empty($item['name']);
			if(!$check_args){
				return FALSE;
			} else { 
				$result = $this->get_started_info->insert($item);
			}
			if($result == FALSE) return FALSE;
		}
		return TRUE;
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
		$result = $this->get_started_stat->findOne(array('id' => (int)$data['id'], 'type' => $data['type']));
		if($result) return FALSE;
		sort($data['items']);
		foreach($data['items'] as $item_id) {
			$check_args = !empty($item_id);
			if(!$check_args){
				return FALSE;
			} else { 
				$result = $this->get_started_stat->insert($data);
			}
			if($result == FALSE) return FALSE;
		}
		return TRUE;
	}

	/**
	 * Get get_started list by page_id
	 * @param $id
	 * @author Weerapat P.
	 */
	function get_list_by_page_id($id = NULL){
		$result = $this->get_started_stat->findOne(array('id' => (int)$id, 'type' => 'page'));
		$result = obj2array($result);
		$items = array();
		if(count($result['items'])) {
			foreach($result['items'] as $item_id)
			{
				$info = $this->get_started_info->findOne(array('id' => (int)$item_id));
				$info = obj2array($info);
				unset($info['_id']);
				$items[] = $info;
			}
		}
		return $items;
	}

	/**
	 * Get get_started list by app_id
	 * @param $id
	 * @author Weerapat P.
	 */
	function get_list_by_app_id($id = NULL){
		$result = $this->get_started_stat->findOne(array('id' => (int)$id, 'type' => 'app'));
		$result = obj2array($result);
		$items = array();
		if(count($result['items'])) {
			foreach($result['items'] as $item_id)
			{
				$info = $this->get_started_info->findOne(array('id' => (int)$item_id));
				$info = obj2array($info);
				unset($info['_id']);
				$items[] = $info;
			}
		}
		return $items;
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
			$items = array_merge($items, $result['items']);
			sort($items);
			return $this->get_started_stat->update(array('id'=>(int)$id, 'type'=>$type),
				array('$set' => array( 'items'=>$items ) )
			);
		}
	}
}

/* End of file get_started_model.php */
/* Location: ./application/models/get_started_model.php */