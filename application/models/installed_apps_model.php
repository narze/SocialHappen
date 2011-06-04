<?php
class Installed_apps_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * [Deprecated]
	 * Check if app is prepared for installation
	 * @param $app_id, $company_id, $user__id
	 * @return boolean
	 */
	function check_install_app($app_id = NULL, $company_id = NULL, $user_id = NULL){
		return true;
	}
	
	/* 
	 * Get installed apps
	 * @param $page_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_installed_apps_by_page_id($page_id = NULL){
		return $this->db->get_where('installed_apps', array('page_id' => $page_id))->result_array();
	}

	/*
	 * Get installed apps
	 * @param $company_id
	 * @return array
	 * @author Manassarn M.
	 * @author Prachya P.
	 */
	function get_installed_apps_by_company_id($company_id = NULL){
		$this->db->join('app','installed_apps.app_id=app.app_id');
		return $this->db->get_where('installed_apps',array('company_id'=>$company_id))->result_array();
	}
	
	/**
	 * Get installed app profile
	 * @param $app_install_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_app_profile_by_app_install_id($app_install_id = NULL){
		$result = $this->db->get_where('installed_apps',array('app_install_id'=>$app_install_id))->result_array();
		return $result[0];
	}
	
	/**
	 * Adds installed app
	 * @param array $data
	 * @return $app_install_id
	 * @author Manassarn M.
	 */
	function add_installed_app($data = array()){
		$this -> db -> insert('installed_apps', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Removes installed app
	 * @param $installed_app_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_installed_app($app_install_id = NULL){
		$this->db->delete('installed_apps', array('app_install_id' => $app_install_id));
		return $this->db->affected_rows();
	}
	
	/**
	 * Update page_id
	 * @param $app_install_id, @page_id
	 * @return TRUE if update is successful
	 */
	function update_page_id($app_install_id = NULL, $page_id = NULL){
		$this->db->update('installed_apps', array('page_id'=>$page_id), array('app_install_id' =>$app_install_id));
		return $this->db->affected_rows()==1;
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		
		$this -> db -> insert('installed_apps', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('installed_apps', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('installed_apps', $data, $where);
	}

	function delete($id) {
		$this -> db -> delete('installed_apps', array('app_install_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('installed_apps');
	}

	function get_installed_apps_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_app_by_company($company_id,$limit = 20, $offset =0){
		return $this -> _get( array('company_id'=>$company_id), $limit, $offset);
	}
	
	function deactivate_app($app_install_id){
		$this->update(array('app_install_available'=> FALSE), array('app_install_id' => $app_install_id));
	}
	
	function activate_app($app_install_id){
		$this->update(array('app_install_available'=> TRUE), array('app_install_id' => $app_install_id));
	}

	function get_app_install_by_app_install_id($app_install_id){
		$app = $this -> _get( array('app_install_id'=>$app_install_id), 1, 0);
		if($app != NULL){
			return $app[0];
		} else{
			return NULL;
		}
	}
}
/* End of file installed_apps_model.php */
/* Location: ./application/models/installed_apps_model.php */