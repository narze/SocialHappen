<?php
class Company_apps_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get company apps
	 * @param $company_id
	 * @return array
	 * @author Prachya P.
	 * @author Wachiraph C.
	 * @author Manassarn M.
	 */
	function get_company_apps_by_company_id($company_id = NULL){
		$this->db->join('app','company_apps.app_id=app.app_id');
		return $this->db->get_where('company_apps',array('company_id'=>$company_id))->result();
	}
	
	/**
	 * Adds company app
	 * @param array $data
	 * @return TRUE if inserted successfully
	 * @author Manassarn M.
	 */
	function add_company_app($data = array()){
		return $this -> db -> insert('company_apps', $data);
	}
	
	/**
	 * Removes company app
	 * @param $company_id
	 * @param $app_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_company_app($company_id = NULL, $app_id){
		$this->db->delete('company_apps', array('company_id' => $company_id, 'app_id' => $app_id));
		return $this->db->affected_rows();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		
		$this -> db -> insert('company_apps', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('company_apps', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('company_apps', $data, $where);
	}

	function delete($company_id,$app_id) {
		$this -> db -> delete('company_apps', array('company_id' => $company_id,'$app_id' => $app_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('company_apps');
	}

}
/* End of file company_apps_model.php */
/* Location: ./application/models/company_apps_model.php */