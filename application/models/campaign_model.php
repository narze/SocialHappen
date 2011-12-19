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
		return $this -> get_page_campaigns_by_page_id_and_campaign_status_id($page_id, NULL, $limit, $offset);
	}
	
	/**
	 * Get page campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function get_page_campaigns_by_page_id_and_campaign_status_id($page_id = NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('page_id',$page_id);
		if(isset($campaign_status_id)){
			$this->db->where('campaign_status_id',$campaign_status_id);
		}
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get('campaign') -> result_array();
		return $this->socialhappen->map_v($result, array('app_install_status', 'campaign_status'));
	}

	/**
	 * Get app campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id($app_install_id =NULL, $limit = NULL, $offset = NULL){
		return $this -> get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id, NULL, $limit, $offset);
	}

	/**
	 * Get app campaigns, ordered
	 * @param $app_install_id
	 * @param $order_by : [title] ['desc'/'asc']
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id_ordered($app_install_id =NULL, $order_by = NULL, $limit = NULL, $offset = NULL){
		$this->db->order_by($order_by);
		return $this -> get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id, NULL, $order_by, $limit, $offset);
	}
		
	/**
	 * Get app campaigns
	 * @param $app_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function get_app_campaigns_by_app_install_id_and_campaign_status_id($app_install_id = NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->where('campaign.app_install_id',$app_install_id);
		if(isset($campaign_status_id)){
			$this->db->where('campaign_status_id',$campaign_status_id);
		}
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get('campaign') -> result_array();
		return $this->socialhappen->map_v($result, array('app_install_status', 'campaign_status'));
	}

	/**
	 * Get campaign profile
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_campaign_profile_by_campaign_id($campaign_id =NULL) {
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get_where('campaign', array('campaign_id' => $campaign_id)) -> result_array();
		return $this->socialhappen->map_one_v($result[0], array('app_install_status', 'campaign_status'));
	}

	/**
	 * Get campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_campaigns_by_app_install_id($app_install_id =NULL) {
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		$result = $this -> db -> get_where('campaign', array('campaign.app_install_id' => $app_install_id)) -> result_array();
		return $this->socialhappen->map_v($result, array('app_install_status', 'campaign_status'));
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
	
	/** 
	 * Count campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_page_id($page_id = NULL){
		return $this->count_campaigns_by_page_id_and_campaign_status_id($page_id);
	}
	
	/** 
	 * Count campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_page_id_and_campaign_status_id($page_id = NULL, $campaign_status_id = NULL){
		$this->db->where(array('page_id' => $page_id));
		if($campaign_status_id) {
			$this->db->where(array('campaign_status_id' => $campaign_status_id));
		}
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this->db->count_all_results('campaign');
	}

	/** 
	 * Count campaigns
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_app_install_id($app_install_id = NULL){
		return $this->count_campaigns_by_app_install_id_and_campaign_status_id($app_install_id);
	}

	/** 
	 * Count campaigns
	 * @param $app_install_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_app_install_id_and_campaign_status_id($app_install_id = NULL, $campaign_status_id = NULL){
		$this->db->where(array('campaign.app_install_id' => $app_install_id));
		if($campaign_status_id) {
			$this->db->where(array('campaign_status_id' => $campaign_status_id));
		}
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this->db->count_all_results('campaign');
	}
	
	/** 
	 * Count campaigns
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_company_id($company_id = NULL){
		return $this->count_campaigns_by_company_id_and_campaign_status_id($company_id);
	}
	
	/** 
	 * Count campaigns
	 * @param $company_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function count_campaigns_by_company_id_and_campaign_status_id($company_id = NULL, $campaign_status_id = NULL){
		$this->db->where(array('installed_apps.company_id' => $company_id));
		if($campaign_status_id) {
			$this->db->where(array('campaign_status_id' => $campaign_status_id));
		}
		$this -> db -> join('installed_apps', 'campaign.app_install_id=installed_apps.app_install_id');
		return $this->db->count_all_results('campaign');
	}
}

/* End of file campaign_model.php */
/* Location: ./application/models/campaign_model.php */
