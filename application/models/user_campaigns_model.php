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
	 * @author Manassarn M.
	 */
	function get_campaign_users_by_campaign_id($campaign_id =NULL) {
		if(!$campaign_id)
			return array();
		$this -> db -> join('user', 'user.user_id=user_campaigns.user_id');
		return $this -> db -> get_where('user_campaigns', array('campaign_id' => $campaign_id)) -> result();
	}

}

/* End of file user_campaigns_model.php */
/* Location: ./application/models/user_campaigns_model.php */
