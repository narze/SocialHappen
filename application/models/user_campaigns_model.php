<?php
class User_campaigns_model extends CI_Model {
	var $campaign_id = '';

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get campaign users
	 * @param $campaign_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_campaign_users_by_campaign_id($campaign_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$result = $this -> db -> get_where('user_campaigns', array('campaign_id' => $campaign_id)) -> result_array();
		return $this->socialhappen->map_v($result, 'user_gender');
	}
	
	/**
	 * Count campaign users
	 * @param $campaign_id
	 * @return array
	 * @author Manassarn M.
	 */
	function count_campaign_users_by_campaign_id($campaign_id = NULL){
		return $this -> db -> where(array('campaign_id' => $campaign_id)) -> count_all_results('user_campaigns');
	}

	/**
	 * Get user campaigns
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_campaigns_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		$result = $this -> db -> get_where('user_campaigns', array('user.user_id' => $user_id)) -> result_array();
		return $this->socialhappen->map_v($result, array('campaign_status', 'user_gender'));
	}
	
	/**
	 * Adds user campaign
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_campaign($data = array()){
		if(!isset($data['user_id']) || !isset($data['campaign_id'])){
			return FALSE;
		}
		if($this->is_user_in_campaign($data['user_id'],$data['campaign_id'])){
			return FALSE;
		}
		return $this -> db -> insert('user_campaigns', $data);
	}
	
	/**
	 * Removes user_campaign
	 * @param $user_id
	 * @param $campaign_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user_campaign($user_id = NULL, $campaign_id = NULL){
		$this->db->delete('user_campaigns', array('user_id' => $user_id, 'campaign_id' => $campaign_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Count user campaigns
	 * @param $user_id
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function count_user_campaigns_by_user_id_and_page_id_and_campaign_status_id($user_id = NULL, $page_id = NULL, $campaign_status_id = NULL){
		$this->db->where(array('user_id' => $user_id, 'page_id' => $page_id));
		if(isset($campaign_status_id)){
			$this->db->where('campaign.campaign_status_id',$campaign_status_id);
		}
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		$this->db->join('installed_apps','campaign.app_install_id=installed_apps.app_install_id');
		$this -> db -> count_all_results('user_campaigns');
	}

	function is_user_in_campaign($user_id = NULL, $campaign_id = NULL){
		if(!$user_id || !$campaign_id){
			return FALSE;
		}
		$this->db->where(array('user_id' => $user_id, 'campaign_id' => $campaign_id));
		return $this->db->count_all_results('user_campaigns') === 1;
	}

	function get_incoming_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_start_timestamp) > CURRENT_TIMESTAMP()');
		$this->db->order_by('campaign_start_timestamp', 'desc');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		$result = $this -> db -> get_where('user_campaigns') -> result_array();
		return $this->socialhappen->map_v($result, array('campaign_status', 'user_gender'));
	}

	function get_active_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_start_timestamp) <= CURRENT_TIMESTAMP()');
		$this->db->where('TIMESTAMP(campaign_end_timestamp) >= CURRENT_TIMESTAMP()');
		$this->db->order_by('campaign_start_timestamp', 'desc');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		$result = $this -> db -> get_where('user_campaigns') -> result_array();
		return $this->socialhappen->map_v($result, array('campaign_status', 'user_gender'));
	}

	function get_expired_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_end_timestamp) < CURRENT_TIMESTAMP()');
		$this->db->order_by('campaign_start_timestamp', 'desc');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		$result = $this -> db -> get_where('user_campaigns') -> result_array();
		return $this->socialhappen->map_v($result, array('campaign_status', 'user_gender'));
	}

	function count_incoming_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_start_timestamp) > CURRENT_TIMESTAMP()');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		return $this->db->count_all_results('user_campaigns');
	}

	function count_active_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_start_timestamp) <= CURRENT_TIMESTAMP()');
		$this->db->where('TIMESTAMP(campaign_end_timestamp) >= CURRENT_TIMESTAMP()');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		return $this->db->count_all_results('user_campaigns');
	}

	function count_expired_user_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('user.user_id',$user_id);
		$this->db->where('campaign_status_id', 2); //enabled
		$this->db->where('TIMESTAMP(campaign_end_timestamp) < CURRENT_TIMESTAMP()');
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		return $this->db->count_all_results('user_campaigns');
	}
}

/* End of file user_campaigns_model.php */
/* Location: ./application/models/user_campaigns_model.php */
