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
	function get_campaign_users_by_campaign_id($campaign_id =NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		return $this -> db -> get_where('user_campaigns', array('campaign_id' => $campaign_id)) -> result_array();
	}
	
	/**
	 * Count campaign users
	 * @param $campaign_id
	 * @return array
	 * @author Manassarn M.
	 */
	function count_campaign_users_by_campaign_id($campaign_id =NULL){
		return $this -> db -> where(array('campaign_id' => $campaign_id)) -> count_all_results('user_campaigns');
	}

	/**
	 * Get user campaigns
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_campaigns_by_user_id($user_id =NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		$this->db->join('campaign','user_campaigns.campaign_id=campaign.campaign_id');
		return $this -> db -> get_where('user_campaigns', array('user.user_id' => $user_id)) -> result_array();
	}
	
	/**
	 * Adds user campaign
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_campaign($data = array()){
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
}

/* End of file user_campaigns_model.php */
/* Location: ./application/models/user_campaigns_model.php */
