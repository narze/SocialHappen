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
		return $result[0];
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
		return $this->db->get_where('company',array('creator_user_id'=>$user_id))->result_array();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('company', $this);
		return $this->db->insert_id();
	}
	
	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('company', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('company', $data, $where);
	}
	
	function update_by_company($company_id, $data = array()) {
		$this->update($data, array('company_id' => $company_id));
	}
	

	function delete($id) {
		$this -> db -> delete('company', array('company_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('company');
	}

	function get_company($company_id,$limit =20, $offset =0){ 
		return $this -> _get( array('company_id'=>$company_id),$limit , $offset);
	}
	
	function get_company_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	
	/**
	 * [Deprecated]
	 * get company list
	 * @param $user_id
	 * @author Teesit M. 
	 */
	function get_company_list_by_user_id($user_id) {
		return $this->db->get_where('company',array('creator_user_id'=>$user_id));
	}
}