<?php
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
}