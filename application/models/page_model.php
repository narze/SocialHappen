<?php
class Page_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get page profile
	 * @param $page_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_page_profile_by_page_id($page_id = NULL){
		$result = $this->db->get_where('page', array('page_id' => $page_id))->result_array();
		return $this->socialhappen->map_one_v($result[0], 'page_status');
	}
	
	/**
	 * Get page profile
	 * @param $campaign_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_page_profile_by_campaign_id($campaign_id = NULL){
		$this->db->join('installed_apps','installed_apps.page_id=page.page_id');
		$this->db->join('campaign','campaign.app_install_id=installed_apps.app_install_id');
		$result = $this->db->get_where('page', array('campaign_id' => $campaign_id))->result_array();
		return $this->socialhappen->map_one_v($result[0], 'page_status');
	}
	
	/**
	 * Get page profile
	 * @param $app_install_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_page_profile_by_app_install_id($app_install_id = NULL){
		$this->db->join('installed_apps','installed_apps.page_id=page.page_id');
		$result = $this->db->get_where('page', array('app_install_id' => $app_install_id))->result_array();
		return issetor($result[0]);
	}
	
	/** 
	 * Get company pages
	 * @param $company_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_company_pages_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->order_by("order_in_dashboard");
		return $this->db->get_where('page', array('company_id' => $company_id))->result_array();
	}
	
	/**
	 * Get page id by facebook_page_id
	 * @param $facebook_page_id
	 * @author Wachiraph C.
	 * @author Manassarn M.
	 */
	function get_page_id_by_facebook_page_id($facebook_page_id =NULL) {
		if(!$facebook_page_id)
			return NULL;
		$result = $this -> db ->select('page_id') -> get_where('page', array('facebook_page_id' => $facebook_page_id))-> result_array();
		return issetor($result[0]['page_id']);
	}
	
	/**
	 * Get facebook_page_id by page id
	 * @param $page_id
	 * @author Wachiraph C.
	 * @author Manassarn M.
	 */
	function get_facebook_page_id_by_page_id($page_id =NULL) {
		if(!$page_id)
			return NULL;
		$result = $this -> db ->select('facebook_page_id') -> get_where('page', array('page_id' => $page_id))-> result_array();
		return issetor($result[0]['facebook_page_id']);
	}
	
	/**
	 * Adds page
	 * @param array $data
	 * @return $page_id
	 * @author Manassarn M.
	 */
	function add_page($data = array()){
		$this -> db -> insert('page', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Removes page
	 * @param $page_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_page($page_id = NULL){
		$this->db->delete('page', array('page_id' => $page_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get app pages
	 * @param $app_install_id
	 * $return array
	 * @author Manassarn M.
	 */
	function get_app_pages_by_app_install_id($app_install_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$result = $this->db->get_where('installed_apps',array('app_install_id' => $app_install_id))->result_array();
		$app_id = $result[0]['app_id'];
		$company_id = $result[0]['company_id'];
		$this->db->join('page','installed_apps.page_id=page.page_id');
		$result = $this->db->get_where('installed_apps',array('app_id' => $app_id, 'page.company_id' => $company_id))->result_array();
		return $this->socialhappen->map_v($result, 'app_install_status');
	}
	
	/**
	 * Get count all apps
	 * @param array $where
	 * @return count
	 * @author Prachya P.
	 */
	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('page');
	}
	
	/**
	 * Update page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function update_page_profile_by_page_id($page_id = NULL, $data = array()){
		if(!$data){
			return FALSE;
		}
		return $this->db->update('page', $data, array('page_id' => $page_id));
	}
	
	/**
	 * Count pages
	 * @param $app_id
	 * @author Manassarn M.
	 */
	function count_pages_by_app_id($app_id = NULL){
		$this->db->where(array('app_id'=>$app_id));
		$this->db->join('installed_apps','page.page_id=installed_apps.page_id');
		return $this->db->count_all_results('page');
	}
	
	/**
	 * Add page user fields
	 * @param $page_id
	 * @param array $fields
	 * @author Manassarn M.
	 */
	function add_page_user_fields_by_page_id($page_id = NULL, $fields = array()){
		if(!$fields || !$page = $this->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		$page_fields = json_decode($page['page_user_fields'], TRUE);
		$added_ids = array();
		foreach($fields as $field){
			if(!issetor($field['name']) || !issetor($field['type']) || !issetor($field['label']) ||
				!in_array($field['type'], array('text','textarea','checkbox','radio')) ||
				(in_array($field['type'], array('checkbox','radio')) && (!isset($field['items']) || !$field['items'] || !is_array($field['items']) || in_array('',$field['items'])))
				) {
				return FALSE;
			}
			if(issetor($field['type']) == 'checkbox'){ //Checkbox cannot be required
				$field['required'] = FALSE;	
			}
			$page_fields[] = $field;
			end($page_fields);
			$added_ids[] = key($page_fields);
		}
		if($this->update_page_profile_by_page_id($page_id, array('page_user_fields' => json_encode($page_fields)))){
			return $added_ids;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get page user fields
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_user_fields_by_page_id($page_id = NULL){
		if(!$page = $this->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		$fields = json_decode($page['page_user_fields'], TRUE);
		return $fields ? $fields : array();
	}
	
	/**
	 * Update page user fields
	 * @param $page_id
	 * @param array $fields
	 * @author Manassarn M.
	 */
	function update_page_user_fields_by_page_id($page_id = NULL, $fields = array()){
		if(!$fields || !$page = $this->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		$page_fields = json_decode($page['page_user_fields'], TRUE);
		foreach($fields as $key => $field){
			if(!isset($page_fields[$key]) ||
				(isset($field['name']) && !$field['name']) || 
				(isset($field['label']) && !$field['label']) ||
				(isset($field['type']) && !in_array($field['type'], array('text','textarea','checkbox','radio'))) ||
				(isset($field['type']) &&  in_array($field['type'], array('checkbox','radio')) && (!isset($field['items']) ||
				!$field['items'] || !is_array($field['items']) || in_array('',$field['items'])))
			){
				return FALSE;
			}
			if(issetor($field['type']) == 'checkbox'){ //Checkbox cannot be required
				$field['required'] = FALSE;	
			}
			foreach($field as $field_key => $field_value){
				$page_fields[$key][$field_key] = $field_value;
			}
		}
		return $this->update_page_profile_by_page_id($page_id, array('page_user_fields' => json_encode($page_fields)));
	}
	
	/**
	 * Remove page user fields
	 * @param $page_id
	 * @param array $ids
	 * @author Manassarn M.
	 */
	function remove_page_user_fields_by_page_id($page_id = NULL, $ids = array()){
		if(!$ids || !$page = $this->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		if(is_int($ids)){
			$ids = array($ids);
		}
		$page_fields = json_decode($page['page_user_fields'], TRUE);
		foreach($ids as $id){
			if(!isset($page_fields[$id])){
				return FALSE;
			}
			unset($page_fields[$id]);
		}
		return $this->update_page_profile_by_page_id($page_id, array('page_user_fields' => json_encode($page_fields)));
	}
}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */