<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Company_model extends CI_Model {
	var $company_id;
	var $company_name = '';
	var $company_address = '';
	var $company_email = '';
	var $company_telephone = ''; 
	var $company_register_date;
	var $company_username = '';
	var $company_password = '';
	var $company_facebook_id = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get profile
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function get_company_profile_by_company_id($company_id = NULL){
		$result = $this->db->get_where('company',array('company_id'=>$company_id))->result_array();
		return issetor($result[0], NULL);
	}
	
	/**
	 * Get all profile
	 * @author Metwara Narksook
	 */
	function get_company_profile($limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$result = $this->db->get_where('company',array())->result_array();
		return issetor($result, NULL);
	}
	
	/**
	 * Count all profile
	 * @author Metwara Narksook
	 */
	function count_company_profile(){
		$result = $this->db->count_all_results('company');
		return issetor($result, NULL);
	}
	
	/**
	 * Get profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_company_profile_by_page_id($page_id = NULL){
		$this->db->join('page','company.company_id=page.company_id');
		$this->db->select(array('company.company_id','creator_user_id','company_name','company_detail','company_address'
						,'company_email','company_telephone','company_register_date','company_username','company_password','company_image'));
		$result = $this->db->get_where('company',array('page_id'=>$page_id))->result_array();
		return issetor($result[0], NULL);
	}
	
	/**
	 * Get profile
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_company_profile_by_campaign_id($campaign_id = NULL){
		$this->db->join('installed_apps','installed_apps.company_id=company.company_id');
		$this->db->join('campaign','campaign.app_install_id=installed_apps.app_install_id');
		$this->db->select(array('company.company_id','creator_user_id','company_name','company_detail','company_address'
						,'company_email','company_telephone','company_register_date','company_username','company_password','company_image'));
		$result = $this->db->get_where('company',array('campaign_id'=>$campaign_id))->result_array();
		return issetor($result[0], NULL);
	}
		
	/**
	 * Get profile
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_company_profile_by_app_install_id($app_install_id = NULL){
		$this->db->join('installed_apps','installed_apps.company_id=company.company_id');
		$this->db->select(array('company.company_id','creator_user_id','company_name','company_detail','company_address'
						,'company_email','company_telephone','company_register_date','company_username','company_password','company_image'));
		$result = $this->db->get_where('company',array('app_install_id'=>$app_install_id))->result_array();
		return issetor($result[0], NULL);
	}
	
	/**
	 * Adds company
	 * @param array $data
	 * @author Manassarn M.
	 */
	function add_company($data = array()){
		$this -> db -> insert('company', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Removes company
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function remove_company($company_id = NULL){
		$this->db->delete('company', array('company_id' => $company_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_companies_by_user_id($user_id = NULL){
		$this->db->order_by("company_id", "asc"); 
		return $this->db->get_where('company',array('creator_user_id'=>$user_id))->result_array();
	}
	
	/**
	 * Update company profile
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function update_company_profile_by_company_id($company_id = NULL, $data = array()){
		return $this->db->update('company', $data, array('company_id' => $company_id));
	}
}