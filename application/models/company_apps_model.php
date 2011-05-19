<?php
class Company_apps_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Get company apps
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function get_company_apps($company_id = NULL) {
		if(!$company_id)
			return array();
		return $this -> db -> get_where('company_apps', 
										array('company_id' => $company_id, 
											  'page_id' => '0')
									 	) -> result();
	}

	/**
	 * Get page apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_apps($page_id = NULL) {
		if(!$page_id)
			return array();
		return $this -> db -> get_where('company_apps', 
										array('page_id' => $page_id)
									 	) -> result();
	}


	//OLD
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}

		$this -> db -> insert('company_apps', $this);
		return $this -> db -> insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('company_apps', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('company_apps', $data, $where);
	}

	function delete($id) {
		$this -> db -> delete('company_apps', array('app_install_id' => $id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('company_apps');
	}

	function get_company_apps_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}

	function get_app_by_company($company_id, $limit =20, $offset =0) {
		return $this -> _get( array('company_id' => $company_id), $limit, $offset);
	}

	function deactivate_app($app_install_id) {
		$this -> update( array('app_install_available' => FALSE), array('app_install_id' => $app_install_id));
	}

	function activate_app($app_install_id) {
		$this -> update( array('app_install_available' => TRUE), array('app_install_id' => $app_install_id));
	}

	function get_app_install_by_app_install_id($app_install_id) {
		$app = $this -> _get( array('app_install_id' => $app_install_id), 1, 0);
		if($app != NULL) {
			return $app[0];
		} else {
			return NULL;
		}
	}

}

/* End of file company_apps_model.php */
/* Location: ./application/models/company_apps_model.php */
