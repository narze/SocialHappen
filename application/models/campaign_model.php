<?php

/**
 * Campaign_model
 */

class Campaign_model extends CI_Model {
	var $campaign_id = '';

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get page campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_campaigns_by_page_id($page_id =NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this -> db -> get_where('campaign', array('page_id' => $page_id)) -> result_array();
	}
	
	/**
	 * Get page campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function get_page_campaigns_by_page_id_and_campaign_status_id($page_id = NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this -> db -> get_where('campaign', array('page_id' => $page_id, 'campaign.campaign_status_id' => $campaign_status_id)) -> result_array();
	}

	/**
	 * Get app campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id($app_install_id =NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this -> db -> get_where('campaign', array('campaign.app_install_id' => $app_install_id)) -> result_array();
	}
		
	/**
	 * Get app campaigns
	 * @param $app_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id = NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this -> db -> get_where('campaign', array('campaign.app_install_id' => $app_install_id, 'campaign.campaign_status_id' => $campaign_status_id)) -> result_array();
	}

	/**
	 * Get campaign profile
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_campaign_id($campaign_id =NULL) {
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get_where('campaign', array('campaign_id' => $campaign_id)) -> result_array();
		return issetor($result[0]);
	}

	/**
	 * Get campaign profile
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_app_install_id($app_install_id =NULL) {
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id', 'left');
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get_where('campaign', array('campaign.app_install_id' => $app_install_id)) -> result_array();
		return issetor($result[0]);
	}
	
	/**
	 * Adds campaign
	 * @param array $data
	 * @author Manassarn M.
	 * @author Wachiraph C.
	 */
	function add_campaign($data = array()){
		if($this -> db -> insert('campaign', $data))
			return $this->db->insert_id();
		return 0;
	}

	/**
	 * Update existed campaign by campaign id
	 * @param $campaign_id
	 * @param $data
	 * @author Wachiraph C.
	 */
	function update_campaign_by_id($campaign_id = 0, $data = array()){
		$this -> db -> update('campaign', $data, array('campaign_id' => $campaign_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Removes campaign
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function remove_campaign($campaign_id = NULL){
		$this->db->delete('campaign', array('campaign_id' => $campaign_id));
		return $this->db->affected_rows();
	}

	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('campaign', $this);
		return $this -> db -> insert_id();
	}

	function get($where = array(), $limit =0, $offset =0) {

		// join campaign_status table
		$this -> db -> join('campaign_status', 'campaign.campaign_status_id=campaign_status.campaign_status_id');

		$query = $this -> db -> get_where('campaign', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('campaign', $data, $where);
	}

	function delete($id) {
		$this -> db -> delete('campaign', array('campaign_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('campaign');
	}

}

/* End of file campaign_model.php */
/* Location: ./application/models/campaign_model.php */
