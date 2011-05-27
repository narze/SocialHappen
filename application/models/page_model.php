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
		return $this->db->get_where('page', array('page_id' => $page_id))->result();
	}
	
	/** 
	 * Get company pages
	 * @param $company_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_company_pages_by_company_id($company_id = NULL){
		return $this->db->get_where('page', array('company_id' => $company_id))->result();
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
}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */