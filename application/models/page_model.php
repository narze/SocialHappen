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
	 * @param $facebook_page_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_page_profile_by_facebook_page_id($facebook_page_id = NULL){
		$result = $this->db->get_where('page', array('facebook_page_id' => $facebook_page_id))->result_array();
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
		if($limit){
			$this->db->limit($limit, $offset);
		}
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
		// $this->db->where(array('facebook_page_id' => $data['facebook_page_id']));
		// if($this -> db ->count_all_results('page')>0) {
		// 	return FALSE;
		// }
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
		if($limit){
			$this->db->limit($limit, $offset);
		}
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
	 * Add page user fields, if exists, just update
	 * @param $page_id
	 * @param array $fields
	 * @author Manassarn M.
	 */
	function add_page_user_fields_by_page_id($page_id = NULL, $fields = array()){
		if(!$fields || !$page = $this->get_page_profile_by_page_id($page_id)){
			return FALSE;
		}
		$page_fields = json_decode($page['page_user_fields'], TRUE);
		$field_names = array_map(create_function('$field', 'return $field["name"];'), $page_fields);
		$added_ids = array();
		$templates = $this->get_user_field_templates();
		$update_fields = array();
		foreach($fields as $field){

			if(!issetor($field['name']) || !issetor($field['type']) || !issetor($field['label']) ||
				!in_array($field['type'], array('text','textarea','checkbox','radio')) ||
				(in_array($field['type'], array('checkbox','radio')) && (!isset($field['items']) || 
				!$field['items'] || !is_array($field['items']) || in_array('',$field['items'])))
				) {
				return FALSE;
			}
			if(isset($field['required']) && $field['required']){ //if isset and is not FALSE (true, 1, 'on'), it is true
				$field['required'] = TRUE;	
			}
			
			if(!isset($field['required'])){ //if not specified
				$field['required'] = FALSE;	
			}
			
			if(isset($templates[$field['name']]['verify_message'])){
				$field['verify_message'] = $templates[$field['name']]['verify_message'];
			} else {
				switch($field['type']){
					case 'text' : $field['verify_message'] = 'Please enter your '. strtolower($field['label']) . '.'; break;
					case 'textarea' : $field['verify_message'] = 'Please enter your '. strtolower($field['label']) . '.'; break;
					case 'checkbox' : $field['verify_message'] = 'Please select at least one of your '. strtolower($field['label']) . '.'; break;
					case 'radio' : $field['verify_message'] = 'Please select your '. strtolower($field['label']) . '.'; break;
				}
			}
			
			if(isset($templates[$field['name']]['options']) && is_array($templates[$field['name']]['options'])){
				$field['options'] = $templates[$field['name']]['options'];
			} else {
				$field['options'] = NULL;
			}
			$key = array_search($field['name'], $field_names);
			if($key !== FALSE){ //If exists, update
				foreach($field as $field_key => $field_value){
					$page_fields[$key][$field_key] = $field_value;
				}
			} else { //Add new field
				$page_fields[] = $field; //Extend old fields
				end($page_fields);
				$added_ids[] = key($page_fields);
			}
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
	 * DEPRECATED
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
			if(!isset($field['required'])){ //if not specified
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
	
	/**
	 * Get user field templates
	 * @author Manassarn M.
	 */
	function get_user_field_templates(){
		$templates = array(
			'id_card_number' => array(
				'name' => 'id_card_number',
				'label' => 'ID Card Number',
				'type' => 'text',
				'verify_message' => 'Please enter your ID card number.'
			),
			'gender' => array(
				'name' => 'gender',
				'label' => 'Gender',
				'type' => 'radio',
				'items' => array('Male','Female','Not sure'),
				'verify_message' => 'Please select your gender.'
			),
			'date_of_birth' => array(
				'name' => 'date_of_birth',
				'label' => 'Date of Birth',
				'type' => 'text',
				'verify_message' => 'Please select your date of birth.',
				'options' => array('calendar' => 'dd-mm-yy')
			),
			'education' => array(
				'name' => 'education',
				'label' => 'Education',
				'type' => 'text',
				'verify_message' => 'Please enter your education.'
			),
			'occupation' => array(
				'name' => 'occupation',
				'label' => 'Occupation',
				'type' => 'text',
				'verify_message' => 'Please enter your occupation.'
			),
			'income' => array(
				'name' => 'income',
				'label' => 'Income',
				'type' => 'text',
				'verify_message' => 'Please enter your monthly income.'
			),
			'marital_status' => array(
				'name' => 'marital_status',
				'label' => 'Marital Status',
				'type' => 'radio',
				'items' => array('Single', 'Engaged', 'Married', 'Widowed', 'Divorced'),
				'verify_message' => 'Please select your marital status.'
			),
			'number_of_children' => array(
				'name' => 'number_of_children',
				'label' => 'Number of Children',
				'type' => 'text',
				'verify_message' => 'Please enter number of children.'
			),
			'address' => array(
				'name' => 'address',
				'label' => 'Address',
				'type' => 'text',
				'verify_message' => 'Please enter your address.'
			),
			'street' => array(
				'name' => 'street',
				'label' => 'Street',
				'type' => 'text',
				'verify_message' => 'Please enter your street.'
			),
			'town' => array(
				'name' => 'town',
				'label' => 'Town',
				'type' => 'text',
				'verify_message' => 'Please enter your town.'
			),
			'city' => array(
				'name' => 'city',
				'label' => 'City',
				'type' => 'text',
				'verify_message' => 'Please enter your city.'
			),
			'zip_code' => array(
				'name' => 'zip_code',
				'label' => 'Zip Code',
				'type' => 'text',
				'verify_message' => 'Please enter your zip code.'
			),
			'country' => array(
				'name' => 'country',
				'label' => 'Country',
				'type' => 'text',
				'verify_message' => 'Please enter your country.'
			),
			'telephone' => array(
				'name' => 'telephone',
				'label' => 'Telephone',
				'type' => 'text',
				'verify_message' => 'Please enter your telephone number.'
			),
			'mobile_phone' => array(
				'name' => 'mobile_phone',
				'label' => 'Mobile Phone',
				'type' => 'text',
				'verify_message' => 'Please enter your mobile phone number.'
			),
			'website' => array(
				'name' => 'website',
				'label' => 'Website',
				'type' => 'text',
				'verify_message' => 'Please enter your website.'
			),
			'twitter' => array(
				'name' => 'twitter',
				'label' => 'Twitter',
				'type' => 'text',
				'verify_message' => 'Please enter your Twitter account name.'
			),
			'facebook' => array(
				'name' => 'facebook',
				'label' => 'Facebook',
				'type' => 'text',
				'verify_message' => 'Please enter your Facebook url.'
			),
		);
		return $templates;
	}
	
	/**
	 * Update facebook_tab_url
	 * @param $page_id
	 * @param $facebook_tab_url
	 */
	function update_facebook_tab_url_by_page_id($page_id = NULL, $facebook_tab_url = NULL){
		return $this->db->update('page', array('facebook_tab_url' => $facebook_tab_url), array('page_id'=>$page_id));
	}
	
	/**
	 * Update facebook_tab_url
	 * @param $facebook_page_id
	 * @param $facebook_tab_url
	 */
	function update_facebook_tab_url_by_facebook_page_id($facebook_page_id = NULL, $facebook_tab_url = NULL){
		return $this->db->update('page', array('facebook_tab_url' => $facebook_tab_url), array('facebook_page_id'=>$facebook_page_id));
	}
}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */