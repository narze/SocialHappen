 <?php
/**
 * Pending invitation
 * @author Manassarn M,
 */
class Invite_pending_model extends CI_Model {
	
	function __construct() {
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
			$this->db = $this->connection->invite;
			
			// select collection
			$this->pending = $this->db->pending;
			
		}catch(Exception $e){
			show_error('Cannot connect to database');
		}
	}
		
	function create_index(){
		return $this->pending->ensureIndex(array('user_facebook_id' => 1, 'campaign_id' => 1), array('unique' => 1));
	}
	
	function drop_collection(){
		return $this->pending->drop();
	}

	function add($user_facebook_id = NULL, $campaign_id = NULL, $invite_key = NULL){
		if(!$user_facebook_id || !$invite_key || !$campaign_id){
			return FALSE;
		}
		$pending = array('user_facebook_id' => (string) $user_facebook_id,
			'campaign_id' => (int) $campaign_id,
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
}

/* End of file invite_pending_model.php */
/* Location: ./application/models/invite_pending_model.php */