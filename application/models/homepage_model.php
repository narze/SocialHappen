<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for homepage
 * @author Manassarn M.
 */
class Homepage_model extends CI_Model {
	
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
			$this->db = $this->connection->campaign;
			
			// select collection
			$this->homepage = $this->db->homepage;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
	
	/** 
	 * Drop homepage collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->homepage->drop();
	}
	
	/**
	 * Create index for homepage collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->homepage->ensureIndex(array('campaign_id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all homepage
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->homepage->count();
	}
	
	/**
	 * Add an homepage
	 * @param $homepage = array(
	 *		'campaign_id' => [campaign_id],
	 *		'enable' => [boolean, FALSE if not set],
	 *		'image' => [image url],
	 *		'message' => [text]
	 * 		)
	 * @author Manassarn M.
	 */
	function add($homepage = array()){
		$check_args = arenotempty($homepage, array('campaign_id', 'image', 'message'));
		if(!$check_args){
			return FALSE;
		} else {
			if(!isset($homepage['enable'])){
				$homepage['enable'] = '';
			}
			$homepage['campaign_id'] = (int) $homepage['campaign_id'];
			return $this->homepage->insert($homepage);
		}
	}
	
	/**
	 * Get homepage by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_campaign_id($campaign_id = NULL){
		$result = $this->homepage
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Update homepage by campaign_id
	 * @param $campaign_id
	 * @param $homepage = array(
	 *		'enable' => [boolean, FALSE if not set],
	 *		'image' => [image url],
	 *		'message' => [text]'image' => [message image url]
	 *		)
	 * @author Manassarn M.
	 */
	function update_by_campaign_id($campaign_id = NULL, $homepage = NULL){
		$check_args = !empty($campaign_id) && arenotempty($homepage, array('image', 'message'));
		if(!$check_args){
			return FALSE;
		} else {
			if(!isset($homepage['enable'])){
				$homepage['enable'] = '';
			}
			$homepage['campaign_id'] = (int) $homepage['campaign_id'];
			return $this->homepage->update(array('campaign_id' => $campaign_id),
				$homepage);
		}
	}
}

/* End of file homepage_model.php */
/* Location: ./application/models/homepage_model.php */