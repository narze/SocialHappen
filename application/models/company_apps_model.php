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
	 * Get app_install
	 * @param $app_install_id
	 * @return array
	 * @author Wachiraph.C
	 */
	function get_app_install_by_app_install_id($app_install_id = NULL){
		$this->db->join('installed_apps','company_apps.app_id=installed_apps.app_id');
		return $this->db->get_where('company_apps',array('app_install_id'=>$app_install_id))->result();
	
	}
	
	/**
	 * Get not installed app by company_id (optional)page_id
	 * @param $company_id,$page_id
	 * @return array
	 * @author Prachya P.
	 */
	function get_company_not_installed_apps($company_id = NULL,$page_id = NULL){
		$installed_app_id=array();
		if($page_id!=NULL)
			$result = $this->db->get_where('installed_apps',array('company_id' => $company_id,'page_id' => $page_id))->result();
		else
			$result = $this->db->get_where('installed_apps',array('company_id' => $company_id))->result();
		foreach($result as $app){
			$installed_app_id[]=$app->app_id;
		}
		$this->db->join('app','company_apps.app_id=app.app_id');
		$this->db->where('company_id',$company_id);
		if(sizeof($installed_app_id)>0)
			$this->db->where_not_in('company_apps.app_id', $installed_app_id);
		return $this->db->get('company_apps')->result();	
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