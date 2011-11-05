<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for app_component : homepage, invite and sharebutton
 * ex. array(
 * 		'campaign_id' => [campaign_id] (unique),
 * 		'homepage = array(
 *			'campaign_id' => [campaign_id],
 *			'enable' => [boolean, FALSE if not set],
 *			'image' => [image url],
 *			'message' => [text]
 * 		),
 * 		'invite' => array(
 *			'facebook_invite' => [boolean, FALSE if not set],
 *			'email_invite' => [boolean, FALSE if not set],
 *			'criteria' => array(
 *				'score' => [score for sending invite],
 *				'maximum' => [maximum time for invite],
 *				'cooldown' => [time to wait when reached maximum],
 *				'acceptance' => array(
 *					'page' => [score gained when user accepted invite and signup this campaign's page],
 *					'campaign' => [score gained when user accepted invite and signup this campaign]
 *				)
 *			),
 *			'message' => array(
 *				'title' => [message title],
 *				'text' => [message text],
 *				'image' => [message image url]
 *			)
 * 		),
 * 		'sharebutton' => array(
 *			'facebook_button' => [boolean, FALSE if not set],
 *			'twitter_button' => [boolean, FALSE if not set],
 *			'criteria' => array(
 *				'score' => [score for sharing],
 *				'maximum' => [max time for sharing],
 *				'cooldown' => [time to wait when reached max]
 *			),
 *			'message' => array(
 *				'title' => [message title],
 *				'text' => [message text],
 *				'caption' => [message caption],
 *				'image' => [message image url],
 *			)
 *		)
 *	)
 * @author Manassarn M.
 */
class App_component extends CI_Model {
	
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
			$this->app_component = $this->db->app_component;
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
	
	/** 
	 * Drop homepage collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->app_component->drop();
	}
	
	/**
	 * Create index for homepage collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->app_component->ensureIndex(array('campaign_id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all homepage
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->app_component->count();
	}
	
	//Homepage
	
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
			return $this->app_component->insert($homepage);
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
			return $this->app_component->update(array('campaign_id' => $campaign_id),
				$homepage);
		}
	}
	
	//Invite
	
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
	
	//App component
	
	/**
	 * Get app_component by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_campaign_id($campaign_id = NULL){
		$result = $this->app_component
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return $result;
	}
}

/* End of file homepage_model.php */
/* Location: ./application/models/homepage_model.php */