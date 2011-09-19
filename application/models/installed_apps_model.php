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
	 * @author Prachya P.
	 */
	function get_installed_apps_by_page_id($page_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->order_by("order_in_dashboard");
		$this->db->join('app','installed_apps.app_id=app.app_id');
		$results = $this->db->get_where('installed_apps', array('page_id' => $page_id))->result_array();
		return $this->socialhappen->map_v($results,array('app_type','app_install_status'));
	}

	/*
	 * Get installed apps
	 * @param $company_id
	 * @return array
	 * @author Manassarn M.
	 * @author Prachya P.
	 */
	function get_installed_apps_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		$this->db->join('app','installed_apps.app_id=app.app_id');
		$results = $this->db->get_where('installed_apps',array('company_id'=>$company_id))->result_array();
		return $this->socialhappen->map_v($results,array('app_type','app_install_status'));
	}
	
	/*
	 * Get installed apps (not in page)
	 * @param $company_id
	 * @return array
	 * @author Prachya P.
	 */
	function get_installed_apps_by_company_id_not_in_page($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->db->limit($limit, $offset);
		//$this->db->where('app_type_id', 3);
		$this->db->join('app','installed_apps.app_id=app.app_id');
		$this->db->order_by("order_in_dashboard");
		$results = $this->db->get_where('installed_apps',array('company_id'=>$company_id,'page_id'=>0))->result_array();
		return $this->socialhappen->map_v($results,array('app_type','app_install_status'));
	}
	
	/*
	 * Count installed apps (not in page)
	 * @param $company_id
	 * @return array
	 * @author Prachya P.
	 */
	function count_installed_apps_by_company_id_not_in_page($company_id = NULL){
		$this->db->join('app','installed_apps.app_id=app.app_id');
		return $this->db->where(array('company_id'=>$company_id,'page_id'=>0))->count_all_results('installed_apps');
	}
	
	/**
	 * Get installed app profile
	 * @param $app_install_id
	 * @return array
	 * @author Manassarn M.
	 */
	function get_app_profile_by_app_install_id($app_install_id = NULL){
		$this->db->join('app','installed_apps.app_id=app.app_id');
		$results = $this->db->get_where('installed_apps',array('app_install_id'=>$app_install_id))->result_array();
		return $this->socialhappen->map_one_v($results[0], array('app_type','app_install_status'));
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
	
	/* 
	 * Count installed apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function count_installed_apps_by_page_id($page_id = NULL){
		$this->db->where(array('page_id' => $page_id));
		$this->db->join('app','installed_apps.app_id=app.app_id');
		return $this->db->count_all_results('installed_apps');
	}
	
	/**
	 * Count installed app by distinct field
	 * @param $distinct,array $where
	 * @return count
	 * @author Prachya P.
	 */
	function count_all_distinct($distinct,$where = array()){
		$this->db->select($distinct);
		$this->db->distinct();
		$this -> db -> where($where);
		$query = $this->db->get('installed_apps');
		return $query->num_rows();
	}
	
	/*
	 * Update data
	 * @author Prachya P.
	 */
	function update($data = array(), $where = array()) {
		$this -> db -> update('installed_apps', $data, $where);
	}
}
/* End of file installed_apps_model.php */
/* Location: ./application/models/installed_apps_model.php */