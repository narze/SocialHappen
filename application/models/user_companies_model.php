<?php
class User_companies_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Adds user company
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_user_company($data = array()){
		return $this -> db -> insert('user_companies', $data);
	}
	
	/**
	 * Removes user_company
	 * @param $user_id
	 * @param $company_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_user_company($user_id = NULL, $company_id = NULL){
		$this->db->delete('user_companies', array('user_id' => $user_id, 'company_id' => $company_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Get user companies
	 * @param $user_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_user_companies_by_user_id($user_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->join('company','user_companies.company_id=company.company_id');
		return $this->db->get_where('user_companies', array('user_id' => $user_id))->result_array();
	}

	/**
	 * Get admins by company id
	 * @param $company_id
	 * @author Wachiraph C.
	 */
	function get_user_companies_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		return $this->db->get_where('user_companies', array('company_id' => $company_id))->result_array();
	}
	
	/**
	 * Get company users
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function get_company_users_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->join('user', 'user_companies.user_id = user.user_id');
		$this->db->join('user_role', 'user_companies.user_role = user_role.user_role_id','left');
		$result = $this->db->get_where('user_companies', array('company_id' => $company_id))->result_array();
		return $this->socialhappen->map_v($result, 'user_gender');
	}
	
	/**
	 * Check if user is company admin
	 * @param $user_id
	 * @param $company_id
	 */
	function is_company_admin($user_id = NULL, $company_id = NULL){
		$this->db->where(array('user_id' => $user_id, 'company_id' => $company_id));
		$count = $this->db->count_all_results('user_companies');
		return $count == 1;
	}
}
/* End of file user_companies_model.php */
/* Location: ./application/models/user_companies_model.php */