<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for invite
 * @author Manassarn M.
 */
class Invite_model extends CI_Model {
	
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
			$this->invite = $this->db->invite;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
	
	/** 
	 * Drop invite collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->invite->drop();
	}
	
	/**
	 * Create index for invite collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->invite->ensureIndex(array('campaign_id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all invite
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->invite->count();
	}
	
	/**
	 * Add an invite
	 * @param $invite = array(
	 * 		'campaign_id' => [campaign_id],
			'facebook_invite' => [boolean, FALSE if not set],
	 *		'email_invite' => [boolean, FALSE if not set],
	 *		'criteria' => array(
	 *			'score' => [score for sending invite],
	 *			'maximum' => [maximum time for invite],
	 *			'cooldown' => [time to wait when reached maximum],
	 *			'acceptance' => array(
	 *				'page' => [score gained when user accepted invite and signup this campaign's page],
	 *				'campaign' => [score gained when user accepted invite and signup this campaign]
	 *			)
	 *		),
	 *		'message' => array(
	 *			'title' => [message title],
	 *			'text' => [message text],
	 *			'image' => [message image url]
	 *		)
	 * )
	 * @author Manassarn M.
	 */
	function add($invite = array()){
		$check_args = arenotempty($invite, array('campaign_id','criteria','message')) 
			&& arenotempty($invite['criteria'], array('score','maximum','cooldown','acceptance')) 
			&& arenotempty($invite['criteria']['acceptance'], array('page','campaign')) 
			&& arenotempty($invite['message'], array('title','text','image'));
		if(!$check_args){
			return FALSE;
		} else {
			$invite['campaign_id'] = (int) $invite['campaign_id'];
			$invite['criteria']['score'] = (int) $invite['criteria']['score'];
			$invite['criteria']['maximum'] = (int) $invite['criteria']['maximum'];
			$invite['criteria']['cooldown'] = (int) $invite['criteria']['cooldown'];
			
			if(!isset($invite['facebook_invite'])){
				$invite['facebook_invite'] = FALSE;
			}
			if(!isset($invite['email_invite'])){
				$invite['email_invite'] = FALSE;
			}
			try {
				$this->invite->insert($invite);
			}
			catch(MongoCursorException $e) {
				return FALSE;
			}
			
			return TRUE;
		}
	}
	
	/**
	 * Get invite by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_campaign_id($campaign_id = NULL){
		$result = $this->invite
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Update invite by campaign_id
	 * @param $campaign_id
	 * @param $invite = array(
	 *		'facebook_invite' => [boolean, FALSE if not set],
	 *		'email_invite' => [boolean, FALSE if not set],
	 *		'criteria' => array(
	 *			'score' => [score for sending invite],
	 *			'maximum' => [maximum time for invite],
	 *			'cooldown' => [time to wait when reached maximum],
	 *			'acceptance' => array(
	 *				'page' => [score gained when user accepted invite and signup this campaign's page],
	 *				'campaign' => [score gained when user accepted invite and signup this campaign]
	 *			)
	 *		),
	 *		'message' => array(
	 *			'title' => [message title],
	 *			'text' => [message text],
	 *			'image' => [message image url]
	 *		)
	 * @author Manassarn M.
	 */
	function update_by_campaign_id($campaign_id = NULL, $invite = NULL){
		$check_args = !empty($campaign_id) && arenotempty($invite, array('criteria','message')) 
			&& arenotempty($invite['criteria'], array('score','maximum','cooldown', 'acceptance')) 
			&& arenotempty($invite['criteria']['acceptance'], array('page','campaign')) 
			&& arenotempty($invite['message'], array('title','text','image'));
				if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $campaign_id;
			$invite['criteria']['score'] = (int) $invite['criteria']['score'];
			$invite['criteria']['maximum'] = (int) $invite['criteria']['maximum'];
			$invite['criteria']['cooldown'] = (int) $invite['criteria']['cooldown'];
			
			if(!isset($invite['facebook_invite'])){
				$invite['facebook_invite'] = FALSE;
			}
			if(!isset($invite['email_invite'])){
				$invite['email_invite'] = FALSE;
			}
			return $this->invite->update(array('campaign_id' => $campaign_id),
				$invite);
		}
	}
}

/* End of file invite_model.php */
/* Location: ./application/models/invite_model.php */