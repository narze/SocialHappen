<?php
class Company_pages_model extends CI_Model {
	var $company_id = '';
	var $facebook_page_id = '';

	function __construct()
	{
		parent::__construct();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		
		$this -> db -> insert('company_pages', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('company_pages', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('company_pages', $data, $where);
	}

	function delete($company_id, $facebook_page_id) {
		$this -> db -> delete('company_pages', array('company_id' => $company_id, 'facebook_page_id' => $facebook_page_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('company_pages');
	}

	function get_company_pages_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_page_by_company($company_id,$limit = 20, $offset =0){
		return $this -> _get( array('company_id'=>$company_id), $limit, $offset);
	}
}