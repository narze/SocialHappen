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
 *				'acceptance_score' => array(
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
class App_component_model extends CI_Model {
	
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
	// function add_homepage($homepage = array()){
		// $check_args = 
		// if(!$check_args){
			// return FALSE;
		// } else {
			// if(!isset($homepage['enable'])){
				// $homepage['enable'] = '';
			// }
			// $homepage['campaign_id'] = (int) $homepage['campaign_id'];
			// try {
				// $this->app_component->update(array('campaign_id' => $campaign_id),
					// array('$set' => array(
						// 'invite' => $invite
						// )
					// )
				// );
			// }
			// catch(MongoCursorException $e) {
				// return FALSE;
			// }
		// }
	// }
	
	/**
	 * Check homepage data
	 * @param $homepage
	 * @author Manassarn M.
	 */
	function homepage_data_check($homepage = array()){
		return arenotempty($homepage, array('image', 'message'));
	}
	
	/**
	 * Process homepage data
	 * @param $homepage
	 * @author Manassarn M.
	 */
	function homepage_data_process($homepage = array()){
		if(!isset($homepage['enable'])){
			$homepage['enable'] = FALSE;
		}
		return $homepage;
	}
	
	/**
	 * Get homepage by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_homepage_by_campaign_id($campaign_id = NULL){
		$result = $this->app_component
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return issetor($result['homepage'], NULL);
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
	function update_homepage_by_campaign_id($campaign_id = NULL, $homepage = NULL){
		$check_args = !empty($campaign_id) && $this->homepage_data_check($homepage);
		if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $campaign_id;
			$homepage = $this->homepage_data_process($homepage);
			return $this->app_component->update(array('campaign_id' => $campaign_id),
				array('$set' => array(
					'homepage' => $homepage
					)
				)
			);
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
	 *			'acceptance_score' => array(
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
	// function add_invite($invite = array()){
		// $check_args = arenotempty($invite, array('campaign_id','criteria','message')) 
			// && arenotempty($invite['criteria'], array('score','maximum','cooldown','acceptance_score')) 
			// && arenotempty($invite['criteria']['acceptance_score'], array('page','campaign')) 
			// && arenotempty($invite['message'], array('title','text','image'));
		// if(!$check_args){
			// return FALSE;
		// } else {
			// $invite['campaign_id'] = (int) $invite['campaign_id'];
			// $invite['criteria']['score'] = (int) $invite['criteria']['score'];
			// $invite['criteria']['maximum'] = (int) $invite['criteria']['maximum'];
			// $invite['criteria']['cooldown'] = (int) $invite['criteria']['cooldown'];
			
			// if(!isset($invite['facebook_invite'])){
				// $invite['facebook_invite'] = FALSE;
			// }
			// if(!isset($invite['email_invite'])){
				// $invite['email_invite'] = FALSE;
			// }
			// try {
				// $this->app_component->update(array('campaign_id' => $campaign_id),
					// array('$set' => array(
						// 'invite' => $invite
						// )
					// )
				// );
			// }
			// catch(MongoCursorException $e) {
				// return FALSE;
			// }
			
			// return TRUE;
		// }
	// }
	
	/**
	 * Check invite data
	 * @param $invite
	 * @author Manassarn M.
	 */
	function invite_data_check($invite = array()){
		return arenotempty($invite, array('criteria','message')) 
			&& arenotempty($invite['criteria'], array('score','maximum','cooldown','acceptance_score')) 
			&& arenotempty($invite['criteria']['acceptance_score'], array('page','campaign')) 
			&& arenotempty($invite['message'], array('title','text','image'));
	}
	
	/**
	 * Process invite data
	 * @param $invite
	 * @author Manassarn M.
	 */
	function invite_data_process($invite = array()){
		$invite['criteria']['score'] = (int) $invite['criteria']['score'];
		$invite['criteria']['maximum'] = (int) $invite['criteria']['maximum'];
		$invite['criteria']['cooldown'] = (int) $invite['criteria']['cooldown'];
		
		if(!isset($invite['facebook_invite'])){
			$invite['facebook_invite'] = FALSE;
		}
		if(!isset($invite['email_invite'])){
			$invite['email_invite'] = FALSE;
		}
		return $invite;
	}
	
	/**
	 * Get invite by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_invite_by_campaign_id($campaign_id = NULL){
		$result = $this->app_component
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return issetor($result['invite'], NULL);
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
	 *			'acceptance_score' => array(
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
	function update_invite_by_campaign_id($campaign_id = NULL, $invite = NULL){
		$check_args = !empty($campaign_id) && $this->invite_data_check($invite);
		if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $campaign_id;
			$invite = $this->invite_data_process($invite);
			
			return $this->app_component->update(array('campaign_id' => $campaign_id),
				array('$set' => array(
					'invite' => $invite
					)
				)
			);
		}
	}
	
	//Sharebutton
	
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
	// function add_sharebutton($sharebutton = array()){
		// $check_args = arenotempty($sharebutton, array('campaign_id','criteria','message')) 
			// && arenotempty($sharebutton['criteria'], array('score','maximum','cooldown')) 
			// && arenotempty($sharebutton['message'], array('title','text','caption','image'));
		// if(!$check_args){
			// return FALSE;
		// } else {
			// $sharebutton['campaign_id'] = (int) $sharebutton['campaign_id'];
			// $sharebutton['criteria']['score'] = (int) $sharebutton['criteria']['score'];
			// $sharebutton['criteria']['maximum'] = (int) $sharebutton['criteria']['maximum'];
			// $sharebutton['criteria']['cooldown'] = (int) $sharebutton['criteria']['cooldown'];
			// if(!isset($sharebutton['facebook_button'])){
				// $sharebutton['facebook_button'] = FALSE;
			// }
			// if(!isset($sharebutton['twitter_button'])){
				// $sharebutton['twitter_button'] = FALSE;
			// }
			// return $this->app_component->insert($sharebutton);
		// }
	// }
	
	/**
	 * Check sharebutton data
	 * @param $sharebutton
	 * @author Manassarn M.
	 */
	function sharebutton_data_check($sharebutton = array()){
		return arenotempty($sharebutton, array('criteria','message')) 
			&& arenotempty($sharebutton['criteria'], array('score','maximum','cooldown')) 
			&& arenotempty($sharebutton['message'], array('title','text','caption','image'));
	}
	
	/**
	 * Process sharebutton data
	 * @param $sharebutton
	 * @author Manassarn M.
	 */
	function sharebutton_data_process($sharebutton = array()){
		$sharebutton['criteria']['score'] = (int) $sharebutton['criteria']['score'];
		$sharebutton['criteria']['maximum'] = (int) $sharebutton['criteria']['maximum'];
		$sharebutton['criteria']['cooldown'] = (int) $sharebutton['criteria']['cooldown'];
		if(!isset($sharebutton['facebook_button'])){
			$sharebutton['facebook_button'] = FALSE;
		}
		if(!isset($sharebutton['twitter_button'])){
			$sharebutton['twitter_button'] = FALSE;
		}
		return $sharebutton;
	}
	
	/**
	 * Get sharebutton by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_sharebutton_by_campaign_id($campaign_id = NULL){
		$result = $this->app_component
			->findOne(array('campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return issetor($result['sharebutton'], NULL);
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
	function update_sharebutton_by_campaign_id($campaign_id = NULL, $sharebutton = NULL){
		$check_args = !empty($campaign_id) && $this->sharebutton_data_check($sharebutton);
		if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $campaign_id;
			$sharebutton = $this->sharebutton_data_process($sharebutton);
			return $this->app_component->update(array('campaign_id' => $campaign_id),
				array('$set' => array(
					'sharebutton' => $sharebutton
					)
				)
			);
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
	
	/**
	 * Add app_component by campaign_id
	 * @param $app_component = array(
	 *		'campaign_id' => [campaign_id] 
	 *		[component_1] => [component 1 array data] 
	 * 		[component_2] => [component 2 array data]
	 * 		and so on...
	 * )
	 * @author Manasssarn M.
	 */
	function add($app_component = array()){
		$check_args = !empty($app_component['campaign_id']);
		if(!$check_args){
			return FALSE;
		} else {
			$campaign_id = (int) $app_component['campaign_id'];
			unset($app_component['campaign_id']);
			foreach($app_component as $component_name => &$info){
				if(!call_user_func(array($this, $component_name.'_data_check'),$info)){
					
				} else {
					$info = call_user_func(array($this, $component_name.'_data_process'), $info);
				}
			}
			unset($info);
			$app_component['campaign_id'] = $campaign_id;
			return $this->app_component->insert($app_component);
		}
	}
  
  /**
   * delete app_component 
   * @param campaign_id
   * 
   * @return result bolean
   * 
   * @author Metwara Narksook
   */
  function delete($campaign_id = NULL){
    $check_args = isset($campaign_id);
    if($check_args){
      return $this->app_component
                  ->remove(array("campaign_id" => $campaign_id), 
                  array('$atomic' => TRUE));
    }else{
      return FALSE;
    }
  }
}

/* End of file app_component_model.php */
/* Location: ./application/models/app_component_model.php */