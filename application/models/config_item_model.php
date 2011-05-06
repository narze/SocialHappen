<?php
class Config_item_model extends CI_Model {
	var $app_install_id = '';
	var $config_key = '';
	var $config_value = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function add($data = array()) {
		foreach($data as $var => $key) {
			$this -> {$var} = $key;
		}
		$this -> db -> insert('app', $this);
	}
	
	function add_config_item($app_install_id, $config_key, $config_value){
		$this->add(array('app_install_id' => $app_install_id,
						'config_key' => $config_key,
						'config_value' => $config_value));
	}

	function _get($where = array(), $limit =0, $offset =0) {
		$query = $this -> db -> get('config_item', $where, $limit, $offset);
		return $query -> result();
	}

	function update($data = array(), $where = array()) {
		$this -> db -> update('config_item', $data, $where);
	}
	
	function update_config_item($app_install_id, $config_key, $config_value){
		$this -> db -> update('config_item_template',
						 array('config_value' => $config_value),
						 array('app_install_id' => $app_install_id,
						'config_key' => $config_key));
	}

	function delete($app_install_id, $config_key) {
		$this -> db -> delete('config_item', array('app_install_id' => $app_install_id,
														 'config_key' => $config_key));
	}

	function count_all($where = array()) {
		$this -> db -> where($where);
		return $this -> db -> count_all_results('config_item');
	}
	
	function count_app_config_item($app_install_id) {
		return $this -> db -> count_all(array('app_install_id' => $app_install_id));
	}

	function get_config_item_list($limit =20, $offset =0) {
		return $this -> _get( array(), $limit, $offset);
	}
	
	function get_config_item_list_for_app_install($app_id, $limit =0, $offset =0) {
		return $this -> _get( array('app_install_id' => $app_id), $limit, $offset);
	}
	
	function get_config_item($app_install_id, $config_key) {
		return $this -> _get( array('app_install_id' => $app_install_id, 'config_key' => $config_key), 1, $offset);
	}
	
}