<?php
class Install_app_model extends CI_Model {
	var $app_id = '';
	var $company_id = '';
	var $user_facebook_id = '';
	var $install_date = '';

	function __construct() {
		parent::__construct();
	}

	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('install_app', $this);
		return $this->db->insert_id();
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get_where('install_app', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('install_app', $data, $where);
	}

	function delete($app_id, $company_id, $user_facebook_id) {
		$this -> db -> delete('install_app', array('app_id' => $app_id,
													'company_id' => $company_id,
													'user_facebook_id' => $user_facebook_id));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('install_app');
	}
	
	function check_install_app($app_id, $company_id, $user_facebook_id){
		return ($this->count_all(array( 'app_id' => $app_id,
										'company_id' => $company_id,
										'user_facebook_id' => $user_facebook_id))) > 0;
	}
	
	function add_new_request($app_id, $company_id, $user_facebook_id){
		if($this->check_install_app($app_id, $company_id, $user_facebook_id)){
			$this->update(array('install_date' => date ("Y-m-d H:i:s", time())),
							array( 'app_id' => $app_id,
										'company_id' => $company_id,
										'user_facebook_id' => $user_facebook_id));
		}else{
			$this->add(array( 'app_id' => $app_id,
										'company_id' => $company_id,
										'user_facebook_id' => $user_facebook_id,
										'install_date' => date ("Y-m-d H:i:s", time())));
		}
	}
}