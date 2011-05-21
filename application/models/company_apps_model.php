<?php
class Company_apps_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get company apps
	 * @param $company_id
	 * @author Prachya P.
	 * @author Wachiraph C.
	 */
	function get_company_apps($company_id = NULL){
		if(!$company_id) return array();
		
		$this->db->join('sh_installed_apps','sh_company_apps.app_id = sh_installed_apps.app_id ');
		
		return $this->db->get_where('company_apps',array('sh_company_apps.company_id'=>$company_id))->result();
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