 <?php
/**
 * Pending invitation
 * @author Manassarn M,
 */
class Invite_pending_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->pending = sh_mongodb_load( array(
			'database' => 'invite',
			'collection' => 'pending'
		));
	}
		
	function create_index(){
		return $this->pending->ensureIndex(array('user_facebook_id' => 1, 'campaign_id' => 1), array('unique' => 1));
	}
	
	function drop_collection(){
		return $this->pending->drop();
	}

	function add($user_facebook_id = NULL, $campaign_id = NULL, $facebook_page_id = NULL, $invite_key = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		}
		$pending = array('user_facebook_id' => (string) $user_facebook_id,
			'campaign_id' => (int) $campaign_id,
			'facebook_page_id' => (string) $facebook_page_id,
			'invite_key' => $invite_key);
		try	{
	 		$result = $this->pending->insert($pending, array('safe' => TRUE));
	 		return get_mongo_id($pending);
		} catch(MongoCursorException $e){
			log_message('error', 'Mongo error : '. $e);
	 		return FALSE;
	 	}
	}

	function get_invite_key_by_user_facebook_id_and_campaign_id($user_facebook_id = NULL, $campaign_id = NULL){
		if(!$user_facebook_id || !$campaign_id){
			return FALSE;
		}
		$result = $this->pending
	      ->findOne(array('user_facebook_id' => (string) $user_facebook_id,
	      'campaign_id' => (int) $campaign_id));
	    
	    $result = obj2array($result);
	    return $result['invite_key'];
	}

	function remove_by_user_facebook_id_and_campaign_id($user_facebook_id = NULL, $campaign_id = NULL){
		if(!$user_facebook_id || !$campaign_id){
			return FALSE;
		}
		return $this->pending
	        ->remove(array("user_facebook_id" => (string) $user_facebook_id,
	        'campaign_id' => (int) $campaign_id), 
	        array('$atomic' => TRUE));
	}

	function get_by_user_facebook_id_and_facebook_page_id($user_facebook_id = NULL, $facebook_page_id = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		} else {
			$result = array();
			$cursor = $this->pending->find(array('user_facebook_id' => (string) $user_facebook_id,
		      'facebook_page_id' => (string) $facebook_page_id));
		    foreach($cursor as $value){
		    	$result[] = $value;
		    }
		    return $result;
		}
	}

	function get_by_user_facebook_id_and_campaign_id($user_facebook_id = NULL, $campaign_id = NULL){
		if(!allnotempty(func_get_args())){
			return FALSE;
		} else {
			$result = $this->pending->findOne(array('user_facebook_id' => (string) $user_facebook_id,
		      'campaign_id' => (int) $campaign_id));
		    return obj2array($result);
		}
	}
}

/* End of file invite_pending_model.php */
/* Location: ./application/models/invite_pending_model.php */