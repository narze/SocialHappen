<?php

/**
 * Campaign_model
 *
 * @author Prachya P.
 */

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
	function get_campaign_users_by_campaign_id($campaign_id =NULL) {
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		return $this -> db -> get_where('user_campaigns', array('campaign_id' => $campaign_id)) -> result_array();
	}

	/**
	 * Get user campaigns
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_campaigns_by_user_id($user_id =NULL) {
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
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
}

/* End of file user_campaigns_model.php */
/* Location: ./application/models/user_campaigns_model.php */
