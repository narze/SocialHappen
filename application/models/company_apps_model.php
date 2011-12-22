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
	function get_company_apps_by_company_id($company_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$this->db->join('app','company_apps.app_id=app.app_id');
		$result = $this->db->get_where('company_apps',array('company_id'=>$company_id))->result_array();
		return $this->socialhappen->map_v($result, array('app_type'));
	}
	
	/**
	 * Get app_install
	 * @param $app_install_id
	 * @return array
	 * @author Wachiraph.C
	 */
	function get_app_install_by_app_install_id($app_install_id = NULL){
		$this->db->join('installed_apps','company_apps.app_id=installed_apps.app_id');
		$result = $this->db->get_where('company_apps',array('app_install_id'=>$app_install_id))->result();
		return $this->socialhappen->map_one_v($result[0], 'app_install_status');
	}
	
	/**
	 * Get not installed app by company_id (optional)page_id
	 * @param $company_id,$page_id
	 * @return array
	 * @author Prachya P.
	 */
	function get_company_not_installed_apps($company_id = NULL,$page_id = NULL, $limit = NULL, $offset = NULL){
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$installed_app_id=array();
		if($page_id!=NULL)
			$result = $this->db->get_where('installed_apps',array('company_id' => $company_id,'page_id' => $page_id))->result_array();
		else
			$result = $this->db->get_where('installed_apps',array('company_id' => $company_id))->result_array();
		foreach($result as $app){
			$installed_app_id[]=$app['app_id'];
		}
		$this->db->join('app','company_apps.app_id=app.app_id');
		$this->db->where('company_id',$company_id);
		if(sizeof($installed_app_id)>0)
			$this->db->where_not_in('company_apps.app_id', $installed_app_id);
		$result = $this->db->get('company_apps')->result_array();	
		return $this->socialhappen->map_v($result, 'app_type');
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
	
	/**
	 * Removes company app
	 * @param $company_id
	 * @return Number of affected rows
	 * @author Manassarn M.
	 */
	function remove_company_apps($company_id = NULL){
		$this->db->delete('company_apps', array('company_id' => $company_id));
		return $this->db->affected_rows();
	}
}
/* End of file company_apps_model.php */
/* Location: ./application/models/company_apps_model.php */