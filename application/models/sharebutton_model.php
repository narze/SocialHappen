<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for sharebutton
 * @author Manassarn M.
 */
class Sharebutton_model extends CI_Model {
	
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
			$this->sharebutton = $this->db->sharebutton;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
	
	/** 
	 * Drop sharebutton collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->sharebutton->drop();
	}
	
	/**
	 * Create index for sharebutton collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->sharebutton->ensureIndex(array('campaign_id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all sharebutton
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->sharebutton->count();
	}
	
	/**
	 * Add an sharebutton
	 * @param $sharebutton = array(
	 * 		'campaign_id' => [campaign_id],
	 *		'facebook_button' => [boolean, FALSE if not set],
	 *		'twitter_button' => [boolean, FALSE if not set],
	 *		'criteria' => array(
	 *			'score' => [score for sharing],
	 *			'maximum' => [max time for sharing],
	 *			'cooldown' => [time to wait when reached max]
	 *		),
	 *		'message' => array(
	 *			'title' => [message title],
	 *			'text' => [message text],
	 *			'caption' => [message caption],
	 *			'image' => [message image url],
	 *		)
	 * )
	 * @author Manassarn M.
	 */
	function add($sharebutton = array()){
		$check_args = arenotempty($sharebutton, array('campaign_id','criteria','message')) 
			&& arenotempty($sharebutton['criteria'], array('score','maximum','cooldown')) 
			&& arenotempty($sharebutton['message'], array('title','text','caption','image'));
		if(!$check_args){
			return FALSE;
		} else {
			$sharebutton['campaign_id'] = (int) $sharebutton['campaign_id'];
			$sharebutton['criteria']['score'] = (int) $sharebutton['criteria']['score'];
			$sharebutton['criteria']['maximum'] = (int) $sharebutton['criteria']['maximum'];
			$sharebutton['criteria']['cooldown'] = (int) $sharebutton['criteria']['cooldown'];
			if(!isset($sharebutton['facebook_button'])){
				$sharebutton['facebook_button'] = FALSE;
			}
			if(!isset($sharebutton['twitter_button'])){
				$sharebutton['twitter_button'] = FALSE;
			}
			return $this->sharebutton->insert($sharebutton);
		}
	}
	
	/**
	 * Get sharebutton by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_campaign_id($campaign_id = NULL){
		$result = $this->sharebutton
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Update sharebutton by campaign_id
	 * @param $campaign_id
	 * @param $sharebutton = array(
	 *		'facebook_button' => [boolean, FALSE if not set],
	 *		'twitter_button' => [boolean, FALSE if not set],
	 *		'criteria' => array(
	 *			'score' => [score for sharing],
	 *			'maximum' => [max time for sharing],
	 *			'cooldown' => [time to wait when reached max]
	 *		),
	 *		'message' => array(
	 *			'title' => [message title],
	 *			'text' => [message text],
	 *			'caption' => [message caption],
	 *			'image' => [message image url],
	 *		)
	 * @author Manassarn M.
	 */
	function update_by_campaign_id($campaign_id = NULL, $sharebutton = NULL){
		$check_args = !empty($campaign_id) && arenotempty($sharebutton, array('criteria','message')) 
			&& arenotempty($sharebutton['criteria'], array('score','maximum','cooldown'))
			&& arenotempty($sharebutton['message'], array('title', 'text', 'caption', 'image'));
		if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $campaign_id;
			$sharebutton['criteria']['score'] = (int) $sharebutton['criteria']['score'];
			$sharebutton['criteria']['maximum'] = (int) $sharebutton['criteria']['maximum'];
			$sharebutton['criteria']['cooldown'] = (int) $sharebutton['criteria']['cooldown'];
			
			if(!isset($sharebutton['facebook_sharebutton'])){
				$sharebutton['facebook_button'] = FALSE;
			}
			if(!isset($sharebutton['email_sharebutton'])){
				$sharebutton['twitter_button'] = FALSE;
			}
			return $this->sharebutton->update(array('campaign_id' => $campaign_id),
				$sharebutton);
		}
	}
}

/* End of file sharebutton_model.php */
/* Location: ./application/models/sharebutton_model.php */